<?php

namespace App\Http\Controllers\Gudang;

use Illuminate\Http\Request;
use App\RefGudang;
use DB;
use Redirect;
use App\Http\Controllers\Controller;
class RefGudangController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
      $RefGudang = RefGudang::all();
      // $satuan = SatuanModel::orderBy('satuan_id', 'DESC')->get();
      return view('admin.gudang.index', compact('RefGudang'));
  }
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    // print_r($request);exit;
    $crud = $request['crud'];
    $a=0;
    $alert=array("","");
    if ($crud =='tambah') {
      $program = new RefGudang;
      $program->nama = $request['nama'];
      $program->alamat = $request['alamat'];
      $program->id_profil = $request['id_profil'];
      $program->status = $request['status'];
      $program->kode = $request['kode'];
      $a = $program-> save();
      // print_r($program);exit;
      $alert=array("Failed to create new data","New data created successfully");
    }elseif ($crud == 'edit') {
      $id = $request['id'];
      $program = RefGudang::find($id);
      $program->nama = $request['nama'];
      $program->alamat = $request['alamat'];
      $program->id_profil = $request['id_profil'];
      $program->status = $request['status'];
      $program->kode   = $request['kode'];
      // print_r($program);exit;
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
    $menu = RefGudang::find($id);
    $menu -> delete();
  }
  public function listdata()
    {
      // $program = RefGudang::orderBy('id', 'DESC')->get();
      $program =DB::select('SELECT
          g.id,
          g.id_profil,
          g.nama,
          g.alamat,
          g.`status`,
          g.kode,
          g.created_at,
          g.updated_at,
          p.nama nama_profil,
          p.jenis_outlet 
        FROM
          ref_gudang AS g
          LEFT JOIN m_profil AS p ON g.id_profil = p.id 
        ORDER BY
          g.id ASC');
      $no = 0;
      $data = array();
      foreach ($program as $list) {
         
          if ($list->status == 1) {
            $status = "Aktif";
          }else{
            $status = "Tidak Aktif";
          }
          $no++;
          $row = array();
          $row[] = $no;
          $row[] = $list->nama_profil;
          $row[] = $list->nama;
          $row[] = $list->alamat;
          $row[] = $status;
          $row[] = $list->kode;
          $row[] = '<a data-id="'.$list->id.'" data-alamat="'.$list->alamat.'" data-nama_profil="'.$list->nama_profil.'" data-nama="'.$list->nama.'" data-kode="'.$list->kode.'"   data-id_profil="'.$list->id_profil.'" id="btn_edit" class="btn btn-xxs  btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
          <a data-id="'.$list->id.'" id="btn_hapus" class="btn btn-danger btn_hapus" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>';
          $data[] = $row;

      }

      $output = array("data" => $data);
      return response()->json($output);

  }
  public function select2profil(Request $request){
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
        $barangs= DB::select("select * from m_profil p WHERE
                LOWER( p.nama ) LIKE '%$search%' ");

        return \Response::json($barangs);
  }
}
