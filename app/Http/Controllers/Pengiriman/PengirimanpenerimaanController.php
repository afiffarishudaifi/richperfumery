<?php

namespace App\Http\Controllers\Pengiriman;

use Illuminate\Http\Request;
use DB;
// use App\InvPengiriman;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent;

class PengirimanpenerimaanController extends Controller
{
  public function __construct(){
    $this->agent = new Agent();
  }
    public function index(){
      $data['gudang'] = DB::select('SELECT
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
      $data['outlet'] = DB::select('SELECT
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
      $data['status'] = \Config::get('constants.status_penerimaanretur');
      if ($this->agent->isMobile()) {
        // code...
        return view('admin_mobile.pengiriman.penerimaan')->with('data',$data);
      }else {
        // code...
        return view('admin.pengiriman.penerimaan')->with('data',$data);
      }
    }
     public function lihatdata(){
        $id_profil = Auth::user()->id_profil;
        $group_id = Auth::user()->group_id;

        $group_where = DB::table('tbl_group')->where('group_id',$group_id)->first();

        if ($group_id == 1) {
          $where="";
        }else{
          $a=DB::table('ref_gudang')->where('id_profil',$id_profil)->get()->first();
          $where="where gudang_tujuan=".$a->id;
        }
        // print_r($where);exit;
       $pengiriman_retur = DB::select("SELECT
        r.id,
        r.id_barang,
        r.id_gudang_pusat,
        r.id_gudang_outlet,
        r.id_log_stok,
        r.id_log_stok_penerimaan,
        r.id_pengiriman,
        r.id_satuan,
        s.satuan_satuan as nama_satuan,
        r.kode_retur,
        r.tanggal_pengiriman,
        r.tanggal_penerimaan,
        r.jumlah,
        r.jumlah_terima,
        r.`status`,
        r.created_at,
        r.updated_at,
        b.barang_nama,
        b.barang_kode,
        b.barang_alias,
        ga.nama nama_gudangpusat,
        outl.nama nama_gudangoutlet,
        r.keterangan
        FROM
        pengiriman_retur AS r
        LEFT JOIN tbl_barang AS b ON r.id_barang = b.barang_id
        LEFT JOIN tbl_satuan AS s ON r.id_satuan = s.satuan_id
        LEFT JOIN ref_gudang AS ga ON r.id_gudang_pusat = ga.id
        LEFT JOIN ref_gudang AS outl ON outl.id = r.id_gudang_outlet WHERE	r.`status` != 0 
        AND r.tanggal_pengiriman > DATE_SUB(DATE(NOW()), INTERVAL 7 DAY) 
        AND r.tanggal_pengiriman <= DATE_SUB(DATE(NOW()), INTERVAL 0 DAY)");
      $no = 0;
      $data = array();
      foreach ($pengiriman_retur as $list) {
          //0 = tambahkan detail barang
           //1 = sedang dikirim
           //2 = terkirim
           //3 = diterima sebagian
           //4 = dikembalikan
        $d_edit = '<a data-id="'.$list->id.'" data-kode="'.$list->kode_retur.'" data-id_satuan="'.$list->id_satuan.'" data-nama_satuan="'.$list->nama_satuan.'" data-id_barang="'.$list->id_barang.'" data-nama_barang="'.$list->barang_nama.'" data-kode_barang="'.$list->barang_kode.'"  data-jumlah="'.$list->jumlah.'" data-jumlah_terima="'.$list->jumlah_terima.'" data-id_gudang_outlet="'.$list->id_gudang_outlet.'" data-nama_gudangoutlet="'.$list->nama_gudangoutlet.'" data-tanggal_terima="'.date('d-m-Y',strtotime($list->tanggal_penerimaan)).'" data-id_gudang_pusat="'.$list->id_gudang_pusat.'" data-nama_gudangpusat="'.$list->nama_gudangpusat.'" data-tanggal="'.tgl_full($list->tanggal_pengiriman,'').'" data-keterangan="'.$list->keterangan.'" data-status="'.$list->status.'" data-id_log_stok="'.$list->id_log_stok.'" data-id_log_stok_penerimaan="'.$list->id_log_stok_penerimaan.'" id="btn_tambah" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="botttom" title="Terima Data"  style="color:white;"><i class="fa fa-check"></i></a> ';
        $d_detail = '<a data-id="'.$list->id.'" data-kode="'.$list->kode_retur.'" data-id_satuan="'.$list->id_satuan.'" data-nama_satuan="'.$list->nama_satuan.'" data-id_barang="'.$list->id_barang.'" data-nama_barang="'.$list->barang_nama.'" data-kode_barang="'.$list->barang_kode.'"  data-jumlah="'.$list->jumlah.'" data-jumlah_terima="'.$list->jumlah_terima.'" data-id_gudang_outlet="'.$list->id_gudang_outlet.'" data-nama_gudangoutlet="'.$list->nama_gudangoutlet.'" data-tanggal_terima="'.date('d-m-Y',strtotime($list->tanggal_penerimaan)).'" data-id_gudang_pusat="'.$list->id_gudang_pusat.'" data-nama_gudangpusat="'.$list->nama_gudangpusat.'" data-tanggal="'.tgl_full($list->tanggal_pengiriman,'').'" data-keterangan="'.$list->keterangan.'" data-status="'.$list->status.'" data-id_log_stok="'.$list->id_log_stok.'" data-id_log_stok_penerimaan="'.$list->id_log_stok_penerimaan.'" id="btn_detail" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Terima Data"  style="color:white;"><i class="fa fa-eye"></i></a> ';
            if($group_where->group_aktif == '2'){
              if($list->status == 0){
                $status = '<label class=" label label-default">draf</label>';
                $action = $d_edit;
              }elseif($list->status == 1){
                $status = '<label class=" label label-warning">Belum diterima</label>';
                $action = $d_edit;
              }elseif($list->status == 2){
                $status = '<label class=" label label-success">diterima</label>';
                $action = $d_detail;
              }elseif($list->status == 3){
                $status = '<label class=" label label-primary">Diterima Sebagian</label>';
                $action = $d_edit;
              }else{
                $status = '';
                $action = '';
              }
            }else{
              if($list->status == 0){
                $status = '<label class=" label label-default">draf</label>';
                $action = $d_detail;
              }elseif($list->status == 1){
                $status = '<label class=" label label-warning">Belum diterima</label>';
                $action = $d_detail;
              }elseif($list->status == 2){
                $status = '<label class=" label label-success">diterima</label>';
                $action = $d_detail;
              }elseif($list->status == 3){
                $status = '<label class=" label label-primary">Diterima Sebagian</label>';
                $action = $d_detail;
              }else{
                $status = '';
                $action = '';
              }
            }

           
          $no++;
          $row = array();
          if ($this->agent->isMobile()) {
            // code...
            $row['no'] = $no;
            $row['kode_retur']  = $list->kode_retur;
            $row['barang_nama'] = $list->barang_nama;
            $row['nama_outlet'] = $list->nama_gudangoutlet;
            $row['nama_gudang'] = $list->nama_gudangpusat;
            $row['status']  = $status;
            $row['tanggal'] = date("d-m-Y", strtotime($list->tanggal_pengiriman));
            $row['tanggal_terima'] = date("d-m-Y", strtotime($list->tanggal_penerimaan));
            $row['jumlah']  = $list->jumlah ;
            $row['tbl'] = $action;
          }else {
            // code...
            $row[] = $no;
            $row[] = $list->kode_retur;
            $row[] = $list->barang_nama;
            $row[] = $list->nama_gudangoutlet;
            $row[] = $list->nama_gudangpusat;
            $row[] = $status;
            $row[] = date("d-m-Y", strtotime($list->tanggal_pengiriman));
            $row[] = date("d-m-Y", strtotime($list->tanggal_penerimaan));
            $row[] = $list->jumlah;
            $row[] = $action;
          }
          $data[] = $row;

      }
      $output = array("data" => $data);
      return response()->json($output);
    }

    public function searchtanggal(Request $request){
       $id_profil = Auth::user()->id_profil;
       $group_id = Auth::user()->group_id;
        if ($group_id == 1) {
          $where="";
        }else{
          $a=DB::table('ref_gudang')->where('id_profil',$id_profil)->get()->first();
          $where="where gudang_tujuan=".$a->id;
        }
        $group_where = DB::table('tbl_group')->where('group_id',$group_id)->first();
        // print_r($where);exit;
       $tanggalrange = explode('s.d.',$request->get('tanggal'));
       $tanggal_start  = tgl_full($tanggalrange[0],99);
       $tanggal_end    = tgl_full($tanggalrange[1],99);
       $pengiriman_retur = DB::select("SELECT
        r.id,
        r.id_barang,
        r.id_gudang_pusat,
        r.id_gudang_outlet,
        r.id_log_stok,
        r.id_log_stok_penerimaan,
        r.id_pengiriman,
        r.id_satuan,
        s.satuan_satuan as nama_satuan,
        r.kode_retur,
        r.tanggal_pengiriman,
        r.tanggal_penerimaan,
        r.jumlah,
        r.jumlah_terima,
        r.`status`,
        r.created_at,
        r.updated_at,
        b.barang_nama,
        b.barang_kode,
        b.barang_alias,
        ga.nama nama_gudangpusat,
        outl.nama nama_gudangoutlet,
        r.keterangan
        FROM
        pengiriman_retur AS r
        LEFT JOIN tbl_barang AS b ON r.id_barang = b.barang_id
        LEFT JOIN tbl_satuan AS s ON r.id_satuan = s.satuan_id
        LEFT JOIN ref_gudang AS ga ON r.id_gudang_pusat = ga.id
        LEFT JOIN ref_gudang AS outl ON outl.id = r.id_gudang_outlet WHERE  r.`status` != 0 
        AND r.tanggal_pengiriman >= '$tanggal_start' 
        AND r.tanggal_pengiriman <= '$tanggal_end'");
      $no = 0;
      $data = array();
      foreach ($pengiriman_retur as $list) {
          //0 = tambahkan detail barang
           //1 = sedang dikirim
           //2 = terkirim
           //3 = diterima sebagian
           //4 = dikembalikan
          $d_edit = '<a data-id="'.$list->id.'" data-kode="'.$list->kode_retur.'" data-id_satuan="'.$list->id_satuan.'" data-nama_satuan="'.$list->nama_satuan.'" data-id_barang="'.$list->id_barang.'" data-nama_barang="'.$list->barang_nama.'" data-kode_barang="'.$list->barang_kode.'"  data-jumlah="'.$list->jumlah.'" data-jumlah_terima="'.$list->jumlah_terima.'" data-id_gudang_outlet="'.$list->id_gudang_outlet.'" data-nama_gudangoutlet="'.$list->nama_gudangoutlet.'" data-tanggal_terima="'.date('d-m-Y',strtotime($list->tanggal_penerimaan)).'" data-id_gudang_pusat="'.$list->id_gudang_pusat.'" data-nama_gudangpusat="'.$list->nama_gudangpusat.'" data-tanggal="'.tgl_full($list->tanggal_pengiriman,'').'" data-keterangan="'.$list->keterangan.'" data-status="'.$list->status.'" data-id_log_stok="'.$list->id_log_stok.'" data-id_log_stok_penerimaan="'.$list->id_log_stok_penerimaan.'" id="btn_tambah" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="botttom" title="Terima Data"  style="color:white;"><i class="fa fa-check"></i></a> ';
        $d_detail = '<a data-id="'.$list->id.'" data-kode="'.$list->kode_retur.'" data-id_satuan="'.$list->id_satuan.'" data-nama_satuan="'.$list->nama_satuan.'" data-id_barang="'.$list->id_barang.'" data-nama_barang="'.$list->barang_nama.'" data-kode_barang="'.$list->barang_kode.'"  data-jumlah="'.$list->jumlah.'" data-jumlah_terima="'.$list->jumlah_terima.'" data-id_gudang_outlet="'.$list->id_gudang_outlet.'" data-nama_gudangoutlet="'.$list->nama_gudangoutlet.'" data-tanggal_terima="'.date('d-m-Y',strtotime($list->tanggal_penerimaan)).'" data-id_gudang_pusat="'.$list->id_gudang_pusat.'" data-nama_gudangpusat="'.$list->nama_gudangpusat.'" data-tanggal="'.tgl_full($list->tanggal_pengiriman,'').'" data-keterangan="'.$list->keterangan.'" data-status="'.$list->status.'" data-id_log_stok="'.$list->id_log_stok.'" data-id_log_stok_penerimaan="'.$list->id_log_stok_penerimaan.'" id="btn_detail" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Terima Data"  style="color:white;"><i class="fa fa-eye"></i></a> ';
            if($group_where->group_aktif == '2'){
              if($list->status == 0){
                $status = '<label class=" label label-default">draf</label>';
                $action = $d_edit;
              }elseif($list->status == 1){
                $status = '<label class=" label label-warning">Belum diterima</label>';
                $action = $d_edit;
              }elseif($list->status == 2){
                $status = '<label class=" label label-success">diterima</label>';
                $action = $d_detail;
              }elseif($list->status == 3){
                $status = '<label class=" label label-primary">Diterima Sebagian</label>';
                $action = $d_edit;
              }else{
                $status = '';
                $action = '';
              }
            }else{
              if($list->status == 0){
                $status = '<label class=" label label-default">draf</label>';
                $action = $d_detail;
              }elseif($list->status == 1){
                $status = '<label class=" label label-warning">Belum diterima</label>';
                $action = $d_detail;
              }elseif($list->status == 2){
                $status = '<label class=" label label-success">diterima</label>';
                $action = $d_detail;
              }elseif($list->status == 3){
                $status = '<label class=" label label-primary">Diterima Sebagian</label>';
                $action = $d_detail;
              }else{
                $status = '';
                $action = '';
              }
            }

           
          $no++;
          $row = array();
          if ($this->agent->isMobile()) {
            // code...
            $row['no'] = $no;
            $row['kode_retur'] = $list->kode_retur;
            $row['barang_nama'] = $list->barang_nama;
            $row['nama_outlet'] = $list->nama_gudangoutlet;
            $row['nama_gudang'] = $list->nama_gudangpusat;
            $row['status'] = $status;
            $row['tanggal'] = date("d-m-Y", strtotime($list->tanggal_pengiriman));
            $row['tanggal_terima'] = date("d-m-Y", strtotime($list->tanggal_penerimaan));
            $row['jumlah'] = $list->jumlah ;
            $row['tbl'] = $action;
          }else {
            // code...
            $row[] = $no;
            $row[] = $list->kode_retur;
            $row[] = $list->barang_nama;
            $row[] = $list->nama_gudangoutlet;
            $row[] = $list->nama_gudangpusat;
            $row[] = $status;
            $row[] = date("d-m-Y", strtotime($list->tanggal_pengiriman));
            $row[] = date("d-m-Y", strtotime($list->tanggal_penerimaan));
            $row[] = $list->jumlah ;
            $row[] = $action;
          }
          $data[] = $row;

      }
      $output = array("data" => $data);
      return response()->json($output);
    }

    public function setuju(Request $request){
      $id_a = $request['id'];
      $log['id_barang'] = $request['id_barang'];
      $log['id_ref_gudang'] = $request['id_gudang_pusat'];
      $log['id_satuan'] = $request['id_satuan'];
      //$log['tanggal'] = date("Y/m/d");
      $log['tanggal']   = $request['tanggal'];
      $log['unit_masuk'] = $request['jumlah'];
      $log['status'] = 'K4';
      $id = DB::table('tbl_log_stok')->insertGetId($log);
      $data['status'] = 2 ;
      $data['id_log_stok_penerimaan'] = $id ;
      DB::table('pengiriman_retur')->where('id',$id_a)->update($data);
      trigger_log($id_a,'Mengubah data Menu Pengiriman Penerimaan menerima barang setuju',2,null);
        // dd($request);
        return redirect($_SERVER['HTTP_REFERER']);
    }

    public function simpanretur(Request $request){

      $tabel_idretur = $request->get('popup_idretur');
      $tabel_status = $request->get('popup_status_hidden');
      $tabel_idlog_stok = $request->get('popup_idlog_stok');
      $tabel_idlog_stok_penerimaan = $request->get('popup_idlog_stok_penerimaan');
      
      $input['id_barang']     = $request->get('popup_idbarang');
      $input['id_satuan']     = $request->get('popup_idsatuan');
      $input['id_ref_gudang'] = $request->get('popup_idgudang_pusat');
      $input['unit_masuk']    = $request->get('popup_jumlahterima');
      $input['unit_keluar']   = '0';
      $input['tanggal']       = tgl_full($request->get('popup_tanggal'),'99');
      $input['status']        = 'K4';

      $data['kode_retur']   = $request->get('popup_kode');
      $data['id_barang']    = $request->get('popup_idbarang');
      $data['id_satuan']    = $request->get('popup_idsatuan');
      $data['jumlah_terima']= $request->get('popup_jumlahterima');
      $data['tanggal_penerimaan'] = tgl_full($request->get('popup_tanggal'),'7');
      $data['keterangan']   = $request->get('popup_keterangan');
      $data['status']       = $request->get('popup_status');

    if($tabel_idretur != '' && $tabel_status == '1'){      
      $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);
      $data['id_log_stok_penerimaan'] = $id_log_stok;      
      DB::table('pengiriman_retur')->where('id',$tabel_idretur)->update($data);
      trigger_log($tabel_idretur,'Mengubah data Menu Pengiriman Penerimaan dengan status menerima',2,null);
    }else{
      DB::table('tbl_log_stok')->where('log_stok_id',$tabel_idlog_stok_penerimaan)->update($input);
      DB::table('pengiriman_retur')->where('id',$tabel_idretur)->update($data);
      trigger_log($tabel_idretur,'Mengubah data Menu Pengiriman Penerimaan dengan status barang kembali belum diterima',2,null);
    }
    //print_r($id_log_stok);exit();
    return response()->json(array('status'=>'1'));

    }

    public function simpanretur_detail(Request $request){
      $tabel_idretur = $request->get('popup_detail_id_table');
      $tabel_idlog_stok_penerimaan = $request->get('popup_detail_idlog_stok_penerimaan');

      $input['id_barang']     = $request->get('popup_detail_idbarang');
      $input['id_satuan']     = $request->get('popup_detail_idsatuan');
      $input['id_ref_gudang'] = $request->get('popup_detail_idgudang_pusat');
      $input['unit_masuk']    = $request->get('popup_detail_jumlahterima');
      $input['unit_keluar']   = '0';
      $input['tanggal']       = tgl_full($request->get('popup_detail_tanggal'),'99');
      $input['status']        = 'K4';

      $data['kode_retur']         = $request->get('popup_detail_kode');
      $data['id_barang']          = $request->get('popup_detail_idbarang');
      $data['id_satuan']          = $request->get('popup_detail_idsatuan');
      $data['jumlah_terima']      = $request->get('popup_detail_jumlahterima');
      $data['tanggal_penerimaan'] = tgl_full($request->get('popup_detail_tanggal'),'99');
      $data['keterangan']         = $request->get('popup_detail_keterangan');
      $data['status']             = $request->get('popup_detail_status_hidden');

      if($tabel_idretur != '' && $tabel_idlog_stok_penerimaan != ''){
        DB::table('tbl_log_stok')->where('log_stok_id',$tabel_idlog_stok_penerimaan)->update($input);
        DB::table('pengiriman_retur')->where('id',$tabel_idretur)->update($data);
        $status = '1';
      }else{
        $status = '2';
      }

      return response()->json(array('status'=>$status));
    }

    
}
