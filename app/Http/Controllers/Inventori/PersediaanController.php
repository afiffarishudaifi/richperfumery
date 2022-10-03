<?php

namespace App\Http\Controllers\Inventori;

use Illuminate\Http\Request;
use DB;
use Auth;
use Redirect;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent;

class PersediaanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function __construct(){
       $this->agent = new Agent();
     }
    public function index()
    {
      if ($this->agent->isMobile()) {
        // code...
        return view('admin_mobile.persediaan.index');
      }else {
        // code...
        return view('admin.persediaan.index');
      }
    }
    
    public function listData(){
        // TODO
        $session = Auth::user();
        $query = DB::table('view_persediaan as vp')
                ->whereRaw('vp.stok NOT LIKE ?',["%-%"])
                ->when($session, function($query, $keyword){
                    if($keyword->group_id != 1 || $keyword->group_id != 7){
                        $query->whereRaw('vp.id_profil = ?',[$keyword->id_profil]);
                    }
                })
                ->get();

        $no = 0;
        $data = array();
        foreach ($query as $list) {
            $total = $list->stok * $list->konversi ;
            $no++;
            $row = array();
            if ($this->agent->isMobile()) {
              $row['no'] = $no;
              $row['kode_barang'] = $list->kode_barang;
              $row['nama_barang'] = $list->nama_barang;
              $row['nama_gudang'] = $list->nama_gudang;
              $row['stok'] = $list->stok;
              $row['nama_satuan'] = $list->nama_satuan;
            }else {
              $row[] = $no;
              $row[] = $list->kode_barang;
              $row[] = $list->nama_barang;
              $row[] = $list->nama_gudang;
              $row[] = $list->stok;
              $row[] = $list->nama_satuan;
            }
            /*$row[] = $list->konversi;*/
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);
    }

    public function listData_lama()
    {
        $id_group = Auth::user()->group_id;
        // print_r($id_group);exit;
        $id_profil = Auth::user()->id_profil;
        if ($id_group == 1) {
            // $a= DB::table('ref_gudang')->where('id_profil',$id_profil)->get()->first();
            $where=' ';
        }else if($id_group == 6){
            $a= DB::table('ref_gudang')->where('id_profil',$id_profil)->get()->first();
            // print_r($a);exit;

            $where='and id_ref_gudang="'.$a->id.'"';
        }else if($id_group == 5){
            $a= DB::table('ref_gudang')->where('id_profil', $id_profil)->get()->first();
            // print_r($a);exit;

            $where = 'and id_ref_gudang="'.$a->id.'"';

        }else if($id_group == 4){
            $a= DB::table('ref_gudang')->where('id_profil', $id_profil)->get()->first();
            // print_r($a);exit;

            $where = 'and id_ref_gudang="'.$a->id.'"';

        }else if($id_group == 3){
            $a= DB::table('ref_gudang')->where('id_profil', $id_profil)->get()->first();
            // print_r($a);exit;

            $where = 'and id_ref_gudang="'.$a->id.'"';

        }else if($id_group == 7){
            $where=' ';

        }
        // print_r($a);exit;
        $persediaan = DB::select("SELECT
                        *
                    FROM
                        (
                        SELECT
                            tls.*,
                            tb.barang_kode AS kode_barang,
                            tb.barang_nama AS nama_barang,
                            rg.nama AS nama_gudang,
                            SUM( tls.unit_masuk - tls.unit_keluar ) AS stok,
                            ts.satuan_nama AS nama_satuan  ,
		                    SUM( (tls.unit_masuk * ts.konversi) - (tls.unit_keluar * ts.konversi) ) as konversi
                        FROM
                            tbl_log_stok AS tls
                            LEFT JOIN tbl_barang AS tb ON tls.id_barang = barang_id
                            LEFT JOIN ref_gudang AS rg ON tls.id_ref_gudang = rg.id
                            LEFT JOIN tbl_satuan AS ts ON tls.id_satuan = ts.satuan_id
                        GROUP BY
                            id_barang,
                            id_ref_gudang, id_satuan
                        ORDER BY
                            tb.barang_nama
                        ) b
                    WHERE
                        b.stok NOT LIKE '%-%' $where ");
        $no = 0;
        $data = array();

        foreach ($persediaan as $list) {
                $total = $list->stok * $list->konversi ;
            $no++;
            $row = array();
            if ($this->agent->isMobile()) {
              $row['no'] = $no;
              $row['kode_barang'] = $list->kode_barang;
              $row['nama_barang'] = $list->nama_barang;
              $row['nama_gudang'] = $list->nama_gudang;
              $row['stok'] = $list->stok;
              $row['nama_satuan'] = $list->nama_satuan;
            }else {
              $row[] = $no;
              $row[] = $list->kode_barang;
              $row[] = $list->nama_barang;
              $row[] = $list->nama_gudang;
              $row[] = $list->stok;
              $row[] = $list->nama_satuan;
            }
            /*$row[] = $list->konversi;*/
            $data[] = $row;

        }

        $output = array("data" => $data);
        return response()->json($output);

    }






}
