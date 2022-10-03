<?php

namespace App\Http\Controllers\Metode;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Http\Controllers\Controller;

class MetodePembayaranController extends Controller
{
       public function __construct(){
        // $id_group = Auth::user()->group_id;
        
      }
      public function index(){
        
        return view('admin.metode.index');
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
      $profil = DB::select('select * from m_metode ');
      $no = 0;
      $data = array();
      foreach ($profil as $list) {
     
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $list->nama;
            $row[] = '<a href="#" data-id="'.$list->id.'" data-nama="'.$list->nama.'" class="btn btn-warning btn-xs btn_edit">Edit</a> 
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
      DB::table('m_metode')->insert($tes);
      
    }elseif ($crud == 'edit') {
      $tes['nama'] = $request['nama'];
      DB::table('m_metode')->where('id',$id_a)->update($tes);

    }
     return redirect($_SERVER['HTTP_REFERER']);
    
    }
     public function destroy($id){
     DB::table('m_metode')->where(array('id' => $id))->delete();
     }
}
