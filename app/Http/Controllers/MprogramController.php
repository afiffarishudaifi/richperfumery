<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\M_program;
use DB;
use Redirect;

class MprogramController extends Controller
{
    //
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function Index()
    {
      $menu['program'] = M_program::all();
      return view('admin.mprogram.index', compact('menu'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $crud = $request['crud'];
      $a=0;
      $alert=array("","");
      if ($crud =='tambah') {
        $program = new M_program;
        $program->kode_tr = $request['kode'];
        $program->nama = $request['nama'];
        $a = $program-> save();
        // print_r($program);exit;
        $alert=array("Failed to create new data","New data created successfully");
      }elseif ($crud == 'edit') {
        $id = $request['id'];
        $program = M_program::find($id);
        $program->kode_tr = $request['kode'];
        $program->nama = $request['nama'];
        $a = $program -> update();
        $alert=array("Failed to update data","Data updated successfully");
      }
      // echo json_encode(array('result'=>$a,'alert'=>$alert[$a]));
      // print_r($request['kode']);exit;

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $menu = M_program::find($id);
      $menu -> delete();
    }
    public function listdata()
    {
      $program = M_program::orderBy('kode_tr', 'DESC')->get();
      $no = 0;
      $data = array();
      foreach ($program as $list) {

          $no++;
          $row = array();
          $row[] = $no;
          $row[] = $list->kode;
          $row[] = $list->nama;
          $row[] = '<a data-id="'.$list->id.'" data-kode="'.$list->kode_tr.'" data-nama="'.$list->nama.'" id="btn_edit" class="btn btn-xxs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
          <a data-id="'.$list->id.'" id="btn_hapus" class="btn btn-danger btn_hapus" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>';
          $data[] = $row;

      }

      $output = array("data" => $data);
      return response()->json($output);

  }

}
