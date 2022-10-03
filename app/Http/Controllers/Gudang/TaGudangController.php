<?php

namespace App\Http\Controllers\Gudang;

use Illuminate\Http\Request;
use DB;
use App\TaGudang;
use Redirect;
use App\Http\Controllers\Controller;

class TaGudangController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
    public function index()
    {
      $gudang['gudang'] = DB::table('ref_gudang')->where('jenis_gudang', '1')->get();
      // dd($gudang);
      $gudang['outlet'] = DB::table('ref_gudang')->where('jenis_gudang', '2')->get();
      return view('admin.pengiriman.index', compact('gudang'));
    }

    public function store(){

      $crud = $request['crud'];
      $a=0;
      $alert=array("","");
      if ($crud =='tambah') {
        $program = new TaGudang;
        $program->kode_pengiriman = $request['nama'];
        $program->gudang_awal = $request['alamat'];
        $program->gudang_tujuan = $request['jenis_gudang'];
        $program->status_pengiriman = $request['status'];
        $program->tanggal_pengiriman = $request['status'];
        $program->jumlah = $request['status'];
        $a = $program-> save();
        // print_r($program);exit;
        $alert=array("Failed to create new data","New data created successfully");
      }elseif ($crud == 'edit') {
        $id = $request['id'];
        $program = TaGudang::find($id);
        $program->nama = $request['nama'];
        $program->alamat = $request['alamat'];
        $program->jenis_gudang = $request['jenis_gudang'];
        $program->status = $request['status'];
        // print_r($program);exit;
        $a = $program -> update();
        $alert=array("Failed to update data","Data updated successfully");
      }
      // echo json_encode(array('result'=>$a,'alert'=>$alert[$a]));
      // print_r($request['kode']);exit;
    }
    public function listData(){
        $program = TaGudang::orderBy('id', 'DESC')->get();
        $no = 0;
        $data = array();
        foreach ($program as $list) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $list->kode_pengiriman;
            $row[] = $list->gudang_awal;
            $row[] = $list->gudang_tujuan;
            $row[] = $list->status_pengiriman;
            $row[] = $list->tanggal_pengiriman;
            $row[] = $list->jumlah;
            $row[] = '<a data-id="'.$list->id.'" data-alamat="'.$list->alamat.'" data-nama="'.$list->nama.'" id="btn_edit" class="btn btn-xxs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
            <a data-id="'.$list->id.'" id="btn_hapus" class="btn btn-danger btn_hapus" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>';
            $data[] = $row;

        }
        $output = array("data" => $data);
        return response()->json($output);
    }
}
