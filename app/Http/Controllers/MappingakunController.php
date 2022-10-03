<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mapping_akun;
use App\AkunModel;
use App\M_program;
use DB;
use Redirect;

class MappingakunController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
    public function index()
    {
      $menu['akun'] = AkunModel::all();
      // print_r($menu);exit;
      return view('admin.mapping.index', compact('menu'));
    }
    public function dataprogram()
    {
      $data = M_program::All();
      foreach ($data as $key => $value) {
        $output[]=$value;
      }
      echo json_encode($output);
    }

}
