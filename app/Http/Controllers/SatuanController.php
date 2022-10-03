<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Redirect;
use App\SatuanModel;

class SatuanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.satuan.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $satuan = new SatuanModel;
      $satuan ->satuan_nama = $request['satuan_nama'];
      $satuan ->satuan_satuan = $request['satuan_satuan'];
      $satuan ->konversi = $request['konversi'];
      $satuan -> save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $satuan = SatuanModel::find($id);
      echo json_encode($satuan);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      $satuan = SatuanModel::find($id);
      $satuan ->satuan_nama = $request['satuan_nama'];
      $satuan ->satuan_satuan = $request['satuan_satuan'];
      $satuan ->konversi = $request['konversi'];
      $satuan->update();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $satuan = SatuanModel::find($id);
      $satuan->delete();
    }

    public function listData()
    {
        $satuan = SatuanModel::orderBy('satuan_id', 'DESC')->get();
        $no = 0;
        $data = array();
        foreach ($satuan as $list) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $list->satuan_nama;
            $row[] = $list->satuan_satuan;
            $row[] = empty($list->konversi)?'':$list->konversi.'ml';
            $row[] = '<a onclick="editForm('.$list->satuan_id.')" class="btn btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
            <a onclick="deleteData('.$list->satuan_id.')" class="btn btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>';
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);

    }
}
