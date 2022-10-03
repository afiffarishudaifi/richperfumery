<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Redirect;

class BarangClosingController extends Controller
{
    //

    public function index(){
    	return view('admin.barang.index_closing');
    }

    public function get_barang(Request $request){
    	$term = trim($request->q);
        if (empty($term)) {
            // return \Response::json([]);
        }
        $search  = strtolower($term);

        $d_closing = DB::table('m_barang_closing');
        $arr = array();
        foreach($d_closing->get() as $d){
            $arr[] = $d->id_barang;
        }
        $im_closing = implode(',',$arr);
        $d_query = DB::table('tbl_barang')->where('barang_id_parent','!=','0')->whereNotIn('barang_id',[$im_closing]);
        if($search != null){
            $d_query->where(function($d_query) use ($search){
            	$d_query->orwhere('barang_nama','LIKE','%'.$search.'%');
            	$d_query->orwhere('barang_alias','LIKE','%'.$search.'%');
            	$d_query->orwhere('barang_kode','LIKE','%'.$search.'%');
            });
        }
        $d_data = $d_query->get();

        return response()->json($d_data);
    }

    public function get_data(Request $request){
    	$barang = DB::table('m_barang_closing as mb')->join('tbl_barang as tb','mb.id_barang','tb.barang_id')->orderBy('mb.id_barang', 'ASC')->SELECT(DB::Raw('mb.id, mb.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, tb.barang_alias as alias_barang'))->get();
        $no = 0;
        $data = array();
        foreach ($barang as $list) {
          $d_barang[] = $list->nama_barang;
          $nama_barang = $list->kode_barang." || ".$list->nama_barang." (".$list->alias_barang.")";
          if($list->alias_barang == null || $list->alias_barang == '' ){
          	$d_barang[] += $list->alias_barang;
          	$nama_barang = $list->kode_barang." || ".$list->nama_barang;
          }

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $nama_barang;
            $row[] = '<div class="btn-group"><a onclick="editForm('.$list->id.')" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i></a>
            <a onclick="deleteData('.$list->id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);
    }

    public function edit($id){
    	$d_data = DB::table('m_barang_closing as mb')->join('tbl_barang as tb','mb.id_barang','tb.barang_id')->where('id',$id)->select(DB::Raw('mb.id, mb.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, tb.barang_alias as alias_barang'))->get();
    	return response()->json($d_data);
    }

    public function simpan(Request $request){
    	$id = $request->popup_id;
    	$data['id_barang'] = $request->popup_barang;
    	DB::beginTransaction();
    	try{
	    	if($id == ''){
	    		DB::table('m_barang_closing')->insert($data);
	    		$status = 1;
	    	}else{
	    		DB::table('m_barang_closing')->where('id',$id)->update($data);
	    		$status = 2;
	    	}
	    	DB::commit();
    	}catch(\Exception $e){
    		DB::rollback();
    		$status = 0;
    	}

    	return response()->json(array('status'=>$status));
    }

    public function hapus(Request $request){
    	$id = $request->id;
    	DB::beginTransaction();
    	try{
    		DB::table('m_barang_closing')->where('id',$id)->delete();
            DB::commit();
            $status = 1;
    	}catch(\Exception $e){
    		DB::rollback();
    		$status = 0;
    	}
        return response()->json(array('status'=>$status));
    }

}
