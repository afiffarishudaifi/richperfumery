<?php

namespace App\Http\Controllers\Inventori;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;

class LaporanStokopnameController extends Controller
{
    //
    public function index(){
	    $id_profil = Auth::user()->id_profil;
      	$group = Auth::user()->group_id;
      	$where = "";
	      if($group == 5 || $group == 6){
	        $where = "WHERE id_profil='$id_profil'";
	      }

      	$data['data'] = $this->data(array());
	    $data['gudang'] = DB::select(base_gudang($where));
    	return view('admin.laporan.index_laporan_stokopname')->with('data',$data);
    }

    public function data($data = array()){
    	if($data != null){
    		$store['tanggal_awal'] 	= date('d-m-Y');
    		$store['tanggal_akhir'] = date('d-m-Y');
    	}else{
    		$store['tanggal_awal'] 	= date('d-m-Y');
    		$store['tanggal_akhir'] = date('d-m-Y');
    	}
    	return $store;
    }

    public function cetak(Request $request){
    	require(public_path('fpdf1813\Mc_table.php'));
    	$tanggal_awal = $request->get('tanggal_awal');
    	$tanggal_akhir = $request->get('tanggal_akhir');
    	$gudang  = $request->get('gudang');
    	//DB::enableQueryLog();
    	$data['data'] = DB::table('tbl_stokopname_baru as tsd')->join('tbl_barang as tb','tsd.id_barang','tb.barang_id')->join('ref_gudang as rg','tsd.id_gudang','rg.id')->where('tsd.tanggal','>=',tgl_full($tanggal_awal,'99'))->where('tsd.tanggal','<=',tgl_full($tanggal_akhir,'99'))->where('tsd.id_gudang',$gudang)->select(DB::Raw('tsd.id_stokopname as id_stokopname, tsd.tanggal, tsd.id_barang, tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, CASE WHEN tb.barang_alias IS NULL THEN "" ELSE tb.barang_alias END AS alias_barang, tsd.id_gudang, rg.nama as nama_gudang, sum(tsd.stok) as stok, sum(tsd.fisik) as fisik, tsd.selisih, tsd.id_satuan, tsd.id_log_stok, tsd.keterangan'))->orderBy('tb.barang_nama','ASC')->groupBy('tsd.id_barang','tsd.id_gudang','tsd.id_satuan')->get();
    	//dd(DB::getQueryLog());
    	$gudang = DB::table('ref_gudang')->where('id',$gudang)->select(DB::Raw('id as id_gudang,nama as nama_gudang'))->first();

        $data['tanggal_awal'] 	= tgl_full($tanggal_awal,'');
        $data['tanggal_akhir'] 	= tgl_full($tanggal_akhir,'');
        $data['kasir']			= Auth::user()->name;
        $data['gudang']			= $gudang->nama_gudang;

      $html = view('admin.laporan.cetak_laporan_stokopname')->with('data',$data);
      trigger_log(NULL, "Cetak Laporan Stokopname", 7);
      return response($html)->header('Content-Type', 'application/pdf');
        //print_r($data['data']);
    }


}
