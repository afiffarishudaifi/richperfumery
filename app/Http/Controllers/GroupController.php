<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Redirect;
use App\GroupModel;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.group.index');
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
      $group = new GroupModel;
      $group ->group_nama  = $request['group_nama'];
      $group ->group_aktif = ($request['group_aktif']=='on')?'2':'1';
      $group ->save();
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
      $group = GroupModel::find($id);
      echo json_encode($group);
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
      $group = GroupModel::find($id);
      $group ->group_nama  = $request['group_nama'];
      $group ->group_aktif = ($request['group_aktif']=='on')?'2':'1';
      $group ->update();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $group = GroupModel::find($id);
      $group -> delete();
    }

    public function listData()
    {
        $satuan = GroupModel::orderBy('group_id', 'DESC')->get();
        $no = 0;
        $data = array();
        foreach ($satuan as $list) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $list->group_nama;
            $row[] = ($list->group_aktif=='2')?'<span class="label label-success">Aktif</span>':'<span class="label label-danger">Tidak Aktif</span>';
            $row[] = '<a href="'.route('t_user.show',$list->group_id).'" class="btn btn-success" data-toggle="tooltip" data-placement="botttom" title="Setting Menu"><i class="fa fa-gear"></i></a>
            <a onclick="editForm('.$list->group_id.')" class="btn btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
            <a onclick="deleteData('.$list->group_id.')" class="btn btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>';
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);

    }
}
