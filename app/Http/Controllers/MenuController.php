<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Redirect;
use App\MenuModel;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $menu = MenuModel::where('menu_id_parent','=','0')->orderby('urutan','asc')->get();
      return view('admin.menu.index', compact('menu'));
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
      $menu = new MenuModel;
      $menu ->menu_id_parent = $request['menu_id_parent'];
      $menu ->menu_nama = $request['menu_nama'];
      $menu ->menu_link = $request['menu_link'];
      $menu -> save();
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
      $menu = MenuModel::find($id);
      echo json_encode($menu);
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
      $menu = MenuModel::find($id);
      $menu ->menu_id_parent = $request['menu_id_parent'];
      $menu ->menu_nama = $request['menu_nama'];
      $menu ->menu_link = $request['menu_link'];
      $menu -> update();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $menu = MenuModel::find($id);
      $menu -> delete();
    }

    public function listData()
    {
        $menu = MenuModel::orderBy('menu_id', 'DESC')->get();
        $no = 0;
        $data = array();
        foreach ($menu as $list) {

          $menu_id = $list->menu_id_parent;
          if ($menu_id!=0) {
            $nama_menu = MenuModel::where('menu_id', '=', $menu_id)->value('menu_nama');
          }else {
            $nama_menu = "--";
          }


            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $list->menu_nama;
            $row[] = $list->menu_link;
            $row[] = $nama_menu;
            $row[] = '<a onclick="editForm('.$list->menu_id.')" class="btn btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
            <a onclick="deleteData('.$list->menu_id.')" class="btn btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>';
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);

    }

}
