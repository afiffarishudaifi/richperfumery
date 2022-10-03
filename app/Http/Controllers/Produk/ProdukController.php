<?php

namespace App\Http\Controllers\Produk;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use DB;
use Redirect;


class ProdukController extends Controller
{
    //
    function index(){
        // return view('admin.produk.detail', compact('gudang'));
        return view('admin.produk.index');
    }
    function tambah(){
        return view('admin.produk.produkadd');
    }
    public function listData(){
      $produk = DB::select('select * from m_produk');
      $no = 0;
      $data = array();
      foreach ($produk as $list) {
        $id = base64_encode($list->id);
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $list->kode_produk;
            $row[] = $list->nama;
            $row[] = 'Rp. '.number_format($list->harga);
            $row[] = '<div class="btn-group"><a href="produk_edit?id='.$id.'" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i></a> <a onclick="deleteData('.$list->id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';

           $data[] = $row;
      }
       $output = array("data" => $data);
       return response()->json($output);
    }
    public function edit(Request $request){
        $id = base64_decode($request['id']);
        // dd($id);
        $data['m_produk'] = DB::select("select * from m_produk where id= '$id'");
        // dd($data['m_produk']);
        $data['m_detail_produk'] = DB::table("m_detail_produk")->where('id_produk',$id)->get()->toJson();
        // $result = new Collection($data['m_detail_produk']);
       
        // print_r($data['m_detail_produk']);exit;
        // $data['detail_produk'] = json_encode($m_detail_produk);
        // print_r($m_detail_produk);exit;
        return view('admin.produk.produkedit', compact('data'));
    }
    public function getedit_data(Request $request){
        // $id = 3;
        $id = $request->get('id');
        $_query = DB::select("SELECT
            d.id,
            d.id_produk,
            d.id_barang,
            b.barang_kode,
            b.barang_nama,
            d.jumlah,
            b.barang_status_bahan,
            b.satuan_id,
            s.satuan_satuan
            FROM
            m_detail_produk AS d
            LEFT JOIN tbl_barang AS b ON d.id_barang = b.barang_id
            LEFT JOIN tbl_satuan AS s ON b.satuan_id = s.satuan_id where id_produk='$id'");
        // print_r(count($_query));exit;
        if (count($_query) > 0 ) {
            foreach ($_query as $key => $value) {
                $arr[] = array('id' => $value->id,'id_barang'=>$value->id_barang,
                        'barang_kode'=>$value->barang_kode,'barang_nama'=>$value->barang_nama,'satuan'=>$value->satuan_satuan,'jumlah'=>$value->jumlah);
            }
        }else{
            $arr = array();
        }
        return response()->json($arr);
        // print_r($arr);exit;
    }
    function select2barang(Request $request){
    $term = trim($request->q);
    // $gudang = $request->gudang;

        if (empty($term)) {
            return \Response::json([]);
        }
        // dd($term);
        // $barangs =BarangModel::query()
        //         ->where('barang_kode', 'LIKE', "%{$term}%")                
        //         ->get();
        $search = strtolower($term);
        // print_r($search );exit;
        $barangs= DB::select("SELECT
            b.barang_id,
            b.satuan_id,
            b.barang_kode,
            b.barang_nama,
            b.barang_id_parent,
            b.barang_status_bahan,
            s.satuan_nama,
            s.satuan_satuan
            FROM
            tbl_barang AS b
            LEFT JOIN tbl_satuan AS s ON b.satuan_id = s.satuan_id
            WHERE
                LOWER( b.barang_kode ) LIKE '%$search%' or  LOWER( b.barang_nama ) LIKE '%$search%'
            ORDER BY
                b.barang_kode ASC ");

        // $formatted_tags = [];

        // foreach ($barangs as $barang) {
        //     $formatted_tags[] = ['id' => $barang->barang_id,'satuan'=>$barang->satuan_nama, 'text' => $barang->barang_kode];
        // }

        return \Response::json($barangs);
  }
    public function simpan(Request $request){
        $crud = $request['crud'];
        // print_r($request['list_kode_id'][0]);exit;
        $data['kode_produk'] = $request['kode'];
        $data['nama'] = $request['nama'];
        $data['harga'] = $request['jumlah_komposisi'];
        $tipe = $request['tipe'];
        if ($tipe != '') $data['id_type_ukuran'] = $tipe;
        // print_r($request['list_kode']);exit();
        if ($crud=='tambah') {            
            // DB::table('m_produk')->insert($data);
           $id = DB::table('m_produk')->insertGetId($data);
           if ($request['list_kode']) {
              foreach ($request['list_kode'] as $key => $value) {
                $tes['id_barang'] = $request['list_kode_id'][$key];
                $tes['jumlah'] = $request['list_jumlah'][$key];
                $tes['id_produk'] = $id;
                DB::table('m_detail_produk')->insert($tes);
            }
           }
            
            return redirect('produk');
        }else if($crud=='edit'){
            $id = $request['id'];
             DB::table('m_produk')->where('id',$id)->update($data);
             DB::table('m_detail_produk')->where(array('id_produk' => $id))->delete();
             if ($request['list_kode']) {                
                 foreach ($request['list_kode'] as $key => $value) {
                     $tes['id_barang'] = $request['list_kode_id'][$key];
                     $tes['jumlah'] = $request['list_jumlah'][$key];
                     $tes['id_produk'] = $id;
                     DB::table('m_detail_produk')->insert($tes);
                 } 
             }
            return redirect('produk');
        }
    }

    public function hapus(Request $request){
        $id = $request->get('id');
        DB::table('m_produk')->where('id',$id)->delete();
        DB::table('m_detail_produk')->where(array('id_produk'=>$id))->delete();
    }
}
