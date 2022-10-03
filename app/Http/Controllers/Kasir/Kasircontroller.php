<?php

namespace App\Http\Controllers\Kasir;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//use Illuminate\Support\Facades\Cache;
use DB;
use Auth;
use App\KasirModel;
use App\SupplierModel;
use App\PelanggahModel;
use Illuminate\Support\Collection;
use Jenssegers\Agent\Agent;
date_default_timezone_set('Asia/Jakarta');
// ================================ PRINTER THERMAL ============================
/*use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;*/
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

class Kasircontroller extends Controller
{
    //
    public function __construct(){
      $this->agent = new Agent();
    }
    public function index(){
      $id_group = Auth::user()->group_id;
      $group_where = DB::table('tbl_group')->where('group_id',$id_group)->first();
      $tombol_create = tombol_create(url('kasir_tambah'),$group_where->group_aktif,2);
      if ($this->agent->isMobile()) {
        // code...
        return view('admin_mobile.kasir.index',compact('tombol_create'));
      }else {
        // code...
        return view('admin.kasir.index',compact('tombol_create'));
      }
    }

    public function get_penyedia(Request $request){
      $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }
        $search = strtolower($term);
        $d_query= DB::select("SELECT
          supplier_id,
          supplier_nama,
          supplier_alamat,
          supplier_telp
          FROM tbl_supplier  WHERE supplier_nama LIKE '%$search%' OR supplier_telp LIKE '%$search%' order by supplier_nama asc");
        return \Response::json($d_query);
    }

    public function get_barang(Request $request){
    $term = trim($request->q);

        if (empty($term)) {
            return \Response::json([]);
        }

        $search = strtolower($term);
        $barangs= DB::select("SELECT
					b.barang_id,
					b.satuan_id,
					b.barang_kode,
					b.barang_nama,
          b.barang_alias,
					b.barang_id_parent,
					b.barang_status_bahan,
					s.satuan_nama,
					s.satuan_satuan,
					d.detail_harga_barang_harga_jual  harga
				FROM
					tbl_barang AS b
					LEFT JOIN tbl_satuan AS s ON b.satuan_id = s.satuan_id
					LEFT JOIN (
					SELECT
						*
					FROM
						tbl_detail_harga_barang AS a
				WHERE
				detail_harga_barang_tanggal =
				( SELECT MAX( detail_harga_barang_tanggal ) FROM tbl_detail_harga_barang AS b WHERE a.barang_id = b.barang_id )) AS d ON b.barang_id = d.barang_id
                WHERE b.barang_kode LIKE '%$search%' OR b.barang_nama LIKE '%$search%' OR b.barang_alias LIKE '%$search%'");

        return \Response::json($barangs);
    }

    public function get_produk(Request $request){
    $term = trim($request->q);

        if (empty($term)) {
            return \Response::json([]);
        }

        $search = strtolower($term);
        /*$barangs= DB::select("SELECT
          b.id as produk_id,
          b.kode_produk as produk_kode,
          b.nama as produk_nama,
          b.harga as produk_harga
        FROM m_produk as b WHERE b.nama LIKE '%$search%' order by b.nama asc");*/
        $tanggal_now = date('Y-m-d');
        $barangs = DB::SELECT("SELECT mp.id as produk_id,
          mp.kode_produk as produk_kode,
          mp.nama as produk_nama,
          mp.harga as produk_harga, 
          CASE WHEN mp2.poin IS NULL THEN 0 ELSE mp2.poin END AS produk_poin, 
          '' AS produk_gambar,
          CASE WHEN mpm.harga IS NULL THEN 0 ELSE mpm.harga END AS diskon_promo,
          CASE WHEN mp.id_type_ukuran IS NULL THEN '' ELSE mp.id_type_ukuran END AS id_tipe
        FROM m_produk as mp
        LEFT JOIN (
          SELECT id_produk, tanggal, poin FROM (
            SELECT * FROM m_produkpoin 
            where kategori = 2 AND tanggal = '$tanggal_now' 
            group by id_produk, id, kategori, hari, tanggal, poin, created_at, updated_at 
            UNION ALL
            SELECT * FROM m_produkpoin 
            where kategori = 1 
            group by id_produk, id, kategori, hari, tanggal, poin, created_at, updated_at
          ) as mpo group by id_produk, mpo.tanggal, mpo.poin order by mpo.id DESC
        ) as mp2 ON mp2.id_produk=mp.id
        LEFT JOIN m_produk_mapping as mpm ON mp.id_type_ukuran=mpm.id_type_ukuran
        WHERE mp.nama LIKE '%$search%'
        group by mp.id, mp.kode_produk, mp.nama, mp.harga, mp.created_at, mp.updated_at, mp2.poin");

        return \Response::json($barangs);
    }

    public function get_pelanggan(Request $request){
      $id_profil = Auth::user()->id_profil;
        $group = Auth::user()->group_id;
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
          $where_gudang = "AND b.id IN ($gudang)";
        }
      $term = trim($request->q);

        if (empty($term)) {
            return \Response::json([]);
        }

        $search = strtolower($term);
        $barangs= DB::select("SELECT
          b.id as pelanggan_id,
          CASE WHEN b.telp IS NULL THEN b.nama ELSE CONCAT(b.nama,' (',b.telp,')') END as pelanggan_nama,
          b.nama as pelanggan_nama2,
          b.telp as pelanggan_telp,
          b.alamat as pelanggan_alamat
        FROM m_pelanggan as b LEFT JOIN ref_gudang as rg ON b.id_gudang=rg.id WHERE (b.nama LIKE '%$search%') $where_gudang order by b.nama asc");

        return \Response::json($barangs);
    }

    public function create(){
      $id_profil = Auth::user()->id_profil;
      $group = Auth::user()->group_id;
      $where = "";
      if($group == 5 || $group == 6){
        $where = "WHERE id_profil='$id_profil'";
      }

      $d_gudang = DB::select(base_gudang($where));
        $id_gudang = array();
        foreach($d_gudang as $d){
          $id_gudang[] = $d->id_gudang;
        }
        $where_gudang = "WHERE mp.status_aktif = 2 AND mp.tanggal_akhir >= DATE(NOW())";
        $where_pelanggan_member = "WHERE (mp.status != 2 OR mp.status IS NULL )  AND mp.status_aktif = 1 AND mp.tanggal_akhir >= DATE(NOW())";
        $where_pelanggan = "WHERE mp.status = 2 AND mp.tanggal_akhir >= DATE(NOW()) AND mp.status_aktif = 1";
        if(sizeof($id_gudang) > 0){
          $gudang = implode(',',$id_gudang);
          $where_gudang = "WHERE rf.id IN ($gudang) AND mp.status_aktif = 2 AND mp.tanggal_akhir >= DATE(NOW()) ";
          $where_pelanggan_member = "WHERE rf.id IN ($gudang) AND (mp.status != 2 OR mp.status IS NULL ) AND mp.status_aktif = 1 AND mp.tanggal_akhir >= DATE(NOW())";
          $where_pelanggan = "WHERE mp.status = 2 AND mp.tanggal_akhir >= DATE(NOW()) AND mp.status_aktif = 1";
        }

      $data['data']       = $this->data(array());
      $data['satuan']     = DB::table('tbl_satuan')->orderBy('satuan_nama','asc')->get();
      $data['carabayar']  = \Config::get('constants.carabayar');
      $data['no_auto']    = $this->noauto();
      /*$data['pelanggan']  = DB::select("select mp.*, rf.nama as nama_gudang from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id $where_gudang");*/
      /*$data['pelanggan']  = DB::select("select mp.id, CASE WHEN mp.telp IS NULL THEN mp.nama ELSE CONCAT(mp.nama,' (',mp.telp,')') END as nama, rf.nama as nama_gudang from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id $where_gudang");*/
      $data['pelanggan']  = DB::SELECT("SELECT * FROM (
      select mp.id as id, CASE WHEN mp.telp IS NULL THEN mp.nama ELSE CONCAT(mp.nama,' (',mp.telp,')') END as nama, rf.nama as nama_gudang, mp.status as status_member from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id 
      $where_pelanggan
      UNION ALL
      select mp.id as id, CASE WHEN mp.telp IS NULL THEN mp.nama ELSE CONCAT(mp.nama,' (',mp.telp,')') END as nama, rf.nama as nama_gudang, mp.status as status_member from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id 
      $where_pelanggan_member
      ) as a");
      $data['gudang']     = DB::select(base_gudang($where));
    //   $data['pembayaran']  = DB::table('m_metode')->orderby('urutan','asc')->get();
      $data['pembayaran'] = DB::table('m_metode')->where('status','=','1')->orderby('urutan','asc')->get();
      if ($this->agent->isMobile()) {
        // code...
        return view('admin_mobile.kasir.create')->with('data',$data);
      }else {
        // code...
        return view('admin.kasir.create')->with('data',$data);
      }

    }
    
    function noauto(){
        $id_profil = (int)Auth::user()->id_profil;
        $group = Auth::user()->group_id;
        $where = "";
        if($id_profil != '' || $id_profil > 0 || $id_profil != '0'){
          $where = "WHERE id_profil='$id_profil'";
        }
        $d_gudang = DB::SELECT(base_gudang($where));
        $id_gudang = array();
        $a_kode = array();
        foreach($d_gudang as $d){
          $id_gudang[] = $d->id_gudang;
          $a_kode[] = $d->kode;
        }
        $where_gudang = "";
        $where_kode = "";
        if(count($id_gudang) > 0){
          $gudang = implode(',',$id_gudang);
          $d_kode = implode(',',$a_kode);
          $where_gudang = "WHERE rf.id IN ($gudang)";
          $where_kode = $d_kode;
        }
        
        // print_r($id_gudang);exit();

        $no_faktur = $where_kode.".";
        if(count($id_gudang) > 1){
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
        // $tanggal = tgl_full($request['tanggal'],'99');
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
        $store['td_statuspromo']  = $data->status_promo;
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
        $store['td_statuspromo']  = "TIDAK";
      }

      return $store;
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
      $data['tanggal']            = tgl_full($request->get('tanggal'),'99');
      $data['tanggal_tempo']      = tgl_full($request->get('tanggal_tempo'),'99');
      $data['tanggal_faktur']     = tgl_full($request->get('tanggal_faktur'),'99');
    //   $data['no_faktur']          = $request->get('nomor');
    //   $data['no_faktur']          = $this->get_nomorfaktur($request->get('tanggal_faktur'), $request->get('gudang'));
      /*$data['id_pelanggan']     = $request->get('id_pelanggan');*/
      $data['id_pelanggan']       = $pelanggan;
      $data['uang_muka']          = $request->get('td_uangmuka');
      $data['ongkos_kirim']       = $request->get('td_ongkir');
      $data['carabayar']          = $request->get('carabayar');
      $data['metodebayar']        = $request->get('viabayar');
      $data['total_potongan']     = $request->get('td_diskon');
      $data['total_tagihan']      = $request->get('td_total');
      $data['keterangan']         = $request->get('keterangan');
      $data['status']             = '1';
      $data['jenis_transaksi']    = '1';
      //$data['paper']            = $request->get('paper');
      $data['id_gudang']          = $request->get('gudang');
      $data['metodebayar2']       = $request->get('viabayar2');
      $data['total_metodebayar']  = $request->get('total_viabayar');
      $data['total_metodebayar2'] = $request->get('total_viabayar2');
      $data['status_promo']       = $request->get('td_statuspromo');

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
      $tabel_poin     = $request->get('tabel_poin');
      $tabel_produk_sebelum = $request->get('tabel_idproduk_sebelum');

      //promo
      $tabel_potongan       = $request->get('tabel_potongan');
      $tabel_potongan_total = $request->get('tabel_potongan_total');
      //$produk_cetak = array();
      $produk_cetak = "";
      try{
      if($id_kasir == ''){
        //created by
        $data['no_faktur']          = $this->get_nomorfaktur($request->get('tanggal_faktur'), $request->get('gudang'));
        $data['created_by']         = Auth::user()->name;
        $data['created_iduser']     = Auth::user()->id; 
        $id = DB::table('tbl_kasir')->insertGetId($data);
        $produk_cetak = $id;
        $total_pembayaran = 0;
        $total_poin = 0;
        for($i=0;$i<count($tabel_id);$i++){
          if($tabel_status[$i] == '1'){
          $produk['id_kasir'] = $id;
          $produk['id_produk']      = $tabel_produk[$i];
          $produk['jumlah']         = $tabel_jumlah[$i];
          $produk['id_satuan']      = $tabel_satuan;
          $produk['harga']          = $tabel_harga[$i];
          $produk['total']          = $tabel_total[$i];
          $porduk['poin']           = $tabel_poin[$i];
          $produk['total_poin']     = $tabel_poin[$i]*$tabel_jumlah[$i];
          $produk['potongan']       = $tabel_potongan[$i];
          $produk['potongan_total'] = $tabel_potongan_total[$i];
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
            $input['status']        = 'J1';
            $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);

            $barang['id_kasir']               = $id;
            $barang['id_detail_kasir_produk'] = $id_kasir_detail_produk;
            $barang['id_barang']              = $d->id_barang;
            $barang['jumlah']                 = $d->jumlah*$tabel_jumlah[$i];
            $barang['id_satuan']              = $d->id_satuan;
            $barang['harga']                  = '0';
            $barang['total']                  = '0';
            $barang['id_log_stok']            = $id_log_stok;
            DB::table('tbl_kasir_detail')->insert($barang);
          }

          }else if($tabel_status[$i] == '2'){
            $input['id_barang']     = $tabel_barang[$i];
            $input['unit_masuk']    = "0";
            $input['unit_keluar']   = $tabel_jumlah[$i];
            $input['id_ref_gudang'] = $request->get('gudang');
            $input['id_satuan']     = $tabel_satuan2[$i];
            $input['tanggal']       = tgl_full($request->get('tanggal'),'99');
            $input['status']        = 'J1';
            $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);

            $barang['id_kasir']               = $id;
            $barang['id_detail_kasir_produk'] = '0';
            $barang['id_barang']              = $tabel_barang[$i];
            $barang['jumlah']                 = $tabel_jumlah[$i];
            $barang['id_satuan']              = $tabel_satuan2[$i];
            $barang['harga']                  = $tabel_harga[$i];
            $barang['total']                  = $tabel_total[$i];
            $barang['id_log_stok']            = $id_log_stok;
            DB::table('tbl_kasir_detail')->insert($barang);
          }
          
          $total_pembayaran += $tabel_total[$i];
          $total_poin += $tabel_poin[$i]; 

        }
        
        $total_pembayaran = $total_pembayaran;
        $total_poin = $total_poin;
        $nominal = DB::table('m_nominal_poin')->limit(1)->first()->nominal;
        $cek_poin = floor($total_pembayaran / $nominal);
        // dd($total_poin);
        if($cek_poin > 0){
          $redeem['id_pelanggan'] = $pelanggan;
          $redeem['unit_masuk']   = $cek_poin;
          $redeem['unit_keluar']  = 0;
          $redeem['tanggal']      = tgl_full($request->get('tanggal'),'99');
          $redeem['status']       = 'PO1';
          $redeem['id_kasir']     = $id;
          DB::table('tbl_transaksi_poin')->insert($redeem);
        }
        trigger_log($id, "Menambahkan Nota Penjualan Outlet", 1, url('kasir_edit/'.$id));
      }else{
        //updated by
        $data['no_faktur']          = $request->get('nomor');
        $data['updated_by']         = Auth::user()->name;
        $data['updated_iduser']     = Auth::user()->id; 
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
        $d_barang = DB::table('tbl_kasir_detail')->where('id_kasir',$id_kasir)->where('id_detail_kasir_produk','0');
        foreach($d_produk->get() as $key => $d){
          $id_store[] = $d->id_kasir_detail_produk;
          $id_store_produk[] = $d->id_produk;
          $id_storeproduk[$key]['id_kasir_detail_produk'] = $d->id_kasir_detail_produk;
          $id_storeproduk[$key]['id_produk'] = $d->id_produk;
        }

        foreach($d_barang->get() as $d){
          $id_store2[] = $d->id_detail_kasir;
        }


        /*foreach($id_storeproduk as $key => $d){                
                if(count($tabel_id) > 0){
                  if(count($tabel_produk) > 0){
                    if(!in_array($d['id_produk'],$tabel_produk) || !in_array($d['id_kasir_detail_produk'],$tabel_id)){
                        $id_del['id_kasir_detail_produk'][] = $d['id_kasir_detail_produk'];
                        $id_del['id_produk'][] = $d['id_produk']; 
                    }
                  }
                }else{
                    $id_del['id_kasir_detail_produk'][] = $d['id_kasir_detail_produk'];
                    $id_del['id_produk'][] = $d['id_produk'];
                }
        } */


        

        foreach($id_store as $d){
                if(count($tabel_id) > 0){
                    if(!in_array($d, $tabel_id)){
                        $id_del[] = $d;
                    }
                }else{
                    $id_del[] = $d;
                }
            }
        foreach($id_store2 as $d){
                if(count($tabel_id) > 0){
                    if(!in_array($d, $tabel_id)){
                        $id_del2[] = $d;
                    }
                }else{
                    $id_del2[] = $d;
                }
        }
        $produk_cetak = $id_kasir;
        $total_pembayaran = 0;
        $total_poin = 0;
        for($i=0;$i<count($tabel_id);$i++){
          $produk['id_kasir'] = $id_kasir;
          $produk['id_produk']    = $tabel_produk[$i];
          $produk['jumlah']       = $tabel_jumlah[$i];
          $produk['id_satuan']    = $tabel_satuan;
          $produk['harga']        = $tabel_harga[$i];
          $produk['total']        = $tabel_total[$i];
          $produk['poin']         = $tabel_poin[$i];
          $produk['total_poin']   = $tabel_poin[$i]*$tabel_jumlah[$i];
          //$produk_cetak[] = $produk;

          //promo
          $produk['potongan']       = $tabel_potongan[$i];
          $produk['potongan_total'] = $tabel_potongan_total[$i];

          if($tabel_id[$i] == ''){
            if($tabel_status[$i] == '1'){

            $id_kasir_detail_produk = DB::table('tbl_kasir_detail_produk')->insertGetId($produk);
            $d_barang = DB::table('m_detail_produk as mdp')->join('tbl_barang as tb','mdp.id_barang','tb.barang_id')->where('id_produk',$tabel_produk[$i])->select(DB::raw('mdp.*,tb.satuan_id as id_satuan'));

            foreach($d_barang->get() as $d){
              $input['id_barang']     = $d->id_barang;
              $input['unit_masuk']    = "0";
              $input['unit_keluar']   = $d->jumlah*$tabel_jumlah[$i];
              $input['id_ref_gudang'] = $request->get('gudang');
              $input['id_satuan']     = $d->id_satuan;
              $input['tanggal']       = tgl_full($request->get('tanggal'),'99');
              $input['status']        = 'J1';
              $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);

              $barang['id_kasir']               = $id_kasir;
              $barang['id_detail_kasir_produk'] = $id_kasir_detail_produk;
              $barang['id_barang']              = $d->id_barang;
              $barang['jumlah']                 = $d->jumlah*$tabel_jumlah[$i];
              $barang['id_satuan']              = $d->id_satuan;
              $barang['harga']                  = '0';
              $barang['total']                  = '0';
              $barang['id_log_stok']            = $id_log_stok;
              DB::table('tbl_kasir_detail')->insert($barang);
            }

            }else if($tabel_status[$i] == '2'){
              $input['id_barang']     = $tabel_barang[$i];
              $input['unit_masuk']    = "0";
              $input['unit_keluar']   = $tabel_jumlah[$i];
              $input['id_ref_gudang'] = $request->get('gudang');
              $input['id_satuan']     = $tabel_satuan2[$i];
              $input['tanggal']       = tgl_full($request->get('tanggal'),'99');
              $input['status']        = 'J1';
              $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);

              $barang['id_kasir']               = $id_kasir;
              $barang['id_detail_kasir_produk'] = '0';
              $barang['id_barang']              = $tabel_barang[$i];
              $barang['jumlah']                 = $tabel_jumlah[$i];
              $barang['id_satuan']              = $tabel_satuan2[$i];
              $barang['harga']                  = $tabel_harga[$i];
              $barang['total']                  = $tabel_total[$i];
              $barang['id_log_stok']            = $id_log_stok;
              DB::table('tbl_kasir_detail')->insert($barang);
            }


          }else if($tabel_id[$i] != ''){
            if($tabel_status[$i] == '1'){
            DB::table('tbl_kasir_detail_produk')->where(array('id_kasir_detail_produk' => $tabel_id[$i]))->update($produk);
            /*$d_barang = DB::table('tbl_kasir_detail')->where('id_detail_kasir_produk',$tabel_id[$i]);
            
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
            DB::table('tbl_kasir_detail')->where(array('id_detail_kasir'=> $d->id_detail_kasir))->update($barang);

              $input['id_barang']     = $d->id_barang;
              $input['unit_masuk']    = "0";
              $input['unit_keluar']   = $jumlah*$tabel_jumlah[$i];
              $input['id_ref_gudang'] = $request->get('gudang');
              $input['id_satuan']     = $d->id_satuan;
              $input['tanggal']       = tgl_full($request->get('tanggal'),'99');
              $input['status']        = 'J1';
            DB::table('tbl_log_stok')->where('log_stok_id',$d->id_log_stok)->update($input);

            }*/
            if($tabel_produk[$i] != $tabel_produk_sebelum[$i]){
              $d_barang_cek = DB::table('tbl_kasir_detail')->where('id_detail_kasir_produk',$tabel_id[$i]);
              foreach ($d_barang_cek->get() as $key => $value) {
                # code...
                DB::table('tbl_log_stok')->where('log_stok_id',$value->id_log_stok)->delete();
              }
              DB::table('tbl_kasir_detail')->where('id_detail_kasir_produk',$tabel_id[$i])->delete();
              $d_barang = DB::table('m_detail_produk as mdp')->join('tbl_barang as tb','mdp.id_barang','tb.barang_id')->where('mdp.id_produk',$tabel_produk[$i])->select(DB::raw('mdp.*,tb.satuan_id as id_satuan'));
              foreach($d_barang->get() as $d){
                $input['id_barang']     = $d->id_barang;
                $input['unit_masuk']    = "0";
                $input['unit_keluar']   = $d->jumlah*$tabel_jumlah[$i];
                $input['id_ref_gudang'] = $request->get('gudang');
                $input['id_satuan']     = $d->id_satuan;
                $input['tanggal']       = tgl_full($request->get('tanggal'),'99');
                $input['status']        = 'J1';
                $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);

                $barang['id_kasir']               = $id_kasir;
                $barang['id_detail_kasir_produk'] = $tabel_id[$i];
                $barang['id_barang']              = $d->id_barang;
                $barang['jumlah']                 = $d->jumlah*$tabel_jumlah[$i];
                $barang['id_satuan']              = $d->id_satuan;
                $barang['harga']                  = '0';
                $barang['total']                  = '0';
                $barang['id_log_stok']            = $id_log_stok;
                // $barang['status_redeem']          = $tabel_status_redeem[$i];
                DB::table('tbl_kasir_detail')->insert($barang);
              }
              }else{
              $d_barang = DB::table('tbl_kasir_detail')->where('id_detail_kasir_produk',$tabel_id[$i]);
              foreach($d_barang->get() as $d){              
                $d_jumlah = DB::table('m_detail_produk')->where('id_produk',$tabel_produk[$i])
                        ->where('id_barang',$d->id_barang);
                $jumlah = 0;
                if($d_jumlah->count() > 0){
                  $jumlah = (int)$d_jumlah->first()->jumlah;
                }
                
                $barang['id_kasir']               = $id_kasir;
                $barang['id_detail_kasir_produk'] = $tabel_id[$i];
                $barang['id_barang']              = $d->id_barang;
                $barang['jumlah']                 = $jumlah*$tabel_jumlah[$i];
                $barang['id_satuan']              = $d->id_satuan;
                $barang['harga']                  = $d->harga;
                $barang['total']                  = $d->jumlah*$d->harga;
                $barang['id_log_stok']            = $d->id_log_stok;
                DB::table('tbl_kasir_detail')->where(array('id_detail_kasir'=> $d->id_detail_kasir))->update($barang);

                $input['id_barang']     = $d->id_barang;
                $input['unit_masuk']    = "0";
                $input['unit_keluar']   = $jumlah*$tabel_jumlah[$i];
                $input['id_ref_gudang'] = $request->get('gudang');
                $input['id_satuan']     = $d->id_satuan;
                $input['tanggal']       = tgl_full($request->get('tanggal'),'99');
                $input['status']        = 'J1';
                DB::table('tbl_log_stok')->where('log_stok_id',$d->id_log_stok)->update($input);

              }
              }
              
          }else if($tabel_status[$i] == '2'){
              $input['id_barang']     = $tabel_barang[$i];
              $input['unit_masuk']    = "0";
              $input['unit_keluar']   = $tabel_jumlah[$i];
              $input['id_ref_gudang'] = $request->get('gudang');
              $input['id_satuan']     = $tabel_satuan2[$i];
              $input['tanggal']       = tgl_full($request->get('tanggal'),'99');
              $input['status']        = 'J1';
              DB::table('tbl_log_stok')->where(array('log_stok_id' => $tabel_idlog[$i]))->update($input);

              $barang['id_kasir']               = $id_kasir;
              $barang['id_detail_kasir_produk'] = '0';
              $barang['id_barang']              = $tabel_barang[$i];
              $barang['jumlah']                 = $tabel_jumlah[$i];
              $barang['id_satuan']              = $tabel_satuan2[$i];
              $barang['harga']                  = $tabel_harga[$i];
              $barang['total']                  = $tabel_total[$i];
              $barang['id_log_stok']            = $tabel_idlog[$i];
              DB::table('tbl_kasir_detail')->where(array('id_detail_kasir' => $tabel_id[$i]))->update($barang);
          }

          }
          $total_pembayaran += $tabel_total[$i];
        //   $total_poin += $tabel_poin[$i];
        }
        
        $total_pembayaran = $total_pembayaran;
        // $total_poin = $total_poin;
        $nominal = DB::table('m_nominal_poin')->limit(1)->first()->nominal;
        $cek_poin = floor($total_pembayaran / $nominal);
        if($cek_poin > 0){
          $redeem['id_pelanggan'] = $pelanggan;
          $redeem['unit_masuk']   = $cek_poin;
          $redeem['unit_keluar']  = 0;
          $redeem['tanggal']      = tgl_full($request->get('tanggal'),'99');
          $redeem['status']       = 'PO1';
          $redeem['id_kasir']     = $id_kasir;
          DB::table('tbl_transaksi_poin')->where('id_kasir',$id_kasir)->update($redeem);
        }

        if(count($id_del2) > 0){
          $d_barang_stok = DB::table('tbl_kasir_detail')->where('id_detail_kasir_produk','0')->whereIn('id_detail_kasir', $id_del2)->get();
          foreach($d_barang_stok as $d){
             DB::table('tbl_log_stok')->where('log_stok_id', $d->id_log_stok)->delete();
          }
          DB::table('tbl_kasir_detail')->where('id_detail_kasir_produk','0')->whereIn('id_detail_kasir', $id_del2)->delete();
        }
        

        if(count($id_del) > 0){
          $d_produk_stok = DB::table('tbl_kasir_detail')->whereIn('id_detail_kasir_produk', $id_del)->get();
          foreach($d_produk_stok as $d){
             DB::table('tbl_log_stok')->where('log_stok_id', $d->id_log_stok)->delete();
          }
          DB::table('tbl_kasir_detail_produk')->whereIn('id_kasir_detail_produk', $id_del)->delete();
          DB::table('tbl_kasir_detail')->whereIn('id_detail_kasir_produk', $id_del)->delete();

        }
        /*if(count($id_del) > 0){
          $d_produk_stok = DB::table('tbl_kasir_detail as tkd')->leftjoin('tbl_kasir_detail_produk as tkdp','tkd.id_detail_kasir_produk','tkdp.id_kasir_detail_produk')->whereIn('tkd.id_detail_kasir_produk', $id_del['id_kasir_detail_produk']);
            if($d_produk_stok->count() > 0){
              $d_produk_stok = $d_produk_stok->whereIn('tkdp.id_produk',$id_del['id_produk'])->get();
              foreach ($d_produk_stok as $d){
                DB::table('tbl_log_stok')->where('log_stok_id',$d->id_log_stok)->delete();
                DB::table('tbl_kasir_detail_produk')->where('id_kasir_detail_produk', $d->id_detail_kasir_produk)->where('id_produk',$d->id_produk)->delete();
                DB::table('tbl_kasir_detail as tkd')->leftjoin('tbl_kasir_detail_produk as tkdp','tkd.id_detail_kasir_produk','tkdp.id_kasir_detail_produk')->where('tkd.id_detail_kasir_produk', $d->id_detail_kasir_produk)->where('tkdp.id_produk',$d->id_produk)->delete();
              }

            }           
          
        }*/
        trigger_log($id_kasir, "Mengubah Nota Penjualan Outlet", 2, url('kasir_edit/'.$id_kasir));
      }

      if ($this->agent->isMobile()) {
        // code...
        //return $this->cetak_laporan($produk_cetak[0]['id_kasir']);
        return $this->cetak_laporan($produk_cetak, 'Cetak Nota Penjualan Outlet Dari Mobile');
      }else {
        // code...
        return redirect('kasir');
      }
      }catch(\Throwable $th){
        $this->simpan_next($request->all());
      }

    }
    
    public function simpan_next($request){
      // TODO
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
      $data['tanggal']            = tgl_full($request->get('tanggal'),'99');
      $data['tanggal_tempo']      = tgl_full($request->get('tanggal_tempo'),'99');
      $data['tanggal_faktur']     = tgl_full($request->get('tanggal_faktur'),'99');
      // $data['no_faktur']          = $request->get('nomor');
    //   $data['no_faktur']          = $this->get_nomorfaktur($request->get('tanggal_faktur'), $request->get('gudang'));
      /*$data['id_pelanggan']     = $request->get('id_pelanggan');*/
      $data['id_pelanggan']       = $pelanggan;
      $data['uang_muka']          = $request->get('td_uangmuka');
      $data['ongkos_kirim']       = $request->get('td_ongkir');
      $data['carabayar']          = $request->get('carabayar');
      $data['metodebayar']        = $request->get('viabayar');
      $data['total_potongan']     = $request->get('td_diskon');
      $data['total_tagihan']      = $request->get('td_total');
      $data['keterangan']         = $request->get('keterangan');
      $data['status']             = '1';
      $data['jenis_transaksi']    = '1';
      //$data['paper']            = $request->get('paper');
      $data['id_gudang']          = $request->get('gudang');
      $data['metodebayar2']       = $request->get('viabayar2');
      $data['total_metodebayar']  = $request->get('total_viabayar');
      $data['total_metodebayar2'] = $request->get('total_viabayar2');
      $data['status_promo']       = $request->get('td_statuspromo');

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
      $tabel_poin     = $request->get('tabel_poin');
      $tabel_produk_sebelum = $request->get('tabel_idproduk_sebelum');

      //promo
      $tabel_potongan       = $request->get('tabel_potongan');
      $tabel_potongan_total = $request->get('tabel_potongan_total');
      //$produk_cetak = array();
      $produk_cetak = "";
      try{
      if($id_kasir == ''){
        //TODO
        //created by
        $data['no_faktur']          = $this->get_nomorfaktur($request->get('tanggal_faktur'), $request->get('gudang'));
        $data['created_by']         = Auth::user()->name;
        $data['created_iduser']     = Auth::user()->id; 
        $id = DB::table('tbl_kasir')->insertGetId($data);
        $produk_cetak = $id;
        $total_pembayaran = 0;
        $total_poin = 0;
        for($i=0;$i<count($tabel_id);$i++){
          if($tabel_status[$i] == '1'){
          $produk['id_kasir'] = $id;
          $produk['id_produk']      = $tabel_produk[$i];
          $produk['jumlah']         = $tabel_jumlah[$i];
          $produk['id_satuan']      = $tabel_satuan;
          $produk['harga']          = $tabel_harga[$i];
          $produk['total']          = $tabel_total[$i];
          $porduk['poin']           = $tabel_poin[$i];
          $produk['total_poin']     = $tabel_poin[$i]*$tabel_jumlah[$i];
          $produk['potongan']       = $tabel_potongan[$i];
          $produk['potongan_total'] = $tabel_potongan_total[$i];
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
            $input['status']        = 'J1';
            $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);

            $barang['id_kasir']               = $id;
            $barang['id_detail_kasir_produk'] = $id_kasir_detail_produk;
            $barang['id_barang']              = $d->id_barang;
            $barang['jumlah']                 = $d->jumlah*$tabel_jumlah[$i];
            $barang['id_satuan']              = $d->id_satuan;
            $barang['harga']                  = '0';
            $barang['total']                  = '0';
            $barang['id_log_stok']            = $id_log_stok;
            DB::table('tbl_kasir_detail')->insert($barang);
          }

          }else if($tabel_status[$i] == '2'){
            $input['id_barang']     = $tabel_barang[$i];
            $input['unit_masuk']    = "0";
            $input['unit_keluar']   = $tabel_jumlah[$i];
            $input['id_ref_gudang'] = $request->get('gudang');
            $input['id_satuan']     = $tabel_satuan2[$i];
            $input['tanggal']       = tgl_full($request->get('tanggal'),'99');
            $input['status']        = 'J1';
            $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);

            $barang['id_kasir']               = $id;
            $barang['id_detail_kasir_produk'] = '0';
            $barang['id_barang']              = $tabel_barang[$i];
            $barang['jumlah']                 = $tabel_jumlah[$i];
            $barang['id_satuan']              = $tabel_satuan2[$i];
            $barang['harga']                  = $tabel_harga[$i];
            $barang['total']                  = $tabel_total[$i];
            $barang['id_log_stok']            = $id_log_stok;
            DB::table('tbl_kasir_detail')->insert($barang);
          }
          
          $total_pembayaran += $tabel_total[$i];
          $total_poin += $tabel_poin[$i]; 

        }
        
        $total_pembayaran = $total_pembayaran;
        $total_poin = $total_poin;
        $nominal = DB::table('m_nominal_poin')->limit(1)->first()->nominal;
        $cek_poin = floor($total_pembayaran / $nominal);
        // dd($total_poin);
        if($cek_poin > 0){
          $redeem['id_pelanggan'] = $pelanggan;
          $redeem['unit_masuk']   = $cek_poin;
          $redeem['unit_keluar']  = 0;
          $redeem['tanggal']      = tgl_full($request->get('tanggal'),'99');
          $redeem['status']       = 'PO1';
          $redeem['id_kasir']     = $id;
          DB::table('tbl_transaksi_poin')->insert($redeem);
        }
        trigger_log($id, "Menambahkan Nota Penjualan Outlet", 1, url('kasir_edit/'.$id));
      }else{
        //TODO
        //updated by
        $data['no_faktur']          = $request->get('nomor');
        $data['updated_by']         = Auth::user()->name;
        $data['updated_iduser']     = Auth::user()->id; 
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
        $d_barang = DB::table('tbl_kasir_detail')->where('id_kasir',$id_kasir)->where('id_detail_kasir_produk','0');
        foreach($d_produk->get() as $key => $d){
          $id_store[] = $d->id_kasir_detail_produk;
          $id_store_produk[] = $d->id_produk;
          $id_storeproduk[$key]['id_kasir_detail_produk'] = $d->id_kasir_detail_produk;
          $id_storeproduk[$key]['id_produk'] = $d->id_produk;
        }

        foreach($d_barang->get() as $d){
          $id_store2[] = $d->id_detail_kasir;
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
        foreach($id_store2 as $d){
                if(count($tabel_id) > 0){
                    if(!in_array($d, $tabel_id)){
                        $id_del2[] = $d;
                    }
                }else{
                    $id_del2[] = $d;
                }
        }
        $produk_cetak = $id_kasir;
        $total_pembayaran = 0;
        $total_poin = 0;
        for($i=0;$i<count($tabel_id);$i++){
          $produk['id_kasir'] = $id_kasir;
          $produk['id_produk']    = $tabel_produk[$i];
          $produk['jumlah']       = $tabel_jumlah[$i];
          $produk['id_satuan']    = $tabel_satuan;
          $produk['harga']        = $tabel_harga[$i];
          $produk['total']        = $tabel_total[$i];
          $produk['poin']         = $tabel_poin[$i];
          $produk['total_poin']   = $tabel_poin[$i]*$tabel_jumlah[$i];
          //$produk_cetak[] = $produk;

          //promo
          $produk['potongan']       = $tabel_potongan[$i];
          $produk['potongan_total'] = $tabel_potongan_total[$i];

          if($tabel_id[$i] == ''){
            if($tabel_status[$i] == '1'){

            $id_kasir_detail_produk = DB::table('tbl_kasir_detail_produk')->insertGetId($produk);
            $d_barang = DB::table('m_detail_produk as mdp')->join('tbl_barang as tb','mdp.id_barang','tb.barang_id')->where('id_produk',$tabel_produk[$i])->select(DB::raw('mdp.*,tb.satuan_id as id_satuan'));

            foreach($d_barang->get() as $d){
              $input['id_barang']     = $d->id_barang;
              $input['unit_masuk']    = "0";
              $input['unit_keluar']   = $d->jumlah*$tabel_jumlah[$i];
              $input['id_ref_gudang'] = $request->get('gudang');
              $input['id_satuan']     = $d->id_satuan;
              $input['tanggal']       = tgl_full($request->get('tanggal'),'99');
              $input['status']        = 'J1';
              $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);

              $barang['id_kasir']               = $id_kasir;
              $barang['id_detail_kasir_produk'] = $id_kasir_detail_produk;
              $barang['id_barang']              = $d->id_barang;
              $barang['jumlah']                 = $d->jumlah*$tabel_jumlah[$i];
              $barang['id_satuan']              = $d->id_satuan;
              $barang['harga']                  = '0';
              $barang['total']                  = '0';
              $barang['id_log_stok']            = $id_log_stok;
              DB::table('tbl_kasir_detail')->insert($barang);
            }

            }else if($tabel_status[$i] == '2'){
              $input['id_barang']     = $tabel_barang[$i];
              $input['unit_masuk']    = "0";
              $input['unit_keluar']   = $tabel_jumlah[$i];
              $input['id_ref_gudang'] = $request->get('gudang');
              $input['id_satuan']     = $tabel_satuan2[$i];
              $input['tanggal']       = tgl_full($request->get('tanggal'),'99');
              $input['status']        = 'J1';
              $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);

              $barang['id_kasir']               = $id_kasir;
              $barang['id_detail_kasir_produk'] = '0';
              $barang['id_barang']              = $tabel_barang[$i];
              $barang['jumlah']                 = $tabel_jumlah[$i];
              $barang['id_satuan']              = $tabel_satuan2[$i];
              $barang['harga']                  = $tabel_harga[$i];
              $barang['total']                  = $tabel_total[$i];
              $barang['id_log_stok']            = $id_log_stok;
              DB::table('tbl_kasir_detail')->insert($barang);
            }


          }else if($tabel_id[$i] != ''){
            if($tabel_status[$i] == '1'){
            DB::table('tbl_kasir_detail_produk')->where(array('id_kasir_detail_produk' => $tabel_id[$i]))->update($produk);
            
            if($tabel_produk[$i] != $tabel_produk_sebelum[$i]){
              $d_barang_cek = DB::table('tbl_kasir_detail')->where('id_detail_kasir_produk',$tabel_id[$i]);
              foreach ($d_barang_cek->get() as $key => $value) {
                # code...
                DB::table('tbl_log_stok')->where('log_stok_id',$value->id_log_stok)->delete();
              }
              DB::table('tbl_kasir_detail')->where('id_detail_kasir_produk',$tabel_id[$i])->delete();
              $d_barang = DB::table('m_detail_produk as mdp')->join('tbl_barang as tb','mdp.id_barang','tb.barang_id')->where('mdp.id_produk',$tabel_produk[$i])->select(DB::raw('mdp.*,tb.satuan_id as id_satuan'));
              foreach($d_barang->get() as $d){
                $input['id_barang']     = $d->id_barang;
                $input['unit_masuk']    = "0";
                $input['unit_keluar']   = $d->jumlah*$tabel_jumlah[$i];
                $input['id_ref_gudang'] = $request->get('gudang');
                $input['id_satuan']     = $d->id_satuan;
                $input['tanggal']       = tgl_full($request->get('tanggal'),'99');
                $input['status']        = 'J1';
                $id_log_stok = DB::table('tbl_log_stok')->insertGetId($input);

                $barang['id_kasir']               = $id_kasir;
                $barang['id_detail_kasir_produk'] = $tabel_id[$i];
                $barang['id_barang']              = $d->id_barang;
                $barang['jumlah']                 = $d->jumlah*$tabel_jumlah[$i];
                $barang['id_satuan']              = $d->id_satuan;
                $barang['harga']                  = '0';
                $barang['total']                  = '0';
                $barang['id_log_stok']            = $id_log_stok;
                // $barang['status_redeem']          = $tabel_status_redeem[$i];
                DB::table('tbl_kasir_detail')->insert($barang);
              }
              }else{
              $d_barang = DB::table('tbl_kasir_detail')->where('id_detail_kasir_produk',$tabel_id[$i]);
              foreach($d_barang->get() as $d){              
                $d_jumlah = DB::table('m_detail_produk')->where('id_produk',$tabel_produk[$i])
                        ->where('id_barang',$d->id_barang);
                $jumlah = 0;
                if($d_jumlah->count() > 0){
                  $jumlah = (int)$d_jumlah->first()->jumlah;
                }
                
                $barang['id_kasir']               = $id_kasir;
                $barang['id_detail_kasir_produk'] = $tabel_id[$i];
                $barang['id_barang']              = $d->id_barang;
                $barang['jumlah']                 = $jumlah*$tabel_jumlah[$i];
                $barang['id_satuan']              = $d->id_satuan;
                $barang['harga']                  = $d->harga;
                $barang['total']                  = $d->jumlah*$d->harga;
                $barang['id_log_stok']            = $d->id_log_stok;
                DB::table('tbl_kasir_detail')->where(array('id_detail_kasir'=> $d->id_detail_kasir))->update($barang);

                $input['id_barang']     = $d->id_barang;
                $input['unit_masuk']    = "0";
                $input['unit_keluar']   = $jumlah*$tabel_jumlah[$i];
                $input['id_ref_gudang'] = $request->get('gudang');
                $input['id_satuan']     = $d->id_satuan;
                $input['tanggal']       = tgl_full($request->get('tanggal'),'99');
                $input['status']        = 'J1';
                DB::table('tbl_log_stok')->where('log_stok_id',$d->id_log_stok)->update($input);

              }
              }
              
          }else if($tabel_status[$i] == '2'){
              $input['id_barang']     = $tabel_barang[$i];
              $input['unit_masuk']    = "0";
              $input['unit_keluar']   = $tabel_jumlah[$i];
              $input['id_ref_gudang'] = $request->get('gudang');
              $input['id_satuan']     = $tabel_satuan2[$i];
              $input['tanggal']       = tgl_full($request->get('tanggal'),'99');
              $input['status']        = 'J1';
              DB::table('tbl_log_stok')->where(array('log_stok_id' => $tabel_idlog[$i]))->update($input);

              $barang['id_kasir']               = $id_kasir;
              $barang['id_detail_kasir_produk'] = '0';
              $barang['id_barang']              = $tabel_barang[$i];
              $barang['jumlah']                 = $tabel_jumlah[$i];
              $barang['id_satuan']              = $tabel_satuan2[$i];
              $barang['harga']                  = $tabel_harga[$i];
              $barang['total']                  = $tabel_total[$i];
              $barang['id_log_stok']            = $tabel_idlog[$i];
              DB::table('tbl_kasir_detail')->where(array('id_detail_kasir' => $tabel_id[$i]))->update($barang);
          }

          }
          $total_pembayaran += $tabel_total[$i];
        //   $total_poin += $tabel_poin[$i];
        }
        
        $total_pembayaran = $total_pembayaran;
        // $total_poin = $total_poin;
        $nominal = DB::table('m_nominal_poin')->limit(1)->first()->nominal;
        $cek_poin = floor($total_pembayaran / $nominal);
        if($cek_poin > 0){
          $redeem['id_pelanggan'] = $pelanggan;
          $redeem['unit_masuk']   = $cek_poin;
          $redeem['unit_keluar']  = 0;
          $redeem['tanggal']      = tgl_full($request->get('tanggal'),'99');
          $redeem['status']       = 'PO1';
          $redeem['id_kasir']     = $id_kasir;
          DB::table('tbl_transaksi_poin')->where('id_kasir',$id_kasir)->update($redeem);
        }

        if(count($id_del2) > 0){
          $d_barang_stok = DB::table('tbl_kasir_detail')->where('id_detail_kasir_produk','0')->whereIn('id_detail_kasir', $id_del2)->get();
          foreach($d_barang_stok as $d){
             DB::table('tbl_log_stok')->where('log_stok_id', $d->id_log_stok)->delete();
          }
          DB::table('tbl_kasir_detail')->where('id_detail_kasir_produk','0')->whereIn('id_detail_kasir', $id_del2)->delete();
        }
        

        if(count($id_del) > 0){
          $d_produk_stok = DB::table('tbl_kasir_detail')->whereIn('id_detail_kasir_produk', $id_del)->get();
          foreach($d_produk_stok as $d){
             DB::table('tbl_log_stok')->where('log_stok_id', $d->id_log_stok)->delete();
          }
          DB::table('tbl_kasir_detail_produk')->whereIn('id_kasir_detail_produk', $id_del)->delete();
          DB::table('tbl_kasir_detail')->whereIn('id_detail_kasir_produk', $id_del)->delete();

        }
        
        trigger_log($id_kasir, "Mengubah Nota Penjualan Outlet", 2, url('kasir_edit/'.$id_kasir));

      }

      if ($this->agent->isMobile()) {
        // code...
        //return $this->cetak_laporan($produk_cetak[0]['id_kasir']);
        return $this->cetak_laporan($produk_cetak, 'Cetak Nota Penjualan Outlet Dari Mobile');
      }else {
        // code...
        return redirect('kasir');
      }

      }catch(\Throwable $th){
        $this->simpan($request->all());
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

      DB::table('tbl_kasir')->where(array('id_kasir' => $id))->delete();
      DB::table('tbl_kasir_detail_produk')->where(array('id_kasir' => $id))->delete();
      DB::table('tbl_kasir_detail')->where(array('id_kasir' => $id))->delete();
      
      DB::table('tbl_kasir_batal')->where('id_kasir', $id)->update(['catatan' => $request->catatan, 'deleted_iduser' => Auth::id()]);
      trigger_log($id, "Membatalkan Nota Penjualan Outlet", 3);
    }

    public function get_edit(Request $request){
      $id = $request->get('id');
      $d_data = DB::table('tbl_kasir_detail_produk as tpd')->leftjoin('m_produk as mp','tpd.id_produk','mp.id')->leftjoin('tbl_satuan as ts2','tpd.id_satuan','ts2.satuan_id')->where('tpd.id_kasir',$id)->select('tpd.*','mp.id_type_ukuran','mp.nama as nama_produk','ts2.satuan_nama as nama_satuan','ts2.satuan_satuan as satuan_satuan')->orderBy('tpd.id_kasir_detail_produk','asc');
      $d_barang = DB::table('tbl_kasir_detail as tkd')->leftjoin('tbl_barang as tb','tkd.id_barang','tb.barang_id')->leftjoin('tbl_satuan as ts2','tkd.id_satuan','ts2.satuan_id')->where('tkd.id_kasir',$id)->where('tkd.id_detail_kasir_produk','0')->select('tkd.*','tb.barang_nama as nama_barang','tb.barang_kode as kode_barang','tb.barang_alias as alias_barang','ts2.satuan_nama as nama_satuan','ts2.satuan_satuan as satuan_satuan')->orderBy('tkd.id_detail_kasir','asc');
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
                            'harga'         => $d->harga,
                            'total'         => $d->total,
                            'poin'          => $d->poin,
                            'potongan'      => ($d->harga == 0) ? 0:$d->potongan,
                            'potongan_total'=> ($d->harga == 0) ? 0:$d->potongan_total,
                            'id_tipe'       => $d->id_type_ukuran ?? '',
                            'status'        => "1",
                            'status_redeem' => (empty($d->status_redeem)?'0':$d->status_redeem));
        }
      }else{
        $arr['produk'] = array();
      }

      if($d_barang->count() > 0){
        foreach($d_barang->get() as $d){
            $arr['barang'][] = array('id' => $d->id_detail_kasir,
                            'id_kasir'  => $d->id_kasir,
                            'id_log_stok'  => $d->id_log_stok,
                            'id_produk'     => '',
                            'nama_produk'   => '',
                            'id_barang'     => $d->id_barang,
                            'nama_barang'   => $d->nama_barang,
                            'kode_barang'   => $d->kode_barang,
                            'alias_barang'  => $d->alias_barang,
                            'jumlah'        => $d->jumlah,
                            'id_satuan'     => $d->id_satuan,
                            'nama_satuan'   => $d->satuan_satuan,
                            'satuan_satuan' => $d->satuan_satuan,
                            'harga'         => $d->harga,
                            'total'         => $d->total,
                            'poin'          => "0",
                            'potongan'      => "0",
                            'potongan_total'=> "0",
                            'id_tipe'       => "",
                            'status'        => "2",
                            'status_redeem' => (empty($d->status_redeem)?'0':$d->status_redeem));
        }
      }else{
        $arr['barang'] = array();
      }

      return response()->json($arr);
    }

    public function attr_pelanggan(Request $request){
      $id = $request->get('id');
      $arr = array();
      if(is_numeric($id)){
      /*$d_data = DB::select("SELECT id, CASE WHEN nama IS NULL THEN '' ELSE nama END AS nama,
                            CASE WHEN alamat IS NULL THEN '' ELSE alamat END AS alamat,
                            CASE WHEN telp IS NULL THEN '' ELSE telp END AS telp,
                            CASE WHEN no_member IS NULL THEN '' ELSE no_member END AS nomor,
                            CASE WHEN status IS NULL THEN '' ELSE status END status
                            FROM m_pelanggan WHERE id = '$id' AND mp.status_aktif = 1 AND mp.tanggal_akhir >= DATE(NOW())");*/
        $d_data = DB::SELECT("SELECT * FROM (
        SELECT mp.id, CASE WHEN mp.nama IS NULL THEN '' ELSE mp.nama END AS nama,
        CASE WHEN mp.alamat IS NULL THEN '' ELSE mp.alamat END AS alamat,
        CASE WHEN mp.telp IS NULL THEN '' ELSE mp.telp END AS telp,
        CASE WHEN mp.no_member IS NULL THEN '' ELSE mp.no_member END AS nomor,
        CASE WHEN mp.status IS NULL THEN '1' ELSE mp.status END status,
        CASE WHEN p.poin IS NULL THEN '0' ELSE p.poin END poin
        FROM m_pelanggan as mp 
        LEFT JOIN (
             SELECT tpp.id_pelanggan, CASE WHEN SUM(tpp.unit_masuk-tpp.unit_keluar) 
             THEN SUM(tpp.unit_masuk-tpp.unit_keluar) ELSE 0 END AS poin 
             FROM tbl_transaksi_poin as tpp 
             GROUP BY id_pelanggan
        )  AS p ON p.id_pelanggan=mp.id
        WHERE mp.id = '$id' AND (mp.status != 2 OR mp.status IS NULL) AND mp.status_aktif = 1
        UNION ALL
        SELECT mp.id, CASE WHEN mp.nama IS NULL THEN '' ELSE mp.nama END AS nama,
        CASE WHEN mp.alamat IS NULL THEN '' ELSE mp.alamat END AS alamat,
        CASE WHEN mp.telp IS NULL THEN '' ELSE mp.telp END AS telp,
        CASE WHEN mp.no_member IS NULL THEN '' ELSE mp.no_member END AS nomor,
        CASE WHEN mp.status IS NULL THEN '1' ELSE mp.status END status,
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
      foreach($d_data as $d){
            $arr[] = array('id_pelanggan' => $d->id,
                            'nama'        => $d->nama,
                            'alamat'      => $d->alamat,
                            'telp'        => $d->telp,
                            'nomor'       => $d->nomor,
                            'status'      => $d->status,
                            'poin'        => $d->poin);
      }
      }else{
      $arr[] = array('id_pelanggan'       => "",
                            'nama'        => "",
                            'alamat'      => "",
                            'telp'        => "",
                            'nomor'       => "",
                            'status'      => "1",
                            'poin'        => '0');
      }

      return response()->json($arr);
    }

    public function edit($id){
      $id_profil = Auth::user()->id_profil;
      $group = Auth::user()->group_id;
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
      /*$data['pelanggan']    = DB::select("select mp.*, rf.nama as nama_gudang from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id $where_gudang");*/
      $data['pelanggan']  = DB::select("select mp.id, CASE WHEN mp.telp IS NULL THEN mp.nama ELSE CONCAT(mp.nama,' (',mp.telp,')') END as nama, rf.nama as nama_gudang from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id $where_gudang");
      $data['gudang']       = DB::select(base_gudang($where));
    //   $data['pembayaran']   = DB::table('m_metode')->orderby('urutan','asc')->get();
      $data['pembayaran'] = DB::table('m_metode')->where('status','=','1')->orderby('urutan','asc')->get();
      /*if ($this->agent->isMobile()) {
        // code...
        return view('admin_mobile.kasir.create')->with('data',$data);
      }else {
        // code...
        return view('admin.kasir.create')->with('data',$data);
      }*/
      if ($this->agent->isMobile()) {
        // code...
        if($group == 1 || $group == 6){
          return view('admin_mobile.kasir.create')->with('data',$data);  
        }else{
          if($d_data->status_posting == 2){
            echo "<script>setTimeout(function() { alert('Penjualan Sudah Tidak Bisa dirubah!'); window.location= '".url('kasir')."' }, 1000)</script>";
          }else{
            return view('admin_mobile.kasir.create')->with('data',$data);
          }
        }
      }else {
        // code...
        if($group == 1 || $group == 6){
          return view('admin.kasir.create')->with('data',$data);
        }else{
          if($d_data->status_posting == 2){
            echo "<script>setTimeout(function() { alert('Penjualan Sudah Tidak Bisa dirubah!'); window.location= '".url('kasir')."' }, 1000)</script>";
          }else{
            return view('admin.kasir.create')->with('data',$data);
          }
        }
      }

    }

    public function detail($id){
      $id_profil = Auth::user()->id_profil;
      $group = Auth::user()->group_id;
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
      $data['pembayaran'] = DB::table('m_metode')->where('status','=','1')->orderby('urutan','asc')->get();
      if ($this->agent->isMobile()) {
        // code...
        return view('admin_mobile.kasir.detail')->with('data',$data);
      }else {
        // code...
        return view('admin.kasir.detail')->with('data',$data);
      }

    }

    public function listData(){
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
        $where_gudang = "WHERE rf.id IN ($gudang)";
       }
      $group_where = DB::table('tbl_group')->where('group_id',$group)->first();

      /*$kasir = KasirModel::with(['dataPelanggan'])->where('jenis_transaksi','1')->whereIn('id_gudang',$id_gudang)->orderBy('tanggal', 'DESC')->get();*/
      /*$kasir = DB::table('tbl_kasir as tk')->leftjoin('m_pelanggan as mp','tk.id_pelanggan','mp.id')->leftjoin('ref_gudang as rg','tk.id_gudang','rg.id')->where('tk.jenis_transaksi','1')->whereIn('tk.id_gudang',$id_gudang)->select(DB::raw('tk.*,mp.nama as nama_pelanggan,rg.nama as nama_gudang'))->orderBy('tk.tanggal','DESC')->get();*/
      /*$kasir = DB::SELECT("SELECT tk.*, mp.nama AS nama_pelanggan, rg.nama as nama_gudang 
                          FROM tbl_kasir AS tk 
                          LEFT JOIN m_pelanggan AS mp ON tk.id_pelanggan=mp.id
                          LEFT JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
                          WHERE tk.jenis_transaksi=1
                          AND tk.id_gudang IN ($gudang)                          
                          AND tk.tanggal > DATE_SUB(DATE(NOW()), INTERVAL 7 DAY) 
                          AND tk.tanggal <= DATE_SUB(DATE(NOW()), INTERVAL 0 DAY)
                          ORDER BY tk.tanggal DESC");*/
      $kasir = DB::SELECT("SELECT tk.*, mp.nama AS nama_pelanggan, mp.telp as telp_pelanggan, rg.nama as nama_gudang, tkp.jumlah_cetak 
                          FROM tbl_kasir AS tk 
                          LEFT JOIN m_pelanggan AS mp ON tk.id_pelanggan=mp.id
                          LEFT JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
                          LEFT JOIN (SELECT id_kasir, COUNT(id_print) as jumlah_cetak FROM tbl_kasir_print GROUP BY id_kasir) AS tkp ON tk.id_kasir = tkp.id_kasir
                          WHERE tk.jenis_transaksi=1
                          AND tk.id_gudang IN ($gudang)
                          ORDER BY tk.tanggal_faktur DESC LIMIT 500");
      $no = 0;
      $data = array();
        foreach ($kasir as $list){
            if($list->carabayar == '1'){
            $total_tagihan = 'Rp. 0';
            }else{
            // $total_tagihan = 'Rp '.format_angka($list->total_tagihan);
            $total_tagihan = 'Rp '.format_angka($list->total_tagihan-($list->total_potongan-$list->ongkos_kirim));
            }
            if($list->telp_pelanggan == null){
              $telp_pelanggan = "";
            }else{
              $telp_pelanggan = " (".$list->telp_pelanggan.")";
            }
            $input_user = $list->created_iduser;
            $no++;
            $row = array();
            if ($this->agent->isMobile()) {
              $row['no'] = $no;
              $row['tanggal'] = tgl_full($list->tanggal_faktur,'');
              $row['no_faktur'] = $list->no_faktur;
              $row['nama_pelanggan'] = $list->nama_pelanggan.$telp_pelanggan;
              $row['nama_gudang'] = $list->nama_gudang;
              /*$row[] = $list->dataPelanggan->nama;
              $row[] = $list->dataGudang->nama;*/
              // $row['total_tagihan'] = 'Rp '.format_angka($list->total_tagihan);
              $row['total_tagihan'] = 'Rp '.format_angka($list->total_tagihan-($list->total_potongan-$list->ongkos_kirim));
              //$row[] = $total_tagihan;
              /*$row[] = '<div class="btn-group"><a  href="'.url('kasir_cetak/'.$list->id_kasir).'" class="btn btn-xs btn-warning" data-toggle="tooltip" data-placement="botttom" title="Print Data"  style="color:white;" target="_blank"><i class="fa  fa-print"></i></a><a  href="'.url('kasir_edit/'.$list->id_kasir).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
              <a onclick="deleteData('.$list->id_kasir.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';*/
              if($group == 1) $row['jumlah_cetak'] = $list->jumlah_cetak ?? 0;
              $row['posting'] = $this->posting($list->status_posting,$list->id_kasir, $id_user, $group_where->group_aktif, $input_user);
            }else {
              $row[] = $no;
              $row[] = tgl_full($list->tanggal_faktur,'');
              $row[] = $list->no_faktur;
              $row[] = $list->nama_pelanggan.$telp_pelanggan;
              $row[] = $list->nama_gudang;
              /*$row[] = $list->dataPelanggan->nama;
              $row[] = $list->dataGudang->nama;*/
              // $row[] = 'Rp '.format_angka($list->total_tagihan);
              $row[] = 'Rp '.format_angka($list->total_tagihan-($list->total_potongan-$list->ongkos_kirim));
              //$row[] = $total_tagihan;
              /*$row[] = '<div class="btn-group"><a  href="'.url('kasir_cetak/'.$list->id_kasir).'" class="btn btn-xs btn-warning" data-toggle="tooltip" data-placement="botttom" title="Print Data"  style="color:white;" target="_blank"><i class="fa  fa-print"></i></a><a  href="'.url('kasir_edit/'.$list->id_kasir).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
              <a onclick="deleteData('.$list->id_kasir.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';*/
              if($group == 1) $row[] = $list->jumlah_cetak ?? 0;
              $row[] = $this->posting($list->status_posting,$list->id_kasir, $id_user, $group_where->group_aktif, $input_user);
            }
            $data[] = $row;
        }

      $output = array("data" => $data);
      return response()->json($output);
    }

    public function searchtanggal(Request $request){        
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
          $where_gudang = "WHERE rf.id IN ($gudang)";
        }

        $group_where = DB::table('tbl_group')->where('group_id',$group)->first();

        $tanggalrange = explode('s.d.',$request->get('tanggal'));
        $tanggal_start  = tgl_full($tanggalrange[0],99);
        $tanggal_end    = tgl_full($tanggalrange[1],99);
        $kasir = DB::SELECT("SELECT tk.*, mp.nama AS nama_pelanggan, rg.nama as nama_gudang, tkp.jumlah_cetak  
                          FROM tbl_kasir AS tk 
                          LEFT JOIN m_pelanggan AS mp ON tk.id_pelanggan=mp.id
                          LEFT JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
                          LEFT JOIN (SELECT id_kasir, COUNT(id_print) as jumlah_cetak FROM tbl_kasir_print GROUP BY id_kasir) AS tkp ON tk.id_kasir = tkp.id_kasir
                          WHERE tk.jenis_transaksi=1
                          AND tk.id_gudang IN ($gudang)
                          AND tk.tanggal_faktur >= '$tanggal_start' 
                          AND tk.tanggal_faktur <= '$tanggal_end'
                          ORDER BY tk.tanggal_faktur DESC");
      $no = 0;
      $data = array();
        foreach ($kasir as $list){
            if($list->carabayar == '1'){
            $total_tagihan = 'Rp. 0';
            }else{
            // $total_tagihan = 'Rp '.format_angka($list->total_tagihan);
            $total_tagihan = 'Rp '.format_angka($list->total_tagihan-($list->total_potongan-$list->ongkos_kirim));
            }
            $input_user = $list->created_iduser;
            $no++;
            $row = array();
            if ($this->agent->isMobile()) {
              $row['no'] = $no;
              $row['tanggal'] = tgl_full($list->tanggal_faktur,'');
              $row['no_faktur'] = $list->no_faktur;
              $row['nama_pelanggan'] = $list->nama_pelanggan;
              $row['nama_gudang'] = $list->nama_gudang;
              // $row['total_tagihan'] = 'Rp '.format_angka($list->total_tagihan);
              $row['total_tagihan'] = 'Rp '.format_angka($list->total_tagihan-($list->total_potongan-$list->ongkos_kirim));
              if($group == 1) $row['jumlah_cetak'] = $list->jumlah_cetak ?? 0;
              $row['posting'] = $this->posting($list->status_posting,$list->id_kasir,$id_user,$group_where->group_aktif, $input_user);
            }else {
              $row[] = $no;
              $row[] = tgl_full($list->tanggal_faktur,'');
              $row[] = $list->no_faktur;
              $row[] = $list->nama_pelanggan;
              $row[] = $list->nama_gudang;
              // $row[] = 'Rp '.format_angka($list->total_tagihan);
              $row[] = 'Rp '.format_angka($list->total_tagihan-($list->total_potongan-$list->ongkos_kirim));
              if($group == 1) $row[] = $list->jumlah_cetak ?? 0;
              $row[] = $this->posting($list->status_posting,$list->id_kasir,$id_user,$group_where->group_aktif, $input_user);
            }
            $data[] = $row;
        }

      $output = array("data" => $data);
      return response()->json($output);


    }

    public function laporan(){
      $file= public_path(). "/Detail_PenjualanPerPelanggan_periode_01_09_2019 s_d 03_10_2019.pdf";

      $headers = array(
                'Content-Type: application/pdf',
              );

      return Response::download($file, 'filename.pdf', $headers);
    }
    
    /*public function posting($status, $id, $id_user, $status_edit, $input_user){
      $id_group = Auth::user()->group_id;
      switch (true) {
        case ($status == '1'&& $status_edit == '2'):
          if($id_group == 8){ //TODO
            if($id_user == $input_user){
              $html = '<div class="btn-group"><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('kasir_edit/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
                <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa fa-trash"></i></a>
                </div>';
            }else{
              $html = '<div class="btn-group"><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('kasir_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a></div>';  
            }
          }else{
            $html = '<div class="btn-group"><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('kasir_edit/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
            <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa fa-trash"></i></a>
            </div>';
          }
          break;
        case ($status == '2' && $status == '2'):
          $html = '<div class="btn-group"><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('kasir_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a></div>';
          break;
        case ($status == '1'&& $status_edit == '1'):
          $html = '<div class="btn-group"><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('kasir_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a></div>';
          break;
        case ($status == '2' && $status == '1'):
          $html = '<div class="btn-group"><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('kasir_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a></div>';
          break;
        default:
          if($id_group == 8){
            if($id_user == $input_user){
              $html = '<div class="btn-group"><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('kasir_edit/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
                <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa fa-trash"></i></a>
                </div>';
            }else{
              $html = '<div class="btn-group"><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('kasir_edit/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
                </div>';  
            }
          }else{
            $html = '<div class="btn-group"><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('kasir_edit/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
              <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa fa-trash"></i></a>
              </div>';
          }
          break;
      }

      return $html;
    }*/
    
    public function posting($status, $id, $id_user, $status_edit, $input_user){
      $id_group = Auth::user()->group_id;
      switch (true) {
        case ($status == '1'&& $status_edit == '2'):
          if($id_group == 8){ //TODO
            if($id_user == $input_user){
              $html = '<div class="btn-group"><a  href="'.url('kasir_retur/'.$id).'" class="btn btn-xs btn-default" data-toggle="tooltip" data-placement="botttom" title="Retur Data"><i class="fa fa-refresh"></i></a><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a>
                <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Pembatalan Nota" style="color:white;"><i class="fa fa-trash"></i></a>
                </div>';
            }else{
              $html = '<div class="btn-group"><a  href="'.url('kasir_retur/'.$id).'" class="btn btn-xs btn-default" data-toggle="tooltip" data-placement="botttom" title="Retur Data"><i class="fa fa-refresh"></i></a><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('kasir_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a></div>';  
            }
          }else if($id_group == 1){
            $html = '<div class="btn-group"><a  href="'.url('kasir_retur/'.$id).'" class="btn btn-xs btn-default" data-toggle="tooltip" data-placement="botttom" title="Retur Data"><i class="fa fa-refresh"></i></a><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('kasir_edit/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
            <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Pembatalan Nota" style="color:white;"><i class="fa fa-trash"></i></a>
            </div>';
          }else{
            $html = '<div class="btn-group"><a  href="'.url('kasir_retur/'.$id).'" class="btn btn-xs btn-default" data-toggle="tooltip" data-placement="botttom" title="Retur Data"><i class="fa fa-refresh"></i></a><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a>
            <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Pembatalan Nota" style="color:white;"><i class="fa fa-trash"></i></a>
            </div>';
          }
          break;
        case ($status == '2' && $status_edit == '2'):
          $html = '<div class="btn-group"><a  href="'.url('kasir_retur/'.$id).'" class="btn btn-xs btn-default" data-toggle="tooltip" data-placement="botttom" title="Retur Data"><i class="fa fa-refresh"></i></a><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('kasir_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a></div>';
          break;
        case ($status == '1'&& $status_edit == '1'):
          $html = '<div class="btn-group"><a  href="'.url('kasir_retur/'.$id).'" class="btn btn-xs btn-default" data-toggle="tooltip" data-placement="botttom" title="Retur Data"><i class="fa fa-refresh"></i></a><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('kasir_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a></div>';
          break;
        case ($status == '2' && $status_edit == '1'):
          $html = '<div class="btn-group"><a  href="'.url('kasir_retur/'.$id).'" class="btn btn-xs btn-default" data-toggle="tooltip" data-placement="botttom" title="Retur Data"><i class="fa fa-refresh"></i></a><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('kasir_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a></div>';
          break;
        default:
          if($id_group == 8){
            if($id_user == $input_user){
              $html = '<div class="btn-group"><a  href="'.url('kasir_retur/'.$id).'" class="btn btn-xs btn-default" data-toggle="tooltip" data-placement="botttom" title="Retur Data"><i class="fa fa-refresh"></i></a><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a>
                <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Pembatalan Nota" style="color:white;"><i class="fa fa-trash"></i></a>
                </div>';
            }else{
              $html = '<div class="btn-group"><a  href="'.url('kasir_retur/'.$id).'" class="btn btn-xs btn-default" data-toggle="tooltip" data-placement="botttom" title="Retur Data"><i class="fa fa-refresh"></i></a><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a>
                </div>';  
            }
          }else if($id_group == 1){
            $html = '<div class="btn-group"><a  href="'.url('kasir_retur/'.$id).'" class="btn btn-xs btn-default" data-toggle="tooltip" data-placement="botttom" title="Retur Data"><i class="fa fa-refresh"></i></a><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('kasir_edit/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
            <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Pembatalan Nota" style="color:white;"><i class="fa fa-trash"></i></a>
            </div>';
          }else{
            $html = '<div class="btn-group"><a  href="'.url('kasir_retur/'.$id).'" class="btn btn-xs btn-default" data-toggle="tooltip" data-placement="botttom" title="Retur Data"><i class="fa fa-refresh"></i></a><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a>
              <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Pembatalan Nota" style="color:white;"><i class="fa fa-trash"></i></a>
              </div>';
          }
          break;
      }

      return $html;
    }
    
    /*public function posting($status, $id, $id_user, $status_edit){
      $id_group = Auth::user()->group_id;
    //   <a  href="'.url('kasir_cetak/'.$id).'" class="btn btn-xs btn-warning" data-toggle="tooltip" data-placement="botttom" title="Print Data"  style="color:white;" target="_blank"><i class="fa  fa-print"></i></a>
      $html = '';
      switch (true) {
        case ($status == '1'&&$status_edit == '2'):
        //   if($id_user == 4){
          if($id_group == 1){
            $html .= '<a  href="'.url('kasir_edit/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>';
          }
          // <a  href="'.url('kasir_edit/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>
          $html .= '<div class="btn-group"><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a>
            <a  href="'.url('kasir_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a>
            <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Batal Data" style="color:white;"><i class="fa fa-trash"></i></a>
            </div>';
          break;
        case ($status == '2' && $status == '2'):
          $html .= '<div class="btn-group"><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('kasir_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a></div>';
          break;
        case ($status == '1'&&$status_edit == '1'):
          $html .= '<div class="btn-group"><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('kasir_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a></div>';
          break;
        case ($status == '2' && $status == '1'):
          $html .= '<div class="btn-group"><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a><a  href="'.url('kasir_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a></div>';
          break;
        default:
        //   if($id_user == 4){
          if($id_group == 1){
            $html .= '<a  href="'.url('kasir_edit/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Edit Data"  style="color:white;"><i class="fa  fa-edit"></i></a>';
          }
          $html .= '<div class="btn-group"><a href="javascript:;" class="btn btn-xs btn-warning btn_infocetak" id="btn_infocetak" title="Print Data"  style="color:white;" data-user="'.$id_user.'" data-kasir="'.$id.'"><i class="fa fa-print"></i></a>
            <a  href="'.url('kasir_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a>
            <a onclick="deleteData('.$id.')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa fa-trash"></i></a>
            </div>';
          break;
      }

      return $html;
    }*/

    function cetak_laporan($id, $keterangan = 'Cetak Nota Penjualan Outlet Dari Mobile'){
      if ($this->agent->isMobile()) {
        $data = DB::table('tbl_kasir as tk')->join('m_pelanggan as mp','tk.id_pelanggan','mp.id')->join('ref_gudang as rg','tk.id_gudang','rg.id')->leftjoin('m_metode as mm','tk.metodebayar','mm.id')->leftjoin('m_metode as mm2','tk.metodebayar2','mm2.id')->where('id_kasir',$id)->select(DB::raw('tk.*,mp.nama as nama_pelanggan,rg.nama as nama_gudang, rg.alamat as alamat_gudang, mm.nama as nama_metodebayar, mm2.nama as nama_metodebayar2'))->first();
        $detail = DB::table('tbl_kasir_detail_produk as tkp')->join('m_produk as mp','tkp.id_produk','mp.id')->join('tbl_satuan as ts','tkp.id_satuan','ts.satuan_id')->where('tkp.id_kasir','=',$id)->select(DB::raw('tkp.*,mp.kode_produk as kode_produk,mp.nama as nama_produk,ts.satuan_nama as nama_satuan,ts.satuan_satuan as inisial_satuan'))->get();
        $barang  = DB::table('tbl_kasir_detail as tkd')->join('tbl_barang as tb','tkd.id_barang','tb.barang_id')->join('tbl_satuan as ts','tkd.id_satuan','ts.satuan_id')->where('tkd.id_kasir','=',$id)->where('tkd.id_detail_kasir_produk','=',0)->select(DB::raw('tkd.*,tb.barang_kode as kode_barang,tb.barang_nama as nama_barang, tb.barang_alias as alias,ts.satuan_nama as nama_satuan,ts.satuan_satuan as inisial_satuan'))->get();
        return view('admin_mobile.kasir.struk_kasir',compact('data','detail','barang', 'keterangan'));
        exit();
      }else {
        // code...
        require(public_path('fpdf1813/Mc_table.php'));
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
        $html = view('admin.kasir.CetakPenjualanOutlet')->with('data',$data);
        // trigger_log($id, "Cetak Penjualan Outlet", 7);
        
        $print['id_kasir'] = $id;
        $print['id_user'] = Auth::id();
        $print['keterangan'] = $keterangan;
        DB::table('tbl_kasir_print')->insert($print);
        
        return response($html)->header('Content-Type', 'application/pdf');
      }

    }

    /*public function cetak_laporan2($id)
  {
    $connector = new WindowsPrintConnector("POS-58AA");
    $printer = new Printer($connector);
    $data_detail_first = DB::table('tbl_kasir as tk')->join('m_pelanggan as mp','tk.id_pelanggan','mp.id')->join('ref_gudang as rg','tk.id_gudang','rg.id')->where('id_kasir',$id)->select(DB::raw('tk.*,mp.nama as nama_pelanggan,rg.nama as nama_gudang, rg.alamat as alamat_gudang'))->first();
    $data_detail_trx = DB::table('tbl_kasir_detail_produk as tkp')->join('m_produk as mp','tkp.id_produk','mp.id')->join('tbl_satuan as ts','tkp.id_satuan','ts.satuan_id')->where('tkp.id_kasir','=',$id)->select(DB::raw('tkp.*,mp.kode_produk as kode_produk,mp.nama as nama_produk,ts.satuan_nama as nama_satuan,ts.satuan_satuan as inisial_satuan'))->get();
    $items = array();

    foreach($data_detail_trx as $d){
      $id_detail_kasir_produk = $d->id_kasir_detail_produk;
      $nama_produk            = $d->nama_produk;
      $jumlah                 = $d->jumlah;
      $satuan                 = $d->inisial_satuan;
      $harga                  = format_angka($d->harga);
      $sub_total              = format_angka($d->total);

      array_push($items, new item($nama_produk, ' #'.$jumlah.'x ', $harga.'=', $sub_total, true));
    }


    $total = new item('Total','','', 'Rp.'.'0', true);
    $bayar = new item('Tunai','','', 'Rp.'.'0', true);
    $kembali = new item('Kembali','','', 'Rp.'.'0', true);
    // Date is kept the same for testing
    // $date = date('l jS \of F Y h:i:s A');
    // $date = "Monday 6th of April 2015 02:56:25 PM";
    // Start the printer
    // $logo = EscposImage::load("resources/escpos-php.png", false);
    $printer = new Printer($connector);
    // Print top logo
    $printer -> setJustification(Printer::JUSTIFY_CENTER);
    // $printer -> graphics($logo);
    // Name of shop
    $printer -> selectPrintMode(Printer::MODE_EMPHASIZED);
    $printer -> text("Toko Bintang Jaya Accessories\n");
    $printer -> selectPrintMode(Printer::MODE_FONT_A);
    $printer -> setJustification(Printer::JUSTIFY_LEFT);
    $printer -> text("Jl. Palang Merah RT 08 RW 09\n");
    $printer -> text("Lingkungan Dadapan\n");
    $printer -> text("Telp. 085267577525\n");
    $printer -> text("Fax.085267577525\n");
    $printer -> feed();
    $printer -> setJustification(Printer::JUSTIFY_LEFT);
    $printer -> text('Tgl:'.date('d-m-Y H:i:s') . "\n");
    $printer -> text('No :'.$data_detail_first->no_faktur. "\n");
    // $printer -> feed();
    // Title of receipt
    // $printer -> setEmphasis(true);
    // $printer -> text("NOTA PENJUALAN\n");
    // $printer -> setEmphasis(false);
    $printer -> setJustification(Printer::JUSTIFY_CENTER);
    $printer -> text('--------------------------------');
    // Items
    // $printer -> setEmphasis(true);
    // $printer -> text(new item('', 'Rp'));
    // $printer -> setEmphasis(false);
    $printer -> setJustification(Printer::JUSTIFY_LEFT);
    foreach ($items as $item) {
      $printer -> text($item);
    }
    $printer -> setJustification(Printer::JUSTIFY_CENTER);
    $printer -> text('--------------------------------');
    $printer -> setJustification(Printer::JUSTIFY_LEFT);
    $printer -> text($total);
    $printer -> text($bayar);
    $printer -> text($kembali);
    // Footer
    $printer -> feed();
    $printer -> setJustification(Printer::JUSTIFY_CENTER);
    $printer -> text("Barang yang sudah dibeli tidak\n");
    $printer -> text("dapat dikembalikan kecuali ada\n");
    $printer -> text("perjanjian.\n");
    $printer -> feed();
    $printer -> text('--------------------------------');
    // dd($printer);

    // Cut the receipt and open the cash drawer
    $printer -> cut();
    $printer -> pulse();
    $printer -> close();
    // return 'hhh';
    return back();

  }*/

    public function simpan_pelanggan(Request $request){
      $id = $request->get('popup_id_table_pelanggan');
      $data['nama'] = $request->get('pelanggan_nama');
      $data['alamat'] = $request->get('pelanggan_alamat');
      $data['telp']   = $request->get('pelanggan_telp');
      if($id == ''){
            DB::table('m_pelanggan')->insert($data);
        }else{
            DB::table('m_pelanggan')->where('id',$id)->update($data);
        }

      return response()->json(array('status' => '1'));
    }


    public function simpan_infocetak(Request $request){
      $data['id_kasir'] = $request->get('popupinfo_idkasir');
      $data['id_user'] = $request->get('popupinfo_iduser');
      $data['keterangan'] = $request->get('popupinfo_keterangan');
      $id_tbl_kasir_print = DB::table('tbl_kasir_print')->insertGetId($data);
    //   trigger_log($id_tbl_kasir_print, "Cetak Penjualan Outlet", 7);

      return response()->json(array('status' => '1'));
    }
    
    public function simpan_infocetak_mobile($id_kasir, $keterangan = 'Cetak Nota Penjualan Outlet Dari Mobile'){
      $data['id_kasir'] = $id_kasir;
      $data['id_user'] = Auth::id();
      $data['keterangan'] = ($keterangan == '') ? 'Cetak Nota Penjualan Outlet Dari Mobile' : $keterangan;
      DB::table('tbl_kasir_print')->insert($data);
      return redirect('kasir');
    }
    
    public function get_nomorfaktur($tanggal, $gudang){
        // TODO
        // $tanggal = tgl_full($request['tanggal'],'99');
        $tanggal = date('dmY',strtotime($tanggal));
        $id_profil = Auth::user()->id_profil;
        $group = Auth::user()->group_id;

        $d_gudang       = $gudang;
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
        return $kode;
    }



}
