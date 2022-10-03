<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Controller;
use Auth;
use Redirect;
use Jenssegers\Agent\Agent;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $group_id,$id_profil;
    public function __construct()
    {
        // dd($this->middleware);
        $this->middleware('auth');
        // $this->group_id = Auth::user()->group_id;
        // $this->id_profil = Auth::user()->id_profil;
        $this->agent = new Agent();

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $group_id = Auth::user()->group_id;
        $id_profil = Auth::user()->id_profil;

        if ($group_id == 1) {
            $a = DB::select('select * from ref_gudang');
            // $where = 'where 1=1';
        } elseif ($group_id == 6) {
            $a = DB::select('select * from ref_gudang');
        // $where = 'where id=1';
        } elseif ($group_id == 7) {
            $a = DB::select('select * from ref_gudang');
        } elseif ($group_id == 5) {
            $a = DB::table('ref_gudang')->where('id_profil', $id_profil)->get();
        }else{
            $a = DB::table('ref_gudang')->where('id_profil', $id_profil)->get();
        }
        $data['gudang'] = $a;
        $data['user_group'] = $group_id;


        if ($this->agent->isMobile()) {
          return view('admin_mobile.data')->with('data',$data);
        }else {
          return view('admin.data')->with('data',$data);
        }
        /*return view('admin_mobile.data')->with('data',$data);*/
    }
    public function getbarangbanyak(Request $request){
        $id_profil = Auth::user()->id_profil;
        $datatanggal = explode('-', $request->range);
        $group_id = Auth::user()->group_id;
        if ($group_id == 1 || $group_id == 7 || $group_id == 6) {
            if ($request->gudang) {
                $where = "WHERE k.id_gudang = ".$request->gudang." and k.tanggal_faktur >= '".date_db(trim($datatanggal[0]))."' and k.tanggal_faktur <='".date_db(trim($datatanggal[1]))."' AND b.barang_status_bahan='1' ";
                $id_gudang ="t.id_ref_gudang=".$request->gudang;
            } else {
                $where ="WHERE b.barang_status_bahan='1'";
                 $id_gudang="t.id_ref_gudang";
            }
            // print_r($where);exit;
        } else {
            $a = DB::table('ref_gudang')->where('id_profil', $id_profil)->get()->first();
            $id = $a->id;
            if ($request->range) {
                $where = "WHERE k.id_gudang ='".$id."' and k.tanggal_faktur >= '".date_db(trim($datatanggal[0]))."' and k.tanggal_faktur <='".date_db(trim($datatanggal[1]))."' AND b.barang_status_bahan='1' ";
                $id_gudang="t.id_ref_gudang=".$id;

            } else {
                $where = "WHERE k.id_gudang ='".$id ."' AND b.barang_status_bahan='1'";
                 $id_gudang="t.id_ref_gudang=".$id;

            }

            // print_r($where );exit;
        }
        DB::enableQueryLog();
                $left_join ="LEFT JOIN (
                        SELECT
                            l.id_barang,
                            b.barang_nama,
                            s.satuan_nama,
                            s.satuan_satuan,
                            ( l.jumlah_masuk - l.jumlah_keluar ) total
                        FROM
                            (
                            SELECT
                                t.id_barang,
                                t.id_ref_gudang,
                                Sum( t.unit_masuk ) AS jumlah_masuk,
                                Sum( t.unit_keluar ) AS jumlah_keluar,
                                t.id_satuan
                            FROM
                                tbl_log_stok AS t
                            WHERE
                               ".$id_gudang."
                            GROUP BY
                                t.id_barang,
                                t.id_ref_gudang
                            ) l
                            LEFT JOIN tbl_barang AS b ON l.id_barang = b.barang_id
                            LEFT JOIN tbl_satuan AS s ON l.id_satuan = s.satuan_id
                        ) q ON q.id_barang = b.barang_id";
        $query = DB::select("SELECT
               	b.barang_id,
                b.barang_kode,
                b.barang_nama,
                s.satuan_nama,
                k.id_gudang ,
                Sum( d.jumlah ) AS total_barang,
                q.total stok
                FROM
                tbl_kasir_detail AS d
                LEFT JOIN tbl_barang AS b ON d.id_barang = b.barang_id
                LEFT JOIN tbl_satuan AS s ON d.id_satuan = s.satuan_id
                LEFT JOIN tbl_kasir AS k ON d.id_kasir = k.id_kasir $left_join
                $where
                GROUP BY
                d.id_barang,
                d.id_satuan ORDER BY
                Sum( d.jumlah ) DESC");
                // dd(DB::getQueryLog());
              $no = 0;
              $data = array();
                foreach ($query as $list) {
                    $no++;
                    $row = array();
                    if ($this->agent->isMobile()) {
                      // code...
                      $row['no'] = $no;
                      $row['barang_nama'] = $list->barang_nama.' || '.$list->satuan_nama;
                      $row['jumlah'] = format_angka($list->total_barang);
                      $row['stok'] = format_angka($list->stok);
                    }else {
                      // code...
                      $row[] = $no;
                      $row[] = $list->barang_nama.' || '.$list->satuan_nama;
                      $row[] = format_angka($list->total_barang);
                      $row[] = format_angka($list->stok);
                    }
                    /*$row['no'] = $no;
                      $row['barang_nama'] = $list->barang_nama.' || '.$list->satuan_nama;
                      $row['jumlah'] = format_angka($list->total_barang);
                      $row['stok'] = format_angka($list->stok);
                    $data[] = $row;*/
                }
                $output = array("data" => $data);
                 return response()->json($output);



    }
    public function getbarangpilihan(Request $request){
        $id_profil = Auth::user()->id_profil;
        $datatanggal = explode('-', $request->range);
        $group_id = Auth::user()->group_id;
        if ($group_id == 1 || $group_id == 7 || $group_id == 6) {
            if ($request->gudang) {
                $where = "WHERE k.id_gudang = ".$request->gudang." and k.tanggal_faktur >= '".date_db(trim($datatanggal[0]))."' and k.tanggal_faktur <='".date_db(trim($datatanggal[1]))."' and d.id_barang IN ( 569, 571,610,587,594,601,602,606,605,607,631,632,633,634)";
                $id_gudang ="t.id_ref_gudang=".$request->gudang;
            } else {
                $where =" where d.id_barang IN (569, 571,610,587,594,601,602,606,605,607,631,632,633,634)";
                 $id_gudang="t.id_ref_gudang";
            }
            // print_r($where);exit;
        } else {
            $a = DB::table('ref_gudang')->where('id_profil', $id_profil)->get()->first();
            $id = $a->id;
            if ($request->range) {
                $where = "WHERE k.id_gudang ='".$id."' and k.tanggal_faktur >= '".date_db(trim($datatanggal[0]))."' and k.tanggal_faktur <='".date_db(trim($datatanggal[1]))."' and d.id_barang IN ( 569, 571,610,587,594,601,602,606,605,607,631,632,633,634 ) ";
                $id_gudang="t.id_ref_gudang=".$id;

            } else {
                $where = "WHERE k.id_gudang ='".$id ."' and d.id_barang IN ( 569, 571,610,587,594,601,602,606,605,607,631,632,633,634)  ";
                 $id_gudang="t.id_ref_gudang=".$id;

            }

            // print_r($where );exit;
        }
        // DB::enableQueryLog();
                $left_join ="LEFT JOIN (
                        SELECT
                            l.id_barang,
                            b.barang_nama,
                            s.satuan_nama,
                            s.satuan_satuan,
                            ( l.jumlah_masuk - l.jumlah_keluar ) total
                        FROM
                            (
                            SELECT
                                t.id_barang,
                                t.id_ref_gudang,
                                Sum( t.unit_masuk ) AS jumlah_masuk,
                                Sum( t.unit_keluar ) AS jumlah_keluar,
                                t.id_satuan
                            FROM
                                tbl_log_stok AS t
                            WHERE
                               ".$id_gudang."
                            GROUP BY
                                t.id_barang,
                                t.id_ref_gudang
                            ) l
                            LEFT JOIN tbl_barang AS b ON l.id_barang = b.barang_id
                            LEFT JOIN tbl_satuan AS s ON l.id_satuan = s.satuan_id
                        ) q ON q.id_barang = b.barang_id";
        $query = DB::select("SELECT
               	b.barang_id,
                b.barang_kode,
                b.barang_nama,
                s.satuan_nama,
                k.id_gudang ,
                Sum( d.jumlah ) AS total_barang,
                q.total stok
                FROM
                tbl_kasir_detail AS d
                LEFT JOIN tbl_barang AS b ON d.id_barang = b.barang_id
                LEFT JOIN tbl_satuan AS s ON d.id_satuan = s.satuan_id
                LEFT JOIN tbl_kasir AS k ON d.id_kasir = k.id_kasir $left_join
                $where
                GROUP BY
                d.id_barang,
                d.id_satuan ORDER BY
                 b.barang_nama ASC");
                // dd(DB::getQueryLog());
              $no = 0;
              $data = array();
                foreach ($query as $list) {
                    $no++;
                    $row = array();
                    $row[] = $no;
                    $row[] = $list->barang_nama.' || '.$list->satuan_nama;
                    $row[] = format_angka($list->total_barang);
                    $row[] = format_angka($list->stok);

                    $data[] = $row;
                }
                $output = array("data" => $data);
                return response()->json($output);
    }
    public function gettabelproduk(Request $request)
    {
        $id_profil = Auth::user()->id_profil;
        $datatanggal = explode('-', $request->range);

        // print_r($request->range);exit;
        // $tanggal = str_replace(' ')
            $tanggal = date('Y-m-d');
            $id_profil2 = Auth::user()->id_profil;
            $group = Auth::user()->group_id;
            $where = "";
            if($group == 5 || $group == 6){
                $where = "WHERE id_profil='$id_profil2'";
            }
            $d_gudang = DB::select(base_gudang($where));
            $id_gudang = array();
            foreach($d_gudang as $d){
                $id_gudang[] = $d->id_gudang;
            }
            $where_gudang = "";
            if(sizeof($id_gudang) > 0){
                $gudang = implode(',',$id_gudang);
                $where_gudang = "AND k.id_gudang IN ($gudang)";
            }

        $group_id = Auth::user()->group_id;
        if ($group_id == 1 || $group_id == 7 || $group_id == 6) {
            if ($request->gudang) {
                $where = "WHERE k.id_gudang = ".$request->gudang." and k.tanggal_faktur >= '".date_db(trim($datatanggal[0]))."' and k.tanggal_faktur <='".date_db(trim($datatanggal[1]))."' ";
            } else {
                $where ="WHERE k.tanggal_faktur='".$tanggal."' $where_gudang";
            }

            // print_r($where);exit;
        } else {
            $a = DB::table('ref_gudang')->where('id_profil', $id_profil)->get()->first();
            $id = $a->id;
            if ($request->range) {
                $where = "WHERE k.id_gudang ='".$id."' and k.tanggal_faktur >= '".date_db(trim($datatanggal[0]))."' and k.tanggal_faktur <='".date_db(trim($datatanggal[1]))."' and k.jenis_transaksi='1' ";

            }else{
                $where = "WHERE k.tanggal_faktur='".$tanggal."' and k.id_gudang ='".$id ."' and k.jenis_transaksi = '1'";

            }

        }
        // print_r($where );exit;

        $produk = DB::select('SELECT
                    *
                FROM
                    (
                    SELECT
                        p.id_produk,
                        sum(p.jumlah) jumlah_produk,
                        mp.nama,
                        mp.kode_produk,
                        SUM( p.total ) jumlah_total
                    FROM
                        tbl_kasir_detail_produk AS p
                        LEFT JOIN m_produk AS mp ON p.id_produk = mp.id
                        LEFT JOIN tbl_kasir AS k ON p.id_kasir = k.id_kasir
                        '.$where.'
                    GROUP BY
                        p.id_produk,
                        mp.nama,
                        mp.kode_produk
                    ) p
                ORDER BY
                    p.jumlah_total DESC');
                    $no = 0;
                $data = array();
                foreach ($produk as $list) {
                    $no++;
                    $row = array();
                    $row[] = $no;
                    $row[] = $list->nama;
                    $row[] = format_angka($list->jumlah_produk);
                    $row[] = format_angka($list->jumlah_total);

                    $data[] = $row;
                }
                $output = array("data" => $data);
                return response()->json($output);

    }
     public function gettabelpelanggan(Request $request)
    {
        $id_profil = Auth::user()->id_profil;
        $datatanggal = explode('-', $request->range);

        // print_r($request->range);exit;
        // $tanggal = str_replace(' ')

        $group_id = Auth::user()->group_id;
        if ($group_id == 1 || $group_id == 7 || $group_id == 6) {
            if ($request->gudang) {
                $where = "WHERE k.id_gudang = ".$request->gudang." and k.tanggal_faktur >= '".date_db(trim($datatanggal[0]))."' and k.tanggal_faktur <='".date_db(trim($datatanggal[1]))."' ";
            } else {
                $where ="";
            }

            // print_r($where);exit;
        } else {
            $a = DB::table('ref_gudang')->where('id_profil', $id_profil)->get()->first();
            $id = $a->id;
            if ($request->range) {
                $where = "WHERE k.id_gudang ='".$id."' and k.tanggal_faktur >= '".date_db(trim($datatanggal[0]))."' and k.tanggal_faktur <='".date_db(trim($datatanggal[1]))."' ";

            }else{
                $where = "WHERE k.id_gudang ='".$id ."' ";

            }

            // print_r($where );exit;
        }
        $produk = DB::select('SELECT
                    *
                FROM
                    (
                    SELECT
                        p.id_produk,
                        sum( p.jumlah ) jumlah_produk,
                        mp.nama,
                        mp.kode_produk,
                        k.id_pelanggan,
                        pel.nama nama_pelanggan,
                        SUM( p.total ) jumlah_total
                    FROM
                        tbl_kasir_detail_produk AS p
                        LEFT JOIN m_produk AS mp ON p.id_produk = mp.id
                        LEFT JOIN tbl_kasir AS k ON p.id_kasir = k.id_kasir
                        LEFT JOIN m_pelanggan pel ON k.id_pelanggan = pel.id
                         '.$where.'
                    GROUP BY
                        p.id_produk,
                        mp.nama,
                        k.id_pelanggan,
                        mp.kode_produk
                    ) p
                ORDER BY
                    p.jumlah_total DESC');
                    $no = 0;
                $data = array();
                foreach ($produk as $list) {
                    $no++;
                    $row = array();
                    if ($this->agent->isMobile()) {
                      $row['no'] = $no;
                      $row['nama_pelanggan'] = $list->nama_pelanggan;
                      $row['nama'] = $list->nama;
                      $row['jumlah_produk'] = format_angka($list->jumlah_produk);
                      $row['jumlah_total'] = format_angka($list->jumlah_total);
                    }else {
                      $row[] = $no;
                      $row[] = $list->nama_pelanggan;
                      $row[] = $list->nama;
                      $row[] = format_angka($list->jumlah_produk);
                      $row[] = format_angka($list->jumlah_total);
                    }
                    /*$row['no'] = $no;
                      $row['nama_pelanggan'] = $list->nama_pelanggan;
                      $row['nama'] = $list->nama;
                      $row['jumlah_produk'] = format_angka($list->jumlah_produk);
                      $row['jumlah_total'] = format_angka($list->jumlah_total);*/

                    $data[] = $row;
                }
                $output = array("data" => $data);
                return response()->json($output);

    }

    public function getbarang(Request $request)
    {
       $id_profil = Auth::user()->id_profil;
        $datatanggal = explode('-', $request->range);

        // print_r($request->range);exit;
        // $tanggal = str_replace(' ')

        $group_id = Auth::user()->group_id;
        if ($group_id == 1 || $group_id == 7 || $group_id == 6) {
            if ($request->gudang) {
                $where = "WHERE k.id_gudang = ".$request->gudang." and k.tanggal_faktur >= '".date_db(trim($datatanggal[0]))."' and k.tanggal_faktur <='".date_db(trim($datatanggal[1]))."' ";
            } else {
                $where ="";
            }

            // print_r($where);exit;
        } else {
            $a = DB::table('ref_gudang')->where('id_profil', $id_profil)->get()->first();
            $id = $a->id;
            if ($request->range) {
                $where = "WHERE k.id_gudang ='".$id."' and k.tanggal_faktur >= '".date_db(trim($datatanggal[0]))."' and k.tanggal_faktur <='".date_db(trim($datatanggal[1]))."' ";
            } else {
                $where = "WHERE k.id_gudang ='".$id ."' ";
            }

            // print_r($where );exit;
        }

        $produk = DB::select('SELECT
                    *
                FROM
                    (
                    SELECT
                        p.id_kasir_detail_produk,
                        p.id_kasir,
                        p.id_produk,
                        p.jumlah,
                        p.id_satuan,
                        p.harga,
                        p.total,
                        mp.nama,
                        mp.kode_produk,
                        k.id_gudang,
                        SUM( p.total ) jumlah_total ,
                        p.created_at
                    FROM
                        tbl_kasir_detail_produk AS p
                        LEFT JOIN m_produk AS mp ON p.id_produk = mp.id
                        LEFT JOIN tbl_kasir AS k ON p.id_kasir = k.id_kasir
                        '.$where.'
                    GROUP BY
                        p.id_produk,
                        p.id_satuan
                    ) p
                ORDER BY
                    p.jumlah_total DESC');
                    $no = 0;
                $data = array();
                foreach ($produk as $list) {
                    $no++;
                    $row = array();
                    $row[] = $no;
                    $row[] = $list->nama;
                    $row[] = $list->jumlah_total;

                    $data[] = $row;
                }
                $output = array("data" => $data);
                return response()->json($output);

    }
     public function getjumlahomset(Request $request)
    {
       $id_profil = Auth::user()->id_profil;
       $datatanggal = explode('-', $request->range);
       $tanggal = date('Y-m-d');

            $id_profil2 = Auth::user()->id_profil;
            $group = Auth::user()->group_id;
            $where = "";
            if($group == 5 || $group == 6){
                $where = "WHERE id_profil='$id_profil2'";
            }
            $d_gudang = DB::select(base_gudang($where));
            $id_gudang = array();
            foreach($d_gudang as $d){
                $id_gudang[] = $d->id_gudang;
            }
            $where_gudang = "";
            if(sizeof($id_gudang) > 0){
                $gudang = implode(',',$id_gudang);
                $where_gudang = "AND k.id_gudang IN ($gudang)";
            }


        $group_id = Auth::user()->group_id;
        if ($group_id == 1 || $group_id == 7 || $group_id == 6) {
            if ($request->gudang) {
                $where = "WHERE k.id_gudang = ".$request->gudang." and k.tanggal_faktur >= '".date_db(trim($datatanggal[0]))."' and k.tanggal_faktur <='".date_db(trim($datatanggal[1]))."' ";
            } else {
                // $where ="";
                $where ="WHERE k.tanggal_faktur='".$tanggal."' $where_gudang";
            }
        } else {
            $a = DB::table('ref_gudang')->where('id_profil', $id_profil)->get()->first();
            // print_r($datatanggal[0]);exit;
            if ($datatanggal[0]) {
                # code...
                $where = "WHERE k.id_gudang = ".$a->id." and k.tanggal_faktur >= '".date_db(trim($datatanggal[0]))."' and k.tanggal_faktur <='".date_db(trim($datatanggal[1]))."' ";
            } else {
                // $where = "WHERE k.id_gudang = ".$a->id."  ";
                $where = "WHERE k.tanggal_faktur='".$tanggal."' and k.id_gudang = ".$a->id." ";
            }
        }
       /* $data=DB::select("SELECT
            me.id,
            sum( su.jumlah)  as data
        FROM
            m_metode AS me
            LEFT JOIN (
            SELECT
                k.metodebayar,
                m.nama AS metode,k.created_at,
                Sum( k.total_tagihan - k.total_potongan ) AS jumlah
            FROM
                tbl_kasir AS k
                LEFT JOIN m_metode AS m ON k.metodebayar = m.id
            $where
            GROUP BY
            k.metodebayar,m.nama
            ) AS su ON su.metodebayar = me.id");*/
            
        $data=DB::select("SELECT p.id, CASE WHEN SUM(p.data) IS NULL THEN '0' ELSE sum(p.data) END AS data, p.tanggal_bayar
        FROM (
            SELECT
            me.id,
            CASE WHEN sum(su.jumlah) IS NULL THEN '0' ELSE sum( su.jumlah) END AS data,
            CASE WHEN su.tanggal_bayar IS NULL THEN DATE_FORMAT(NOW(), '%d-%m-%Y') ELSE DATE_FORMAT(su.tanggal_bayar, '%d-%m-%Y') END AS tanggal_bayar
            FROM
                m_metode AS me
                LEFT JOIN (
                    SELECT q.metodebayar,
                    q.metode AS metode,q.created_at, SUM(q.jumlah) AS jumlah, q.tanggal_bayar FROM (
                SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN 0 ELSE SUM( k.total_tagihan - k.total_potongan ) 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN SUM(k.total_metodebayar) ELSE 0 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar2 as metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN SUM(k.total_metodebayar2) ELSE 0 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    JOIN m_metode AS m ON k.metodebayar2 = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                        ) q 
                        GROUP BY
                q.metodebayar, q.metode
                ) AS su ON su.metodebayar = me.id
                ) AS p ");


        return json_encode($data);


    }
    public function getjumlahomsetdanongkir(Request $request)
    {
       $id_profil = Auth::user()->id_profil;
       $datatanggal = explode('-', $request->range);
       $tanggal = date('Y-m-d');

            $id_profil2 = Auth::user()->id_profil;
            $group = Auth::user()->group_id;
            $where = "";
            if($group == 5 || $group == 6){
                $where = "WHERE id_profil='$id_profil2'";
            }
            $d_gudang = DB::select(base_gudang($where));
            $id_gudang = array();
            foreach($d_gudang as $d){
                $id_gudang[] = $d->id_gudang;
            }
            $where_gudang = "";
            if(sizeof($id_gudang) > 0){
                $gudang = implode(',',$id_gudang);
                $where_gudang = "AND k.id_gudang IN ($gudang)";
            }


        $group_id = Auth::user()->group_id;
        if ($group_id == 1 || $group_id == 7 || $group_id == 6) {
            if ($request->gudang) {
                $where = "WHERE k.id_gudang = ".$request->gudang." and k.tanggal_faktur >= '".date_db(trim($datatanggal[0]))."' and k.tanggal_faktur <='".date_db(trim($datatanggal[1]))."' ";
            } else {
                // $where ="";
                $where ="WHERE k.tanggal_faktur='".$tanggal."' $where_gudang";
            }
        } else {
            $a = DB::table('ref_gudang')->where('id_profil', $id_profil)->get()->first();
            // print_r($datatanggal[0]);exit;
            if ($datatanggal[0]) {
                # code...
                $where = "WHERE k.id_gudang = ".$a->id." and k.tanggal_faktur >= '".date_db(trim($datatanggal[0]))."' and k.tanggal_faktur <='".date_db(trim($datatanggal[1]))."' ";
            } else {
                // $where = "WHERE k.id_gudang = ".$a->id."  ";
                $where = "WHERE k.tanggal_faktur='".$tanggal."' and k.id_gudang = ".$a->id." ";
            }
        }
        $data=DB::select("SELECT p.id, CASE WHEN SUM(p.data) IS NULL THEN '0' ELSE sum(p.data) END AS data, p.tanggal_bayar
        FROM (
            SELECT
            me.id,
            CASE WHEN sum(su.jumlah) IS NULL THEN '0' ELSE sum( su.jumlah) END AS data,
            CASE WHEN su.tanggal_bayar IS NULL THEN DATE_FORMAT(NOW(), '%d-%m-%Y') ELSE DATE_FORMAT(su.tanggal_bayar, '%d-%m-%Y') END AS tanggal_bayar
            FROM
                m_metode AS me
                LEFT JOIN (
                    SELECT q.metodebayar,
                    q.metode AS metode,q.created_at, SUM(q.jumlah) AS jumlah, q.tanggal_bayar FROM (
                SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN 0 ELSE SUM( k.total_tagihan - k.total_potongan ) 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN SUM(k.total_metodebayar) ELSE 0 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar2 as metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN SUM(k.total_metodebayar2) ELSE 0 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    JOIN m_metode AS m ON k.metodebayar2 = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                        ) q 
                        GROUP BY
                q.metodebayar, q.metode
                ) AS su ON su.metodebayar = me.id
                UNION
                SELECT '99' AS id,CASE WHEN sum(ongkos_kirim) IS NULL THEN '0' ELSE sum(ongkos_kirim) END AS data,
                CASE WHEN tanggal_bayar IS NULL THEN DATE_FORMAT(NOW(), '%d-%m-%Y') 
                ELSE DATE_FORMAT(tanggal_bayar, '%d-%m-%Y') END AS tanggal_bayar
                FROM tbl_kasir AS k $where
                ) AS p ");


        return json_encode($data);


    }
    public function getjumlahnota(Request $request)
    {
        $id_profil = Auth::user()->id_profil;
        $datatanggal = explode('-', $request->range);

        // print_r($request->range);exit;
        // $tanggal = str_replace(' ')
            $tanggal = date('Y-m-d');
            $id_profil2 = Auth::user()->id_profil;
            $group = Auth::user()->group_id;
            $where = "";
            if($group == 5 || $group == 6){
                $where = "WHERE id_profil='$id_profil2'";
            }
            $d_gudang = DB::select(base_gudang($where));
            $id_gudang = array();
            foreach($d_gudang as $d){
                $id_gudang[] = $d->id_gudang;
            }
            $where_gudang = "";
            if(sizeof($id_gudang) > 0){
                $gudang = implode(',',$id_gudang);
                $where_gudang = "AND k.id_gudang IN ($gudang)";
            }

        $group_id = Auth::user()->group_id;
        if ($group_id == '1' || $group_id == '6' ) {
            if ($request->gudang) {
                $where = "WHERE k.id_gudang = ".$request->gudang." and k.tanggal_faktur >= '".date_db(trim($datatanggal[0]))."' and k.tanggal_faktur <='".date_db(trim($datatanggal[1]))."' and k.jenis_transaksi = '1' ";
            } else {
                $where =" WHERE k.tanggal_faktur='".$tanggal."' and k.jenis_transaksi = '1' $where_gudang";
            }

            // print_r($where);exit;
        } else{
           $a = DB::table('ref_gudang')->where('id_profil', $id_profil)->get()->first();
            $id = $a->id;
            if ($request->gudang) {
                $where = "WHERE k.id_gudang ='".$id."' and k.tanggal_faktur >= '".date_db(trim($datatanggal[0]))."' and k.tanggal_faktur <='".date_db(trim($datatanggal[1]))."' ";

            }else{
                $where = "WHERE k.id_gudang ='".$id ."' and k.jenis_transaksi = '1' and k.tanggal_faktur='".$tanggal."'";

            }
        }
        // DB::enableQueryLog();

        $data1=DB::select("SELECT  COUNT(k.id_kasir) jumlah_nota  FROM tbl_kasir AS k ".$where." ");
        // $data=DB::table("tbl_kasir as k")->where();
            // dd(DB::getQueryLog());

        // print_r($where);exit;

        return json_encode($data1);


    }
    public function getomset(Request $request){
        $id_profil = Auth::user()->id_profil;
        $datatanggal = explode('-',$request->range);
        $tanggal = date('Y-m-d');

        // print_r(date_db(trim($datatanggal[1])));exit;
        // $tanggal = str_replace(' ');
                $id_profil2 = Auth::user()->id_profil;
                $group = Auth::user()->group_id;
                $where = "";
                if($group == 5 || $group == 6){
                  $where = "WHERE id_profil='$id_profil2'";
                }
                $d_gudang = DB::select(base_gudang($where));
                $id_gudang = array();
                foreach($d_gudang as $d){
                  $id_gudang[] = $d->id_gudang;
                }
                $where_gudang = "";
                if(sizeof($id_gudang) > 0){
                  $gudang = implode(',',$id_gudang);
                  $where_gudang = "AND k.id_gudang IN ($gudang)";
                }

        $group_id = Auth::user()->group_id;
        if ($group_id == 1 || $group_id == 7 || $group_id == 6) {
          if ($request->gudang) {
            $where = "WHERE k.id_gudang = ".$request->gudang." and k.tanggal_faktur >= '".date_db(trim($datatanggal[0]))."' and k.tanggal_faktur <='".date_db(trim($datatanggal[1]))."' ";

            }else{              
                $where ="WHERE k.tanggal_faktur='".$tanggal."' $where_gudang";
            }

        }else{
            $a = DB::table('ref_gudang')->where('id_profil', $id_profil)->get()->first();
            // print_r($datatanggal[0]);exit;
            if ($datatanggal[0]) {
                # code...
                $where = "WHERE k.id_gudang = ".$a->id." and k.tanggal_faktur >= '".date_db(trim($datatanggal[0]))."' and k.tanggal_faktur <='".date_db(trim($datatanggal[1]))."' ";
            }else{
                $where = "WHERE k.tanggal_faktur='".$tanggal."' and k.id_gudang = ".$a->id." ";

            }

        }

        /*$data=DB::select("SELECT
            me.id,
            me.nama as name,
            su.jumlah  as data
        FROM
            m_metode AS me
            LEFT JOIN (
            SELECT
                k.metodebayar,
                m.nama AS metode,k.created_at,
                Sum( k.total_tagihan - k.total_potongan ) AS jumlah
            FROM
                tbl_kasir AS k
                LEFT JOIN m_metode AS m ON k.metodebayar = m.id
            $where
            GROUP BY
            k.metodebayar,m.nama
            ) AS su ON su.metodebayar = me.id");*/

            $data=DB::select("
            SELECT * FROM (
            SELECT
            me.id,
            me.nama as name,
            me.urutan as urutan,
            CASE WHEN su.jumlah IS NULL THEN '0' ELSE su.jumlah END AS data
                FROM
                    m_metode AS me
                    LEFT JOIN (
                    SELECT q.metodebayar,
                    q.metode AS metode,q.created_at, SUM(q.jumlah) AS jumlah FROM (
                SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN 0 ELSE SUM( k.total_tagihan - k.total_potongan ) 
                    END AS jumlah
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN SUM(k.total_metodebayar) ELSE 0 
                    END AS jumlah
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar2 as metodebayar,
                    m.nama AS metode,k.created_at,
                                    CASE WHEN k.metodebayar2 IS NOT NULL 
                                    THEN SUM(k.total_metodebayar2) ELSE 0 
                                    END AS jumlah
                FROM
                    tbl_kasir AS k
                    JOIN m_metode AS m ON k.metodebayar2 = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                        ) q 
                        GROUP BY
                q.metodebayar, q.metode
                ) AS su ON su.metodebayar = me.id
                UNION ALL
                SELECT '99' AS id, 'Ongkos Kirim' AS name, '99' AS urutan, SUM(k.ongkos_kirim) AS data FROM tbl_kasir AS k $where
                ) AS p ORDER BY p.urutan ASC
                ");

            $no = 0;
            $data1 = array();
            $jumlah= 0;
            foreach ($data as $list) {
                    $no++;
                    $jumlah += $list->data ;
                    $row = array();
                    $row[] = $list->name;
                    $row[] = 'Rp.'.rupiah($list->data) ;
                    $data1[] = $row;
            }
            $output = array("data" => $data1);
            return response()->json($output);

    }
    public function datakosong()
    {
        $dat = "Array(
            [0] => Array
                (
                    [name] => Tunai
                    [data] => Array
                        (
                            [0] => 0
                        )

                    [y] => 0
                )

            [1] => Array
                (
                    [name] => Debet
                    [data] => Array
                        (
                            [0] => 0
                        )

                    [y] => 0
                )

            [2] => Array
                (
                    [name] => Flazz
                    [data] => Array
                        (
                            [0] => 0
                        )

                    [y] => 0
                )

            [3] => Array
                (
                    [name] => Kredit
                    [data] => Array
                        (
                            [0] => 0
                        )

                    [y] => 0
                )

            [4] => Array
                (
                    [name] => Transfer
                    [data] => Array
                        (
                            [0] => 0
                        )

                    [y] => 0
                )

            [5] => Array
                (
                    [name] => OVO
                    [data] => Array
                        (
                            [0] => 0
                        )

                    [y] => 0
                )

            [6] => Array
                (
                    [name] => Hutang
                    [data] => Array
                        (
                            [0] => 0
                        )

                    [y] => 0
                )

        )";
        return $dat;
        # code...
    }
}
