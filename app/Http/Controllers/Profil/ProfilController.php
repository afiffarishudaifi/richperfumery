<?php

namespace App\Http\Controllers\Profil;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent;

class ProfilController extends Controller
{
     public function __construct(){
        // $id_group = Auth::user()->group_id;
        $this->agent = new Agent();

      }
      public function index(){
        if ($this->agent->isMobile()) {
          // code...
          return view('admin_mobile.profil.index');
        }else {
          // code...
          return view('admin.profil.index');
        }
      }
      public function lihatdata(){
        $id_group = Auth::user()->group_id;
        $id_profil = Auth::user()->id_profil;
        // print_r($id_profil);exit;
        if ($id_group == 1) {
              // $a= DB::table('ref_gudang')->where('id_profil',$id_profil)->get()->first();
              $where=' ';
        }else{
              $a= DB::table('ref_gudang')->where('id_profil', $id_profil)->get()->first();                 $where=' where id="'.$id_profil.'" ';
        }

      // print_r($where);exit;
      $profil = DB::select('select * from m_profil '.$where.' ');
      $no = 0;
      $data = array();
      foreach ($profil as $list) {
        $id = base64_encode($list->id);
        if ($list->jenis_outlet==1) {
           $jenis = '<label class="label label-success">Pusat</label> ';
        }else{
            $jenis = '<label class="label label-primary">Outlet</label> ';

        }
            $no++;
            $row = array();
            if ($this->agent->isMobile()) {
              $row['no'] = $no;
              $row['nama'] = $list->nama;
              $row['inisial'] = $list->inisial;
              $row['telp'] = $list->telp;
              $row['jenis'] = $jenis;
              $row['alamat'] = $list->alamat;
              $row['aksi'] = '<a href="#" data-nama="'.$list->nama.'" data-telp="'.$list->telp.'" data-inisial="'.$list->inisial.'" data-jenis_outlet="'.$list->jenis_outlet.'" data-alamat="'.$list->alamat.'" data-id="'.$list->id.'" class="btn btn-warning btn-xs btn_edit">Edit</a>
              <a class="btn btn-xs btn-danger btn_hapus" data-id="'.$list->id.'">hapus</a> ';
            }else {
              $row[] = $no;
              $row[] = $list->nama;
              $row[] = $list->inisial;
              $row[] = $list->telp;
              $row[] = $jenis;
              $row[] = $list->alamat;
              $row[] = '<a href="#" data-nama="'.$list->nama.'" data-telp="'.$list->telp.'" data-inisial="'.$list->inisial.'" data-jenis_outlet="'.$list->jenis_outlet.'" data-alamat="'.$list->alamat.'" data-id="'.$list->id.'" class="btn btn-warning btn-xs btn_edit">Edit</a>
              <a class="btn btn-xs btn-danger btn_hapus" data-id="'.$list->id.'">hapus</a> ';
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
      $tes['inisial'] = $request['inisial'];
      $tes['alamat'] = $request['alamat'];
      $tes['telp'] = $request['telp'];
      $tes['inisial'] = $request['inisial'];
      $tes['jenis_outlet'] = $request['jenis'];
      DB::table('m_profil')->insert($tes);

    }elseif ($crud == 'edit') {
      $tes['nama'] = $request['nama'];
      $tes['inisial'] = $request['inisial'];
      $tes['alamat'] = $request['alamat'];
      $tes['telp'] = $request['telp'];
      $tes['inisial'] = $request['inisial'];
      $tes['jenis_outlet'] = $request['jenis'];
      DB::table('m_profil')->where('id',$id_a)->update($tes);

    }
     return redirect($_SERVER['HTTP_REFERER']);

    }
     public function destroy($id){
     DB::table('m_profil')->where(array('id' => $id))->delete();
     }
}
