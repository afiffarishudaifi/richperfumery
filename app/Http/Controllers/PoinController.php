<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;

class PoinController extends Controller
{
    //

    public function index(){
    	return view('admin.poin.index');
    }

    public function get_data()
    {
        $d_data = DB::table('m_nominal_poin')->offset(0)->limit(1)->first();
        
        return response()->json($d_data);
    }

    public function simpan(Request $request){
        $id = $request->id;
        $data_s = array();
        $data['nominal'] = $request->nominal;        

    	if($id==''){
            DB::table('m_nominal_poin')->insert($data);
            $data_s = ['status' => '0', 'ket' => "Toleransi Terlambat Berhasil Ditambah"];
    	}else{
            DB::table('m_nominal_poin')->where('id','=',$id)->update($data);
            $data_s = ['status' => '1', 'ket' => "Toleransi Terlambat Berhasil Diperbarui"];
    	}

    	return json_encode($data_s);
    }
}
