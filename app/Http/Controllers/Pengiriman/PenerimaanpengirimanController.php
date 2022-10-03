<?php

namespace App\Http\Controllers\Pengiriman;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent;

class PenerimaanpengirimanController extends Controller
{
  public function __construct(){
    $this->agent = new Agent();
  }
    public function Index()
    {
      $id = Auth::user()->group_id;
      // print_r($id);exit;
    $gudang['gudang'] = DB::select('SELECT
      g.id,
      g.id_profil,
      g.nama,
      g.alamat,
      g.`status`,
      g.created_at,
      g.updated_at,
      p.nama nama_profil,
      p.jenis_outlet
    FROM
      ref_gudang AS g
      LEFT JOIN m_profil AS p ON g.id_profil = p.id
      WHERE p.jenis_outlet =1
    ORDER BY
      g.id ASC');
    // dd($gudang);
        $gudang['outlet'] = DB::select('SELECT
          g.id,
          g.id_profil,
          g.nama,
          g.alamat,
          g.`status`,
          g.created_at,
          g.updated_at,
          p.nama nama_profil,
          p.jenis_outlet
        FROM
          ref_gudang AS g
          LEFT JOIN m_profil AS p ON g.id_profil = p.id
          WHERE p.jenis_outlet =2
        ORDER BY
          g.id ASC');
          if ($this->agent->isMobile()) {
            // code...
            return view('admin_mobile.pengiriman.penerimaanpengiriman', compact('gudang'));
          }else {
            // code...
            return view('admin.pengiriman.penerimaanpengiriman', compact('gudang'));
          }

    }
    public function listData(){
    $id_profil = Auth::user()->id_profil;
    $group_id = Auth::user()->group_id;
    if ($group_id == 1) {
      $where="where i.tanggal_pengiriman > DATE_SUB(DATE(NOW()), INTERVAL 7 DAY) 
                    AND i.tanggal_pengiriman <= DATE_SUB(DATE(NOW()), INTERVAL 0 DAY) AND i.status_pengiriman != '0'";
    }else{
      $a=DB::table('ref_gudang')->where('id_profil',$id_profil)->get()->first();
      $where="where gudang_tujuan=".$a->id." AND i.tanggal_pengiriman > DATE_SUB(DATE(NOW()), INTERVAL 7 DAY) 
                    AND i.tanggal_pengiriman <= DATE_SUB(DATE(NOW()), INTERVAL 0 DAY) AND i.status_pengiriman != '0'";
    }
      $program = DB::select("SELECT
        i.id,
        i.kode_pengiriman,
        i.gudang_awal,
        i.gudang_tujuan,
        i.id_pengiriman,
        i.status_pengiriman,
        i.tanggal_pengiriman,
        r.nama AS gudang,
        r.alamat,
        g.nama AS tujuan,
        p.nama pengirim
        FROM
        pengiriman AS i
        JOIN ref_gudang AS r ON i.gudang_awal = r.id
        JOIN ref_gudang AS g ON i.gudang_tujuan = g.id
        LEFT JOIN m_pengirim AS p ON p.id = i.id_pengiriman $where ");
      $no = 0;
      $data = array();
      foreach ($program as $list) {
          //0 = tambahkan detail barang
           //1 = sedang dikirim
           //2 = terkirim
           //3 = diterima sebagian
           //4 = dikembalikan
        if ($list->status_pengiriman == 0) {
               # code...
         $status = "<span class='label label-default'>Tambahkan Detail Barang</span";
         $tombol = '';
        }elseif ($list->status_pengiriman == 1) {
           $status = "<span class='label label-warning'>sedang dikirim</span";
           $tombol = '<a class=" btn btn-xs btn-success" href="penerimaanpengiriman_tambah?id='.enc($list->id).'" data-toggle="tooltip" data-placement="top" title="Detail Barang"><i class="fa fa-eye"></i></a>';
        }elseif ($list->status_pengiriman == 2) {
            $status = "<span class='label label-success'>terkirim</span";
            $tombol = '<a class=" btn btn-xs btn-success" href="penerimaanpengiriman_edit?id='.enc($list->id).'" data-toggle="tooltip" data-placement="top" title="Detail Barang"><i class="fa fa-eye"></i></a>';
        }elseif ($list->status_pengiriman == 3) {
            $status = "<span class='label label-primary'>Diterima Sebagian</span";
            $tombol = '<a class=" btn btn-xs btn-success" href="penerimaanpengiriman_edit?id='.enc($list->id).'" data-toggle="tooltip" data-placement="top" title="Detail Barang"><i class="fa fa-eye"></i></a>';
            # code...
        }elseif ($list->status_pengiriman == 4) {
            $status = "<span class='label label-danger'>Dikembalikan</span";
            $tombol = '';
            # code...
        }
          $tanggal = date("d-m-Y", strtotime($list->tanggal_pengiriman));
          $tanggal1 = date("Y-m-d", strtotime($list->tanggal_pengiriman));
          $id_a = enc($list->id);
          $no++;
          $row = array();
          if ($this->agent->isMobile()) {
            // code...
            $row['no'] = $no;
            $row['kode_pengiriman'] = $list->kode_pengiriman;
            $row['gudang'] = $list->gudang;
            $row['tujuan'] = $list->tujuan;
            $row['tanggal'] = $tanggal;
            $row['status'] = $status ;
            /*$row['aksi'] = '
            <a class=" btn btn-xs btn-success" href="detail_penerimaanpengiriman?id='.enc($list->id).'" data-toggle="tooltip" data-placement="top" title="Detail Barang"><i class="fa fa-eye"></i></a>
            <a href="cetaknotapengiriman?id='.$id_a.'" data-id="'.$list->id.'" target="_blank" id="btn_cetak" class="btn btn-xs btn-warning btn_cetak" data-toggle="tooltip" data-placement="bottom" title="cetak nota" style="color:white;"><i class="fa  fa-print"></i></a>
            ';*/
            $row['aksi'] = '
            <a class=" btn btn-xs btn-success" href="penerimaanpengiriman_tambah?id='.enc($list->id).'" data-toggle="tooltip" data-placement="top" title="Detail Barang"><i class="fa fa-eye"></i></a>
            <a href="cetaknotapengiriman?id='.$id_a.'" data-id="'.$list->id.'" target="_blank" id="btn_cetak" class="btn btn-xs btn-warning btn_cetak" data-toggle="tooltip" data-placement="bottom" title="cetak nota" style="color:white;"><i class="fa  fa-print"></i></a>
            ';
          }else {
            // code...
            $row[] = $no;
            $row[] = $list->kode_pengiriman;
            $row[] = $list->gudang;
            $row[] = $list->tujuan;
            $row[] = $tanggal;
            $row[] = $status ;
            /*$row[] = '
            <a class=" btn btn-xs btn-success" href="detail_penerimaanpengiriman?id='.enc($list->id).'" data-toggle="tooltip" data-placement="top" title="Detail Barang"><i class="fa fa-eye"></i></a>
            <a class=" btn btn-xs btn-success" href="penerimaanpengiriman_tambah?id='.enc($list->id).'" data-toggle="tooltip" data-placement="top" title="Detail Barang"><i class="fa fa-eye"></i></a>
            <a href="cetaknotapengiriman?id='.$id_a.'" data-id="'.$list->id.'" target="_blank" id="btn_cetak" class="btn btn-xs btn-warning btn_cetak" data-toggle="tooltip" data-placement="botttom" title="cetak nota" style="color:white;"><i class="fa  fa-print"></i></a>
            ';*/            
            $row[] = '
            <a class=" btn btn-xs btn-success" href="penerimaanpengiriman_tambah?id='.enc($list->id).'" data-toggle="tooltip" data-placement="top" title="Detail Barang"><i class="fa fa-check"></i></a>
            <a href="cetaknotapengiriman?id='.$id_a.'" data-id="'.$list->id.'" target="_blank" id="btn_cetak" class="btn btn-xs btn-warning btn_cetak" data-toggle="tooltip" data-placement="botttom" title="cetak nota" style="color:white;"><i class="fa  fa-print"></i></a>
            ';
          }
          $data[] = $row;

      }
      $output = array("data" => $data);
      return response()->json($output);
  }

  public function searchtanggal(Request $request){
    $id_profil = Auth::user()->id_profil;
    $group_id = Auth::user()->group_id;

    $tanggalrange = explode('s.d.',$request->get('tanggal'));
    $tanggal_start  = tgl_full($tanggalrange[0],99);
    $tanggal_end    = tgl_full($tanggalrange[1],99);
    if ($group_id == 1) {
      $where="where i.tanggal_pengiriman >= '$tanggal_start'  
                    AND i.tanggal_pengiriman <= '$tanggal_end'";
    }else{
      $a=DB::table('ref_gudang')->where('id_profil',$id_profil)->get()->first();
      $where="where gudang_tujuan=".$a->id." AND i.tanggal_pengiriman >= '$tanggal_start'  
                    AND i.tanggal_pengiriman <= '$tanggal_end'";
    }
      $program = DB::select("SELECT
        i.id,
        i.kode_pengiriman,
        i.gudang_awal,
        i.gudang_tujuan,
        i.id_pengiriman,
        i.status_pengiriman,
        i.tanggal_pengiriman,
        r.nama AS gudang,
        r.alamat,
        g.nama AS tujuan,
        p.nama pengirim
        FROM
        pengiriman AS i
        JOIN ref_gudang AS r ON i.gudang_awal = r.id
        JOIN ref_gudang AS g ON i.gudang_tujuan = g.id
        LEFT JOIN m_pengirim AS p ON p.id = i.id_pengiriman $where ");
      $no = 0;
      $data = array();
      foreach ($program as $list) {
          //0 = tambahkan detail barang
           //1 = sedang dikirim
           //2 = terkirim
           //3 = diterima sebagian
           //4 = dikembalikan
        if ($list->status_pengiriman == 0) {
               # code...
         $status = "<span class='label label-default'>Tambahkan Detail Barang</span";
        }elseif ($list->status_pengiriman == 1) {
           $status = "<span class='label label-warning'>sedang dikirim</span";
        }elseif ($list->status_pengiriman == 2) {
            $status = "<span class='label label-success'>terkirim</span";
        }elseif ($list->status_pengiriman == 3) {
            $status = "<span class='label label-primary'>Diterima Sebagian</span";
            # code...
        }elseif ($list->status_pengiriman == 4) {
            $status = "<span class='label label-danger'>Dikembalikan</span";
            # code...
            // <a class=" btn btn-xs btn-success" href="detail_penerimaanpengiriman?id='.enc($list->id).'" data-toggle="tooltip" data-placement="top" title="Detail Barang"><i class="fa fa-eye"></i></a>
        }
          $tanggal = date("d-m-Y", strtotime($list->tanggal_pengiriman));
          $tanggal1 = date("Y-m-d", strtotime($list->tanggal_pengiriman));
          $id_a = enc($list->id);
          $no++;
          $row = array();
          if ($this->agent->isMobile()) {
            // code...
            $row['no'] = $no;
            $row['kode_pengiriman'] = $list->kode_pengiriman;
            $row['gudang'] = $list->gudang;
            $row['tujuan'] = $list->tujuan;
            $row['tanggal'] = $tanggal;
            $row['status'] = $status ;
            $row['aksi'] = '
            
            <a class=" btn btn-xs btn-success" href="penerimaanpengiriman_tambah?id='.enc($list->id).'" data-toggle="tooltip" data-placement="top" title="Detail Barang"><i class="fa fa-check"></i></a>
            <a href="cetaknotapengiriman?id='.$id_a.'" data-id="'.$list->id.'" target="_blank" id="btn_cetak" class="btn btn-xs btn-warning btn_cetak" data-toggle="tooltip" data-placement="bottom" title="cetak nota" style="color:white;"><i class="fa  fa-print"></i></a>
            ';
          }else {
            // code...
            $row[] = $no;
            $row[] = $list->kode_pengiriman;
            $row[] = $list->gudang;
            $row[] = $list->tujuan;
            $row[] = $tanggal;
            $row[] = $status ;
            $row[] = '
            <a class=" btn btn-xs btn-success" href="penerimaanpengiriman_tambah?id='.enc($list->id).'" data-toggle="tooltip" data-placement="top" title="Detail Barang"><i class="fa fa-check"></i></a>
            <a href="cetaknotapengiriman?id='.$id_a.'" data-id="'.$list->id.'" target="_blank" id="btn_cetak" class="btn btn-xs btn-warning btn_cetak" data-toggle="tooltip" data-placement="botttom" title="cetak nota" style="color:white;"><i class="fa  fa-print"></i></a>
            ';
          }
          $data[] = $row;

      }
      $output = array("data" => $data);
      return response()->json($output);
  }

  public function data(Request $request){
      $id = dec($request['id']);
      $gudang['id'] = $id;
      $g = DB::table('pengiriman')->where('id', $id)->first();
      // print_r($g[0]->status_pengiriman);exit;
      $gudang['status'] = $g->status_pengiriman;
      $gudang['gudang'] = $g->gudang_awal;
      $gudang['tujuan'] = $g->gudang_tujuan;
      $gudang['tanggal'] = tgl_full($g->tanggal_pengiriman,'99');
    // // dd($gudang);
    // $gudang['outlet'] = DB::table('ref_gudang')->where('jenis_gudang', '2')->get();
    if ($this->agent->isMobile()) {
      // code...
      return view('admin_mobile.pengiriman.detailpenerimaan', compact('gudang'));
    }else {
      // code...
      return view('admin.pengiriman.detailpenerimaan', compact('gudang'));
    }

  }
  public function show($id){
      // $id_inv = $request['id'];
      $barang = DB::select("SELECT
          d.id,
          d.id_inv_pengiriman,
          d.id_barang,
          d.nama,
          d.jumlah,
          d.`status`,
          d.retur,
          d.diterima,
          b.barang_kode,
          b.barang_nama,
          s.satuan_nama,
          s.satuan_satuan
        FROM
          pengiriman_detail AS d
          LEFT JOIN tbl_barang AS b ON d.id_barang = b.barang_id
          LEFT JOIN tbl_satuan AS s ON d.id_satuan = s.satuan_id
        WHERE
          d.id_inv_pengiriman = '$id'");
        $no = 0;
        $data = array();
        foreach ($barang as $list) {
          //0 = belum dikirim
          //1 = sedang dikirim
          //2 = terkirim/diterima
          //3 = dikembalikan/return
            if ($list->status == 0) {
              $status = "<span class='label label-default'>Belum Dikirim</span";
              }elseif ($list->status == 1) {
                $status = "<span class='label label-warning'>Sedang Dikirim</span";
              }elseif ($list->status == 2) {
                  $status = "<span class='label label-success'>Diterima</span";
              }elseif ($list->status == 3) {
                  $status = "<span class='label label-danger'>Dikembalikan</span";
                  # code...
              }
              if ($list->status == 0) {
                $edit = '<a  data-id_barang="'.$list->id_barang.'" data-nama="'.$list->nama.'" data-jumlah="'.$list->jumlah.'" data-kode="'.$list->barang_kode.'" data-satuan="'.$list->satuan_nama.'" id="btn_edit" class="btn btn-xs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>';
                $hapus = '<a data-id="'.$list->id.'" id="btn_hapus" class="btn btn-xs btn-danger btn_hapus" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>';
                $ass = '';
              }else if ($list->status == 1) {
                $ass = ' <a data-id_detail_pengiriman="'.$list->id.'" data-id_barang="'.$list->id_barang.'" data-nama="'.$list->nama.'" data-jumlah="'.$list->jumlah.'" data-kode="'.$list->barang_kode.'" data-satuan="'.$list->satuan_nama.'" id="btn_a" class="btn btn-xs  btn-success" data-toggle="tooltip" data-placement="botttom" title="Terima Barang"  style="color:white;"><i class="fa  fa-check-square"></i></a>';
                $edit = '<a  data-id_barang="'.$list->id_barang.'" data-nama="'.$list->nama.'" data-jumlah="'.$list->jumlah.'" data-kode="'.$list->barang_kode.'" data-satuan="'.$list->satuan_nama.'" id="btn_edit" class="btn btn-xs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>';
                $hapus = '<a data-id="'.$list->id.'" id="btn_hapus" class="btn btn-xs btn-danger btn_hapus" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>';
              }else{
                $ass = '';
                $edit = '';
                $hapus = '';
              }
            $no++;
            $row = array();
            if ($this->agent->isMobile()) {
              // code...
              $row['no'] = $no;
              $row['nama'] = $list->nama;
              $row['satuan_nama'] = $list->satuan_nama;
              // $row['jhagscf'] = empty($list->retur)?0:$list->retur;
              // $row['jhagscf'] = empty($list->diterima)?0:$list->diterima;
              $row['jumlah'] = $list->jumlah;
              $row['status'] =  $status;
              $row['aksi'] = '';
            }else {
              // code...
              $row[] = $no;
              $row[] = $list->nama;
              $row[] = $list->satuan_nama;
              // $row[] = empty($list->retur)?0:$list->retur;
              // $row[] = empty($list->diterima)?0:$list->diterima;
              $row[] = $list->jumlah;
              $row[] =  $status;
              $row[] = '';
            }
            $data[] = $row;

        }
        $output = array("data" => $data);
        return response()->json($output);
    }

    public function create(Request $request){
      $id = dec($request['id']);
      $d_data = DB::table('pengiriman as p')->leftjoin('ref_gudang as rg','p.gudang_awal','rg.id')->leftjoin('ref_gudang as rg2','p.gudang_tujuan','rg2.id')->where('p.id',$id)->select(DB::raw('p.id , p.id_pengiriman, p.tanggal_pengiriman, p.tanggal_penerimaan, p.kode_pengiriman, p.status_pengiriman as status, p.gudang_awal as id_gudangawal, rg.nama as nama_gudangawal, p.gudang_tujuan as id_gudangtujuan, rg2.nama as nama_gudangtujuan'))->first();
      $data['data'] = $this->data_penerimaan($d_data);
      $data['gudang'] = DB::table('ref_gudang')->get();
      $data['status'] = \Config::get('constants.status_penerimaanpengiriman');
      $data['crud']   = 'tambah';
      //print_r($data['data']);exit();
      return view('admin.pengiriman.create_penerimaan')->with('data',$data);

    }

    public function data_penerimaan($data = array()){
      if($data != null){
        $store['id_pengiriman']     = $data->id;
        $store['tanggal_pengiriman']= tgl_full($data->tanggal_pengiriman,'');
        $store['tanggal_penerimaan']= ($data->tanggal_penerimaan==''||$data->tanggal_penerimaan==null)?date('d-m-Y'):tgl_full($data->tanggal_penerimaan,'');
        $store['kode_pengiriman']   = $data->kode_pengiriman;
        $store['id_gudangawal']     = $data->id_gudangawal;
        $store['id_gudangtujuan']   = $data->id_gudangtujuan;
        $store['nama_gudangawal']   = $data->nama_gudangawal;
        $store['nama_gudangtujuan'] = $data->nama_gudangtujuan;
        $store['status']            = $data->status;
      }else{
        $store['id_pengiriman']     = "";
        $store['tanggal_pengiriman']= "";
        $store['tanggal_penerimaan']= "";
        $store['kode_pengiriman']   = "";
        $store['id_gudangawal']     = "";
        $store['id_gudangtujuan']   = "";
        $store['nama_gudangawal']   = "";
        $store['nama_gudangtujuan'] = "";
        $store['status']            = "";
      }
      return $store;
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

    public function get_edit(Request $request){
        $id_pengiriman = $request->get('id');
        $d_data = DB::table('pengiriman_detail as pd')->leftjoin('tbl_barang as tb','pd.id_barang','tb.barang_id')->leftjoin('tbl_satuan as ts','pd.id_satuan','ts.satuan_id')->where('id_inv_pengiriman',$id_pengiriman)->select(DB::raw('pd.id, pd.id_inv_pengiriman as id_pengiriman, pd.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, pd.id_satuan, ts.satuan_satuan as nama_satuan, pd.id_log_stok, pd.id_log_stok_penerimaan, pd.jumlah, pd.diterima as jumlah_terima, pd.harga, pd.total, pd.keterangan'));
        
        if($d_data->count() > 0){
          foreach ($d_data->get() as $d) {
            $arr[] = array('id'=>$d->id,
                            'id_pengiriman' => $d->id_pengiriman,
                            'id_barang'     => $d->id_barang,
                            'nama_barang'   => $d->nama_barang,
                            'kode_barang'   => $d->kode_barang,
                            'id_satuan'     => $d->id_satuan,
                            'nama_satuan'   => $d->nama_satuan,
                            'jumlah'        => $d->jumlah,
                            'jumlah_terima' => $d->jumlah_terima,
                            'harga'         => $d->harga,
                            'total'         => $d->total,
                            'keterangan'    => ($d->keterangan==null)?'':$d->keterangan,
                            'id_log_stok'   => $d->id_log_stok,
                            'id_log_stok_penerimaan' => $d->id_log_stok_penerimaan);
          }
        }else{
          $arr = array();
        }

        return response()->json($arr);
    }

    public function simpan(Request $request){
      // print_r(tgl_full($request->get('tanggal_penerimaan'),''));exit();
      //print_r($request->all());exit();
      $id_pengiriman = $request->get('id_pengiriman');      
      $data['status_pengiriman'] = $request->get('status_penerimaan');
      $data['tanggal_penerimaan']= tgl_full($request->get('tanggal_penerimaan'),'6');

      $tabel_id = $request->get('tabel_id');
      $tabel_idpengiriman = $request->get('tabel_idpengiriman');
      $tabel_idbarang = $request->get('tabel_idbarang');
      $tabel_idsatuan = $request->get('tabel_idsatuan');
      $tabel_jumlah   = $request->get('tabel_jumlah');
      $tabel_jumlahterima = $request->get('tabel_jumlah_terima');
      $tabel_harga    = $request->get('tabel_harga');
      $tabel_total    = $request->get('tabel_total');
      $tabel_idlog_stok = $request->get('tabel_idlog_stok');
      $tabel_idlog_stok_penerimaan  = $request->get('tabel_idlog_stok_penerimaan');
      $tabel_keterangan = $request->get('tabel_keterangan');

      DB::table('pengiriman')->where('id',$id_pengiriman)->update($data);
      trigger_log($id_pengiriman,'Mengubah data Menu Penerimaan Pengiriman',2,null);
      
      for($i=0;$i < sizeof($tabel_id);$i++){
      $input['id_barang']   = $tabel_idbarang[$i];
      $input['id_satuan']   = $tabel_idsatuan[$i];
      $input['id_ref_gudang'] = $request->get('id_gudangtujuan');
      $input['unit_masuk']  = $tabel_jumlahterima[$i];
      $input['unit_keluar'] = '0';
      $input['tanggal']     = tgl_full($request->get('tanggal_penerimaan'),'99');
      $input['status']      = 'K2';

      $barang['diterima'] = $tabel_jumlahterima[$i];
      $barang['keterangan'] = $tabel_keterangan[$i];
      $barang['status'] = $request->get('status_penerimaan'); //tambahan titan

      $cek = DB::table('tbl_log_stok')->where(array('log_stok_id'=>$tabel_idlog_stok_penerimaan[$i]));
      if($cek->count()>0){
        if($request->get('status_penerimaan')=='3' || $request->get('status_penerimaan')=='2'){
          DB::table('tbl_log_stok')->where(array('log_stok_id' => $tabel_idlog_stok_penerimaan[$i]))->update($input);
          $barang['id_log_stok_penerimaan'] = $tabel_idlog_stok_penerimaan[$i];
        
        }else if($request->get('status_penerimaan')=='1'){ //tambahan titan
          DB::table('tbl_log_stok')->where(array('log_stok_id' => $tabel_idlog_stok_penerimaan[$i]))->delete();
          $barang['id_log_stok_penerimaan'] = "";
        }
      }else{
          if($request->get('status_penerimaan')!='1'){ //tambahan titan
            $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);
            $barang['id_log_stok_penerimaan'] = $id_log_stok;
          }
      }
        DB::table('pengiriman_detail')->where(array('id'=>$tabel_id[$i]))->update($barang);
        
      }
      //print_r($log);exit();

      return redirect('penerimaanpengiriman');
    }


    public function edit($id){
      $id = dec($request['id']);
      $d_data = DB::table('pengiriman as p')->leftjoin('ref_gudang as rg','p.gudang_awal','rg.id')->leftjoin('ref_gudang as rg2','p.gudang_tujuan','rg2.id')->where('p.id',$id)->select(DB::raw('p.id , p.id_pengiriman, p.tanggal_pengiriman, p.tanggal_penerimaan, p.kode_pengiriman, p.status_pengiriman as status, p.gudang_awal as id_gudangawal, rg.nama as nama_gudangawal, p.gudang_tujuan as id_gudangtujuan, rg2.nama as nama_gudangtujuan'))->first();
      $data['data'] = $this->data_penerimaan($d_data);
      $data['gudang'] = DB::table('ref_gudang')->get();
      $data['status'] = \Config::get('constants.status_penerimaanpengiriman');

      return view('admin.pengiriman.create_penerimaan')->with('data',$data);
    }


}
