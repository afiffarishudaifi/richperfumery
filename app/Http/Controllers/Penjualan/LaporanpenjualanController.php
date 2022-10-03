<?php

namespace App\Http\Controllers\Penjualan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;

class LaporanpenjualanController extends Controller
{
    public function index(){
    /*$group_id = Auth::user()->group_id;
    $id_profil = Auth::user()->id_profil;
    
    if ($group_id == 1) {
        $a = DB::select('select * from ref_gudang');
    } elseif ($group_id == 6) {
        $a = DB::select('select * from ref_gudang');
    } elseif ($group_id == 5) {
        $a = DB::table('ref_gudang')->where('id_profil', $id_profil)->get();
    }*/
    $id_profil = Auth::user()->id_profil;
    $group = Auth::user()->group_id;
    $where = "";
        if($group == 5 || $group == 6){
          $where = "WHERE id_profil='$id_profil'";
        }
    $data['gudang'] = DB::select(base_gudang($where));
    return view('admin.laporan.index_laporan_penjualan')->with('data',$data);
    }

    public function cetaklaporan(Request $request){
    $data['gudang'] = $request->gudang;
    $data['tanggal'] = tgl_full($request->tanggal,'');
    $data['hari']    = tgl_full($request->tanggal,'hari');
    $data['kasir']   = Auth::user()->name;
    
    return view('admin.laporan.cetak_laporan_penjualan')->with('data',$data);
    }
}
