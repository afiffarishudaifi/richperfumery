<?php

namespace App\Http\Controllers\kasir;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

class LaporanKasirController extends Controller
{
    //
    public function index(){
      $html = public_path("/Detail_PenjualanPerPelanggan_periode_01_09_2019_s_d_03_10_2019.pdf");
      return response()->file($html);

    }
}
