<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Redirect;
use QrCode;
use App\BarangModel;
use App\SatuanModel;
use Auth;
use Intervention\Image\ImageManagerStatic as Image;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['barang'] = BarangModel::where('barang_id_parent','=','0')->get();
        $data['satuan'] = SatuanModel::orderBy('satuan_id', 'DESC')->get();

        $id_profil = Auth::user()->id_profil;
        $group = Auth::user()->group_id;
        $where = "";
        if($group == 5 || $group == 6){
          $where = "WHERE id_profil='$id_profil'";
        }
        $d_gudang = DB::select(base_gudang($where));
        $id_gudang = array();
        foreach($d_gudang as $d){
          $id_gudang[] = $d->id_gudang;
        }
        $where_gudang = "";
        if(sizeof($id_gudang) > 0){
          $gudang = implode(',',$id_gudang);
          $where_gudang = "WHERE g.id IN ($gudang)";
        }
        $data['gudang'] = DB::select("
        SELECT
          g.id,
          g.id_profil,
          g.nama,
          g.alamat,
          g.`status`,
          g.created_at,
          g.updated_at,
          p.jenis_outlet
        FROM
          ref_gudang g
          LEFT JOIN m_profil p ON g.id_profil = p.id $where_gudang
        ");
        return view('admin.barang.index', compact('data'));
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
      $barang = new BarangModel;
      $barang ->barang_kode = $request['barang_kode'];
      $barang ->barang_nama = $request['barang_nama'];
      $barang ->barang_id_parent = $request['barang_id_parent'];
      $barang ->satuan_id = $request['satuan_id'];
      //$barang ->satuan_konversi_id = "7";
      $barang ->barang_status_bahan = $request['barang_status_bahan'];
      $barang ->barang_alias = $request['alias_barang'];
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
      $barang = BarangModel::find($id);
      echo json_encode($barang);
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
      $barang = BarangModel::find($id);
      $barang ->barang_kode = $request['barang_kode'];
      $barang ->barang_nama = $request['barang_nama'];
      $barang ->barang_id_parent = $request['barang_id_parent'];
      $barang ->satuan_id = $request['satuan_id'];
      //$barang ->satuan_konversi_id = "7";
      $barang ->barang_status_bahan = $request['barang_status_bahan'];
      $barang ->barang_alias = $request['alias_barang'];
      $barang->update();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $barang = BarangModel::find($id);
        $barang->delete();
    }

    public function listData()
    {
        // DB::enableQueryLog();
        $barang = BarangModel::leftjoin('tbl_satuan','tbl_satuan.satuan_id','=','tbl_barang.satuan_id')->orderBy('barang_id', 'ASC')->get();
        // dd(DB::getQueryLog());
        $no = 0;
        $data = array();
        $bahan = "";
        foreach ($barang as $list) {

          $barang_id = $list->barang_id_parent;
          if ($barang_id!=0) {
            $nama_barang = BarangModel::where('barang_id', '=', $barang_id)->value('barang_nama');
          }else {
            $nama_barang = "--";
          }

          if ($list->barang_status_bahan=="1") {
            $bahan = "Bahan Baku";
          }elseif ($list->barang_status_bahan=="2") {
            $bahan = "Pendukung";
          }

          $lihat ='<a href="'.route('detail_harga_barang.show',$list->barang_id).'" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="botttom" title="Detail Harga Barang"><i class="fa fa-plus"></i></a>';
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $list->barang_kode;
            $row[] = $list->barang_nama;
            $row[] = $list->barang_alias;
            $row[] = $nama_barang;
            $row[] = $list->satuan_nama;
            $row[] = $bahan;
            $row[] = '<div class="btn-group"><a href="'.route('detail_harga_barang.show',$list->barang_id).'" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="botttom" title="Detail Harga Barang"><i class="fa fa-plus"></i></a>
            <a onclick="editForm('.$list->barang_id.')" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa fa-edit"></i></a>
            <a onclick="barcode('.$list->barang_id.','.$list->satuan_id.')" class="btn btn-xs btn-warning" data-toggle="tooltip" data-placement="botttom" title="QR Code"  style="color:black;"><i class="fa fa-qrcode"></i></a>
            <a onclick="deleteData('.$list->barang_id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);

    }

    public function barcode(Request $request){
      $id = $request->get('id');
      $satuan = $request->get('satuan');
      // DB::enableQueryLog();
      $barang = DB::table('tbl_barang')->where('barang_id',$id)->first();
      $satuan = DB::table('tbl_satuan')->where('satuan_id',$satuan)->first();
      //dd(DB::getQueryLog());
        
        //print_r($data);exit;
        $data = $barang->barang_id."||".$barang->barang_nama."||".$satuan->satuan_id."||".$satuan->satuan_nama;
        $image = \QrCode::format('png')
                         ->merge(public_path('richperfumery2.png'), 0.2, true)
                         ->size(500)->errorCorrection('H')
                         ->generate($data);
      $barcode = base64_encode($image);
      /*define('UPLOAD_DIR', public_path('barcode/'));
      $file = UPLOAD_DIR . uniqid() . '.png';
      $image_resize = Image::make($barcode);              
      $image_resize->resize(120, 120);
      $image_resize->save($file);   */

      return response($barcode)->header('Content-type','image/png');
    }

    public function barcode_detail(Request $request){
      $id = $request->get('id');
      $satuan = $request->get('satuan');
      $gudang = $request->get('gudang');
      $d_query = DB::table('tbl_barang')->where('barang_id',$id)->select(DB::raw('barang_id as id_barang, barang_nama as nama_barang, barang_kode as kode_barang, barang_alias as alias_barang, 
        satuan_id as id_satuan'));
      $data = array();
      if($d_query->count() > 0){
        $d = $d_query->first();
        $data['id_barang'] = $d->id_barang;
        $data['nama_barang'] = $d->nama_barang;
        $data['id_satuan'] = $satuan;
        $data['id_gudang'] = $gudang;
      }
      return response()->json($data);
    }

}
