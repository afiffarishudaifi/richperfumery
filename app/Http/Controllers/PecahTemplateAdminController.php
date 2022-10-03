<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent;


class PecahTemplateAdminController extends Controller
{
  public function __construct()
  {
      $this->agent = new Agent();

  }
  public function index()
  {
    $group_id = Auth::user()->group_id;
    $id_profil = Auth::user()->id_profil;

    if ($group_id == 1 ) {
      $a = DB::select('select * from ref_gudang');
      // $where = 'where 1=1';
    }else if($group_id == 6){
      $a = DB::select('select * from ref_gudang');
      // $where = 'where id=1';

    }else if($group_id == 5){
      $a = DB::table('ref_gudang')->where('id_profil', $id_profil)->get();

    }else{
      $a = DB::select('select * from ref_gudang');
    }

    $data['gudang'] = $a;
    $data['user_group'] = $group_id;
    // return view('admin.data')->with('data', $data);

    if ($this->agent->isMobile()) {
      return view('admin_mobile.data')->with('data',$data);
    }else {
      return view('admin.data')->with('data', $data);
    }


  }
}
