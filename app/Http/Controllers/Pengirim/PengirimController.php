<?php

namespace App\Http\Controllers\Pengirim;

use Illuminate\Http\Request;
use DB;

use App\Http\Controllers\Controller;

class PengirimController extends Controller
{
   public function index(){

       return view('admin.pengirim.index');
    }
    public function lihatdata(){
      $profil = DB::select('select * from m_pengirim');
      $no = 0;
      $data = array();
      foreach ($profil as $list) {
        $id = base64_encode($list->id);
        
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $list->nama;
            $row[] = $list->no_hp;
            $row[] = $list->lokasi;
            $row[] = '<a href="#" data-nama="'.$list->nama.'" data-telp="'.$list->no_hp.'" data-lokasi="'.$list->lokasi.'"  data-id="'.$list->id.'" class="btn btn-warning btn-xs btn_edit">Edit</a> 
            <a class="btn btn-xs btn-danger btn_hapus" data-id="'.$list->id.'">hapus</a> ';

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
      $tes['lokasi'] = $request['alamat'];
      $tes['no_hp'] = $request['telp'];
      DB::table('m_pengirim')->insert($tes);      
    }elseif ($crud == 'edit') {
      $tes['nama'] = $request['nama'];
      $tes['lokasi'] = $request['alamat'];
      $tes['no_hp'] = $request['telp'];
      DB::table('m_pengirim')->where('id',$id_a)->update($tes);

    }
     return redirect($_SERVER['HTTP_REFERER']);
    
    }
     public function destroy($id){
     DB::table('m_pengirim')->where(array('id' => $id))->delete();
     }
}
