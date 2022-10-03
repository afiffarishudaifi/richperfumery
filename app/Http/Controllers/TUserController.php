<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Redirect;
use App\GroupModel;
use App\TUserModel;
use App\MenuModel;

class TUserController extends Controller
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
        $t_user = new TUserModel;
        $group_id = $request['group_id'];

        DB::table('tbl_t_user')->where('group_id', '=', $group_id)->delete();

        if(!empty($request['menu_id'])) {
        foreach($request['menu_id'] as $menu_id){
            // echo "value : ".$menu_id.'<br/>';
            DB::table('tbl_t_user')->insert(array('group_id' => $group_id, 'menu_id' => $menu_id));

        }
        return Redirect::route('t_user.show',$group_id)->with('error',1);
      }else {
        return Redirect::route('t_user.show',$group_id)->with('error',1);
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
        $menu = MenuModel::orderBy('menu_id_parent','ASC')->get();
        $nama_group = GroupModel::where('group_id','=',$id)->value('group_nama');
        $t_user = TUserModel::join('tbl_menu','tbl_menu.menu_id','=','tbl_t_user.menu_id')->where('tbl_t_user.group_id','=',$id)->get();

        $menu2 = MenuModel::where('menu_id_parent','=','0')->get();

        return view('admin.t_user.menu', compact('menu', 'nama_group','id','t_user','menu2'))->with('error',0);
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
        $group_id = TUserModel::where('t_user_id','=',$id)->value('group_id');
        $t_user = TUserModel::find($id);
        $t_user->delete();
        return Redirect::route('t_user.show',$group_id);
        // dd($group_id);
    }
}
