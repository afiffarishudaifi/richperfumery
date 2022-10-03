<?php

namespace App\Http\Controllers\Pembelian;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Yajra\Datatables\Datatables;
use DB;
use App\PembelianModel;
use Auth;

class SuratJalanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
      $id_group = Auth::user()->group_id;
      $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();
      $tombol_create = tombol_create(url('suratjalan_tambah'),$group_where->group_aktif, '2');
      //print_r(url('suratjalan_tambah').'/'.$group_where->group_aktif.'/'.'2');exit();
      return view('admin.suratjalan.index',compact('tombol_create'));
    }

    public function get_penyedia(Request $request){
      $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }
        $search = strtolower($term);
        $d_query= DB::select("SELECT 
          supplier_id, 
          supplier_nama, 
          supplier_alamat, 
          supplier_telp,
          supplier_tempo 
          FROM tbl_supplier WHERE supplier_nama LIKE '%$search%' order by supplier_nama asc");
        return \Response::json($d_query);
    }

    public function get_barang(Request $request){
      $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }
        $search = strtolower($term);
        $d_query= DB::select("SELECT
          b.barang_id,
          b.satuan_id,
          b.barang_kode,
          b.barang_nama,
          b.barang_alias,
          b.barang_id_parent,
          b.barang_status_bahan,
          s.satuan_nama,
          s.satuan_satuan,
          d.detail_harga_barang_harga_jual  harga
        FROM
          tbl_barang AS b
          LEFT JOIN tbl_satuan AS s ON b.satuan_id = s.satuan_id
          LEFT JOIN (
          SELECT
            * 
          FROM
            tbl_detail_harga_barang AS a 
        WHERE
        detail_harga_barang_tanggal = 
        ( SELECT MAX( detail_harga_barang_tanggal ) FROM tbl_detail_harga_barang AS b WHERE a.barang_id = b.barang_id )) AS d ON b.barang_id = d.barang_id
                WHERE b.barang_kode LIKE '%$search%' OR b.barang_nama LIKE '%$search%' OR b.barang_alias LIKE '%$search%'");
        return \Response::json($d_query);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function tambah()
    {
      $data['data']       = $this->data(array());     
      $data['satuan']     = DB::table('tbl_satuan')->orderBy('satuan_nama','asc')->get();
      $data['rekening']   = \Config::get('constants.rekening');
      $data['viabayar']   = \Config::get('constants.viabayar_pemilik');
      $data['carabayar']  = \Config::get('constants.carabayar');
      $data['supplier']   = "";
      return view('admin.suratjalan.create')->with('data',$data);
    }

    public function data($data = array()){
      if($data != null){
        $store['id_pembelian']    = $data->id_pembelian;
        $store['tanggal']         = tgl_full($data->tanggal,'');
        $store['tanggal_faktur']  = tgl_full($data->tanggal_faktur,'');
        $store['tanggal_tempo']   = tgl_full($data->tanggal_tempo,'');
        $store['id_penyedia']     = $data->id_supplier;
        $store['nama_penyedia']   = $data->nama_supplier;
        $store['nomor']           = $data->no_faktur;
        $store['uang_muka']       = $data->uang_muka;
        $store['td_ongkir']       = $data->ongkos_kirim;
        $store['td_potongan']     = $data->total_potongan;
        $store['td_tagihan']      = $data->total_tagihan;
        $store['td_subtotal']     = $data->total_subtotal;
        $store['pajak']           = $data->pajak;
        $store['carabayar']       = $data->carabayar;
        $store['keterangan']      = $data->keterangan;
        $store['jenis']			  = $data->jenis;
        $store['status_jenis']	  = $data->status_jenis;
      }else{  
        $store['id_pembelian']    = "";
        $store['tanggal']         = "";
        $store['tanggal_faktur']  = "";
        $store['tanggal_tempo']   = "";
        $store['id_penyedia']     = "";
        $store['nama_penyedia']   = "";
        $store['nomor']           = "";
        $store['uang_muka']       = "Rp 0";
        $store['td_ongkir']       = "";
        $store['td_potongan']     = "";
        $store['td_tagihan']      = "";
        $store['td_subtotal']     = "";
        $store['pajak']           = "";
        $store['carabayar']       = "";
        $store['keterangan']      = "";
        $store['jenis']			  = "2";
        $store['status_jenis']	  = "2";
      }

      return $store;
    }

    public function simpan(Request $request){
      $id_pembelian = $request->get('id_pembelian');
      $data['tanggal']        = tgl_full($request->get('tanggal'),'99');
      $data['tanggal_tempo']  = tgl_full($request->get('tanggal_tempo'),'99');
      $data['tanggal_faktur'] = tgl_full($request->get('tanggal_faktur'),'99');
      $data['no_faktur']      = $request->get('nomor');
      $data['id_supplier']    = $request->get('id_penyedia');
      $data['uang_muka']      = $request->get('td_uangmuka');
      $data['ongkos_kirim']   = $request->get('td_ongkir');
      $data['pajak']          = $request->get('td_pajak');
      $data['carabayar']      = $request->get('carabayar');
      $data['total_potongan'] = $request->get('td_diskon');
      $data['total_subtotal'] = $request->get('td_total');
      $data['total_tagihan']  = $request->get('td_netto');
      $data['keterangan']     = $request->get('keterangan');
      $data['status_bayar']   = $request->get('carabayar');
      //$data['status_bayar']   = '1';
      $data['status_penerimaan']= '1';
      $data['jenis']		  = $request->get('jenis');
      $data['status_jenis']	  = $request->get('status_jenis');

      $tabel_id = ($request->get('tabel_id')) ? $request->get('tabel_id'):[];
      $tabel_barang   = $request->get('tabel_idbarang');
      $tabel_jumlah   = $request->get('tabel_jumlah');
      $tabel_harga    = $request->get('tabel_harga');
      $tabel_satuan   = $request->get('tabel_idsatuan');
      $tabel_total    = $request->get('tabel_total');
      $tabel_idlog    = $request->get('tabel_idlog_stok');

      if($id_pembelian == ''){
        $id = DB::table('tbl_pembelian')->insertGetId($data);
        trigger_log($id,'Menambah data Menu Surat Jalan',1,url('suratjalan_edit/' . $id));
        
        for($i=0;$i<count($tabel_id);$i++){
          $barang['id_pembelian'] = $id;
          $barang['id_barang']    = $tabel_barang[$i];
          $barang['id_satuan']    = $tabel_satuan[$i];
          $barang['jumlah']       = $tabel_jumlah[$i];
          $barang['harga']        = $tabel_harga[$i];
          $barang['total']        = $tabel_total[$i];
          DB::table('tbl_pembelian_detail')->insert($barang);
        }
      }else{
        DB::table('tbl_pembelian')->where('id_pembelian',$id_pembelian)->update($data);
        trigger_log($id_pembelian,'Mengubah data Menu Surat Jalan',2,url('suratjalan_edit/' . $id_pembelian));

        $id_del = array();
        $id_store = array();
        $d_barang = DB::table('tbl_pembelian_detail')->where('id_pembelian',$id_pembelian);
        foreach($d_barang->get() as $d){
          $id_store[] = $d->id_detail_pembelian;
        }

        foreach($id_store as $d){
                if(count($tabel_id) > 0){
                    if(!in_array($d, $tabel_id)){
                        $id_del[] = $d;
                    }
                }else{
                    $id_del[] = $d;
                }
            }

        $id_del_log = array();
        $id_store_log = array();
        $d_barang_log = DB::table('tbl_log_stok')->where('log_stok_id',$tabel_idlog);
        foreach($d_barang->get() as $d){
          $id_store[] = $d->id_detail_pembelian;
        }

        foreach($id_store as $d){
                if(count($tabel_id) > 0){
                    if(!in_array($d, $tabel_id)){
                        $id_del[] = $d;
                    }
                }else{
                    $id_del[] = $d;
                }
            }       
        

        for($i=0;$i<count($tabel_id);$i++){
          $barang['id_pembelian'] = $id_pembelian;
          $barang['id_barang']    = $tabel_barang[$i];
          $barang['id_satuan']    = $tabel_satuan[$i];
          $barang['jumlah']       = $tabel_jumlah[$i];
          $barang['harga']        = $tabel_harga[$i];
          $barang['total']        = $tabel_total[$i];
          
          if($tabel_id[$i] == ''){
            DB::table('tbl_pembelian_detail')->insert($barang);
          }else if($tabel_id[$i] != ''){
            DB::table('tbl_pembelian_detail')->where(array('id_detail_pembelian' => $tabel_id[$i]))->update($barang);
          }

          //DB::table('tbl_log_stok')->where(array('log_stok_id' => $tabel_idlog[$i]))->delete();

        }

         if(count($id_del) > 0){
          DB::table('tbl_log_stok')->whereIn('log_stok_id', $id_del_log)->delete();
        }

        if(count($id_del) > 0){
          DB::table('tbl_pembelian_detail')->whereIn('id_detail_pembelian', $id_del)->delete();
        }

      }

      return redirect('suratjalan');
    }

    public function hapus(Request $request){
      $id = $request->get('id');
      $d_barang = DB::table('tbl_pembelian_detail')->where('id_pembelian',$id);
      $id_log_stok = array();
      foreach($d_barang->get() AS $d){
        $id_log_stok[] = $d->id_log_stok; 
      }
      DB::table('tbl_log_stok')->whereIn('log_stok_id',$id_log_stok)->delete();
      DB::table('tbl_pembelian')->where(array('id_pembelian' => $id))->delete();
      trigger_log($id,'Menghapus data Menu Surat Jalan',3,null);
      DB::table('tbl_pembelian_detail')->where(array('id_pembelian' => $id))->delete();

    }

    public function get_edit(Request $request){
      $id = $request->get('id');
      $d_data = DB::table('tbl_pembelian_detail as tpd')->leftjoin('tbl_barang as tb','tpd.id_barang','tb.barang_id')->leftjoin('tbl_satuan as ts','tb.satuan_id','ts.satuan_id')->leftjoin('tbl_satuan as ts2','tpd.id_satuan','ts2.satuan_id')->where('tpd.id_pembelian',$id)->select('tpd.*','tb.barang_nama as nama_barang','tb.barang_alias as alias_barang','ts.satuan_id as id_satuan','ts.satuan_nama as nama_satuan','ts2.satuan_id as id_satuan2','ts2.satuan_nama as nama_satuan2')->orderBy('tpd.id_detail_pembelian','asc');
      if($d_data->count() > 0){
        foreach($d_data->get() as $d){
            if($d->alias_barang != null || $d->alias_barang != ""){
                $nama_barang = $d->nama_barang." || (".$d->alias_barang.")";
              }else{
                $nama_barang = $d->nama_barang;
              }
            $arr[] = array('id' => $d->id_detail_pembelian,
                            'id_pembelian'  => $d->id_pembelian,
                            'id_log_stok'   => $d->id_log_stok,
                            'id_barang'     => $d->id_barang,
                            'nama_barang'   => $nama_barang,
                            'alias'         => $d->alias_barang,
                            'jumlah'        => $d->jumlah,
                            'id_satuan'     => $d->id_satuan,
                            'nama_satuan'   => $d->nama_satuan,
                            'id_satuan2'    => $d->id_satuan2,
                            'nama_satuan2'  => $d->nama_satuan2,
                            'harga'         => $d->harga,
                            'total'         => $d->total);
        }
      }else{
        $arr = array();
      }

      return response()->json($arr);
      //return response()->json($d_data->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $id_pembelian = PembelianModel::max('pembelian_id');
      $id_pembelian++;
      $jumlah = DetailPembelianModel::where('pembelian_id',$id_pembelian)->sum('pembelian_detail_jumlah');

      $ppn = '';

      if ($pajak == 'ya') {
        $ppn = ($jumlah*10)/100;
      }else{
        $ppn = 0;
      }


        $pembelian = new PembelianModel;
        $pembelian->supplier_id = $request['supplier_id'];
        $pembelian->pembelian_no_faktur = $request['pembelian_no_faktur'];
        $pembelian->pembelian_tanggal = $request['pembelian_tanggal'];
        $pembelian->pembelian_ppn_status = $request['pembelian_ppn_status'];
        $pembelian->pembelian_cara_bayar = $request['pembelian_cara_bayar'];
        $pembelian->pembelian_jumlah = $request['pembelian_jumlah'];
        $pembelian->pembelian_ppn_jumlah = $request['pembelian_ppn_jumlah'];
        $pembelian->pembelian_ongkir = $request['pembelian_ongkir'];
        $pembelian->pembelian_total = $request['pembelian_total'];
        $pembelian->save();

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id){
        //
      $d_data = DB::table('tbl_pembelian as tp')->leftjoin('tbl_supplier as ts','tp.id_supplier','ts.supplier_id')->select('tp.*','ts.supplier_nama as nama_supplier')->where('id_pembelian',$id)->first();
      $data['data']   = $this->data($d_data);     
      $data['satuan'] = DB::table('tbl_satuan')->orderBy('satuan_nama','asc')->get();
      $data['rekening']   = \Config::get('constants.rekening');
      $data['viabayar']   = \Config::get('constants.viabayar_pemilik');
      $data['carabayar']  = \Config::get('constants.carabayar');
      $data['supplier']   = DB::table('tbl_supplier')->get();
      return view('admin.suratjalan.create')->with('data',$data);
      //print_r($data['data']);
    }

    public function detail($id){
        //
      $d_data = DB::table('tbl_pembelian as tp')->leftjoin('tbl_supplier as ts','tp.id_supplier','ts.supplier_id')->select('tp.*','ts.supplier_nama as nama_supplier')->where('id_pembelian',$id)->first();
      $data['data']   = $this->data($d_data);     
      $data['satuan'] = DB::table('tbl_satuan')->orderBy('satuan_nama','asc')->get();
      $data['rekening']   = \Config::get('constants.rekening');
      $data['viabayar']   = \Config::get('constants.viabayar_pemilik');
      $data['carabayar']  = \Config::get('constants.carabayar');
      $data['supplier']   = DB::table('tbl_supplier')->get();
      return view('admin.suratjalan.detail')->with('data',$data);
      //print_r($data['data']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $pembelian = PembelianModel::find($id);
      $pembelian->suplier_id = $request['suplier_id'];
      $pembelian->pembelian_no_faktur = $request['pembelian_no_faktur'];
      $pembelian->pembelian_tanggal = $request['pembelian_tanggal'];
      $pembelian->pembelian_ppn_status = $request['pembelian_ppn_status'];
      $pembelian->pembelian_cara_bayar = $request['pembelian_cara_bayar'];
      $pembelian->pembelian_jumlah = $request['pembelian_jumlah'];
      $pembelian->pembelian_ppn_jumlah = $request['pembelian_ppn_jumlah'];
      $pembelian->pembelian_ongkir = $request['pembelian_ongkir'];
      $pembelian->pembelian_total = $request['pembelian_total'];
      $pembelian->update();
      trigger_log($id,'Mengubah data Menu Surat Jalan',2,url('suratjalan_edit/' . $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pembelian = PembelianModel::find($id);
        $pembelian->delete();
        trigger_log($id_pembelian,'Menghapus data Menu Surat Jalan',3,null);

    }

    public function listData()
    {
        //$pembelian = PembelianModel::with(['dataPenyedia'])->where('jenis','2')->orderBy('id_pembelian', 'DESC')->get();
        $id_group = Auth::user()->group_id;
        $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();

        $tanggal_start = date('Y-m-d', strtotime('-7 days'));
        $tanggal_end = date('Y-m-d');
        $pembelian = DB::table('tbl_pembelian as tp')->leftjoin('tbl_supplier as ts','tp.id_supplier','ts.supplier_id')->where('tp.jenis','2')->where('tp.tanggal','>',$tanggal_start)->where('tp.tanggal','<=',$tanggal_end)->SELECT(DB::raw('tp.id_pembelian, tp.tanggal, tp.no_faktur, ts.supplier_nama as nama_supplier, tp.status_bayar, tp.status_jenis, tp.total_tagihan'))->orderBy('tp.id_pembelian', 'DESC')->get();
        $no = 0;
        $data = array();
        foreach ($pembelian as $list) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = tgl_full($list->tanggal,'');
            $row[] = $list->no_faktur;
            //$row[] = $list->dataPenyedia->supplier_nama;
            $row[] = $list->nama_supplier;
            $row[] = $this->get_status_jenis($list->status_jenis);
            $row[] = $this->get_aksi($list->status_jenis,$list->id_pembelian,$group_where->group_aktif);
            /*$row[] = '<div class="btn-group"><a href="'.url('suratjalan_edit/'.$list->id_pembelian).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
            <a onclick="deleteData('.$list->id_pembelian.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';*/
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);

    }

    public function searchtanggal(Request $request)
    {
        //$pembelian = PembelianModel::with(['dataPenyedia'])->where('jenis','2')->orderBy('id_pembelian', 'DESC')->get();
        $id_group = Auth::user()->group_id;
        $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();

        $tanggalrange = explode('s.d.',$request->get('tanggal'));
        $tanggal_start  = tgl_full($tanggalrange[0],99);
        $tanggal_end    = tgl_full($tanggalrange[1],99);
        $pembelian = DB::table('tbl_pembelian as tp')->leftjoin('tbl_supplier as ts','tp.id_supplier','ts.supplier_id')->where('tp.jenis','2')->where('tp.tanggal','>=',$tanggal_start)->where('tp.tanggal','<=',$tanggal_end)->SELECT(DB::raw('tp.id_pembelian, tp.tanggal, tp.no_faktur, ts.supplier_nama as nama_supplier, tp.status_bayar, tp.status_jenis, tp.total_tagihan'))->orderBy('tp.id_pembelian', 'DESC')->get();
        $no = 0;
        $data = array();
        foreach ($pembelian as $list) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = tgl_full($list->tanggal,'');
            $row[] = $list->no_faktur;
            //$row[] = $list->dataPenyedia->supplier_nama;
            $row[] = $list->nama_supplier;
            $row[] = $this->get_status_jenis($list->status_jenis);
            $row[] = $this->get_aksi($list->status_jenis,$list->id_pembelian,$group_where->group_aktif);
            /*$row[] = '<div class="btn-group"><a href="'.url('suratjalan_edit/'.$list->id_pembelian).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
            <a onclick="deleteData('.$list->id_pembelian.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';*/
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);

    }

    function get_status($status){
      switch ($status){
        case '1':
          $html = '<label class="label label-sm label-success">Sudah Dibayar</label>';
          break;
        case '2':
          $html = '<label class="label label-sm label-warning">Sebagian Dibayar</label>';
          break;
        case '3':
          $html = '<label class="label label-sm label-danger">Belum Dibayar</label>';
          break;
        default:
          # code...
          $html = '<label class="label label-sm label-success">Sudah Dibayar</label>';
          break;
      }
      return $html;
    }

    function get_status_jenis($status){
      switch ($status){
        case '1':
          $html = '<label class="label label-sm label-success">Sudah Ada Harga</label>';
          break;
        case '2':
          $html = '<label class="label label-sm label-warning">Belum Ada Harga</label>';
          break;
        default:
          # code...
          $html = '<label class="label label-sm label-success">Sudah Ada Harga</label>';
          break;
      }
      return $html;
    }

    function get_aksi($status,$id,$status_edit){
      switch (true) {
        case ($status=='1'&&$status_edit=='2'):
          $html = '<div class="btn-group"><a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
          break;
        case ($status=='2'&&$status_edit=='2'):
          $html = '<div class="btn-group"><a href="'.url('suratjalan_edit/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
            <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
          break;
        case ($status=='1'&&$status_edit=='1'):
          $html = '<div class="btn-group"><a href="'.url('suratjalan_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-eye"></i></a></div>';
          break;
        case ($status=='2'&&$status_edit=='1'):
          $html = '<div class="btn-group"><a href="'.url('suratjalan_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-eye"></i></a></div>';
          break;                        
        default:
        $html = '<div class="btn-group"><a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
          break;
      }

      return $html;

    }

    public function totalBarang(Request $request)
    {
      $id_pembelian = PembelianModel::max('pembelian_id');
      $id_pembelian++;
      $pajak = $request['pajak'];
      $ongkir = $request['ongkir'];
      // $id = $request['id'];
      $jumlah = DetailPembelianModel::where('pembelian_id',$id_pembelian)->sum('pembelian_detail_jumlah');

      $ppn = '';

      if ($pajak == 'ya') {
        $ppn = ($jumlah*10)/100;
      }else{
        $ppn = 0;
      }

      $total = $jumlah+$ppn+$ongkir;

      return response()->json($total);
    }

}
