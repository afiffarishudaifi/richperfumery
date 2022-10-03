<?php

namespace App\Http\Controllers\Redeem;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Illuminate\Support\Collection;
use Jenssegers\Agent\Agent;
use App\KasirModel;
use App\SupplierModel;
use App\PelanggahModel;
date_default_timezone_set('Asia/Jakarta');
class item{
  private $name;
  private $qty;
  private $price;
  private $subtotal;
  private $dollarSign;
  public function __construct($name = '', $qty = '', $price = '', $subtotal = '', $dollarSign = false){
    $this -> name = $name;
    $this -> qty = $qty;
    $this -> price = $price;
    $this -> subtotal = $subtotal;
    $this -> dollarSign = $dollarSign;
  }
  public function __toString(){
    $rightCols = 8;
    $leftCols = 50;
    if ($this -> dollarSign) {
      $leftCols = $leftCols / 2 - $rightCols / 2;
    }
    $left = str_pad($this -> name.''.$this -> qty.''.$this -> price, $leftCols) ;
    $sign = ($this -> dollarSign ? '' : '');
    $right = str_pad($sign . $this -> subtotal, $rightCols, ' ', STR_PAD_LEFT);
    return "$left$right\n";
  }
}

class RedeemController extends Controller
{
    //
    public function __construct(){
      $this->agent = new Agent();
    }

    public function index(){
    	$id_group = Auth::user()->group_id;
	    $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();
	    $tombol_create = tombol_create(url('redeem_tambah'),$group_where->group_aktif,2);
	    if ($this->agent->isMobile()) {
	      return view('admin_mobile.redeem.index',compact('tombol_create'));
	    }else {
	      return view('admin.redeem.index',compact('tombol_create'));
	    }
    	return view('admin.redeem.index');
    }

    public function create(){
      $id_profil = Auth::user()->id_profil;
      $group = Auth::user()->group_id;
      $where = "";
      $where_pelanggan_member = "WHERE (mp.status != 2 OR mp.status IS NULL )  AND mp.status_aktif = 1 AND mp.tanggal_akhir >= DATE(NOW())";
      $where_pelanggan = "WHERE mp.status = 2 AND mp.tanggal_akhir >= DATE(NOW()) AND mp.status_aktif = 1 AND mp.id_gudang = '9'";
      if($group == 5 || $group == 6){
        $where = "WHERE id_profil='$id_profil'";
      }

      $d_gudang = DB::select(base_gudang($where));
        $id_gudang = array();
        foreach($d_gudang as $d){
          $id_gudang[] = $d->id_gudang;
        }
        $where_gudang = "";
        if(sizeof($id_gudang) > 0){
          $gudang = implode(',',$id_gudang);
          $where_gudang = "WHERE rf.id IN ($gudang)";
          /*$where_pelanggan_member = "WHERE rf.id IN ($gudang) AND (mp.status != 2 OR mp.status IS NULL ) AND mp.status_aktif = 1 AND mp.tanggal_akhir >= DATE(NOW())";*/
          $where_pelanggan_member = "WHERE rf.id IN ($gudang) AND (mp.status != 2 OR mp.status IS NULL ) AND mp.status_aktif = 1 AND mp.tanggal_akhir >= DATE(NOW())";
          $where_pelanggan = "WHERE mp.status = 2 AND mp.tanggal_akhir >= DATE(NOW()) AND mp.status_aktif = 1";
        }

      $data['data']       = $this->data(array());
      $data['satuan']     = DB::table('tbl_satuan')->orderBy('satuan_nama','asc')->get();
      $data['carabayar']  = \Config::get('constants.carabayar');
      $data['no_auto']    = $this->noauto();
      /*$data['pelanggan']  = DB::SELECT("SELECT * FROM (
      select mp.id as id, CASE WHEN mp.telp IS NULL THEN mp.nama ELSE CONCAT(mp.nama,' (',mp.telp,')') END as nama, rf.nama as nama_gudang, mp.status as status_member from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id 
      $where_pelanggan
      UNION ALL
      select mp.id as id, CASE WHEN mp.telp IS NULL THEN mp.nama ELSE CONCAT(mp.nama,' (',mp.telp,')') END as nama, rf.nama as nama_gudang, mp.status as status_member from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id 
      $where_pelanggan_member
      ) as a");*/
      /*$data['pelanggan']  = DB::SELECT("SELECT * FROM (
      select mp.id as id, CASE WHEN mp.telp IS NULL THEN mp.nama ELSE CONCAT(mp.nama,' (',mp.telp,')') END as nama, 
      rf.nama as nama_gudang, mp.status as status_member from m_pelanggan as mp 
      LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id
      WHERE rf.id IN (9) AND mp.status = 2 AND mp.status_aktif = 1 AND mp.tanggal_akhir >= DATE(NOW())
      ) as a");*/
      /*$data['pelanggan']  = DB::SELECT("SELECT * FROM (
      select mp.id as id, CASE WHEN mp.telp IS NULL THEN mp.nama ELSE CONCAT(mp.nama,' (',mp.telp,')') END as nama, rf.nama as nama_gudang, mp.status as status_member from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id 
      $where_pelanggan
      UNION ALL
      select mp.id as id, CASE WHEN mp.telp IS NULL THEN mp.nama ELSE CONCAT(mp.nama,' (',mp.telp,')') END as nama, rf.nama as nama_gudang, mp.status as status_member from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id 
      $where_pelanggan_member
      ) as a");*/
      $data['pelanggan']  = DB::SELECT("SELECT * FROM (
      select mp.id as id, CASE WHEN mp.telp IS NULL THEN mp.nama ELSE CONCAT(mp.nama,' (',mp.telp,')') END as nama, rf.nama as nama_gudang, mp.status as status_member from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id 
      $where_pelanggan
      ) as a");
      $data['gudang']     = DB::select(base_gudang($where));
      $data['pembayaran'] = DB::table('m_metode')->where('status','2')->orderby('urutan','asc')->get();
      // dd($data['pelanggan']);
      if ($this->agent->isMobile()) {
        return view('admin_mobile.redeem.create')->with('data',$data);
      }else {
        return view('admin.redeem.create')->with('data',$data);
      }

    }

    public function data($data = array()){
      if($data != null){
        $store['id_kasir']        = $data->id_kasir;
        $store['tanggal']         = tgl_full($data->tanggal,'');
        $store['tanggal_faktur']  = tgl_full($data->tanggal_faktur,'');
        $store['tanggal_tempo']   = tgl_full($data->tanggal_tempo,'');
        $store['id_pelanggan']    = $data->id_pelanggan;
        $store['nama_pelanggan']  = $data->nama_pelanggan;
        $store['alamat_pelanggan']= $data->alamat_pelanggan;
        $store['telp_pelanggan']  = $data->telp_pelanggan;
        $store['nomor']           = $data->no_faktur;
        $store['uang_muka']       = $data->uang_muka;
        $store['td_ongkir']       = $data->ongkos_kirim;
        $store['td_potongan']     = $data->total_potongan;
        $store['td_tagihan']      = $data->total_tagihan;
        $store['td_subtotal']     = $data->total_subtotal;
        $store['carabayar']       = $data->carabayar;
        $store['metodebayar']     = $data->metodebayar;
        $store['metodebayar2']    = $data->metodebayar2;
        $store['total_metodebayar']  = $data->total_metodebayar;
        $store['total_metodebayar2'] = $data->total_metodebayar2;
        $store['keterangan']      = $data->keterangan;
        //$store['paper']           = $data->paper;
        $store['id_gudang']       = $data->id_gudang;
        $store['nama_gudang']     = $data->nama_gudang;
        // $store['poin']            = get_poin($data->id_kasir,$data->id_pelanggan);
        $store['poin']            = '0';
        $store['poin_transaksi']  = '0';
      }else{
        $store['id_kasir']    = "";
        $store['tanggal']         = "";
        $store['tanggal_faktur']  = "";
        $store['tanggal_tempo']   = "";
        $store['id_pelanggan']    = "";
        $store['nama_pelanggan']  = "";
        $store['alamat_pelanggan']= "";
        $store['telp_pelanggan']  = "";
        $store['nomor']           = $this->noauto();
        $store['uang_muka']       = "Rp 0";
        $store['td_ongkir']       = "";
        $store['td_potongan']     = "";
        $store['td_tagihan']      = "";
        $store['td_subtotal']     = "";
        $store['carabayar']       = "";
        $store['metodebayar']     = "";
        $store['metodebayar2']     = "";
        $store['total_metodebayar']  = "";
        $store['total_metodebayar2'] = "";
        $store['keterangan']      = "";
        //$store['paper']           = '1';
        $store['id_gudang']       = "";
        $store['nama_gudang']     = "";
        $store['poin']            = "0";
        $store['poin_transaksi']  = "0";
      }

      return $store;
    }

    private function noauto(){
        $id_profil = Auth::user()->id_profil;
        $group = Auth::user()->group_id;
        $where = "";
        if($id_profil != ''){
          $where = "WHERE id_profil='$id_profil'";
        }

        $d_gudang = DB::select(base_gudang($where));
        $id_gudang = array();
        $a_kode = array();
        foreach($d_gudang as $d){
          $id_gudang[] = $d->id_gudang;
          $a_kode[] = $d->kode;
        }
        $where_gudang = "";
        $where_kode = "";
        if(sizeof($id_gudang) > 0){
          $gudang = implode(',',$id_gudang);
          $d_kode = implode(',',$a_kode);
          $where_gudang = "WHERE rf.id IN ($gudang)";
          $where_kode = $d_kode;
        }

        $no_faktur = $where_kode.".";
        if(sizeof($id_gudang) > 1){
          $kode_gudang = DB::table('ref_gudang')->where('id','=',8)->orderBy('id','ASC')->first(); 
          $no_faktur = $kode_gudang->kode.".";
        }

        $nama = $no_faktur.date('dmY');
        $d_barang = DB::table('tbl_kasir')->where('no_faktur', 'like', $nama.'%')->orderBy('no_faktur', 'desc');
        if($d_barang->get()->count() > 0){
            $kode = $d_barang->first()->no_faktur;
        }else{
            $kode = 0;
        }
        $kode_kat = strlen($nama);
        $kode = substr($kode, ($kode_kat));
        $kode = sprintf('%04d', $kode+1);
        $kode = $nama.$kode;
        
        return $kode;
    }

    public function get_nota(Request $request){
        $tanggal = date('dmY',strtotime($request['tanggal']));
        $id_profil = Auth::user()->id_profil;
        $group = Auth::user()->group_id;

        $d_gudang       = $request->get('gudang');
        $gudang_kode    = DB::table('ref_gudang')->where('id','=',$d_gudang)->first(); 
        $no_faktur      = $gudang_kode->kode.".";

        $nama = $no_faktur.$tanggal;
        $d_barang = DB::table('tbl_kasir')->where('no_faktur', 'like', $nama.'%')->orderBy('no_faktur', 'desc');
        if($d_barang->get()->count() > 0){
            $kode = $d_barang->first()->no_faktur;
        }else{
            $kode = 0;
        }

        $kode_kat = strlen($nama);
        $kode = substr($kode, ($kode_kat));
        $kode = sprintf('%04d', $kode+1);
        $kode = $nama.$kode;
        return response()->json($kode);
    }


    public function get_produk(Request $request){
	    $term = trim($request->q);
  	    if (empty($term)) {
  	        return \Response::json([]);
  	    }
		  $search = strtolower($term);
	    $tanggal_now = date('Y-m-d');
	    $barangs = DB::SELECT("SELECT mp.id as produk_id,
	          mp.kode_produk as produk_kode,
	          mp.nama as produk_nama,
	          mp.harga as produk_harga, 
	          CASE WHEN mp2.poin IS NULL THEN 0 ELSE mp2.poin END AS poin, '' AS produk_gambar FROM m_produk as mp
	      LEFT JOIN (
	      SELECT id_produk, tanggal, poin FROM (
	        SELECT * FROM m_produkpoin 
	        where kategori = 2 AND tanggal = '$tanggal_now' 
	        group by id_produk, id, kategori, hari, tanggal, poin, created_at, updated_at 
	        UNION ALL
	        SELECT * FROM m_produkpoin 
	        where kategori = 1 
	        group by id_produk, id, kategori, hari, tanggal, poin, created_at, updated_at
          UNION ALL
          SELECT * FROM m_produkpoin
          where kategori = 3 
          group by id_produk, id, kategori, hari, tanggal, poin, created_at, updated_at
	      ) as mpo group by id_produk, mpo.tanggal, mpo.poin order by mpo.id DESC
	    ) as mp2 ON mp2.id_produk=mp.id
	    WHERE mp.nama LIKE '%$search%' AND mp2.poin > 0
	    group by mp.id, mp.kode_produk, mp.nama, mp.harga, mp.created_at, mp.updated_at, mp2.poin");
		
		  return \Response::json($barangs);
    }

    public function detail($id){
    	$id_profil = Auth::user()->id_profil;
	    $group = Auth::user()->group_id;
      $where_pelanggan = "WHERE mp.status = 2 AND mp.tanggal_akhir >= DATE(NOW()) AND mp.status_aktif = 1 AND mp.id_gudang='9'";
	    $where = "";
	    if($group == 5 || $group == 6){
	      $where = "WHERE id_profil='$id_profil'";
	    }

	    $d_gudang = DB::select(base_gudang($where));
	    $id_gudang = array();
	    foreach($d_gudang as $d){
	      $id_gudang[] = $d->id_gudang;
	    }
	    $where_gudang = "";
	    if(sizeof($id_gudang) > 0){
	      $gudang = implode(',',$id_gudang);
	      $where_gudang = "WHERE rf.id IN ($gudang)";
	    }

	    $d_data = DB::table('tbl_kasir as tp')->leftjoin('m_pelanggan as mp','tp.id_pelanggan','mp.id')->join('ref_gudang as rg','tp.id_gudang','rg.id')->select('tp.*','mp.nama as nama_pelanggan','mp.alamat as alamat_pelanggan','mp.telp as telp_pelanggan','rg.nama as nama_gudang')->where('id_kasir',$id)->first();
	    $data['data']         = $this->data($d_data);
	    $data['satuan']       = DB::table('tbl_satuan')->orderBy('satuan_nama','asc')->get();
	    $data['carabayar']    = \Config::get('constants.carabayar');
	    $data['no_auto']      = "";
	    $data['pelanggan']    = DB::select("select mp.*, rf.nama as nama_gudang from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id $where_gudang");
	    $data['gudang']       = DB::select(base_gudang($where));
	    // $data['pembayaran']   = DB::table('m_metode')->orderby('urutan','asc')->get();
      $data['pembayaran'] = DB::table('m_metode')->where('status','2')->orderby('urutan','asc')->get();
	    if ($this->agent->isMobile()) {
	        // code...
	        return view('admin_mobile.redeem.detail')->with('data',$data);
	    }else {
	        // code...
	        return view('admin.redeem.detail')->with('data',$data);
	    }
    }

    public function edit($id){
    	$id_profil = Auth::user()->id_profil;
      	$group = Auth::user()->group_id;
        $where_pelanggan = "WHERE mp.status = 2 AND mp.tanggal_akhir >= DATE(NOW()) AND mp.status_aktif = 1";
      	$where = "";
      	if($group == 5 || $group == 6){
      	  $where = "WHERE id_profil='$id_profil'";
      	}

      	$d_gudang = DB::select(base_gudang($where));
        $id_gudang = array();
        foreach($d_gudang as $d){
          $id_gudang[] = $d->id_gudang;
        }
        $where_gudang = "";
        if(sizeof($id_gudang) > 0){
          $gudang = implode(',',$id_gudang);
          $where_gudang = "WHERE rf.id IN ($gudang)";
        }

      	$d_data = DB::table('tbl_kasir as tp')->leftjoin('m_pelanggan as mp','tp.id_pelanggan','mp.id')->join('ref_gudang as rg','tp.id_gudang','rg.id')->select('tp.*','mp.nama as nama_pelanggan','mp.alamat as alamat_pelanggan','mp.telp as telp_pelanggan','rg.nama as nama_gudang')->where('id_kasir',$id)->first();
      	$data['data']         = $this->data($d_data);
      	$data['satuan']       = DB::table('tbl_satuan')->orderBy('satuan_nama','asc')->get();
      	$data['carabayar']    = \Config::get('constants.carabayar');
      	$data['no_auto']      = "";
      	/*$data['pelanggan']  = DB::select("select mp.id, CASE WHEN mp.telp IS NULL THEN mp.nama ELSE CONCAT(mp.nama,' (',mp.telp,')') END as nama, rf.nama as nama_gudang from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id $where_gudang");*/
        $data['pelanggan']  = DB::SELECT("SELECT * FROM (
        select mp.id as id, CASE WHEN mp.telp IS NULL THEN mp.nama ELSE CONCAT(mp.nama,' (',mp.telp,')') END as nama, rf.nama as nama_gudang, mp.status as status_member from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id 
        $where_pelanggan
        ) as a");
      	$data['gudang']       = DB::select(base_gudang($where));
      	// $data['pembayaran']   = DB::table('m_metode')->orderby('urutan','asc')->get();
        $data['pembayaran'] = DB::table('m_metode')->where('status','2')->orderby('urutan','asc')->get();
      	if ($this->agent->isMobile()) {
        // code...
        	return view('admin_mobile.redeem.create')->with('data',$data);
      	}else {
        // code...
        	return view('admin.redeem.create')->with('data',$data);
      	}
    }

    public function get_data(Request $request){
      $tanggal_start 	= '';
      $tanggal_end 		= '';
      if(isset($request->tanggal)){
      $tanggalrange 	= explode('s.d.',$request->tanggal);
      $tanggal_start  	= tgl_full($tanggalrange[0],99);
      $tanggal_end    	= tgl_full($tanggalrange[1],99);
      }

      $id_profil 	= Auth::user()->id_profil;
      $group 		= Auth::user()->group_id;
      $id_user 		= Auth::user()->id;
      $where = "";
      if($group == 5 || $group == 6){
        $where = "WHERE id_profil='$id_profil'";
      }
      $d_gudang = DB::select(base_gudang($where));
      $id_gudang = array();
      foreach($d_gudang as $d){
        $id_gudang[] = $d->id_gudang; 
      }
      $where_gudang = "";
      if(sizeof($id_gudang) > 0){
        $gudang = implode(',',$id_gudang);
      }

      $where_data[] = "(tk.jenis_transaksi=3 AND tk.metodebayar=10)";
      if($gudang != ''){
      	$where_data[] = "tk.id_gudang IN ($gudang)";
      }
      if($tanggal_start != ''){
      	$where_data[] = "tk.tanggal_faktur >= '$tanggal_start'";
      }
      if($tanggal_end != ''){
      	$where_data[] = "tk.tanggal_faktur <= '$tanggal_end'";
      }
      $add_where = " WHERE ".implode(" AND ",$where_data);
      
      $group_where = DB::table('tbl_group')->where('group_id',$group)->first();
      $kasir = DB::SELECT("SELECT tk.*, mp.nama AS nama_pelanggan, mp.telp as telp_pelanggan, rg.nama as nama_gudang 
                          FROM tbl_kasir AS tk 
                          LEFT JOIN m_pelanggan AS mp ON tk.id_pelanggan=mp.id
                          LEFT JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
                          $add_where
                          ORDER BY tk.tanggal_faktur DESC LIMIT 500");
      $no = 0;
      $data = array();
        foreach ($kasir as $list){
            if($list->carabayar == '1'){
            $total_tagihan = 'Rp. 0';
            }else{
            $total_tagihan = 'Rp '.format_angka($list->total_tagihan);
            }
            if($list->telp_pelanggan == null){
              $telp_pelanggan = "";
            }else{
              $telp_pelanggan = " (".$list->telp_pelanggan.")";
            }
            $no++;
            $row = array();
            if ($this->agent->isMobile()) {
              $row['no'] = $no;
              $row['tanggal'] = tgl_full($list->tanggal_faktur,'');
              $row['no_faktur'] = $list->no_faktur;
              $row['nama_pelanggan'] = $list->nama_pelanggan.$telp_pelanggan;
              $row['nama_gudang'] = $list->nama_gudang;
              $row['total_redeem'] = format_angka($list->total_redeem)." Poin";
              $row['posting'] = $this->posting($list->status_posting,$list->id_kasir, $id_user, $group_where->group_aktif);
            }else {
              $row[] = $no;
              $row[] = tgl_full($list->tanggal_faktur,'');
              $row[] = $list->no_faktur;
              $row[] = $list->nama_pelanggan.$telp_pelanggan;
              $row[] = $list->nama_gudang;
              $row[] = format_angka($list->total_redeem)." Poin";
              $row[] = $this->posting($list->status_posting,$list->id_kasir, $id_user, $group_where->group_aktif);
            }
            $data[] = $row;
        }

      $output = array("data" => $data);
      return response()->json($output);
    }

    public function get_searchtanggal(Request $request){
    	$tanggal_start 	= '';
      $tanggal_end 		= '';
      if(isset($request->tanggal) != ''){
      $tanggalrange 	= explode('s.d.',$request->tanggal);
      $tanggal_start  	= tgl_full($tanggalrange[0],99);
      $tanggal_end    	= tgl_full($tanggalrange[1],99);
      }

      $id_profil = Auth::user()->id_profil;
      $group = Auth::user()->group_id;
      $id_user = Auth::user()->id;
      $where = "";
      if($group == 5 || $group == 6){
        $where = "WHERE id_profil='$id_profil'";
      }
      $d_gudang = DB::select(base_gudang($where));
      $id_gudang = array();
      foreach($d_gudang as $d){
        $id_gudang[] = $d->id_gudang; 
      }
      $where_gudang = "";
      if(sizeof($id_gudang) > 0){
        $gudang = implode(',',$id_gudang);
       }

      $where_data[] = "(tk.jenis_transaksi=3 AND tk.metodebayar=10)";
      if($gudang != ''){
      	$where_data[] = "tk.id_gudang IN ($gudang)";
      }
      if($tanggal_start != ''){
      	$where_data[] = "tk.tanggal_faktur >= '$tanggal_start'";
      }
      if($tanggal_end != ''){
      	$where_data[] = "tk.tanggal_faktur <= '$tanggal_end'";
      }
      $add_where = "WHERE".implode(" AND ",$where_data);
      print_r($where_data);exit();
      
      $group_where = DB::table('tbl_group')->where('group_id',$group)->first();
      $kasir = DB::SELECT("SELECT tk.*, mp.nama AS nama_pelanggan, mp.telp as telp_pelanggan, rg.nama as nama_gudang 
                          FROM tbl_kasir AS tk 
                          LEFT JOIN m_pelanggan AS mp ON tk.id_pelanggan=mp.id
                          LEFT JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
                          $where
                          ORDER BY tk.tanggal_faktur DESC LIMIT 500");
      $no = 0;
      $data = array();
        foreach ($kasir as $list){
            if($list->carabayar == '1'){
            $total_tagihan = 'Rp. 0';
            }else{
            $total_tagihan = 'Rp '.format_angka($list->total_tagihan);
            }
            if($list->telp_pelanggan == null){
              $telp_pelanggan = "";
            }else{
              $telp_pelanggan = " (".$list->telp_pelanggan.")";
            }
            $no++;
            $row = array();
            if ($this->agent->isMobile()) {
              $row['no'] = $no;
              $row['tanggal'] = tgl_full($list->tanggal_faktur,'');
              $row['no_faktur'] = $list->no_faktur;
              $row['nama_pelanggan'] = $list->nama_pelanggan.$telp_pelanggan;
              $row['nama_gudang'] = $list->nama_gudang;
              $row['total_redeem'] = format_angka($list->total_redeem,0)." Poin";
              $row['posting'] = $this->posting($list->status_posting,$list->id_kasir, $id_user, $group_where->group_aktif);
            }else {
              $row[] = $no;
              $row[] = tgl_full($list->tanggal_faktur,'');
              $row[] = $list->no_faktur;
              $row[] = $list->nama_pelanggan.$telp_pelanggan;
              $row[] = $list->nama_gudang;
              $row[] = format_angka($list->total_redeem,0)." Poin";
              $row[] = $this->posting($list->status_posting,$list->id_kasir, $id_user, $group_where->group_aktif);
            }
            $data[] = $row;
        }

      $output = array("data" => $data);
      return response()->json($output);
    }

    public function get_edit(Request $request){
      $id = $request->get('id');
      $d_data = DB::table('tbl_kasir_detail_produk as tpd')->leftjoin('m_produk as mp','tpd.id_produk','mp.id')->leftjoin('tbl_satuan as ts2','tpd.id_satuan','ts2.satuan_id')->where('tpd.id_kasir',$id)->select('tpd.*','mp.nama as nama_produk','ts2.satuan_nama as nama_satuan','ts2.satuan_satuan as satuan_satuan')->orderBy('tpd.id_kasir_detail_produk','asc');

      if($d_data->count() > 0){
        foreach($d_data->get() as $d){
            $arr['produk'][] = array('id' => $d->id_kasir_detail_produk,
                            'id_detail_kasir'  => "",
                            'id_kasir'  => $d->id_kasir,
                            'id_log_stok'  => $d->id_log_stok,
                            'id_produk'     => $d->id_produk,
                            'nama_produk'   => $d->nama_produk,
                            'id_barang'     => "",
                            'nama_barang'   => "",
                            'kode_barang'   => "",
                            'alias_barang'  => "",
                            'jumlah'        => $d->jumlah,
                            'id_satuan'     => $d->id_satuan,
                            'nama_satuan'   => $d->satuan_satuan,
                            'satuan_satuan' => $d->satuan_satuan,
                            'harga'         => 0,
                            'total'         => 0,
                            'status'        => "1",
                            'status_redeem' => (empty($d->status_redeem)?'0':$d->status_redeem),
                            'poin'          => $d->poin,
                            'total_poin'    => $d->total_poin);
        }
      }else{
        $arr['produk'] = array();
      }

      return response()->json($arr);
    }

    public function simpan(Request $request){
      $d_pelanggan = $request->get('id_pelanggan');
      if(is_numeric($d_pelanggan)){
        $pelanggan = $d_pelanggan;
      }else{
        $data_pelanggan['nama']   = $d_pelanggan;
        $data_pelanggan['telp']   = $request->get('telp_pelanggan');
        $data_pelanggan['alamat'] = $request->get('alamat_pelanggan');
        $data_pelanggan['id_gudang'] = $request->get('gudang');
        $data_pelanggan['status'] = $request->get('status_pelanggan');
        $data_pelanggan['tanggal_awal'] = tgl_full($request->get('tanggal'),'99');
        $data_pelanggan['tanggal_akhir'] = date('Y-m-d',strtotime('-1 day',strtotime('+5 years',strtotime(tgl_full($request->get('tanggal'),'99')))));
        $data_pelanggan['status_aktif'] = '1';

        if($request->get('status_pelanggan') != ""){
          $data_pelanggan['no_member'] = $request->get('nomor_pelanggan');
        }
        $id_pelanggan   = DB::table('m_pelanggan')->insertGetId($data_pelanggan);
        $pelanggan = $id_pelanggan;
      }

      $id_kasir = $request->get('id_kasir');
      $data['tanggal']        = tgl_full($request->get('tanggal'),'99');
      $data['tanggal_tempo']  = tgl_full($request->get('tanggal_tempo'),'99');
      $data['tanggal_faktur'] = tgl_full($request->get('tanggal_faktur'),'99');
      $data['no_faktur']      = $request->get('nomor');
      /*$data['id_pelanggan'] = $request->get('id_pelanggan');*/
      $data['id_pelanggan']   = $pelanggan;
      $data['uang_muka']      = 0;
      $data['ongkos_kirim']   = 0;
      $data['carabayar']      = 1;
      $data['metodebayar']    = 10;
      $data['total_potongan'] = 0;
      $data['total_tagihan']  = 0;
      $data['keterangan']     = $request->get('keterangan');
      $data['status']         = '1';
      $data['jenis_transaksi']= '3';
      $data['id_gudang']      = $request->get('gudang');
      $data['total_redeem']   = $request->get('td_poin');

      $tabel_id = ($request->get('tabel_id')) ? $request->get('tabel_id'):[];
      $tabel_produk   = $request->get('tabel_idproduk');
      $tabel_barang   = $request->get('tabel_idbarang');
      $tabel_jumlah   = $request->get('tabel_jumlah');
      $tabel_harga    = $request->get('tabel_harga');
      $tabel_satuan   = '9';
      $tabel_satuan2  = $request->get('tabel_idsatuan');
      $tabel_total    = $request->get('tabel_total');
      $tabel_idlog    = $request->get('tabel_idlog');
      $tabel_status   = $request->get('tabel_status');
      $tabel_status_redeem = $request->get('tabel_statusredeem');
      $tabel_poin     = $request->get('tabel_poin_produk');
      $tabel_totalpoin = $request->get('tabel_total_poin_produk');
      //$produk_cetak = array();
      $produk_cetak = "";

      if($id_kasir == ''){
        $id = DB::table('tbl_kasir')->insertGetId($data);
        $produk_cetak = $id;
        $total_poin = 0;
        for($i=0;$i<count($tabel_id);$i++){
          $produk['id_kasir'] = $id;
          $produk['id_produk']    = $tabel_produk[$i];
          $produk['jumlah']       = $tabel_jumlah[$i];
          $produk['id_satuan']    = $tabel_satuan;
          $produk['harga']        = $tabel_harga[$i];
          $produk['total']        = $tabel_total[$i];
          $produk['status_redeem']= $tabel_status_redeem[$i];
          $produk['poin']         = $tabel_poin[$i];
          $produk['total_poin']   = $tabel_totalpoin[$i];
          $id_kasir_detail_produk = DB::table('tbl_kasir_detail_produk')->insertGetId($produk);
          //$produk_cetak[] = $produk;

          $d_barang = DB::table('m_detail_produk as mdp')->join('tbl_barang as tb','mdp.id_barang','tb.barang_id')->where('mdp.id_produk',$tabel_produk[$i])->select(DB::raw('mdp.*,tb.satuan_id as id_satuan'));

          foreach($d_barang->get() as $d){
            $input['id_barang']     = $d->id_barang;
            $input['unit_masuk']    = "0";
            $input['unit_keluar']   = $d->jumlah*$tabel_jumlah[$i];
            $input['id_ref_gudang'] = $request->get('gudang');
            $input['id_satuan']     = $d->id_satuan;
            $input['tanggal']       = tgl_full($request->get('tanggal'),'99');
            $input['status']        = 'R1';
            $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);

            $barang['id_kasir']               = $id;
            $barang['id_detail_kasir_produk'] = $id_kasir_detail_produk;
            $barang['id_barang']              = $d->id_barang;
            $barang['jumlah']                 = $d->jumlah*$tabel_jumlah[$i];
            $barang['id_satuan']              = $d->id_satuan;
            $barang['harga']                  = '0';
            $barang['total']                  = '0';
            $barang['id_log_stok']            = $id_log_stok;
            // $barang['status_redeem']          = $tabel_status_redeem[$i];
            DB::table('tbl_kasir_detail')->insert($barang);
          }

          
          $total_poin += $tabel_totalpoin[$i];
        }

        $redeem['id_pelanggan'] = $pelanggan;
        $redeem['unit_masuk']   = 0;
        $redeem['unit_keluar']  = $total_poin;
        $redeem['tanggal']      = tgl_full($request->get('tanggal'),'99');
        $redeem['status']       = "PO2";
        $redeem['id_kasir']     = $id;
        DB::table('tbl_transaksi_poin')->insert($redeem);

      }else{
        DB::table('tbl_kasir')->where('id_kasir',$id_kasir)->update($data);

        $id_del = array();
        $id_store = array();
        $id_del2 = array();
        $id_store2 = array();
        $id_del_barang = array();
        $id_store_barang = array();
        $id_del_log = array();
        $id_store_log = array();
        $d_produk = DB::table('tbl_kasir_detail_produk')->where('id_kasir',$id_kasir);
        foreach($d_produk->get() as $key => $d){
          $id_store[] = $d->id_kasir_detail_produk;
          $id_store_produk[] = $d->id_produk;
          $id_storeproduk[$key]['id_kasir_detail_produk'] = $d->id_kasir_detail_produk;
          $id_storeproduk[$key]['id_produk'] = $d->id_produk;
        }

        foreach($id_store as $d){
                if(count($tabel_id) > 0){
                    if(!in_array($d, $tabel_id)){
                        $id_del[] = $d;
                    }
                }else{
                    $id_del[] = $d;
                }
            }

        $produk_cetak = $id_kasir;
        $total_poin = 0;
        for($i=0;$i<count($tabel_id);$i++){
          $produk['id_kasir'] = $id_kasir;
          $produk['id_produk']    = $tabel_produk[$i];
          $produk['jumlah']       = $tabel_jumlah[$i];
          $produk['id_satuan']    = $tabel_satuan;
          $produk['harga']        = $tabel_harga[$i];
          $produk['total']        = $tabel_total[$i];
          $produk['status_redeem']= $tabel_status_redeem[$i];
          $produk['poin']         = $tabel_poin[$i];
          $produk['total_poin']   = $tabel_totalpoin[$i];
          //$produk_cetak[] = $produk;

          if($tabel_id[$i] == ''){
            $id_kasir_detail_produk = DB::table('tbl_kasir_detail_produk')->insertGetId($produk);
            $d_barang = DB::table('m_detail_produk as mdp')->join('tbl_barang as tb','mdp.id_barang','tb.barang_id')->where('id_produk',$tabel_produk[$i])->select(DB::raw('mdp.*,tb.satuan_id as id_satuan'));

            foreach($d_barang->get() as $d){
              $input['id_barang']     = $d->id_barang;
              $input['unit_masuk']    = "0";
              $input['unit_keluar']   = $d->jumlah*$tabel_jumlah[$i];
              $input['id_ref_gudang'] = $request->get('gudang');
              $input['id_satuan']     = $d->id_satuan;
              $input['tanggal']       = tgl_full($request->get('tanggal'),'99');
              $input['status']        = 'R1';
              $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);

              $barang['id_kasir']               = $id_kasir;
              $barang['id_detail_kasir_produk'] = $id_kasir_detail_produk;
              $barang['id_barang']              = $d->id_barang;
              $barang['jumlah']                 = $d->jumlah*$tabel_jumlah[$i];
              $barang['id_satuan']              = $d->id_satuan;
              $barang['harga']                  = '0';
              $barang['total']                  = '0';
              $barang['id_log_stok']            = $id_log_stok;
              // $barang['status_redeem']          = $tabel_status_redeem[$i];
              DB::table('tbl_kasir_detail')->insert($barang);
            }

          }else if($tabel_id[$i] != ''){
            DB::table('tbl_kasir_detail_produk')->where(array('id_kasir_detail_produk' => $tabel_id[$i]))->update($produk);
            $d_barang = DB::table('tbl_kasir_detail')->where('id_detail_kasir_produk',$tabel_id[$i]);
            
            foreach($d_barang->get() as $d){              
              $jumlah = DB::table('m_detail_produk')->where('id_produk',$tabel_produk[$i])
                      ->where('id_barang',$d->id_barang)->first()->jumlah;
              
              $barang['id_kasir']               = $id_kasir;
              $barang['id_detail_kasir_produk'] = $tabel_id[$i];
              $barang['id_barang']              = $d->id_barang;
              $barang['jumlah']                 = $jumlah*$tabel_jumlah[$i];
              $barang['id_satuan']              = $d->id_satuan;
              $barang['harga']                  = $d->harga;
              $barang['total']                  = $d->jumlah*$d->harga;
              $barang['id_log_stok']            = $d->id_log_stok;
              // $barang['status_redeem']          = $tabel_status_redeem[$i];
            DB::table('tbl_kasir_detail')->where(array('id_detail_kasir'=> $d->id_detail_kasir))->update($barang);

              $input['id_barang']     = $d->id_barang;
              $input['unit_masuk']    = "0";
              $input['unit_keluar']   = $jumlah*$tabel_jumlah[$i];
              $input['id_ref_gudang'] = $request->get('gudang');
              $input['id_satuan']     = $d->id_satuan;
              $input['tanggal']       = tgl_full($request->get('tanggal'),'99');
              $input['status']        = 'R1';
            DB::table('tbl_log_stok')->where('log_stok_id',$d->id_log_stok)->update($input);

            }

          }
          $total_poin += $tabel_poin[$i];

        }


        $redeem['id_pelanggan'] = $pelanggan;
        $redeem['unit_masuk']   = 0;
        $redeem['unit_keluar']  = $total_poin;
        $redeem['tanggal']      = tgl_full($request->get('tanggal'),'99');
        $redeem['status']       = "PO2";
        $redeem['id_kasir']     = $id_kasir;
        DB::table('tbl_transaksi_poin')->where('id_kasir',$id_kasir)->update($redeem);

        if(count($id_del) > 0){
          $d_produk_stok = DB::table('tbl_kasir_detail')->whereIn('id_detail_kasir_produk', $id_del)->get();
          foreach($d_produk_stok as $d){
             DB::table('tbl_log_stok')->where('log_stok_id', $d->id_log_stok)->delete();
          }
          DB::table('tbl_kasir_detail_produk')->whereIn('id_kasir_detail_produk', $id_del)->delete();
          DB::table('tbl_kasir_detail')->whereIn('id_detail_kasir_produk', $id_del)->delete();

        }

      }

      if ($this->agent->isMobile()) {
        // code...
        //return $this->cetak_laporan($produk_cetak[0]['id_kasir']);
        return $this->cetak_laporan($produk_cetak);
      }else {
        // code...
        return redirect('redeem');
      }

    }


    public function hapus(Request $request){
      $id = $request->get('id');
      $d_barang = DB::table('tbl_kasir_detail')->where('id_kasir',$id);
      $id_log_stok = array();
      foreach($d_barang->get() AS $d){
        $id_log_stok[] = $d->id_log_stok;
      }
      DB::table('tbl_log_stok')->whereIn('log_stok_id',$id_log_stok)->delete();
      DB::table('tbl_transaksi_poin')->where('id_kasir',$id)->delete();

      DB::table('tbl_kasir')->where(array('id_kasir' => $id))->delete();
      DB::table('tbl_kasir_detail_produk')->where(array('id_kasir' => $id))->delete();
      DB::table('tbl_kasir_detail')->where(array('id_kasir' => $id))->delete();

    }


    public function cetak_laporan($id){
      if ($this->agent->isMobile()) {
        $data = DB::table('tbl_kasir as tk')->join('m_pelanggan as mp','tk.id_pelanggan','mp.id')->join('ref_gudang as rg','tk.id_gudang','rg.id')->leftjoin('m_metode as mm','tk.metodebayar','mm.id')->leftjoin('m_metode as mm2','tk.metodebayar2','mm2.id')->where('id_kasir',$id)->select(DB::raw('tk.*,mp.nama as nama_pelanggan,rg.nama as nama_gudang, rg.alamat as alamat_gudang, mm.nama as nama_metodebayar, mm2.nama as nama_metodebayar2'))->first();
        $detail = DB::table('tbl_kasir_detail_produk as tkp')->join('m_produk as mp','tkp.id_produk','mp.id')->join('tbl_satuan as ts','tkp.id_satuan','ts.satuan_id')->where('tkp.id_kasir','=',$id)->select(DB::Raw('tkp.*, mp.kode_produk as kode_produk, mp.nama as nama_produk, ts.satuan_nama as nama_satuan, ts.satuan_satuan as inisial_satuan'))->get();
        $barang  = DB::table('tbl_kasir_detail as tkd')->join('tbl_barang as tb','tkd.id_barang','tb.barang_id')->join('tbl_satuan as ts','tkd.id_satuan','ts.satuan_id')->where('tkd.id_kasir','=',$id)->where('tkd.id_detail_kasir_produk','=',0)->select(DB::raw('tkd.*,tb.barang_kode as kode_barang,tb.barang_nama as nama_barang, tb.barang_alias as alias,ts.satuan_nama as nama_satuan,ts.satuan_satuan as inisial_satuan'))->get();
        return view('admin_mobile.redeem.struk_redeem',compact('data','detail'));
        exit();
      }else {
        // code...
        require(public_path('fpdf1813\Mc_table.php'));
        $data['data'] = DB::table('tbl_kasir as tk')->join('m_pelanggan as mp','tk.id_pelanggan','mp.id')->join('ref_gudang as rg','tk.id_gudang','rg.id')->leftjoin('m_metode as mm','tk.metodebayar','mm.id')->leftjoin('m_metode as mm2','tk.metodebayar2','mm2.id')->where('id_kasir',$id)->select(DB::raw('tk.*,mp.nama as nama_pelanggan, mp.alamat as alamat_pelanggan, mp.telp as telp_pelanggan, rg.nama as nama_gudang, rg.alamat as alamat_gudang, mm.nama as nama_metodebayar, mm2.nama as nama_metodebayar2'))->first();
        $data['detail'] = DB::table('tbl_kasir_detail_produk as tkp')->join('m_produk as mp','tkp.id_produk','mp.id')->join('tbl_satuan as ts','tkp.id_satuan','ts.satuan_id')->where('tkp.id_kasir','=',$id)->select(DB::raw('tkp.*,mp.kode_produk as kode_produk,mp.nama as nama_produk,ts.satuan_nama as nama_satuan,ts.satuan_satuan as inisial_satuan'))->get();
        $data['barang'] = DB::table('tbl_kasir_detail as tkd')->join('tbl_barang as tb','tkd.id_barang','tb.barang_id')->join('tbl_satuan as ts','tkd.id_satuan','ts.satuan_id')->where('tkd.id_kasir','=',$id)->where('tkd.id_detail_kasir_produk','=','0')->select(DB::raw('tkd.*,tb.barang_kode as kode_barang,tb.barang_nama as nama_barang, tb.barang_alias as alias,ts.satuan_nama as nama_satuan,ts.satuan_satuan as inisial_satuan'))->get();
        if(Auth::user()->id_profil != 0){
          $id_profil = Auth::user()->id_profil;
        }else{
          $id_profil = '1';
        }

        $data['rich'] = DB::table('m_profil as mp')->where('id',$id_profil)->select(DB::raw('mp.*'))->first();
        //dd($data['rich']."/".Auth::user()->id_profil);
        $html = view('admin.redeem.CetakRedeem')->with('data',$data);
        return response($html)->header('Content-Type', 'application/pdf');
      }
    }

    private function posting($status, $id, $id_user, $status_edit){
      switch (true) {
        case ($status == '1'&& $status_edit == '2'):
          $html = '<div class="btn-group"><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('redeem_edit/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
            <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa fa-trash"></i></a>
            </div>';
          break;
        case ($status == '2' && $status == '2'):
          $html = '<div class="btn-group"><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('redeem_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a></div>';
          break;
        case ($status == '1' && $status_edit == '1'):
          $html = '<div class="btn-group"><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('redeem_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a></div>';
          break;
        case ($status == '2' && $status == '1'):
          $html = '<div class="btn-group"><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('redeem_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a></div>';
          break;
        default:
          $html = '<div class="btn-group"><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('redeem_edit/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
            <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa fa-trash"></i></a>
            </div>';
          break;
      }

      return $html;
    }

    public function attr_pelanggan(Request $request){
      $id = $request->get('id');
      $arr = array();
      if(is_numeric($id)){
        /*$d_data = DB::SELECT("SELECT * FROM (
          SELECT mp.id, CASE WHEN mp.nama IS NULL THEN '' ELSE mp.nama END AS nama,
          CASE WHEN mp.alamat IS NULL THEN '' ELSE mp.alamat END AS alamat,
          CASE WHEN mp.telp IS NULL THEN '' ELSE mp.telp END AS telp,
          CASE WHEN mp.no_member IS NULL THEN '' ELSE mp.no_member END AS nomor,
          CASE WHEN mp.status IS NULL THEN '' ELSE mp.status END status,
          CASE WHEN p.poin IS NULL THEN '0' ELSE p.poin END poin
          FROM m_pelanggan as mp 
          LEFT JOIN (
               SELECT tpp.id_pelanggan, CASE WHEN SUM(tpp.unit_masuk-tpp.unit_keluar) 
               THEN SUM(tpp.unit_masuk-tpp.unit_keluar) ELSE 0 END AS poin 
               FROM tbl_transaksi_poin as tpp 
               GROUP BY id_pelanggan
          )  AS p ON p.id_pelanggan=mp.id
          WHERE mp.id = '$id' AND mp.status != 2 AND mp.status_aktif = 1
          UNION ALL
          SELECT mp.id, CASE WHEN mp.nama IS NULL THEN '' ELSE mp.nama END AS nama,
          CASE WHEN mp.alamat IS NULL THEN '' ELSE mp.alamat END AS alamat,
          CASE WHEN mp.telp IS NULL THEN '' ELSE mp.telp END AS telp,
          CASE WHEN mp.no_member IS NULL THEN '' ELSE mp.no_member END AS nomor,
          CASE WHEN mp.status IS NULL THEN '' ELSE mp.status END status,
          CASE WHEN p.poin IS NULL THEN '0' ELSE p.poin END poin
          FROM m_pelanggan as mp 
          LEFT JOIN (
               SELECT tpp.id_pelanggan, CASE WHEN SUM(tpp.unit_masuk-tpp.unit_keluar) 
               THEN SUM(tpp.unit_masuk-tpp.unit_keluar) ELSE 0 END AS poin 
               FROM tbl_transaksi_poin as tpp 
               GROUP BY id_pelanggan
          )  AS p ON p.id_pelanggan=mp.id
          WHERE mp.id = '$id' AND mp.status = 2 AND mp.status_aktif = 1 AND mp.tanggal_akhir >= DATE(NOW())) as a"
        );*/

      $d_data = DB::SELECT("SELECT * FROM (
        SELECT mp.id, CASE WHEN mp.nama IS NULL THEN '' ELSE mp.nama END AS nama,
        CASE WHEN mp.alamat IS NULL THEN '' ELSE mp.alamat END AS alamat,
        CASE WHEN mp.telp IS NULL THEN '' ELSE mp.telp END AS telp,
        CASE WHEN mp.no_member IS NULL THEN '' ELSE mp.no_member END AS nomor,
        CASE WHEN mp.status IS NULL THEN '' ELSE mp.status END status,
        CASE WHEN p.poin IS NULL THEN '0' ELSE p.poin END poin
        FROM m_pelanggan as mp 
        LEFT JOIN (
             SELECT tpp.id_pelanggan, CASE WHEN SUM(tpp.unit_masuk-tpp.unit_keluar) 
             THEN SUM(tpp.unit_masuk-tpp.unit_keluar) ELSE 0 END AS poin 
             FROM tbl_transaksi_poin as tpp 
             GROUP BY id_pelanggan
        )  AS p ON p.id_pelanggan=mp.id
        WHERE mp.id = '$id' AND mp.status = 2 AND mp.status_aktif = 1 AND mp.tanggal_akhir >= DATE(NOW())) as a"
      );

      $arr = array();
      if(count($d_data) > 0){
        foreach($d_data as $d){
              $arr[] = array('id_pelanggan' => $d->id,
                              'nama'        => $d->nama,
                              'alamat'      => $d->alamat||'',
                              'telp'        => $d->telp,
                              'nomor'       => $d->nomor,
                              'status'      => $d->status,
                              'poin'        => $d->poin);
        }
      }
      }

      return response()->json($arr);
    }


    public function attr_get_pelanggan(Request $request){
      $gudang = $request->get('gudang');

      $where_pelanggan = "";
      $where_pelanggan_member = "";
      if(isset($gudang)){
        $where_pelanggan_member = "WHERE rf.id IN ($gudang) AND (mp.status != 2 OR mp.status IS NULL ) AND mp.status_aktif = 1 AND mp.tanggal_akhir >= DATE(NOW())";
        $where_pelanggan = "WHERE mp.status = 2 AND mp.tanggal_akhir >= DATE(NOW()) AND mp.status_aktif = 1";
      }
      /*$d_data = DB::SELECT("SELECT * FROM (
      select mp.id as id, CASE WHEN mp.telp IS NULL THEN mp.nama ELSE CONCAT(mp.nama,' (',mp.telp,')') END as nama, rf.nama as nama_gudang, mp.status as status_member from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id 
      $where_pelanggan
      UNION ALL
      select mp.id as id, CASE WHEN mp.telp IS NULL THEN mp.nama ELSE CONCAT(mp.nama,' (',mp.telp,')') END as nama, rf.nama as nama_gudang, mp.status as status_member from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id 
      $where_pelanggan_member
      ) as a");*/
      $d_data = DB::SELECT("SELECT * FROM (
      select mp.id as id, CASE WHEN mp.telp IS NULL THEN mp.nama ELSE CONCAT(mp.nama,' (',mp.telp,')') END as nama, rf.nama as nama_gudang, mp.status as status_member from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id 
      $where_pelanggan
      ) as a");

      $arr = array();
      foreach($d_data as $d){
        $arr[] = array('id_pelanggan' => $d->id,
                      'nama_pelanggan' => $d->nama);
      }

      return response()->json($arr);

    }

    public function simpan_infocetak(Request $request){
      DB::beginTransaction();
      try{
        $data['id_kasir'] = $request->get('popupinfo_idkasir');
        $data['id_user'] = $request->get('popupinfo_iduser');
        $data['keterangan'] = $request->get('popupinfo_keterangan');
        DB::table('tbl_kasir_print')->insert($data);
        $status = 1;
        DB::commit();
      }catch(\Exception $e){
        DB::rollback();
        $status = 0;
      }
      return response()->json(array('status' => $status));

    }



}
