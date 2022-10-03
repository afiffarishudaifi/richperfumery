<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use DB;

class SatuankonversiController extends Controller
{
    //

    public function index(){
    	$data['satuan']     = DB::table('tbl_satuan')->orderBy('satuan_nama','asc')->get();
    	return view('admin.satuan_konversi.index')->with('data',$data);
    }

    
    public function listData()
    {      

        $konversi = DB::table('tbl_satuan_konversi as tsk')->join('tbl_satuan as ts','tsk.id_satuan_awal','ts.satuan_id')->join('tbl_satuan as ts2','tsk.id_satuan_akhir','ts2.satuan_id')->select(DB::raw('tsk.*, ts.satuan_nama as nama_satuan_awal,ts2.satuan_nama as nama_satuan_akhir'))->orderBy('id_konversi', 'DESC')->get();

        $no = 0;
        $data = array();
        foreach ($konversi as $list) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $list->nama_satuan_awal;
            $row[] = $list->jumlah_awal;
            $row[] = $list->nama_satuan_akhir;
            $row[] = $list->jumlah_akhir;
            $row[] = '<div class="btn-group"><button class="btn btn-xs btn-primary" onclick="edit('.$list->id_konversi.')" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i> </button>
            <a onclick="deleteData('.$list->id_konversi.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>'.
            			'<input type="hidden" id="table_id'.$list->id_konversi.'" value="'.$list->id_konversi.'">'.
                        '<input type="hidden" id="table_idsatuan_awal'.$list->id_konversi.'" value="'.$list->id_satuan_awal.'">'.
                        '<input type="hidden" id="table_jumlah_awal'.$list->id_konversi.'" value="'.$list->jumlah_awal.'">'.
                        '<input type="hidden" id="table_idsatuan_akhir'.$list->id_konversi.'" value="'.$list->id_satuan_akhir.'">'.
                        '<input type="hidden" id="table_jumlah_akhir'.$list->id_konversi.'" value="'.$list->jumlah_akhir.'">'.
                        '<input type="hidden" id="table_jumlah_bagi'.$list->id_konversi.'" value="'.$list->jumlah_bagi.'">';
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);
    }

    function simpan(Request $request){
        $id = $request->get("popup_id_table");

    	$data['id_satuan_awal']  = $request->get("popup_satuan_awal");
        $data['jumlah_awal'] 	 = $request->get("popup_jumlah_awal");
        $data['id_satuan_akhir'] = $request->get("popup_satuan_akhir");
        $data['jumlah_akhir'] 	 = $request->get("popup_jumlah_akhir");
        $data['jumlah_bagi'] 	 = $request->get("popup_jumlah_bagi");

        if($id == ''){
            DB::table('tbl_satuan_konversi')->insert($data);
        }else{
            DB::table('tbl_satuan_konversi')->where('id_konversi',$id)->update($data);
        }

        return response()->json(array('status' => '1'));
    }

    public function hapus(Request $request){
      $id = $request->get('id');
      DB::table('tbl_satuan_konversi')->where('id_konversi',$id)->delete();
    }


}
