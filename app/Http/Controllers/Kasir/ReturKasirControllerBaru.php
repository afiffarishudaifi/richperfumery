<?php

namespace App\Http\Controllers\Kasir;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use Jenssegers\Agent\Agent;

class ReturKasirControllerBaru extends Controller
{
  //
  public function __construct()
  {
    $this->agent = new Agent();
  }

  public function index()
  {
    $id_profil = Auth::user()->id_profil;
    $group = Auth::user()->group_id;
    $where = "";
    if ($group == 5 || $group == 6 || $group == 8) { //TODO
      $where = "WHERE id_profil='$id_profil'";
    }
    $d_gudang = DB::select(base_gudang($where));
    $id_gudang = array();
    foreach ($d_gudang as $d) {
      $id_gudang[] = $d->id_gudang;
    }
    $where_gudang = "";
    if (sizeof($id_gudang) > 0) {
      $gudang = implode(',', $id_gudang);
      $where_gudang = "WHERE g.id IN ($gudang)";
    }
    $group_where = DB::table('tbl_group')->where('group_id', $group)->first();

    $data['gudang'] = DB::select("
        SELECT
          g.id,
          g.id_profil,
          g.nama,
          g.alamat,
          g.`status`,
          g.created_at,
          g.updated_at,
          p.jenis_outlet
        FROM
          ref_gudang g
          LEFT JOIN m_profil p ON g.id_profil = p.id $where_gudang
        ");
    $data['satuan']     = DB::table('tbl_satuan')->orderBy('satuan_nama', 'asc')->get();
    $data['pelanggan']  = DB::select("select mp.*, g.nama as nama_gudang from m_pelanggan as mp LEFT JOIN ref_gudang as g ON mp.id_gudang=g.id $where_gudang");
    $data['tombol_create'] = tombol_create('', $group_where->group_aktif, 1);
    if ($this->agent->isMobile()) {
      // code...
      return view('admin_mobile.kasir.index_retur_baru')->with('data', $data);
    } else {
      // code...
      return view('admin.kasir.index_retur_baru')->with('data', $data);
    }
  }

  public function data($data = array(), $check = true)
  {
    if ($data != null) {
      if($check){
        $store['id_retur'] = $data->id_retur;
        $store['kode_retur']     = $data->kode_retur;
      }else{
        $store['id_kasir'] = $data->id_kasir;
      }
      $store['tanggal']         = tgl_full($data->tanggal, '');
      $store['tanggal_faktur']  = tgl_full($data->tanggal_faktur, '');
      $store['tanggal_tempo']   = tgl_full($data->tanggal_tempo, '');
      $store['id_pelanggan']    = $data->id_pelanggan;
      $store['nama_pelanggan']  = $data->nama_pelanggan;
      $store['alamat_pelanggan'] = $data->alamat_pelanggan;
      $store['telp_pelanggan']  = $data->telp_pelanggan;
      $store['nomor']           = $data->no_faktur;
      $store['keterangan']      = $data->keterangan;
      $store['id_gudang']       = $data->id_gudang;
      $store['nama_gudang']     = $data->nama_gudang;
    } else {
      if($check){
        $store['id_retur'] = $data->id_retur;
        $store['kode_retur']     = $data->kode_retur;
      }else{
        $store['id_kasir'] = "";
      }
      $store['tanggal']         = "";
      $store['tanggal_faktur']  = "";
      $store['tanggal_tempo']   = "";
      $store['id_pelanggan']    = "";
      $store['nama_pelanggan']  = "";
      $store['alamat_pelanggan'] = "";
      $store['telp_pelanggan']  = "";
      $store['keterangan']      = "";
      $store['id_gudang']       = "";
      $store['nama_gudang']     = "";
    }

    return $store;
  }

  public function data2($data = array())
  {
    if ($data != null) {

      $store['id_retur'] = $data->id_retur;
      $store['tanggal']         = tgl_full($data->tanggal, '');
      $store['tanggal_faktur']  = tgl_full($data->tanggal_faktur, '');
      $store['tanggal_tempo']   = tgl_full($data->tanggal_tempo, '');
      $store['id_pelanggan']    = $data->id_pelanggan;
      $store['nama_pelanggan']  = $data->nama_pelanggan;
      $store['alamat_pelanggan'] = $data->alamat_pelanggan;
      $store['telp_pelanggan']  = $data->telp_pelanggan;
      $store['nomor']           = $data->no_faktur;
      $store['keterangan']      = $data->keterangan;
      $store['id_gudang']       = $data->id_gudang;
      $store['nama_gudang']     = $data->nama_gudang;
      $store['kode_retur']     = $data->kode_retur;
    } else {
      $store['id_retur'] = $data->id_retur;
      $store['tanggal']         = "";
      $store['tanggal_faktur']  = "";
      $store['tanggal_tempo']   = "";
      $store['id_pelanggan']    = "";
      $store['nama_pelanggan']  = "";
      $store['alamat_pelanggan'] = "";
      $store['telp_pelanggan']  = "";
      $store['keterangan']      = "";
      $store['id_gudang']       = "";
      $store['nama_gudang']     = "";
      $store['kode_retur']     = $data->kode_retur;
    }

    return $store;
  }

  public function listData(Request $request)
  {
    $id_profil = Auth::user()->id_profil;
    $group = Auth::user()->group_id;
    $where = "";
    if ($group == 5 || $group == 6 || $group == 8) { // TODO
      $where = "WHERE id_profil='$id_profil'";
    }
    $d_gudang = DB::select(base_gudang($where));
    $id_gudang = array();
    foreach ($d_gudang as $d) {
      $id_gudang[] = $d->id_gudang;
    }
    $group_where = DB::table('tbl_group')->where('group_id', $group)->first();

    $draw = $request->get('draw');
    $start = $request->get('start');
    $length = $request->get('length');
    $filter = $request->get('search');
    $search = (isset($filter['value'])) ? strtolower($filter['value']) : false;
    $tanggal = (isset($filter['value'])) ? tgl_full($filter['value'], '99') : false;

    $tanggal_start = date('Y-m-d', strtotime('-7 days'));
    $tanggal_end = date('Y-m-d');
    $return = DB::table('tbl_kasir_retur as tkr')
      ->leftjoin('m_pelanggan as mp', 'tkr.id_pelanggan', 'mp.id')
      ->leftjoin('ref_gudang as rg', 'tkr.id_gudang', 'rg.id')
      ->where('tkr.tanggal_retur', '>', $tanggal_start)
      ->where('tkr.tanggal_retur', '<=', $tanggal_end)
      ->whereIn('tkr.id_gudang', $id_gudang)
      ->select(DB::raw('tkr.id_retur, tkr.kode_retur, tkr.tanggal, tkr.id_gudang, tkr.keterangan, tkr.no_faktur, tkr.id_pelanggan, mp.nama as nama_pelanggan, rg.nama as nama_gudang'))
      ->orderBy('tkr.id_retur', 'DESC');

    //dd(DB::getQueryLog());
    if ($search) {
      $return = DB::table('tbl_kasir_retur as tkr')
        ->leftjoin('m_pelanggan as mp', 'tkr.id_pelanggan', 'mp.id')
        ->leftjoin('ref_gudang as rg', 'tkr.id_gudang', 'rg.id')
        ->where('tkr.tanggal_retur', '>', $tanggal_start)
        ->where('tkr.tanggal_retur', '<=', $tanggal_end)
        ->where(function ($query) use ($search, $tanggal) {
          $query->orwhere('tkr.kode_retur', 'like', '%' . $search . '%');
          $query->orwhere('tkr.tanggal', 'like', '%' . $tanggal . '%');
          $query->orwhere('rg.nama', 'like', '%' . $search . '%');
          $query->orwhere('mp.nama', 'like', '%' . $search . '%');
        })
        ->whereIn('tkr.id_gudang', $id_gudang)
        ->select(DB::raw('tkr.id_retur, tkr.kode_retur, tkr.tanggal, tkr.id_gudang, tkr.keterangan, tkr.no_faktur, tkr.id_pelanggan, mp.nama as nama_pelanggan, rg.nama as nama_gudang'))
        ->orderBy('tkr.id_retur', 'DESC');
    }
    $totalrecord = count($return->get());
    $return_2   = $return->offset($start)->limit($length);
    $no       = ($start == 0) ? 0 : $start;
    $arr = array();
    foreach ($return_2->get() as $list) {
      $no++;
      $arr[] = array(
        'nomor' => $no,
        'kode_retur'   => $list->kode_retur,
        'tanggal'    => tgl_full($list->tanggal, ''),
        'no_faktur'  => $list->no_faktur,
        'nama_pelanggan' => $list->nama_pelanggan,
        'nama_pelanggan'      => $list->nama_pelanggan,
        'nama_gudang'  => $list->nama_gudang,
        'aksi'      => $this->get_aksi($list->id_retur, $group_where->group_aktif) .
          '<input type="hidden" id="table_id' . $list->id_retur . '" value="' . $list->id_retur . '">' .
          '<input type="hidden" id="table_kode' . $list->id_retur . '" value="' . $list->kode_retur . '">' .
          '<input type="hidden" id="table_tanggal' . $list->id_retur . '" value="' . tgl_full($list->tanggal, '') . '">' .
          '<input type="hidden" id="table_idgudang' . $list->id_retur . '" value="' . $list->id_gudang . '">' .
          '<input type="hidden" id="table_namagudang' . $list->id_retur . '" value="' . $list->nama_gudang . '">' .
          '<input type="hidden" id="table_idpelanggan' . $list->id_retur . '" value="' . $list->id_pelanggan . '">' .
          '<input type="hidden" id="table_namapelanggan' . $list->id_retur . '" value="' . $list->nama_pelanggan . '">' .
          '<input type="hidden" id="table_keterangan' . $list->id_retur . '" value="' . $list->keterangan . '">'
      );
    }

    $data = array(
      'draw' => $draw,
      'recordsTotal' => $totalrecord,
      'recordsFiltered' => $totalrecord,
      'data' => $arr
    );
    return response()->json($data);
  }

  public function listDataLama(Request $request)
  {
    // DB::enableQueryLog();
    $id_profil = Auth::user()->id_profil;
    $group = Auth::user()->group_id;
    $where = "";
    if ($group == 5 || $group == 6 || $group == 8) { // TODO
      $where = "WHERE id_profil='$id_profil'";
    }
    $d_gudang = DB::select(base_gudang($where));
    $id_gudang = array();
    foreach ($d_gudang as $d) {
      $id_gudang[] = $d->id_gudang;
    }
    $group_where = DB::table('tbl_group')->where('group_id', $group)->first();

    $draw = $request->get('draw');
    $start = $request->get('start');
    $length = $request->get('length');
    $filter = $request->get('search');
    $search = (isset($filter['value'])) ? strtolower($filter['value']) : false;
    $tanggal = (isset($filter['value'])) ? tgl_full($filter['value'], '99') : false;

    $tanggal_start = date('Y-m-d', strtotime('-7 days'));
    $tanggal_end = date('Y-m-d');
    $return = DB::table('tbl_kasir_detail_retur as tkr')->leftjoin('tbl_barang as tb', 'tkr.id_barang', 'tb.barang_id')
      ->leftjoin('tbl_satuan as ts', 'tkr.id_satuan', 'ts.satuan_id')
      ->leftjoin('ref_gudang as rg', 'tkr.id_gudang', 'rg.id')
      ->leftjoin('m_pelanggan as mp', 'tkr.id_pelanggan', 'mp.id')
      ->where('tkr.tanggal', '>', $tanggal_start)
      ->where('tkr.tanggal', '<=', $tanggal_end)
      ->whereIn('tkr.id_gudang', $id_gudang)
      ->select(DB::raw('tkr.id_returkasir_detail, tkr.kode_retur, tkr.tanggal, tkr.id_barang, 
          tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, tb.barang_alias as alias_barang, 
          tkr.id_satuan, ts.satuan_nama as nama_satuan, tkr.id_gudang, rg.nama as nama_gudang, tkr.id_pelanggan, 
          mp.nama as nama_pelanggan, tkr.jumlah, tkr.harga, tkr.total, tkr.keterangan, tkr.id_log_stok'))
      ->orderBy('tkr.id_returkasir_detail', 'DESC');

    //dd(DB::getQueryLog());
    if ($search) {
      $return = DB::table('tbl_kasir_detail_retur as tkr')->leftjoin('tbl_barang as tb', 'tkr.id_barang', 'tb.barang_id')->leftjoin('tbl_satuan as ts', 'tkr.id_satuan', 'ts.satuan_id')->leftjoin('ref_gudang as rg', 'tkr.id_gudang', 'rg.id')->leftjoin('m_pelanggan as mp', 'tkr.id_pelanggan', 'mp.id')
        ->where('tkr.tanggal', '>', $tanggal_start)
        ->where('tkr.tanggal', '<=', $tanggal_end)
        ->where(function ($query) use ($search, $tanggal) {
          $query->orwhere('tkr.kode_retur', 'like', '%' . $search . '%');
          $query->orwhere('tkr.tanggal', 'like', '%' . $tanggal . '%');
          $query->orwhere('tb.barang_nama', 'like', '%' . $search . '%');
          $query->orwhere('rg.nama', 'like', '%' . $search . '%');
          $query->orwhere('mp.nama', 'like', '%' . $search . '%');
        })
        ->whereIn('tkr.id_gudang', $id_gudang)
        ->select(DB::raw('tkr.id_returkasir_detail, tkr.kode_retur, tkr.tanggal, tkr.id_barang, 
          tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, tb.barang_alias as alias_barang, 
          tkr.id_satuan, ts.satuan_nama as nama_satuan, tkr.id_gudang, rg.nama as nama_gudang, tkr.id_pelanggan, 
          mp.nama as nama_pelanggan, tkr.jumlah, tkr.harga, tkr.total, tkr.keterangan, tkr.id_log_stok'))
        ->orderBy('tkr.id_returkasir_detail', 'DESC');
    }
    $totalrecord = count($return->get());
    $return_2   = $return->offset($start)->limit($length);
    $no       = ($start == 0) ? 0 : $start;
    $arr = array();
    foreach ($return_2->get() as $list) {
      $no++;
      $arr[] = array(
        'nomor' => $no,
        'kode_retur'   => $list->kode_retur,
        'tanggal'    => tgl_full($list->tanggal, ''),
        'nama_barang'  => $list->nama_barang,
        'jumlah'    => $list->jumlah,
        'nama_satuan'  => $list->nama_satuan,
        'nama_pelanggan' => $list->nama_pelanggan,
        'nama_gudang'  => $list->nama_gudang,
        'harga'      => $list->harga,
        'total'      => $list->total,
        'aksi'      => $this->get_aksi($list->id_returkasir_detail, $group_where->group_aktif) .
          '<input type="hidden" id="table_id' . $list->id_returkasir_detail . '" value="' . $list->id_returkasir_detail . '">' .
          '<input type="hidden" id="table_kode' . $list->id_returkasir_detail . '" value="' . $list->kode_retur . '">' .
          '<input type="hidden" id="table_tanggal' . $list->id_returkasir_detail . '" value="' . tgl_full($list->tanggal, '') . '">' .
          '<input type="hidden" id="table_idbarang' . $list->id_returkasir_detail . '" value="' . $list->id_barang . '">' .
          '<input type="hidden" id="table_namabarang' . $list->id_returkasir_detail . '" value="' . $list->nama_barang . '">' .
          '<input type="hidden" id="table_kodebarang' . $list->id_returkasir_detail . '" value="' . $list->kode_barang . '">' .
          '<input type="hidden" id="table_aliasbarang' . $list->id_returkasir_detail . '" value="' . $list->alias_barang . '">' .
          '<input type="hidden" id="table_idgudang' . $list->id_returkasir_detail . '" value="' . $list->id_gudang . '">' .
          '<input type="hidden" id="table_namagudang' . $list->id_returkasir_detail . '" value="' . $list->nama_gudang . '">' .
          '<input type="hidden" id="table_idpelanggan' . $list->id_returkasir_detail . '" value="' . $list->id_pelanggan . '">' .
          '<input type="hidden" id="table_namapelanggan' . $list->id_returkasir_detail . '" value="' . $list->nama_pelanggan . '">' .
          '<input type="hidden" id="table_idsatuan' . $list->id_returkasir_detail . '" value="' . $list->id_satuan . '">' .
          '<input type="hidden" id="table_jumlah' . $list->id_returkasir_detail . '" value="' . $list->jumlah . '">' .
          '<input type="hidden" id="table_harga' . $list->id_returkasir_detail . '" value="' . $list->harga . '">' .
          '<input type="hidden" id="table_total' . $list->id_returkasir_detail . '" value="' . $list->total . '">' .
          '<input type="hidden" id="table_keterangan' . $list->id_returkasir_detail . '" value="' . $list->keterangan . '">' .
          '<input type="hidden" id="table_idlog_stok' . $list->id_returkasir_detail . '" value="' . $list->id_log_stok . '">'
      );
    }

    $data = array(
      'draw' => $draw,
      'recordsTotal' => $totalrecord,
      'recordsFiltered' => $totalrecord,
      'data' => $arr
    );
    return response()->json($data);
  }
  
  public function searchtanggal(Request $request)
  {
    $id_profil = Auth::user()->id_profil;
    $group = Auth::user()->group_id;
    $where = "";
    if ($group == 5 || $group == 6 || $group == 8) { // TODO
      $where = "WHERE id_profil='$id_profil'";
    }
    $d_gudang = DB::select(base_gudang($where));
    $id_gudang = array();
    foreach ($d_gudang as $d) {
      $id_gudang[] = $d->id_gudang;
    }
    $group_where = DB::table('tbl_group')->where('group_id', $group)->first();

    $draw = $request->get('draw');
    $start = $request->get('start');
    $length = $request->get('length');
    $filter = $request->get('search');
    $search = (isset($filter['value'])) ? strtolower($filter['value']) : false;
    $tanggal = (isset($filter['value'])) ? tgl_full($filter['value'], '99') : false;

    $tanggalrange = explode('s.d.', $request->get('tanggal'));
    $tanggal_start  = tgl_full($tanggalrange[0], 99);
    $tanggal_end    = tgl_full($tanggalrange[1], 99);
    // $return2 = DB::table('tbl_kasir_detail_retur_baru as tkr')
    //   ->leftjoin('tbl_barang as tb', 'tkr.id_barang', 'tb.barang_id')
    //   ->leftjoin('tbl_satuan as ts', 'tkr.id_satuan', 'ts.satuan_id')
    //   ->join('tbl_kasir_retur as tblk', 'tblk.id_retur', 'tkr.id_retur')
    //   ->leftjoin('ref_gudang as rg', 'tblk.id_gudang', 'rg.id')
    //   ->leftjoin('m_pelanggan as mp', 'tblk.id_pelanggan', 'mp.id')
    //   ->where('tblk.tanggal', '>=', $tanggal_start)
    //   ->where('tblk.tanggal', '<=', $tanggal_end)
    //   // ->whereIn('tblk.id_gudang', $id_gudang)
    //   ->select(DB::raw('tkr.id_detail_kasir_retur, tblk.id_retur, tblk.no_faktur, tblk.kode_retur, tblk.tanggal, tkr.id_barang,
    //          tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, tb.barang_alias as alias_barang, 
    //          tkr.id_satuan, ts.satuan_nama as nama_satuan, tblk.id_gudang, rg.nama as nama_gudang, tblk.id_pelanggan, 
    //          mp.nama as nama_pelanggan, tkr.jumlah, tblk.keterangan'))
    //   ->orderBy('tkr.id_detail_kasir_retur', 'DESC');

    $return = DB::table('tbl_kasir_retur as tp')
      ->leftjoin('m_pelanggan as mp', 'tp.id_pelanggan', 'mp.id')
      ->join('ref_gudang as rg', 'tp.id_gudang', 'rg.id')
      ->select('tp.*', 'mp.nama as nama_pelanggan', 'mp.alamat as alamat_pelanggan', 'mp.telp as telp_pelanggan', 'rg.nama as nama_gudang')
      ->where('tp.tanggal', '>=', $tanggal_start)
      ->where('tp.tanggal', '<=', $tanggal_end)
      ->orderBy('tp.id_retur', 'DESC');

    if ($search) {
      // $return = DB::table('tbl_kasir_detail_retur_baru as tkr')
      //   ->leftjoin('tbl_barang as tb', 'tkr.id_barang', 'tb.barang_id')
      //   ->leftjoin('tbl_satuan as ts', 'tkr.id_satuan', 'ts.satuan_id')
      //   ->join('tbl_kasir_retur as tblk', 'tblk.id_retur', 'tkr.id_retur')
      //   ->leftjoin('ref_gudang as rg', 'tblk.id_gudang', 'rg.id')
      //   ->leftjoin('m_pelanggan as mp', 'tblk.id_pelanggan', 'mp.id')
      //   ->where('tblk.tanggal', '>=', $tanggal_start)
      //   ->where('tblk.tanggal', '<=', $tanggal_end)
      //   ->where(function ($query) use ($search, $tanggal) {
      //     $query->orwhere('tkr.kode_retur', 'like', '%' . $search . '%');
      //     $query->orwhere('tkr.tanggal', 'like', '%' . $tanggal . '%');
      //     $query->orwhere('tb.barang_nama', 'like', '%' . $search . '%');
      //     $query->orwhere('rg.nama', 'like', '%' . $search . '%');
      //     $query->orwhere('mp.nama', 'like', '%' . $search . '%');
      //   })
      //   // ->whereIn('tblk.id_gudang', $id_gudang)
      //   ->select(DB::raw('tkr.id_detail_kasir_retur, tblk.id_retur, tblk.no_faktur, tblk.kode_retur, tblk.tanggal, tkr.id_barang, 
      //       tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, tb.barang_alias as alias_barang, 
      //       tkr.id_satuan, ts.satuan_nama as nama_satuan, tblk.id_gudang, rg.nama as nama_gudang, tblk.id_pelanggan, 
      //       mp.nama as nama_pelanggan, tkr.jumlah, tblk.keterangan'))
      //   ->orderBy('tkr.id_detail_kasir_retur', 'DESC');


      $return = DB::table('tbl_kasir_retur as tp')
      ->leftjoin('m_pelanggan as mp', 'tp.id_pelanggan', 'mp.id')
      ->join('ref_gudang as rg', 'tp.id_gudang', 'rg.id')
      ->select('tp.*', 'mp.nama as nama_pelanggan', 'mp.alamat as alamat_pelanggan', 'mp.telp as telp_pelanggan', 'rg.nama as nama_gudang')
      ->where('tp.tanggal', '>=', $tanggal_start)
      ->where('tp.tanggal', '<=', $tanggal_end)
      ->where(function ($query) use ($search, $tanggal) {
          $query->orwhere('tp.kode_retur', 'like', '%' . $search . '%');
          $query->orwhere('tp.tanggal', 'like', '%' . $tanggal . '%');
          $query->orwhere('rg.nama', 'like', '%' . $search . '%');
          $query->orwhere('mp.nama', 'like', '%' . $search . '%');
        })
      ->orderBy('tp.id_retur', 'DESC');
    }
    $totalrecord = count($return->get());
    $return_2    = $return->offset($start)->limit($length);
    $no          = ($start == 0) ? 0 : $start;
    $arr = array();
    foreach ($return_2->get() as $list) {
      $no++;
      $arr[] = array(
        'nomor' => $no,
        'kode_retur'    => $list->kode_retur,
        'no_faktur'    => $list->no_faktur,
        'tanggal'       => tgl_full($list->tanggal, ''),
        // 'nama_barang'   => $list->nama_barang,
        // 'jumlah'        => $list->jumlah,
        // 'nama_satuan'   => $list->nama_satuan,
        'nama_pelanggan' => $list->nama_pelanggan,
        'nama_gudang'   => $list->nama_gudang,
        // 'harga'         => $list->harga,
        // 'total'         => $list->total,
        'aksi'          => $this->get_aksi($list->id_retur, $group_where->group_aktif) .
          '<input type="hidden" id="table_id' . $list->id_retur . '" value="' . $list->id_retur . '">' .
          '<input type="hidden" id="table_kode' . $list->id_retur . '" value="' . $list->kode_retur . '">' .
          '<input type="hidden" id="table_tanggal' . $list->id_retur . '" value="' . tgl_full($list->tanggal, '') . '">' .
          '<input type="hidden" id="table_idgudang' . $list->id_retur . '" value="' . $list->id_gudang . '">' .
          '<input type="hidden" id="table_namagudang' . $list->id_retur . '" value="' . $list->nama_gudang . '">' .
          '<input type="hidden" id="table_idpelanggan' . $list->id_retur . '" value="' . $list->id_pelanggan . '">' .
          '<input type="hidden" id="table_namapelanggan' . $list->id_retur . '" value="' . $list->nama_pelanggan . '">' .
          '<input type="hidden" id="table_keterangan' . $list->id_retur . '" value="' . $list->keterangan . '">'
      );
    }

    $data = array(
      'draw' => $draw,
      'recordsTotal' => $totalrecord,
      'recordsFiltered' => $totalrecord,
      'data' => $arr
    );
    return response()->json($data);
  }
  
  public function searchtanggallama(Request $request)
  {
    $id_profil = Auth::user()->id_profil;
    $group = Auth::user()->group_id;
    $where = "";
    if ($group == 5 || $group == 6 || $group == 8) { // TODO
      $where = "WHERE id_profil='$id_profil'";
    }
    $d_gudang = DB::select(base_gudang($where));
    $id_gudang = array();
    foreach ($d_gudang as $d) {
      $id_gudang[] = $d->id_gudang;
    }
    $group_where = DB::table('tbl_group')->where('group_id', $group)->first();

    $draw = $request->get('draw');
    $start = $request->get('start');
    $length = $request->get('length');
    $filter = $request->get('search');
    $search = (isset($filter['value'])) ? strtolower($filter['value']) : false;
    $tanggal = (isset($filter['value'])) ? tgl_full($filter['value'], '99') : false;

    $tanggalrange = explode('s.d.', $request->get('tanggal'));
    $tanggal_start  = tgl_full($tanggalrange[0], 99);
    $tanggal_end    = tgl_full($tanggalrange[1], 99);
    $return = DB::table('tbl_kasir_detail_retur as tkr')->leftjoin('tbl_barang as tb', 'tkr.id_barang', 'tb.barang_id')
      ->leftjoin('tbl_satuan as ts', 'tkr.id_satuan', 'ts.satuan_id')
      ->leftjoin('ref_gudang as rg', 'tkr.id_gudang', 'rg.id')
      ->leftjoin('m_pelanggan as mp', 'tkr.id_pelanggan', 'mp.id')
      ->where('tkr.tanggal', '>=', $tanggal_start)
      ->where('tkr.tanggal', '<=', $tanggal_end)
      ->whereIn('tkr.id_gudang', $id_gudang)
      ->select(DB::raw('tkr.id_returkasir_detail, tkr.kode_retur, tkr.tanggal, tkr.id_barang,
             tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, tb.barang_alias as alias_barang, 
             tkr.id_satuan, ts.satuan_nama as nama_satuan, tkr.id_gudang, rg.nama as nama_gudang, tkr.id_pelanggan, 
             mp.nama as nama_pelanggan, tkr.jumlah, tkr.harga, tkr.total, tkr.keterangan, tkr.id_log_stok'))
      ->orderBy('tkr.id_returkasir_detail', 'DESC');

    if ($search) {
      $return = DB::table('tbl_kasir_detail_retur as tkr')->leftjoin('tbl_barang as tb', 'tkr.id_barang', 'tb.barang_id')->leftjoin('tbl_satuan as ts', 'tkr.id_satuan', 'ts.satuan_id')->leftjoin('ref_gudang as rg', 'tkr.id_gudang', 'rg.id')->leftjoin('m_pelanggan as mp', 'tkr.id_pelanggan', 'mp.id')
        ->where('tkr.tanggal', '>=', $tanggal_start)
        ->where('tkr.tanggal', '<=', $tanggal_end)
        ->where(function ($query) use ($search, $tanggal) {
          $query->orwhere('tkr.kode_retur', 'like', '%' . $search . '%');
          $query->orwhere('tkr.tanggal', 'like', '%' . $tanggal . '%');
          $query->orwhere('tb.barang_nama', 'like', '%' . $search . '%');
          $query->orwhere('rg.nama', 'like', '%' . $search . '%');
          $query->orwhere('mp.nama', 'like', '%' . $search . '%');
        })
        ->whereIn('tkr.id_gudang', $id_gudang)
        ->select(DB::raw('tkr.id_returkasir_detail, tkr.kode_retur, tkr.tanggal, tkr.id_barang, 
            tb.barang_nama as nama_barang, tb.barang_kode as kode_barang, tb.barang_alias as alias_barang, 
            tkr.id_satuan, ts.satuan_nama as nama_satuan, tkr.id_gudang, rg.nama as nama_gudang, tkr.id_pelanggan, 
            mp.nama as nama_pelanggan, tkr.jumlah, tkr.harga, tkr.total, tkr.keterangan, tkr.id_log_stok'))
        ->orderBy('tkr.id_returkasir_detail', 'DESC');
    }
    $totalrecord = count($return->get());
    $return_2    = $return->offset($start)->limit($length);
    $no          = ($start == 0) ? 0 : $start;
    $arr = array();
    foreach ($return_2->get() as $list) {
      $no++;
      $arr[] = array(
        'nomor' => $no,
        'kode_retur'    => $list->kode_retur,
        'tanggal'       => tgl_full($list->tanggal, ''),
        'nama_barang'   => $list->nama_barang,
        'jumlah'        => $list->jumlah,
        'nama_satuan'   => $list->nama_satuan,
        'nama_pelanggan' => $list->nama_pelanggan,
        'nama_gudang'   => $list->nama_gudang,
        'harga'         => $list->harga,
        'total'         => $list->total,
        'aksi'          => $this->get_aksi($list->id_returkasir_detail, $group_where->group_aktif) .
          '<input type="hidden" id="table_id' . $list->id_returkasir_detail . '" value="' . $list->id_returkasir_detail . '">' .
          '<input type="hidden" id="table_kode' . $list->id_returkasir_detail . '" value="' . $list->kode_retur . '">' .
          '<input type="hidden" id="table_tanggal' . $list->id_returkasir_detail . '" value="' . tgl_full($list->tanggal, '') . '">' .
          '<input type="hidden" id="table_idbarang' . $list->id_returkasir_detail . '" value="' . $list->id_barang . '">' .
          '<input type="hidden" id="table_namabarang' . $list->id_returkasir_detail . '" value="' . $list->nama_barang . '">' .
          '<input type="hidden" id="table_kodebarang' . $list->id_returkasir_detail . '" value="' . $list->kode_barang . '">' .
          '<input type="hidden" id="table_aliasbarang' . $list->id_returkasir_detail . '" value="' . $list->alias_barang . '">' .
          '<input type="hidden" id="table_idgudang' . $list->id_returkasir_detail . '" value="' . $list->id_gudang . '">' .
          '<input type="hidden" id="table_namagudang' . $list->id_returkasir_detail . '" value="' . $list->nama_gudang . '">' .
          '<input type="hidden" id="table_idpelanggan' . $list->id_returkasir_detail . '" value="' . $list->id_pelanggan . '">' .
          '<input type="hidden" id="table_namapelanggan' . $list->id_returkasir_detail . '" value="' . $list->nama_pelanggan . '">' .
          '<input type="hidden" id="table_idsatuan' . $list->id_returkasir_detail . '" value="' . $list->id_satuan . '">' .
          '<input type="hidden" id="table_jumlah' . $list->id_returkasir_detail . '" value="' . $list->jumlah . '">' .
          '<input type="hidden" id="table_harga' . $list->id_returkasir_detail . '" value="' . $list->harga . '">' .
          '<input type="hidden" id="table_total' . $list->id_returkasir_detail . '" value="' . $list->total . '">' .
          '<input type="hidden" id="table_keterangan' . $list->id_returkasir_detail . '" value="' . $list->keterangan . '">' .
          '<input type="hidden" id="table_idlog_stok' . $list->id_returkasir_detail . '" value="' . $list->id_log_stok . '">'
      );
    }

    $data = array(
      'draw' => $draw,
      'recordsTotal' => $totalrecord,
      'recordsFiltered' => $totalrecord,
      'data' => $arr
    );
    return response()->json($data);
  }

  public function get_barang_lama(Request $request)
  {
    $term = trim($request->q);
    if (empty($term)) {
      return \Response::json([]);
    }
    $search = strtolower($term);
    $d_query = DB::select("SELECT
					b.barang_id,
					b.satuan_id,
					b.barang_kode,
					b.barang_nama,
          			b.barang_alias,
					b.barang_id_parent,
					b.barang_status_bahan,
					s.satuan_nama,
					s.satuan_satuan,
					d.detail_harga_barang_harga_jual harga
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
    return \Response::json($d_query);
  }

  public function get_barang(Request $request)
  {
    $term = trim($request->q);

    if (empty($term)) {
      return \Response::json([]);
    }

    $search = strtolower($term);
    $barangs = DB::select("SELECT
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
						tbl_detail_retur AS a
				WHERE
				detail_harga_barang_tanggal =
				( SELECT MAX( detail_harga_barang_tanggal ) FROM tbl_detail_retur AS b WHERE a.barang_id = b.barang_id )) AS d ON b.barang_id = d.barang_id
                WHERE b.barang_kode LIKE '%$search%' OR b.barang_nama LIKE '%$search%' OR b.barang_alias LIKE '%$search%'");

    return \Response::json($barangs);
  }

  public function get_produk(Request $request)
  {
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

  public function attr_pelanggan(Request $request)
  {
    $id = $request->get('id');
    $arr = array();
    if (is_numeric($id)) {
      $d_data = DB::SELECT(
        "SELECT * FROM (
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
        WHERE mp.id = '$id' AND mp.tanggal_akhir >= DATE(NOW())) as a"
      );
      foreach ($d_data as $d) {
        $arr[] = array(
          'id_pelanggan' => $d->id,
          'nama'        => $d->nama,
          'alamat'      => $d->alamat,
          'telp'        => $d->telp,
          'nomor'       => $d->nomor,
          'status'      => $d->status,
          'poin'        => $d->poin
        );
      }
    } else {
      $arr[] = array(
        'id_pelanggan'       => "",
        'nama'        => "",
        'alamat'      => "",
        'telp'        => "",
        'nomor'       => "",
        'status'      => "1",
        'poin'        => '0'
      );
    }

    return response()->json($arr);
  }

  public function hapus(Request $request)
  {
    $id = $request->get('id');
    $d_barang = DB::table('tbl_kasir_retur')->where('id_retur', $id);
    // $id_log_stok = array();
    // foreach ($d_barang->get() as $d) {
    //   $id_log_stok[] = $d->id_log_stok;
    // }
    // DB::table('tbl_log_stok')->whereIn('log_stok_id', $id_log_stok)->delete();
    DB::table('tbl_kasir_retur')->where(array('id_retur' => $id))->delete();
    DB::table('tbl_kasir_detail_retur_baru')->where(array('id_retur' => $id))->delete();
    DB::table('tbl_kasir_detail_produK_retur')->where(array('id_retur' => $id))->delete();
    trigger_log($id, "Menghapus Retur Penjualan", 3);
  }

  function get_aksi($id, $status)
  {
    $session = Auth::user();
    switch ($status) {
      case '2':
        if ($session->group_id == 8) {
          $html = '<div class="btn-group"><a  href="' . url('kasirreturbaru_detail/' . $id) . '" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a></div>';
        } else {
          $html = '<div class="btn-group"><a  href="' . url('kasirreturbaru_detail/' . $id) . '" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a> <a onclick="deleteData(' . $id . ')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa  fa-trash"></i></a></div>';
        }
        break;
      case '1':
        $html = '<div class="btn-group"><a  href="' . url('kasirreturbaru_detail/' . $id) . '" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a></div>';
        break;
      default:
        if ($session->group_id == 8) {
          $html = '<div class="btn-group"><a  href="' . url('kasirreturbaru_detail/' . $id) . '" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a></div>';
        } else {
          $html = '<div class="btn-group"><a  href="' . url('kasirreturbaru_detail/' . $id) . '" class="btn btn-xs btn-primary" data-toggle="tooltip" data-placement="botttom" title="Detail Data"  style="color:white;"><i class="fa fa-eye"></i></a> <a onclick="deleteData(' . $id . ')" class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="botttom" title="Hapus Data" style="color:white;"><i class="fa fa-trash"></i></a></div>';
        }
        break;
    }
    return $html;
  }

  public function detail($id)
  {
    $id_profil = Auth::user()->id_profil;
    $group = Auth::user()->group_id;
    $where = "";
    if ($group == 5 || $group == 6) {
      $where = "WHERE id_profil='$id_profil'";
    }

    $d_gudang = DB::select(base_gudang($where));
    $id_gudang = array();
    foreach ($d_gudang as $d) {
      $id_gudang[] = $d->id_gudang;
    }
    $where_gudang = "";
    if (sizeof($id_gudang) > 0) {
      $gudang = implode(',', $id_gudang);
      $where_gudang = "WHERE rf.id IN ($gudang)";
    }

    $d_data = DB::table('tbl_kasir_retur as tp')
      ->leftjoin('m_pelanggan as mp', 'tp.id_pelanggan', 'mp.id')
      ->join('ref_gudang as rg', 'tp.id_gudang', 'rg.id')
      ->select('tp.*', 'mp.nama as nama_pelanggan', 'mp.alamat as alamat_pelanggan', 'mp.telp as telp_pelanggan', 'rg.nama as nama_gudang')
      ->where('tp.id_retur', $id)->first();

    $data['data']         = $this->data2($d_data);
    $data['satuan']       = DB::table('tbl_satuan')->orderBy('satuan_nama', 'asc')->get();
    $data['carabayar']    = \Config::get('constants.carabayar');
    $data['no_auto']      = "";
    $data['pelanggan']    = DB::select("select mp.*, rf.nama as nama_gudang from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id $where_gudang");
    $data['gudang']       = DB::select(base_gudang($where));
    // $data['pembayaran'] = DB::table('m_metode')->where('status', '=', '1')->orderby('urutan', 'asc')->get();
    // dd($data);
    if ($this->agent->isMobile()) {
      // code...
      return view('admin_mobile.kasir.detail_retur_baru')->with('data', $data);
    } else {
      // code...
      return view('admin.kasir.detail_retur_baru')->with('data', $data);
    }
  }
  
   public function get_edit(Request $request)
  {
    $id = $request->get('id');
    $d_data = DB::table('tbl_kasir_detail_produk_retur as tpd')
      ->leftjoin('m_produk as mp', 'tpd.id_produk', 'mp.id')
      ->leftjoin('tbl_satuan as ts2', 'tpd.id_satuan', 'ts2.satuan_id')
      ->where('tpd.id_retur', $id)
      ->select('tpd.*', 'mp.id_type_ukuran', 'mp.nama as nama_produk', 'ts2.satuan_nama as nama_satuan', 'ts2.satuan_satuan as satuan_satuan')
      ->orderBy('tpd.id_kasir_detail_produk_retur', 'asc');

    $d_barang = DB::table('tbl_kasir_detail_retur_baru as tkd')
      ->leftjoin('tbl_barang as tb', 'tkd.id_barang', 'tb.barang_id')
      ->leftjoin('tbl_satuan as ts2', 'tkd.id_satuan', 'ts2.satuan_id')
      ->where('tkd.id_retur', $id)
      // ->whereNull('tkd.id_detail_kasir_produk_retur')
      ->select('tkd.*', 'tb.barang_nama as nama_barang', 'tb.barang_kode as kode_barang','tb.barang_alias as alias_barang', 'tb.barang_kode as kode_barang', 'tb.barang_alias as alias_barang', 'ts2.satuan_nama as nama_satuan', 'ts2.satuan_satuan as satuan_satuan')
      ->orderBy('tkd.id_detail_kasir_retur', 'asc');

    if ($d_data->count() > 0) {
      foreach ($d_data->get() as $d) {
        $arr['produk'][] = array(
          'id_retur'      => $d->id_retur,
          'id_produk'     => $d->id_produk,
          'nama_produk'   => $d->nama_produk,
          'id_barang'     => "",
          'nama_barang'   => "",
          'kode_barang'   => "",
          'alias_barang'  => "",
          'jumlah'        => $d->jumlah,
          'nama_satuan'   => $d->satuan_satuan
        );
      }
    } else {
      $arr['produk'] = array();
    }

    if ($d_barang->count() > 0) {
      foreach ($d_barang->get() as $d) {
        $arr['barang'][] = array(
          'id_retur'      => $d->id_retur,
          'id_produk'     => '',
          'nama_produk'   => '',
          'id_barang'     => $d->id_barang,
          'nama_barang'   => $d->nama_barang,
          'kode_barang'   => $d->kode_barang,
          'alias_barang'  => $d->alias_barang,
          'jumlah'        => $d->jumlah,
          'nama_satuan'   => $d->satuan_satuan
        );
      }
    } else {
      $arr['barang'] = array();
    }

    return response()->json($arr);
  }

  public function get_editlama(Request $request)
  {
    $id = $request->get('id');
    $d_data = DB::table('tbl_kasir_detail_produk_retur as tpd')
      ->leftjoin('m_produk as mp', 'tpd.id_produk', 'mp.id')
      ->leftjoin('tbl_satuan as ts2', 'tpd.id_satuan', 'ts2.satuan_id')
      ->where('tpd.id_retur', $id)
      ->select('tpd.*', 'mp.id_type_ukuran', 'mp.nama as nama_produk', 'ts2.satuan_nama as nama_satuan', 'ts2.satuan_satuan as satuan_satuan');
      // ->orderBy('tpd.id_retur_detail_produk', 'asc');

    $d_barang = DB::table('tbl_kasir_detail_retur_baru as tkd')
      ->leftjoin('tbl_barang as tb', 'tkd.id_barang', 'tb.barang_id')
      ->leftjoin('tbl_satuan as ts2', 'tkd.id_satuan', 'ts2.satuan_id')
      ->where('tkd.id_retur', $id)->whereNull('tkd.id_detail_kasir_produk_retur')
      ->select('tkd.*', 'tb.barang_nama as nama_barang', 'tb.barang_kode as kode_barang','tb.barang_alias as alias_barang', 'tb.barang_kode as kode_barang', 'tb.barang_alias as alias_barang', 'ts2.satuan_nama as nama_satuan', 'ts2.satuan_satuan as satuan_satuan');
      // ->orderBy('tkd.id_detail_kasir', 'asc');

    if ($d_data->count() > 0) {
      foreach ($d_data->get() as $d) {
        $arr['produk'][] = array(
          'id_retur'      => $d->id_retur,
          'id_produk'     => $d->id_produk,
          'nama_produk'   => $d->nama_produk,
          'id_barang'     => "",
          'nama_barang'   => "",
          'kode_barang'   => "",
          'alias_barang'  => "",
          'jumlah'        => $d->jumlah,
          'nama_satuan'   => $d->satuan_satuan
        );
      }
    } else {
      $arr['produk'] = array();
    }

    if ($d_barang->count() > 0) {
      foreach ($d_barang->get() as $d) {
        $arr['barang'][] = array(
          'id_retur'      => $d->id_retur,
          'id_produk'     => '',
          'nama_produk'   => '',
          'id_barang'     => $d->id_barang,
          'nama_barang'   => $d->nama_barang,
          'kode_barang'   => $d->kode_barang,
          'alias_barang'  => $d->alias_barang,
          'jumlah'        => $d->jumlah,
          'nama_satuan'   => $d->satuan_satuan
        );
      }
    } else {
      $arr['barang'] = array();
    }

    return response()->json($arr);
  }

  public function get_edit_retur(Request $request)
  {
    $id = $request->get('id');
    $subReturBarang = DB::table('tbl_kasir_detail_retur_baru')->select('id_kasir_detail', DB::raw('SUM(jumlah) as jumlah_retur'))->groupBy('id_kasir_detail');
    $subReturProduk = DB::table('tbl_kasir_detail_produk_retur')->select('id_kasir_detail_produk', DB::raw('SUM(jumlah) as jumlah_retur'))->groupBy('id_kasir_detail_produk');
    $d_data = DB::table('tbl_kasir_detail_produk as tpd')
    ->leftjoin('m_produk as mp', 'tpd.id_produk', 'mp.id')
    ->leftjoin('tbl_satuan as ts2', 'tpd.id_satuan', 'ts2.satuan_id')
    ->leftjoinSub($subReturProduk, 'srp', function($join){
      $join->on('tpd.id_kasir_detail_produk', '=', 'srp.id_kasir_detail_produk');
    })
    ->where('tpd.id_kasir', $id)
    ->select('tpd.id_kasir_detail_produk', 'tpd.id_produk', 'tpd.jumlah', 'tpd.id_satuan', 'srp.jumlah_retur', 'mp.nama as nama_produk', 'ts2.satuan_nama as nama_satuan', 'ts2.satuan_satuan as satuan_satuan')
    ->orderBy('tpd.id_kasir_detail_produk', 'asc');

    $d_barang = DB::table('tbl_kasir_detail as tkd')
    ->leftjoin('tbl_barang as tb', 'tkd.id_barang', 'tb.barang_id')
    ->leftjoin('tbl_satuan as ts2', 'tkd.id_satuan', 'ts2.satuan_id')
    ->leftjoinSub($subReturBarang, 'srb', function($join){
      $join->on('tkd.id_detail_kasir', '=', 'srb.id_kasir_detail');
    })
    ->where('tkd.id_kasir', $id)
    ->where('tkd.id_detail_kasir_produk', '0')
    ->select('tkd.id_detail_kasir', 'tkd.id_barang', 'tkd.jumlah', 'tkd.id_satuan', 'srb.jumlah_retur', 'tb.barang_nama as nama_barang', 'tb.barang_kode as kode_barang', 'tb.barang_alias as alias_barang', 'ts2.satuan_nama as nama_satuan', 'ts2.satuan_satuan as satuan_satuan')->orderBy('tkd.id_detail_kasir', 'asc');
    if ($d_data->count() > 0) {
      foreach ($d_data->get() as $d) {
        $arr['produk'][] = array(
          'id' => $d->id_kasir_detail_produk,
          'id_detail_kasir'  => "",
          'id_produk'     => $d->id_produk,
          'nama_produk'   => $d->nama_produk,
          'jumlah'        => $d->jumlah,
          'id_satuan'     => $d->id_satuan,
          'nama_satuan'   => $d->satuan_satuan,
          'status'        => "1",
          'jumlah_retur'  => $d->jumlah_retur ?? 0,
          'keterangan_retur'  => ($d->jumlah_retur != null) ? $d->jumlah_retur.' '.$d->satuan_satuan.' Barang / Produk Ini Sudah Diretur' : '-',
        );
      }
    } else {
      $arr['produk'] = array();
    }

    if ($d_barang->count() > 0) {
      foreach ($d_barang->get() as $d) {
        $arr['barang'][] = array(
          'id' => $d->id_detail_kasir,
          'id_barang'     => $d->id_barang,
          'nama_barang'   => $d->nama_barang,
          'kode_barang'   => $d->kode_barang,
          'alias_barang'  => $d->alias_barang,
          'jumlah'        => $d->jumlah,
          'id_satuan'     => $d->id_satuan,
          'nama_satuan'   => $d->satuan_satuan,
          'status'        => "2",
          'jumlah_retur'  => $d->jumlah_retur ?? 0,
          'keterangan_retur'  => ($d->jumlah_retur != null) ? $d->jumlah_retur.' '.$d->satuan_satuan.' Barang / Produk Ini Sudah Diretur' : '-',
        );
      }
    } else {
      $arr['barang'] = array();
    }

    return response()->json($arr);
  }

  public function retur($id)
  {
    $id_profil = Auth::user()->id_profil;
    $group = Auth::user()->group_id;
    $where = "";
    if ($group == 5 || $group == 6) {
      $where = "WHERE id_profil='$id_profil'";
    }

    $d_gudang = DB::select(base_gudang($where));
    $id_gudang = array();
    foreach ($d_gudang as $d) {
      $id_gudang[] = $d->id_gudang;
    }
    $where_gudang = "";
    if (sizeof($id_gudang) > 0) {
      $gudang = implode(',', $id_gudang);
      $where_gudang = "WHERE rf.id IN ($gudang)";
    }

    $d_data = DB::table('tbl_kasir as tp')->leftjoin('m_pelanggan as mp', 'tp.id_pelanggan', 'mp.id')->join('ref_gudang as rg', 'tp.id_gudang', 'rg.id')->select('tp.*', 'mp.nama as nama_pelanggan', 'mp.alamat as alamat_pelanggan', 'mp.telp as telp_pelanggan', 'rg.nama as nama_gudang')->where('id_kasir', $id)->first();
    $data['data']         = $this->data($d_data, false);
    $data['satuan']       = DB::table('tbl_satuan')->orderBy('satuan_nama', 'asc')->get();
    $data['carabayar']    = \Config::get('constants.carabayar');
    $data['no_auto']      = "";
    $data['pelanggan']  = DB::select("select mp.id, CASE WHEN mp.telp IS NULL THEN mp.nama ELSE CONCAT(mp.nama,' (',mp.telp,')') END as nama, rf.nama as nama_gudang from m_pelanggan as mp LEFT JOIN ref_gudang as rf ON mp.id_gudang=rf.id $where_gudang");
    $data['gudang']       = DB::select(base_gudang($where));
    $data['pembayaran'] = DB::table('m_metode')->where('status', '=', '1')->orderby('urutan', 'asc')->get();
    // dd($data['data']);
    if ($this->agent->isMobile()) {
      if ($group == 1 || $group == 6) {
        return view('admin_mobile.kasir.retur.index')->with('data', $data);
      } else {
        if ($d_data->status_posting == 2) {
          echo "<script>setTimeout(function() { alert('Penjualan Sudah Tidak Bisa dirubah!'); window.location= '" . url('kasir') . "' }, 1000)</script>";
        } else {
          return view('admin_mobile.kasir.retur.index')->with('data', $data);
        }
      }
    } else {
      if ($group == 1 || $group == 6) {
        return view('admin.kasir.retur.index')->with('data', $data);
      } else {
        if ($d_data->status_posting == 2) {
          echo "<script>setTimeout(function() { alert('Penjualan Sudah Tidak Bisa dirubah!'); window.location= '" . url('kasir') . "' }, 1000)</script>";
        } else {
          return view('admin.kasir.retur.index')->with('data', $data);
        }
      }
    }
  }

  public function simpan_retur(Request $request)
  {
    DB::beginTransaction();
    try {
      $PARAM1 = $request->PARAM1;
      $PARAM2 = $request->PARAM2;
      unset($PARAM1['tanggal_retur']);
      $PARAM1['tanggal_retur'] = date('Y-m-d', strtotime($request->PARAM1['tanggal_retur']));
      $id_retur = DB::table('tbl_kasir_retur')->insertGetId($PARAM1);
      $_PARAMBARANG2 = [];
      foreach ($PARAM2 as $value) {
        if ($value['status'] == 1) {
          $_PARAM2['id_retur'] = $id_retur;
          $_PARAM2['id_kasir_detail_produk'] = $value['id_kasir_detail_produk'];
          $_PARAM2['id_satuan'] = $value['id_satuan'];
          $_PARAM2['jumlah'] = $value['jumlah_retur'];
          $_PARAM2['id_produk'] = $value['id_produk'];
          $id_detail_kasir_produk_retur = DB::table('tbl_kasir_detail_produk_retur')->insertGetId($_PARAM2);
          $d_barang = DB::table('m_detail_produk as mdp')->join('tbl_barang as tb', 'mdp.id_barang', 'tb.barang_id')->where('mdp.id_produk', $value['id_produk'])->select(DB::raw('mdp.*,tb.satuan_id as id_satuan'))->get();
          foreach ($d_barang as $barang) {
            $_PARAMBARANG2[] = [
              'id_retur'  => $id_retur,
              'id_kasir_detail' => NULL,
              'id_detail_kasir_produk_retur' => $id_detail_kasir_produk_retur,
              'id_barang' => $barang->id_barang,
              'jumlah'    => $barang->jumlah * $value['jumlah_retur'],
              'id_satuan' => $barang->id_satuan
            ];
          }
        } else {
          $_PARAMBARANG2[] = [
            'id_retur'  => $id_retur,
            'id_kasir_detail' => $value['id_kasir_detail'],
            'id_detail_kasir_produk_retur' => NULL,
            'id_barang' => $value['id_barang'],
            'jumlah'    => $value['jumlah_retur'],
            'id_satuan' => $value['id_satuan']
          ];
        }
      }
      if (!empty($_PARAMBARANG2)) DB::table('tbl_kasir_detail_retur_baru')->insert(collect($_PARAMBARANG2)->toArray());
      DB::commit();
      return redirect()->back()->with('message', 'Data Berhasil di Retur!');
    } catch (\Throwable $th) {
      DB::rollback();
      return redirect()->back();
      // dd($th->getMessage() . ' ' . $th->getLine());
    }
  }
}
