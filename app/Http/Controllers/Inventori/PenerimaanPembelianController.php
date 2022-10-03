<?php

namespace App\Http\Controllers\Inventori;

use App\PembelianModel;
use App\SupplierModel;
use App\DetailPembelianModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class PenerimaanPembelianController extends Controller
{
    //
    public function index(){
        $id_group = Auth::user()->group_id;
        $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();
        $tombol_create = tombol_create('',$group_where->group_aktif,4);
    	return view('admin.penerimaan.index',compact('tombol_create'));
    }

    public function get_edit(Request $request){
      $id = $request->get('id');
      $d_data = DB::table('tbl_pembelian_detail as tpd')->leftjoin('tbl_barang as tb','tpd.id_barang','tb.barang_id')->leftjoin('tbl_satuan as ts','ts.satuan_id','tpd.id_satuan')->where('tpd.id_pembelian',$id)->select('tpd.*','tb.barang_nama as nama_barang','ts.satuan_nama as nama_satuan')->orderBy('tpd.id_detail_pembelian','asc');
      if($d_data->count() > 0){
        foreach($d_data->get() as $d){
            $arr[] = array('id' => $d->id_detail_pembelian,
                            'id_pembelian'  => $d->id_pembelian,
                            'id_log_stok'   => $d->id_log_stok,
                            'id_barang'     => $d->id_barang,
                            'nama_barang'   => $d->nama_barang,
                            'jumlah'        => $d->jumlah,
                            'jumlah_terima' => $d->jumlah_terima,
                            'nama_satuan'   => $d->nama_satuan,
                            'id_satuan'     => $d->id_satuan,
                            'harga'         => $d->harga,
                            'total'         => $d->total);
        }
      }else{
        $arr = array();
      }

      return response()->json($arr);
    }

    public function get_gudang(Request $request){
      $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }
        $search = strtolower($term);
        $d_query= DB::select("SELECT * FROM ref_gudang WHERE nama LIKE '%$search%' order by nama asc");
        return \Response::json($d_query);
    }

    public function listData(){
        $id_group = Auth::user()->group_id;
        $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();

        $tanggal_start = date('Y-m-d', strtotime('-7 days'));
        $tanggal_end = date('Y-m-d');
    	$pembelian = PembelianModel::with(['dataPenyedia'])->where('tanggal','>',$tanggal_start)->where('tanggal','<=',$tanggal_end)->orderBy('tanggal', 'DESC')->get();
        $no = 0;
        $data = array();
        foreach ($pembelian as $list) {
            $no++;
            $row = array();
            if($list->status_penerimaan == 1){
            $row[] = $no.' <input type="checkbox" id="check_verifikasi" class="check_verifikasi" value="'.$list->id_pembelian.'" jenis="'.$list->status_penerimaan.'">';
            $row[] = $list->no_faktur;
            $row[] = '';
            $row[] = tgl_full($list->tanggal,'');
            $row[] = $list->dataPenyedia->supplier_nama;
            $row[] = '<div class="text-center">'.$this->get_status($list->status_penerimaan).'</div>';
            $row[] = '<div class="text-center">'.$this->get_aksi(1,$list->id_pembelian,$group_where->group_aktif).'</div>';
            $data[] = $row;
            }else{
            $row[] = $no;
            $row[] = $list->no_faktur;
            $row[] = ($list->id_gudang==null) ? '':$list->dataGudang->nama;
            $row[] = tgl_full($list->tanggal,'');
            $row[] = $list->dataPenyedia->supplier_nama;
            $row[] = '<div class="text-center">'.$this->get_status($list->status_penerimaan).'</div>';
            $row[] = '<div class="text-center">'.$this->get_aksi(2,$list->id_pembelian,$group_where->group_aktif).'</div>';
            $data[] = $row;
            }

        }

        $output = array("data" => $data);
        return response()->json($output);

    }

    public function searchtanggal(Request $request){
        $id_group = Auth::user()->group_id;
        $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();

        $tanggalrange = explode('s.d.',$request->get('tanggal'));
        $tanggal_start  = tgl_full($tanggalrange[0],99);
        $tanggal_end    = tgl_full($tanggalrange[1],99);
        $pembelian = PembelianModel::with(['dataPenyedia'])->where('tanggal','>=',$tanggal_start)->where('tanggal','<=',$tanggal_end)->orderBy('tanggal', 'DESC')->get();
        $no = 0;
        $data = array();
        foreach ($pembelian as $list) {
            $no++;
            $row = array();
            if($list->status_penerimaan == 1){
            $row[] = $no.' <input type="checkbox" id="check_verifikasi" class="check_verifikasi" value="'.$list->id_pembelian.'" jenis="'.$list->status_penerimaan.'">';
            $row[] = $list->no_faktur;
            $row[] = '';
            $row[] = tgl_full($list->tanggal,'');
            $row[] = $list->dataPenyedia->supplier_nama;
            $row[] = '<div class="text-center">'.$this->get_status($list->status_penerimaan).'</div>';
            $row[] = '<div class="text-center">'.$this->get_aksi(1,$list->id_pembelian,$group_where->group_aktif).'</div>';
            $data[] = $row;
            }else{
            $row[] = $no;
            $row[] = $list->no_faktur;
            $row[] = ($list->id_gudang==null) ? '':$list->dataGudang->nama;
            $row[] = tgl_full($list->tanggal,'');
            $row[] = $list->dataPenyedia->supplier_nama;
            $row[] = '<div class="text-center">'.$this->get_status($list->status_penerimaan).'</div>';
            $row[] = '<div class="text-center">'.$this->get_aksi(2,$list->id_pembelian,$group_where->group_aktif).'</div>';
            $data[] = $row;
            }

        }

        $output = array("data" => $data);
        return response()->json($output);

    }

    /*public function get_data(){
        $pembelian = PembelianModel::with(['dataPenyedia'])->orderBy('tanggal', 'DESC');
        DB::statement(DB::raw('set @rownum=0'));
        if($pembelian->count()>0){
            foreach ($pembelian->get() as $d) {
                # code...
                if($d->status_penerimaan == 1){
                    $aksi = '<div class="text-center">'.$this->get_status($d->status_penerimaan).'</div>';
                    $status = '<div class="text-center">'.$this->get_aksi(1,$d->id_pembelian).'</div>';
                    $checkbox = '<input type="checkbox" id="check_verifikasi" value="1" jenis="'.$d->id_pembelian.'">';
                }else{
                    $aksi = '<div class="text-center">'.$this->get_status($d->status_penerimaan).'</div>';
                    $status = '<div class="text-center">'.$this->get_aksi(2,$d->id_pembelian).'</div>';
                    $checkbox = '';
                }
                $arr[] = array('id_pembelian' => $d->id_pembelian,
                                'nomor'       => $d->no_faktur,
                                'status_id'   => $d->status_penerimaan,
                                'tanggal'     => tgl_full($d->tanggal,''),
                                'nama_supplier' => $d->dataPenyedia->supplier_nama,
                                'nama_gudang'   => ($d->id_gudang==null) ? '':$d->dataGudang->nama,
                                'aksi'        => $aksi,
                                'status'      => $status,
                                'checkbox'    => $checkbox);
            }
            
        }else{
            $arr = array();
        }

        $data = new Collection($arr);
        return Datatables::of($data)->make(true);
    }*/

    public function create($id){
        $d_data = DB::table('tbl_pembelian as tp')->leftjoin('tbl_supplier as ts','tp.id_supplier','ts.supplier_id')->leftjoin('ref_gudang AS rg','tp.id_gudang','rg.id')->select('tp.*','ts.supplier_nama as nama_supplier','rg.nama as nama_gudang')->where('id_pembelian',$id)->first();
        $data['data']   = $this->data($d_data);     
        $data['satuan'] = DB::table('tbl_satuan')->orderBy('satuan_nama','asc')->get();
        $data['rekening']   = \Config::get('constants.rekening');
        $data['viabayar']   = \Config::get('constants.viabayar_pemilik');
        $data['carabayar']  = \Config::get('constants.carabayar');
        $data['supplier']   = DB::table('tbl_supplier')->get();
        $data['status']     = \Config::get('constants.status_penerimaan');
    	return view('admin.penerimaan.create')->with('data',$data);
    }

    public function detail($id){
        $d_data = DB::table('tbl_pembelian as tp')->leftjoin('tbl_supplier as ts','tp.id_supplier','ts.supplier_id')->leftjoin('ref_gudang AS rg','tp.id_gudang','rg.id')->select('tp.*','ts.supplier_nama as nama_supplier','rg.nama as nama_gudang')->where('id_pembelian',$id)->first();
        $data['data']   = $this->data($d_data);     
        $data['satuan'] = DB::table('tbl_satuan')->orderBy('satuan_nama','asc')->get();
        $data['rekening']   = \Config::get('constants.rekening');
        $data['viabayar']   = \Config::get('constants.viabayar_pemilik');
        $data['carabayar']  = \Config::get('constants.carabayar');
        $data['supplier']   = DB::table('tbl_supplier')->get();
        $data['status']     = \Config::get('constants.status_penerimaan');
        return view('admin.penerimaan.detail')->with('data',$data);
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
        $store['viabayar']        = $data->viabayar;
        $store['carabayar']       = $data->carabayar;
        $store['id_rek']          = $data->id_rekening;
        $store['no_rek']          = $data->no_rekening;
        $store['keterangan']      = $data->keterangan;
        $store['tanggal_penerimaan'] = ($data->tanggal_terima != NULL) ? tgl_full($data->tanggal_terima,'') : '';
        $store['nomor_penerimaan']= "";
        $store['nomor_surat']     = "";
        $store['status_bayar']    = $data->status_bayar;
        $store['status_penerimaan'] = $data->status_penerimaan;
        $store['id_gudang']       = $data->id_gudang;
        $store['nama_gudang']     = $data->nama_gudang;
      }else{
        $store['id_pembelian']    = "";
        $store['tanggal']         = "";
        $store['tanggal_faktur']  = "";
        $store['tanggal_tempo']   = "";
        $store['id_penyedia']     = "";
        $store['nama_penyedia']   = "";
        $store['nomor']           = "P0001";
        $store['uang_muka']       = "Rp 0";
        $store['td_ongkir']       = "";
        $store['td_potongan']     = "";
        $store['td_tagihan']      = "";
        $store['td_subtotal']     = "";
        $store['pajak']           = "";
        $store['viabayar']        = "";
        $store['carabayar']       = "";
        $store['id_rek']          = "";
        $store['no_rek']          = "";
        $store['keterangan']      = "";
        $store['tanggal_penerimaan'] = "";
        $store['nomor_penerimaan']= "";
        $store['nomor_surat']     = "";
        $store['status_bayar']    = "";
        $store['status_penerimaan']="";
        $store['id_gudang']       = "";
        $store['nama_gudang']     = "";
      }

      return $store;
    }

    public function simpan_multi(Request $request){
        $id = ($request->get('id')) ? $request->get('id'):[];
        $gudang = $request->get('gudang');
        for($i=0; $i<count($id); $i++){
            $id_pembelian = $id[$i];
            $data['id_gudang'] = $gudang[$i];
            $data['status_penerimaan'] = '3';
            $data['tanggal_terima']    = date('Y-m-d');
            DB::table('tbl_pembelian')->where('id_pembelian',$id_pembelian)->update($data);
            $d[] = array($id[$i]);            
        }
        $d_barang = DB::table('tbl_pembelian_detail')->whereIn('id_pembelian',$d);
            if($d_barang->count() > 0){
                foreach($d_barang->get() as $value){
                    $id_detail_pembelian    = $value->id_detail_pembelian;
                    $input['id_barang']     = $value->id_barang;
                    $input['unit_masuk']    = $value->jumlah;
                    $input['unit_keluar']   = '0';
                    $input['id_ref_gudang'] = $gudang[0];
                    $input['id_satuan']     = $value->id_satuan;
                    $input['tanggal']       = date('Y-m-d');
                    $input['status']        = 'P1';

                    $id = DB::table('tbl_log_stok')->insertGetId($input);
                    $barang['id_log_stok'] = $id;
                    $barang['jumlah_terima'] = $value->jumlah;
                    DB::table('tbl_pembelian_detail')->where('id_detail_pembelian',$id_detail_pembelian)->update($barang);
                }
                
            }else{
                $input = array();
            }
        trigger_log(NULL, "Melakukan Penerimaan Multi Barang dari Pembelian Barang", 2);
        return response()->json(array('status' => '1'));


    }

    public function simpan(Request $request){
      $id_pembelian = $request->get('id_pembelian');
      $data['tanggal_terima']       = tgl_full($request->get('tanggal_penerimaan'),'99');
      $data['status_penerimaan']    = $request->get('status_penerimaan');
      $data['id_gudang']            = $request->get('gudang');

      $tabel_id = ($request->get('tabel_id')) ? $request->get('tabel_id'):[];
      $tabel_barang   = $request->get('tabel_idbarang');
      $tabel_jumlah   = $request->get('tabel_jumlah');
      $tabel_satuan   = $request->get('tabel_idsatuan');
      $tabel_jumlah_terima  = $request->get('tabel_jumlah_terima');
      $tabel_gudang   = $request->get('gudang');
      $tabel_tanggal  = tgl_full($request->get('tanggal_penerimaan'),'99');
      $tabel_idlog    = $request->get('tabel_idlog');
      $tabel_keluar   = "0";
      DB::table('tbl_pembelian')->where('id_pembelian',$id_pembelian)->update($data);
      
        for($i=0;$i<count($tabel_id);$i++){
            $input['id_barang']     = $tabel_barang[$i];
            $input['unit_masuk']    = $tabel_jumlah[$i];
            $input['unit_keluar']   = $tabel_keluar;
            $input['id_satuan']     = $tabel_satuan[$i];
            $input['id_ref_gudang'] = $tabel_gudang;
            $input['tanggal']       = $tabel_tanggal;
            $input['status']        = 'P1';

            $barang['jumlah_terima']= $tabel_jumlah_terima[$i];

            $cek = DB::table('tbl_log_stok')->where(array('log_stok_id' => $tabel_idlog[$i]));
            if($cek->count() > 0){
               if($request->get('status_penerimaan') == 3){
                DB::table('tbl_log_stok')->where(array('log_stok_id' => $tabel_idlog[$i]))->update($input);
                $barang['id_log_stok'] = $tabel_idlog[$i];
               }else{
                DB::table('tbl_log_stok')->where(array('log_stok_id' => $tabel_idlog[$i]))->delete();
                $barang['id_log_stok'] = "";
               }     
            }else{
                $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);
                $barang['id_log_stok'] = $id_log_stok;
            }            
            DB::table('tbl_pembelian_detail')->where(array('id_detail_pembelian' => $tabel_id[$i]))->update($barang);

          }
      trigger_log($id_pembelian, "Penerimaan Barang dari Pembelian Barang", 2);
      return redirect('penerimaanbarang');
    }


    

    public function get_status($status){
      switch ($status) {
        case '1':
          $html = '<label class="label label-sm label-danger">Belum Diterima</label>';
          break;
        case '2':
          $html = '<label class="label label-sm label-warning">Sebagian Diterima</label>';
          break;
        case '3':
          $html = '<label class="label label-sm label-success">Sudah Diterima</label>';
          break;
        default:
          $html = '<label class="label label-sm label-danger">Belum Diterima</label>';
          break;
      }
      return $html;
    }

    public function get_aksi($aksi,$id, $status){
        switch (true) {
            case ($aksi == '1' && $status == '2'):
                $html = '<div class="btn-group"><a href="'.url('penerimaan_tambah/'.$id).'" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="botttom" title="Terima Data"  style="color:white;"><i class="fa  fa-check"></i></div>';
                break;            
            case ($aksi == '2' && $status == '2'):
                $html = '<div class="btn-group"><a href="'.url('penerimaan_tambah/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Terima Data"  style="color:white;"><i class="fa  fa-check"></i></div>';
                break;
            case ($aksi == '1' && $status == '1'):
                $html = '<div class="btn-group"><a href="'.url('penerimaan_detail/'.$id).'" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="botttom" title="Terima Data"  style="color:white;"><i class="fa  fa-eye"></i></div>';
                break;            
            case ($aksi == '2' && $status == '1'):
                $html = '<div class="btn-group"><a href="'.url('penerimaan_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Terima Data"  style="color:white;"><i class="fa  fa-eye"></i></div>';
                break;            
            default:
                $html = '<div class="btn-group"><a href="'.url('penerimaan_tambah/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Terima Data"  style="color:white;"><i class="fa  fa-check"></i></div>';
                break;
        }
        return $html;
    }

    function check_status($id, $id_jenis){

    }
}
