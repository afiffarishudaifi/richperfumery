<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Redirect;
use App\DetailHargaBarangModel;
use App\BarangModel;

class DetailHargaBarangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
      $barang = new DetailHargaBarangModel;
      $barang ->barang_id = $request['barang_id'];
      $barang ->detail_harga_barang_tanggal = $request['detail_harga_barang_tanggal'];
      $barang ->detail_harga_barang_harga_jual = $request['detail_harga_barang_harga_jual'];
      $barang -> save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $barang_id = $id;
        $nama_barang = BarangModel::where('barang_id','=',$barang_id)->value('barang_nama');
        return view('admin.detail_harga_barang.index', compact('barang_id','nama_barang'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $detail_harga_barang = DetailHargaBarangModel::find($id);
      echo json_encode($detail_harga_barang);
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
      $barang = DetailHargaBarangModel::find($id);
      $barang ->barang_id = $request['barang_id'];
      $barang ->detail_harga_barang_tanggal = $request['detail_harga_barang_tanggal'];
      $barang ->detail_harga_barang_harga_jual = $request['detail_harga_barang_harga_jual'];
      $barang -> update();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $barang = DetailHargaBarangModel::find($id);
      $barang -> delete();
    }

    public function listData($barang_id)
    {
        $menu = DetailHargaBarangModel::where('barang_id','=',$barang_id)->orderBy('detail_harga_barang_id', 'DESC')->get();
        $no = 0;
        $data = array();
        foreach ($menu as $list) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = tanggal_indonesia($list->detail_harga_barang_tanggal,false);
            $row[] = "<div align='right'>"."Rp. ".number_format($list->detail_harga_barang_harga_jual,0,',','.')."</div>";
            $row[] = '<a onclick="editForm('.$list->detail_harga_barang_id.')" class="btn btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
            <a onclick="deleteData('.$list->detail_harga_barang_id.')" class="btn btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>';
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);

    }

}
