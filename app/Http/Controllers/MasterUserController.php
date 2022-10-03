<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Redirect;
use App\GroupModel;
use App\MasterUserModel;
use Auth;

class MasterUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $session = Auth::user();
        $group = GroupModel::when($session,function($query, $keyword){
                if($keyword->group_id != 1 || $keyword->group_id != 6){  
                  if($keyword->id_profil != 0 || $keyword->id_profil != '' ){
                    if($keyword->group_id == 5){
                      $query->whereRaw("group_id = ?",[8]);
                      $query->orWhereRaw("group_id = ?",[$keyword->group_id]);
                    }
                  }
                }
              })->get();
        $profil = DB::table('m_profil')
                ->whereRaw('id = ?',[$session->id_profil]);
        if($profil->count() > 0){
          $namaprofil = $profil->first()->nama;
        }else{
          $namaprofil = '';
        }
        return view('admin.master_user.index', compact('group','session','namaprofil'));
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
      $master_user = new MasterUserModel;
      $master_user ->id_profil = $request['id_profil'];
      $master_user ->group_id = $request['group_id'];
      $master_user ->name = $request['name'];
      $master_user ->email = $request['email'];
      $master_user ->users_email = $request['users_email'];
      $master_user ->password = bcrypt($request['password']);
      $master_user -> save();
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
    /*public function edit($id)
    {
      $master_user = DB::select("SELECT
        users.id,
        users.group_id,
        users.id_profil,
        users.`name`,
        users.email,
        users.`password`,
        users.users_email,
        m_profil.nama,
        m_profil.id AS id_profil1,
        m_profil.jenis_outlet
        FROM
        users
        LEFT JOIN m_profil ON m_profil.id = users.id_profil where users.id='$id'
        ORDER BY
        users.id DESC");
      echo json_encode($master_user[0]);
    }*/
    
    public function edit($id){ // TODO
      $data = DB::table('users as u')
            ->leftjoin('m_profil as mp','mp.id','u.id_profil')
            ->whereRaw('u.id = ?',[$id])
            ->selectRaw('u.*, mp.nama, mp.id as id_profil, mp.jenis_outlet')
            ->orderBy('u.id','desc')
            ->first();
      return response()->json($data); 
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
      $master_user = MasterUserModel::find($id);

      if($request['password'] != '' || $request['password'] != null) {
        $master_user ->password = bcrypt($request['password']);
      }

      $master_user ->id_profil = $request['id_profil'];
      $master_user ->group_id = $request['group_id'];
      $master_user ->name = $request['name'];
      $master_user ->email = $request['email'];
      $master_user ->users_email = $request['users_email'];
      $master_user -> update();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      $master_user = MasterUserModel::find($id);
      $master_user -> delete();
    }

    public function listData()
    {
      // DB::enableQueryLog();
        /*$satuan = DB::select("SELECT
        users.id,
        users.group_id,
        users.id_profil,
        users.`name`,
        users.email,
        users.`password`,
        users.users_email,
        users.remember_token,
        users.created_at,
        users.updated_at,
        tbl_group.group_nama,
        m_profil.nama nama_profil,
        m_profil.id id_profil1,
        m_profil.jenis_outlet
        FROM
        users
        LEFT JOIN tbl_group ON tbl_group.group_id = users.group_id
        LEFT JOIN m_profil ON m_profil.id = users.id_profil
        ORDER BY
        users.id DESC");
        $no = 0;*/
        $session = Auth::user();
        $no = 0;
        $satuan = DB::table('users as u')
                ->leftjoin('tbl_group as tb','tb.group_id','u.group_id')
                ->leftjoin('m_profil as mp','mp.id','u.id_profil')
                ->when($session, function($query, $keyword){
                  if($keyword->group_id == 5){
                    if($keyword->id_profil != 0 || $keyword->id_profil != ''){
                      $query->whereRaw('mp.id = ?',[$keyword->id_profil]);
                    }
                  }
                })
                ->selectRaw("u.*,tb.group_nama, mp.nama as nama_profil, mp.id as id_profil, mp.jenis_outlet")
                ->orderBy('u.id','desc')->get();
        // dd( DB::getQueryLog());
        // print_r($satuan);exit;
        $data = array();
        foreach ($satuan as $list) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $list->nama_profil;
            $row[] = $list->name;
            $row[] = $list->group_nama;
            $row[] = $list->users_email;
            $row[] = $list->email;
            $row[] = "*************";
            $row[] = '<a onclick="editForm('.$list->id.')" data-nama_profil="'.$list->nama_profil.'" class="btn btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
            <a onclick="deleteData('.$list->id.')" class="btn btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a>';
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);

    }
    
    public function get_profil(Request $request){
      // TODO
      $term = trim($request->q);
      $search = strtolower($term);
      $session = Auth::user();
    //   $cek = 0;
    //   if($session->group_id <> 1 || $session->group_id <> 6){
    //       $cek = 1;
    //   }
    //   print_r(json_encode($cek));
    //   print_r(json_encode($session->group_id));exit();
      $data = DB::table('m_profil')
            ->when($session, function($query, $keyword){
              if($keyword->group_id == 5 || $keyword->group_id == 8){
                  $id_profil = $keyword->id_profil;
                    if($id_profil != 0 || $id_profil != ''){
                      $query->whereRaw("id = ?",[$id_profil]);
                    }
              }
            })
            ->when($search, function($query_sub, $keyword){
              if($keyword != ''){
                $query_sub->where(function($query) use($keyword){
                  $query->whereRaw('LOWER(nama) LIKE ?',[$keyword]);
                });
              }
            })
            // ->toSql();
            ->get();
      return response()->json($data);
    }
}
