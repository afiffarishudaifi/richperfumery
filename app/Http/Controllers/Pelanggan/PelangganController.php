<?php

namespace App\Http\Controllers\Pelanggan;

use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Controller;
use Auth;
use App\PelangganModel;
use Jenssegers\Agent\Agent;

class PelangganController extends Controller
{
  public function __construct()
  {
      $this->agent = new Agent();

  }
    public function Index()
    {
        $id_profil = Auth::user()->id_profil;
        $group = Auth::user()->group_id;
        $where = "";
        if($group == 5 || $group == 6){
          $where = "WHERE id_profil='$id_profil'";
        }
        $d_gudang = DB::select(base_gudang($where));
        $id_gudang = array();
        foreach($d_gudang as $d){
          $id_gudang[] = $d->id_gudang;
        }
        $where_gudang = "";
        if(sizeof($id_gudang) > 0){
          $gudang = implode(',',$id_gudang);
          $where_gudang = "WHERE g.id IN ($gudang)";
        }
        $data['gudang'] = DB::select("
        SELECT
          g.id,
          g.id_profil,
          g.nama,
          g.alamat,
          g.`status`,
          g.created_at,
          g.updated_at,
          p.jenis_outlet
        FROM
          ref_gudang g
          LEFT JOIN m_profil p ON g.id_profil = p.id $where_gudang
        ");
        if ($this->agent->isMobile()) {
          // code...
          return view('admin_mobile.pelanggan.index')->with('data',$data);
        }else {
          // code...
          return view('admin.pelanggan.index')->with('data',$data);
        }
    }
    
    public function lihatdata(){
      $id_profil = Auth::user()->id_profil;
      $group = Auth::user()->group_id;
      $where = "";
      if($group == 5 || $group == 6){
        $where = "WHERE id_profil='$id_profil'";
      }
      $d_gudang = DB::select(base_gudang($where));
      $id_gudang = array();
      foreach($d_gudang as $d){
        $id_gudang[] = $d->id_gudang;
      }
      $where_gudang = "";
      if(sizeof($id_gudang) > 0){
        $gudang = implode(',',$id_gudang);
        $where_gudang = "WHERE rf.id IN ($gudang)";
      }
      $profil = DB::select("select mp.*, rf.nama as nama_gudang from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id $where_gudang");
      $no = 0;
      $data = array();
      foreach ($profil as $list) {
            if ($list->status == 1) {
             $status = "<label class='label label-default'>Biasa</label>";
            }else if($list->status == 2){
              $status = "<label class='label label-success'>Member</label>";
            }else if($list->status == 3){
              $status = "<label class='label label-warning'>Karyawan</label>";
            }else if($list->status == 4){
              $status = "<label class='label label-danger'>Reseller</label>";
            }else{
               $status = "<label class='label label-default'>tidak ada</label>";
            }
            $gudang = "";
            if($list->nama_gudang != null || $list->nama_gudang != 0 || $list->nama_gudang != ''){
              $gudang = $list->nama_gudang;
            }
            $no++;
            $row = array();
            if ($this->agent->isMobile()) {
              $row['no'] = $no;
              $row['nama'] = $list->nama;
              $row['telp'] = $list->telp;
              $row['alamat'] = $list->alamat;
              $row['gudang'] = $gudang;
              $row['status'] = $status;
              $row['aksi'] = '<div class="btn-group"><a href="#" data-nama="'.$list->nama.'" data-telp="'.$list->telp.'" data-alamat="'.$list->alamat.'" data-status="'.$list->status.'" data-no_member="'.$list->no_member.'" data-tempat="'.$list->tempat.'" data-tanggal_lahir="'.$list->tanggal_lahir.'" data-email="'.$list->email.'" data-jenis="'.$list->jenis_kelamin.'" data-id="'.$list->id.'" data-gudang="'.$list->id_gudang.'" data-nama_gudang="'.$list->nama_gudang.'" data-tanggal_awal="'.tgl_full($list->tanggal_awal,'').'" data-tanggal_akhir="'.tgl_full($list->tanggal_akhir,'').'" data-status_aktif="'.$list->status_aktif.'" class="btn btn-xs btn-primary btn_edit"><i class="fa fa-edit"></i> </a>
              <a class="btn btn-xs btn-danger btn_hapus" data-id="'.$list->id.'"><i class="fa fa-trash"></i> </a></div>';
            }else {
              $row[] = $no;
              $row[] = $list->nama;
              $row[] = $list->telp;
              $row[] = $list->alamat;
              $row[] = $gudang;
              $row[] = $status;
              $row[] = '<div class="btn-group"><a href="#" data-nama="'.$list->nama.'" data-telp="'.$list->telp.'" data-alamat="'.$list->alamat.'" data-status="'.$list->status.'" data-no_member="'.$list->no_member.'" data-tempat="'.$list->tempat.'" data-tanggal_lahir="'.$list->tanggal_lahir.'" data-email="'.$list->email.'" data-jenis="'.$list->jenis_kelamin.'" data-id="'.$list->id.'" data-gudang="'.$list->id_gudang.'" data-nama_gudang="'.$list->nama_gudang.'" data-tanggal_awal="'.tgl_full($list->tanggal_awal,'').'" data-tanggal_akhir="'.tgl_full($list->tanggal_akhir,'').'" data-status_aktif="'.$list->status_aktif.'" class="btn btn-xs btn-primary btn_edit"><i class="fa fa-edit"></i> </a>
              <a class="btn btn-xs btn-danger btn_hapus" data-id="'.$list->id.'"><i class="fa fa-trash"></i> </a></div>';
            }
           $data[] = $row;
      }
       $output = array("data" => $data);
       return response()->json($output);
    }
    
    public function store(Request $request){
    $a=0;
    $alert=array("","");
    $id_a = $request['id'];
    $crud = $request['crud'];
    if ($crud =='tambah') {
      $tes['nama'] = $request['nama'];
      $tes['alamat'] = $request['alamat'];
      $tes['telp'] = $request['telp'];
      $tes['status'] = $request['status'];
      $tes['no_member'] = $request['nomember'];
      $tes['tempat'] = $request['tempat1'];
      $tes['tanggal_lahir'] = $request['tgl_lahir1'];
      $tes['email'] = $request['email1'];
      $tes['jenis_kelamin'] = $request['jenis'];
      $tes['id_gudang'] = $request['gudang'];
      $tes['tanggal_awal'] = tgl_full($request['tanggal_awal'],'99');
      $tes['tanggal_akhir'] = tgl_full($request['tanggal_akhir'],'99');
      $tes['status_aktif'] = $request['status_aktif'];
     DB::table('m_pelanggan')->insert($tes);

    }elseif ($crud == 'edit') {
      $tes['nama'] = $request['nama'];
      $tes['alamat'] = $request['alamat'];
      $tes['telp'] = $request['telp'];
      $tes['status'] = $request['status'];
      $tes['no_member'] = $request['nomember'];
      $tes['tempat'] = $request['tempat1'];
      $tes['tanggal_lahir'] = $request['tgl_lahir1'];
      $tes['email'] = $request['email1'];
      $tes['jenis_kelamin'] = $request['jenis'];
      $tes['id_gudang'] = $request['gudang'];
      $tes['tanggal_awal'] = tgl_full($request['tanggal_awal'],'99');
      $tes['tanggal_akhir'] = tgl_full($request['tanggal_akhir'],'99');
      $tes['status_aktif'] = $request['status_aktif'];
      DB::table('m_pelanggan')->where('id',$id_a)->update($tes);

    }
     return redirect($_SERVER['HTTP_REFERER']);

    }

    public function destroy($id){
     DB::table('m_pelanggan')->where(array('id' => $id))->delete();
    }

    public function cek_status_pelanggan(){
      /*$data['status_aktif'] = "2";
      DB::table('m_pelanggan')->where('status','=','2')->where('date(tanggal_akhir)','<=','date(now())')->update($data);*/
      DB::SELECT("UPDATE m_pelanggan SET `status_aktif` = 2 where status=2 and date(tanggal_akhir) >= date(now())");
      DB::SELECT("UPDATE m_pelanggan SET `status_aktif` = 2 where status=2 and date(tanggal_akhir) <= date(now())");
    }
}
