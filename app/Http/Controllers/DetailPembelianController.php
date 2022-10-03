<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DetailPembelianModel;
use DB;
use App\PembelianModel;
use App\SupplierModel;
use App\LogStokModel;
use App\StokModel;

class DetailPembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $id_pembelian = PembelianModel::max('id_pembelian');
      $id_pembelian++;

      $supplier = SupplierModel::orderBy('supplier_nama','DESC')->get();
      $barang = LogStokModel::select('barang_nama','tbl_barang.barang_id')->join('tbl_stok','tbl_stok.stok_id','tbl_log_stok.stok_id')
                ->join('tbl_barang','tbl_stok.barang_id','tbl_barang.barang_id')->get();
      return view('admin.detail_pembelian.index',compact('supplier','barang','id_pembelian'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      // $id_pembelian = DB::table('tbl_pembelian')->latest('pembelian_id')->first();

        $detail_pembelian = new DetailPembelianModel;
        $detail_pembelian->pembelian_id = $request['id_pembelian'];
        $detail_pembelian->barang_id = $request['barang_id'];
        $detail_pembelian->pembelian_detail_qty = $request['pembelian_detail_qty'];
        $detail_pembelian->pembelian_detail_harga = $request['pembelian_detail_harga'];
        $detail_pembelian->pembelian_detail_diskon = $request['pembelian_detail_diskon'];
        $detail_pembelian->pembelian_detail_jumlah = $request['pembelian_detail_jumlah'];
        $detail_pembelian->save();

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
      $detail_pembelian = DetailPembelianModel::find($id);
      echo json_encode($detail_pembelian);
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
      $detail_pembelian =  DetailPembelianModel::find($id);
      $detail_pembelian->pembelian_id = $request['id_pembelian'];
      $detail_pembelian->barang_id = $request['barang_id'];
      $detail_pembelian->pembelian_detail_qty = $request['pembelian_detail_qty'];
      $detail_pembelian->pembelian_detail_harga = $request['pembelian_detail_harga'];
      $detail_pembelian->pembelian_detail_diskon = $request['pembelian_detail_diskon'];
      $detail_pembelian->pembelian_detail_jumlah = $request['pembelian_detail_jumlah'];
      $detail_pembelian->update();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $detail_pembelian = DetailPembelianModel::find($id);
      $detail_pembelian -> delete();
    }

    public function listData()
    {
      $id_pembelian = PembelianModel::max('pembelian_id');
      $id_pembelian++;
      $jumlah = DetailPembelianModel::where('pembelian_id',$id_pembelian)->sum('pembelian_detail_jumlah');

        $detail_pembelian = DetailPembelianModel::select('barang_nama','pembelian_detail_harga','pembelian_detail_qty','pembelian_detail_id','pembelian_detail_jumlah')
        ->where('pembelian_id',$id_pembelian)
        ->join('tbl_barang','tbl_pembelian_detail.barang_id','=','tbl_barang.barang_id')
        ->orderBy('tbl_pembelian_detail.created_at', 'DESC')->get();
        $no = 0;
        $data = array();
        foreach ($detail_pembelian as $list) {

            $no++;
            $row = array();
            $row[] = $list->barang_nama;
            $row[] = number_format($list->pembelian_detail_harga,0,',','.');
            $row[] = $list->pembelian_detail_qty;
            $row[] = number_format($list->pembelian_detail_jumlah,0,',','.');
            // $row[] = '<input type="text" id="jumlahCount" name="jumlahCount" value="'.$jumlah.'" /> ';
            // $row[] = '<p id="jumlahCount">"'.$jumlah.'"</p>';
            $row[] = '<a onclick="editForm('.$list->pembelian_detail_id.')" class="btn btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
            <a onclick="deleteData('.$list->pembelian_detail_id.')" class="btn btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>';
            $data[] = $row;

        }

        // $output = array("data" => $data);
        $output = array("data" => $data,"jmlh" => $jumlah);
        // return response()->json($output);
        return $output;

    }

    public function getDataBarang($id)
    {
      $id_stok =StokModel::where('barang_id',$id)->value('stok_id');
      $barang = LogStokModel::where('tbl_log_stok.stok_id',$id_stok)->get()->last();

      return $barang;
    }

}
