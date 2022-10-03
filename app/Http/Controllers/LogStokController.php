<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StokModel;
use App\LogStokModel;
use App\BarangModel;

class LogStokController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $barang_id = StokModel::where('stok_id','=',$id)->value('barang_id');
        $barang_nama = BarangModel::where('barang_id','=',$barang_id)->value('barang_nama');
        $barang_kode = BarangModel::where('barang_id','=',$barang_id)->value('barang_kode');
        $log_stok = LogStokModel::where('stok_id','=',$id)->get();
        return view('admin.log_stok.index',compact('barang_nama','barang_kode','log_stok'));
        // dd($barang_nama);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
