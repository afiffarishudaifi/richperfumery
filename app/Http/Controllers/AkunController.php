<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Redirect;
use App\AkunModel;
use Alert;

class AkunController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $akun = AkunModel::where('akun_id_parent','=','0')->get();
      return view('admin.akun.index', compact('akun'));
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

      $akun_kode = $request['akun_kode'];

      $data = AkunModel::where('akun_kode','=',$akun_kode)->get();

      if(count($data) > 0) {
        // $response['msg'] = "Kode perkiraan sudah digunakan";
        // echo json_encode($response);
        Alert::success('Kode perkiraan sudah digunakan', 'Gagal')->persistent('Close');
      }else {
        $akun = new AkunModel;
        $akun ->akun_id_parent = $request['akun_id_parent'];
        $akun ->akun_kode = $request['akun_kode'];
        $akun ->akun_nama = $request['akun_nama'];
        $akun ->akun_saldo_normal = $request['akun_saldo_normal'];
        $akun -> save();
      }



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
      $akun = AkunModel::find($id);
      echo json_encode($akun);
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
      $akun_kode = $request['akun_kode'];


      $akun_kode_database = AkunModel::where('akun_id','=',$id)->value('akun_kode');

      if ($akun_kode_database == $akun_kode) {
        $akun = AkunModel::find($id);
        $akun ->akun_id_parent = $request['akun_id_parent'];
        $akun ->akun_kode = $request['akun_kode'];
        $akun ->akun_nama = $request['akun_nama'];
        $akun ->akun_saldo_normal = $request['akun_saldo_normal'];
        $akun -> update();
      }else {
        $data = AkunModel::where('akun_kode','=',$akun_kode)->get();
        if(count($data) > 0) {
          // $response['msg'] = "Kode perkiraan sudah digunakan";
          // echo json_encode($response);
          Alert::success('Kode perkiraan sudah digunakan', 'Gagal !')->persistent('Close');
        }else {
          $akun = AkunModel::find($id);
          $akun ->akun_id_parent = $request['akun_id_parent'];
          $akun ->akun_kode = $request['akun_kode'];
          $akun ->akun_nama = $request['akun_nama'];
          $akun ->akun_saldo_normal = $request['akun_saldo_normal'];
          $akun -> update();
        }
      }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $akun = AkunModel::find($id);
      $akun -> delete();
    }

    public function listData()
    {
        $akun = AkunModel::orderBy('akun_id', 'ASC')->get();
        $no = 0;
        $data = array();
        foreach ($akun as $list) {

          $akun_id = $list->akun_id_parent;
          if ($akun_id!=0) {
            $nama_akun = AkunModel::where('akun_id', '=', $akun_id)->value('akun_nama');
          }else {
            $nama_akun = "--";
          }


            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $list->akun_kode;
            $row[] = $list->akun_nama;
            $row[] = $list->akun_saldo_normal;
            $row[] = $nama_akun;
            $row[] = '<a onclick="editForm('.$list->akun_id.')" class="btn btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
            <a onclick="deleteData('.$list->akun_id.')" class="btn btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>';
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);

    }
}
