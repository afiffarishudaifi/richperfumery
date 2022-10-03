<?php

namespace App\Http\Controllers\Kasir;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//use Illuminate\Support\Facades\Cache;
use DB;
use Auth;
use App\PembatalanModel;
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

class Pembatalancontroller extends Controller
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
        return view('admin_mobile.kasir.index_pembatalan',compact('tombol_create'));
      }else {
        // code...
        return view('admin.kasir.index_pembatalan',compact('tombol_create'));
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
        $d_barang = DB::table('tbl_kasir_batal')->where('no_faktur', 'like', $nama.'%')->orderBy('no_faktur', 'desc');
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
        $d_barang = DB::table('tbl_kasir_batal')->where('no_faktur', 'like', $nama.'%')->orderBy('no_faktur', 'desc');
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
        //ongkirbaru
        if($data->total_metodeongkir != 0 || $data->total_metodeongkir != null){
          $store['nominalongkir1']  = $data->total_metodeongkir;
        }else{
          $store['nominalongkir1']  = $data->ongkos_kirim;
        }
        $store['nominalongkir1']  = $data->ongkos_kirim;
        $store['viaongkir1']      = $data->metodeongkir;
        $store['nominalongkir2']  = $data->total_metodeongkir2;
        $store['viaongkir2']      = $data->metodeongkir2;
        //end
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
         //ongkirbaru
         $store['nominalongkir1'] = null;
         $store['viaongkir1']     = null;
         $store['nominalongkir2'] = null;
         $store['viaongkir2']     = null;
         //end
        $store['td_potongan']     = "";
        $store['td_tagihan']      = "";
        $store['td_subtotal']     = "";
        $store['carabayar']       = "";
        $store['metodebayar']     = "";
        $store['metodebayar2']    = "";
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

      $d_data = DB::table('tbl_kasir_batal as tp')->leftjoin('m_pelanggan as mp','tp.id_pelanggan','mp.id')->join('ref_gudang as rg','tp.id_gudang','rg.id')->select('tp.*','mp.nama as nama_pelanggan','mp.alamat as alamat_pelanggan','mp.telp as telp_pelanggan','rg.nama as nama_gudang')->where('id_kasir',$id)->first();
      $data['data']         = $this->data($d_data);
      $data['satuan']       = DB::table('tbl_satuan')->orderBy('satuan_nama','asc')->get();
      $data['carabayar']    = \Config::get('constants.carabayar');
      $data['no_auto']      = "";
      /*$data['pelanggan']    = DB::select("select mp.*, rf.nama as nama_gudang from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id $where_gudang");*/
      $data['pelanggan']  = DB::select("select mp.id, CASE WHEN mp.telp IS NULL THEN mp.nama ELSE CONCAT(mp.nama,' (',mp.telp,')') END as nama, rf.nama as nama_gudang from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id $where_gudang");
      $data['gudang']       = DB::select(base_gudang($where));
      // $data['pembayaran']   = DB::table('m_metode')->orderby('urutan','asc')->get();
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
    
    public function get_edit(Request $request){
      $id = $request->get('id');
      $d_data = DB::table('tbl_kasir_detail_produk_batal as tpd')->leftjoin('m_produk as mp','tpd.id_produk','mp.id')->leftjoin('tbl_satuan as ts2','tpd.id_satuan','ts2.satuan_id')->where('tpd.id_kasir',$id)->select('tpd.*','mp.id_type_ukuran','mp.nama as nama_produk','ts2.satuan_nama as nama_satuan','ts2.satuan_satuan as satuan_satuan')->orderBy('tpd.id_kasir_detail_produk','asc');
      $d_barang = DB::table('tbl_kasir_detail_batal as tkd')->leftjoin('tbl_barang as tb','tkd.id_barang','tb.barang_id')->leftjoin('tbl_satuan as ts2','tkd.id_satuan','ts2.satuan_id')->where('tkd.id_kasir',$id)->where('tkd.id_detail_kasir_produk','0')->select('tkd.*','tb.barang_nama as nama_barang','tb.barang_kode as kode_barang','tb.barang_alias as alias_barang','ts2.satuan_nama as nama_satuan','ts2.satuan_satuan as satuan_satuan')->orderBy('tkd.id_detail_kasir','asc');
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
                            'id_tipe'       => "3",
                            'status'        => "2",
                            'status_redeem' => (empty($d->status_redeem)?'0':$d->status_redeem));
        }
      }else{
        $arr['barang'] = array();
      }

      return response()->json($arr);
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

      $d_data = DB::table('tbl_kasir_batal as tp')
                ->leftjoin('m_pelanggan as mp','tp.id_pelanggan','mp.id')
                ->join('ref_gudang as rg','tp.id_gudang','rg.id')
                ->select('tp.*','mp.nama as nama_pelanggan','mp.alamat as alamat_pelanggan','mp.telp as telp_pelanggan','rg.nama as nama_gudang')
                ->where('id_kasir',$id)->first();
      $data['data']         = $this->data($d_data);
      $data['satuan']       = DB::table('tbl_satuan')->orderBy('satuan_nama','asc')->get();
      $data['carabayar']    = \Config::get('constants.carabayar');
      $data['no_auto']      = "";
      $data['pelanggan']    = DB::select("select mp.*, rf.nama as nama_gudang from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id $where_gudang");
      $data['gudang']       = DB::select(base_gudang($where));
      $data['pembayaran'] = DB::table('m_metode')->where('status','=','1')->orderby('urutan','asc')->get();
      // dd($data);
      if ($this->agent->isMobile()) {
        // code...
        return view('admin_mobile.kasir.detail_pembatalan')->with('data',$data);
      }else {
        // code...
        return view('admin.kasir.detail_pembatalan')->with('data',$data);
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
       
      $kasir = DB::SELECT("SELECT tk.*, mp.nama AS nama_pelanggan, mp.telp as telp_pelanggan, rg.nama as nama_gudang, u.name as nama_hapus, tkp.jumlah_cetak
                          FROM tbl_kasir_batal AS tk 
                          JOIN users AS u ON tk.deleted_iduser = u.id
                          LEFT JOIN m_pelanggan AS mp ON tk.id_pelanggan=mp.id
                          LEFT JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
                          LEFT JOIN (SELECT id_kasir, COUNT(id_print) as jumlah_cetak FROM tbl_kasir_print GROUP BY id_kasir) AS tkp ON tk.id_kasir = tkp.id_kasir
                          WHERE tk.jenis_transaksi=1
                          AND tk.id_gudang IN ($gudang)
                          AND tk.tanggal > DATE_SUB(DATE(NOW()), INTERVAL 7 DAY) 
                          AND tk.tanggal <= DATE_SUB(DATE(NOW()), INTERVAL 0 DAY)
                          ORDER BY tk.tanggal_faktur DESC LIMIT 500");

      $no = 0;
      $data = array();
        foreach ($kasir as $list){
            if($list->carabayar == '1'){
                $total_tagihan = 'Rp. 0';
            }else{
                $total_tagihan = 'Rp '.format_angka($list->total_tagihan-($list->total_potongan-$list->ongkos_kirim));
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
              $row['total_tagihan'] = 'Rp '.format_angka($list->total_tagihan-($list->total_potongan-$list->ongkos_kirim));
              $row['penghapus'] = $list->nama_hapus;
              if($group == 1) $row['jumlah_cetak'] = $list->jumlah_cetak ?? 0;
              $row['posting'] = $this->posting($list->id_kasir);
            }else {
              $row[] = $no;
              $row[] = tgl_full($list->tanggal_faktur,'');
              $row[] = $list->no_faktur;
              $row[] = $list->nama_pelanggan.$telp_pelanggan;
              $row[] = $list->nama_gudang;
              $row[] = 'Rp '.format_angka($list->total_tagihan-($list->total_potongan-$list->ongkos_kirim));
              $row[] = $list->nama_hapus;
              if($group == 1) $row[] = $list->jumlah_cetak ?? 0;
              $row[] = $this->posting($list->id_kasir);
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
        $kasir = DB::SELECT("SELECT tk.*, mp.nama AS nama_pelanggan, rg.nama as nama_gudang, u.name as nama_hapus
                          FROM tbl_kasir_batal AS tk 
                          JOIN users as u ON tk.deleted_iduser = u.id
                          LEFT JOIN m_pelanggan AS mp ON tk.id_pelanggan=mp.id
                          LEFT JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
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
              $row['penghapus'] = $list->nama_hapus;
              $row['posting'] = $this->posting($list->id_kasir);
            }else {
              $row[] = $no;
              $row[] = tgl_full($list->tanggal_faktur,'');
              $row[] = $list->no_faktur;
              $row[] = $list->nama_pelanggan;
              $row[] = $list->nama_gudang;
              // $row[] = 'Rp '.format_angka($list->total_tagihan);
              $row[] = 'Rp '.format_angka($list->total_tagihan-($list->total_potongan-$list->ongkos_kirim));
              $row[] = $list->nama_hapus;
              $row[] = $this->posting($list->id_kasir);
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

    public function posting($id){
      $html = '<div class="btn-group">
            <a href="'.url('kasir_pembatalan_detail/'.$id).'" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a>
            </div>';
      return $html;
    }

    function cetak_laporan($id){
      if ($this->agent->isMobile()) {
        $data = DB::table('tbl_kasir_batal as tk')->join('m_pelanggan as mp','tk.id_pelanggan','mp.id')->join('ref_gudang as rg','tk.id_gudang','rg.id')->leftjoin('m_metode as mm','tk.metodebayar','mm.id')->leftjoin('m_metode as mm2','tk.metodebayar2','mm2.id')->where('id_kasir',$id)->select(DB::raw('tk.*,mp.nama as nama_pelanggan,rg.nama as nama_gudang, rg.alamat as alamat_gudang, mm.nama as nama_metodebayar, mm2.nama as nama_metodebayar2'))->first();
        $detail = DB::table('tbl_kasir_detail_produk_batal as tkp')->join('m_produk as mp','tkp.id_produk','mp.id')->join('tbl_satuan as ts','tkp.id_satuan','ts.satuan_id')->where('tkp.id_kasir','=',$id)->select(DB::raw('tkp.*,mp.kode_produk as kode_produk,mp.nama as nama_produk,ts.satuan_nama as nama_satuan,ts.satuan_satuan as inisial_satuan'))->get();
        $barang  = DB::table('tbl_kasir_detail_batal as tkd')->join('tbl_barang as tb','tkd.id_barang','tb.barang_id')->join('tbl_satuan as ts','tkd.id_satuan','ts.satuan_id')->where('tkd.id_kasir','=',$id)->where('tkd.id_detail_kasir_produk','=',0)->select(DB::raw('tkd.*,tb.barang_kode as kode_barang,tb.barang_nama as nama_barang, tb.barang_alias as alias,ts.satuan_nama as nama_satuan,ts.satuan_satuan as inisial_satuan'))->get();
        return view('admin_mobile.kasir.struk_kasir',compact('data','detail','barang'));
        exit();
      }else {
        // code...
        require(public_path('fpdf1813/Mc_table.php'));
        $data['data'] = DB::table('tbl_kasir_batal as tk')->join('m_pelanggan as mp','tk.id_pelanggan','mp.id')->join('ref_gudang as rg','tk.id_gudang','rg.id')->leftjoin('m_metode as mm','tk.metodebayar','mm.id')->leftjoin('m_metode as mm2','tk.metodebayar2','mm2.id')->where('id_kasir',$id)->select(DB::raw('tk.*,mp.nama as nama_pelanggan, mp.alamat as alamat_pelanggan, mp.telp as telp_pelanggan, rg.nama as nama_gudang, rg.alamat as alamat_gudang, mm.nama as nama_metodebayar, mm2.nama as nama_metodebayar2'))->first();
        $data['detail'] = DB::table('tbl_kasir_detail_produk_batal as tkp')->join('m_produk as mp','tkp.id_produk','mp.id')->join('tbl_satuan as ts','tkp.id_satuan','ts.satuan_id')->where('tkp.id_kasir','=',$id)->select(DB::raw('tkp.*,mp.kode_produk as kode_produk,mp.nama as nama_produk,ts.satuan_nama as nama_satuan,ts.satuan_satuan as inisial_satuan'))->get();
        $data['barang'] = DB::table('tbl_kasir_detail_batal as tkd')->join('tbl_barang as tb','tkd.id_barang','tb.barang_id')->join('tbl_satuan as ts','tkd.id_satuan','ts.satuan_id')->where('tkd.id_kasir','=',$id)->where('tkd.id_detail_kasir_produk','=','0')->select(DB::raw('tkd.*,tb.barang_kode as kode_barang,tb.barang_nama as nama_barang, tb.barang_alias as alias,ts.satuan_nama as nama_satuan,ts.satuan_satuan as inisial_satuan'))->get();
        if(Auth::user()->id_profil != 0){
          $id_profil = Auth::user()->id_profil;
        }else{
          $id_profil = '1';
        }

        $data['rich'] = DB::table('m_profil as mp')->where('id',$id_profil)->select(DB::raw('mp.*'))->first();
        $html = view('admin.kasir.CetakPenjualanOutlet')->with('data',$data);
        return response($html)->header('Content-Type', 'application/pdf');
      }
    }

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
}
