<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Redirect;
use App\SupplierModel;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.supplier.index');
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
      $supplier = new SupplierModel;
      $supplier ->supplier_nama     = $request['supplier_nama'];
      $supplier ->supplier_alamat   = $request['supplier_alamat'];
      $supplier ->supplier_telp     = $request['supplier_telp'];
      $supplier ->supplier_tempo    = $request['supplier_tempo'];
      $supplier -> save();
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
      $supplier = SupplierModel::find($id);
      echo json_encode($supplier);
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
      $supplier = SupplierModel::find($id);
      $supplier ->supplier_nama = $request['supplier_nama'];
      $supplier ->supplier_alamat = $request['supplier_alamat'];
      $supplier ->supplier_telp = $request['supplier_telp'];
      $supplier ->supplier_tempo = $request['supplier_tempo'];
      $supplier -> update();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $supplier = SupplierModel::find($id);
      $supplier -> delete();
    }

    public function listData()
    {
        $supplier = SupplierModel::orderBy('supplier_id', 'DESC')->get();
        $no = 0;
        $data = array();
        foreach ($supplier as $list) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $list->supplier_nama;
            $row[] = $list->supplier_alamat;
            $row[] = $list->supplier_telp;
            $row[] = ($list->supplier_tempo == "" ||$list->supplier_tempo == 0) ?  "0"." Hari":$list->supplier_tempo." Hari";
            $row[] = '<a onclick="editForm('.$list->supplier_id.')" class="btn btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
            <a onclick="deleteData('.$list->supplier_id.')" class="btn btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>';
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);

    }
}
