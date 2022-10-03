<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use App\UserModel;
use Alert;

class AdminUserController extends Controller
{

  protected $pesan = array(
      'name.required' => 'Isikan Nama Anda',
      'email.required' => 'Isikan Email Anda'
  );

  protected $aturan = array(
      'name' => 'required',
      'email' => 'required'

  );

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = Auth::user()->id;
        $name = UserModel::where('id','=',$id)->value('name');
        $email = UserModel::where('id','=',$id)->value('email');
        $users_email = UserModel::where('id','=',$id)->value('users_email');
        return view('admin.profil.index',compact('name','email','users_email'))->with('success','0');
        // dd($email);
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
      $this->validate($request, $this->aturan, $this->pesan);

      $id = Auth::user()->id;
      $user_model =UserModel::find($id);

      if ($request['password']=="") {
        $user_model->name = $request['name'];
        $user_model->email = $request['email'];
        $user_model->users_email = $request['users_email'];
        $user_model->update();
      }else {
        $user_model->name = $request['name'];
        $user_model->email = $request['email'];
        $user_model->users_email = $request['users_email'];
        $user_model->password = bcrypt($request['password']);
        $user_model->update();
      }

        Alert::success('Profil berhasil diubah', 'Success');
        return redirect('admin_user');
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
