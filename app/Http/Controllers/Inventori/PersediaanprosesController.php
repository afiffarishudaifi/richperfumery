<?php

namespace App\Http\Controllers\Inventori;

use Illuminate\Http\Request;
use DB;
use Redirect;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent;

class PersediaanprosesController extends Controller
{
  public function __construct(){
    $this->agent = new Agent();
  }
    public function index(){
      if ($this->agent->isMobile()) {
        // code...
        return view('admin_mobile.persediaan.persedianproses');
      }else {
        // code...
        return view('admin.persediaan.persedianproses');
      }
    }
    public function listData()
    {
        $persediaan = DB::select("SELECT
                            r.id,
                            ba.barang_nama AS barang,
                            r.jumlah,
                            g.nama AS nama_gudang,
                            'retur pengiriman' AS jenis,
                            ba.barang_kode,
                            s.satuan_nama
                            FROM
                            pengiriman_retur AS r
                            LEFT JOIN ref_gudang AS g ON r.id_gudang_pusat = g.id
                            LEFT JOIN tbl_barang AS ba ON r.id_barang = ba.barang_id
                            LEFT JOIN tbl_satuan AS s ON r.id_satuan = s.satuan_id
                            WHERE
                            r.`status` = 1");
        $pengiriman_retur = DB::select("SELECT
                            a.id,
                            a.nama AS barang,
                            a.jumlah,
                            g.nama AS nama_gudang,
                            'pengiriman' AS jenis,
                            ba.barang_kode,
                            s.satuan_nama
                            FROM
                            pengiriman_detail AS a
                            LEFT JOIN pengiriman AS p ON a.id_inv_pengiriman = p.id
                            LEFT JOIN ref_gudang AS g ON p.gudang_tujuan = g.id
                            LEFT JOIN tbl_barang AS ba ON a.id_barang = ba.barang_id
                            LEFT JOIN tbl_satuan AS s ON a.id_satuan = s.satuan_id
                            WHERE
                            a.`status` = 1");
        $datase = array_merge($persediaan,$pengiriman_retur);
        $no = 0;
        $data = array();
        foreach ($datase as $list) {
            $no++;
            $row = array();
            if ($this->agent->isMobile()) {
              // code...
              $row['no'] = $no++;
              $row['barang_kode'] = $list->barang_kode;
              $row['barang'] = $list->barang;
              $row['nama_gudang'] = $list->nama_gudang;
              $row['jumlah'] = $list->jumlah;
              $row['satuan_nama'] = $list->satuan_nama;
              $row['jenis'] = $list->jenis;
            }else {
              // code...
              $row[] = $no;
              $row[] = $list->barang_kode;
              $row[] = $list->barang;
              $row[] = $list->nama_gudang;
              $row[] = $list->jumlah;
              $row[] = $list->satuan_nama;
              $row[] = $list->jenis;
            }
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);

    }

    public function listData2()
    {
        $persediaan = DB::select("SELECT
                            r.id,
                            ba.barang_nama AS barang,
                            r.jumlah,
                            g.nama AS nama_gudang,
                            'retur pengiriman' AS jenis,
                            ba.barang_kode,
                            s.satuan_nama
                            FROM
                            pengiriman_retur AS r
                            LEFT JOIN ref_gudang AS g ON r.id_gudang_pusat = g.id
                            LEFT JOIN tbl_barang AS ba ON r.id_barang = ba.barang_id
                            LEFT JOIN tbl_satuan AS s ON r.id_satuan = s.satuan_id
                            WHERE
                            r.`status` = 1");
        $pengiriman_retur = DB::select("SELECT
                            a.id,
                            a.nama AS barang,
                            a.jumlah,
                            g.nama AS nama_gudang,
                            'pengiriman' AS jenis,
                            ba.barang_kode,
                            s.satuan_nama
                            FROM
                            pengiriman_detail AS a
                            LEFT JOIN pengiriman AS p ON a.id_inv_pengiriman = p.id
                            LEFT JOIN ref_gudang AS g ON p.gudang_tujuan = g.id
                            LEFT JOIN tbl_barang AS ba ON a.id_barang = ba.barang_id
                            LEFT JOIN tbl_satuan AS s ON a.id_satuan = s.satuan_id
                            WHERE
                            a.`status` = 1");
        $datase = array_merge($persediaan,$pengiriman_retur);
        $no = 0;
        $data = array();
        foreach ($datase as $list) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $list->barang_kode;
            $row[] = $list->barang;
            $row[] = $list->nama_gudang;
            $row[] = $list->jumlah;
            $row[] = $list->satuan_nama;
            $row[] = $list->jenis;
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);

    }
}
