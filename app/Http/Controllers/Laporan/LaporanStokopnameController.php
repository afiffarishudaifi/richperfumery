<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\Exports\LaporanViewExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanStokopnameController extends Controller
{
    //
    public function index(){
	    $id_profil = Auth::user()->id_profil;
      	$group = Auth::user()->group_id;
      	$where = "";
	      /*if($group == 5 || $group == 6){
	        $where = "WHERE id_profil='$id_profil'";
	      }*/
	      if($group == 5){
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
    	require(public_path('fpdf1813/Mc_table.php'));
    	$tanggal_awal = $request->get('tanggal_awal');
    	$tanggal_akhir = $request->get('tanggal_akhir');
    	$gudang  = $request->get('gudang');
    	//DB::enableQueryLog();
        $data['data'] = $this->get_selisihopname($gudang,tgl_full($tanggal_awal,'99'),tgl_full($tanggal_akhir,'99'));
    	//dd(DB::getQueryLog());
    	if($gudang == 'all'){
    	   $data['gudang'] = 'Semua Gudang';
        }else{
        $gudang = DB::table('ref_gudang')->where('id',$gudang)->select(DB::Raw('id as id_gudang,nama as nama_gudang'))->first();
        $data['gudang']         = $gudang->nama_gudang;
        }

        $data['tanggal_awal'] 	= tgl_full($tanggal_awal,'');
        $data['tanggal_akhir'] 	= tgl_full($tanggal_akhir,'');
        $data['kasir']			= Auth::user()->name;

      $html = view('admin.laporan.cetak_laporan_stokopname')->with('data',$data);
      trigger_log(NULL, "Cetak Laporan Stokopname PDF", 7);
      return response($html)->header('Content-Type', 'application/pdf');
        //print_r($data['data']);exit;
    }

    public function get_selisihopname($gudang,$tanggal_awal,$tanggal_akhir){
        if($gudang=='all'){
            $where = "WHERE tsb.tanggal >= '$tanggal_awal' AND tsb.tanggal <= '$tanggal_akhir'";
            $where2= "WHERE tls.tanggal < '$tanggal_awal' ";
            $html = DB::select("SELECT q.id_barang,
                q.kode_barang,
                q.nama_barang,
                q.id_gudang,
                q.nama_gudang,
                q.id_satuan,
                q.nama_satuan,
                sum(q.stok) as stok,
                sum(q.fisik) as fisik,
                sum(q.stok-q.fisik) as selisih,
                q.keterangan 
            FROM (                                              
                SELECT 
                    tsb.id_barang as id_barang,
                    tsb.id_gudang as id_gudang,
                    tsb.id_satuan as id_satuan,
                    tb.barang_kode AS kode_barang,
                    tb.barang_nama AS nama_barang,
                    rg.nama AS nama_gudang,     
                    ts.satuan_nama AS nama_satuan,
                    '0' AS unit_masuk,
                    '0' AS unit_keluar,
                    tsb.fisik AS fisik,
                    tsb.stok AS stok,
                    CASE WHEN tsb.keterangan IS NULL THEN '' ELSE tsb.keterangan END AS keterangan
                FROM tbl_stokopname_baru as tsb
                LEFT JOIN tbl_barang AS tb ON tsb.id_barang = barang_id
                LEFT JOIN ref_gudang AS rg ON tsb.id_gudang = rg.id
                LEFT JOIN tbl_satuan AS ts ON tsb.id_satuan = ts.satuan_id
                -- WHERE tsb.id_gudang = '$gudang' AND tsb.tanggal >= '$tanggal_awal' AND tsb.tanggal <= '$tanggal_akhir'
                $where
            ) q 
            JOIN(
                SELECT  
                    q.id_barang,
                    q.id_gudang,
                    q.id_satuan,
                    q.nama_barang,
                    sum(q.unit_masuk-q.unit_keluar) as stok
                FROM (                                              
                    SELECT
                        tls.id_barang as id_barang,
                        tls.id_ref_gudang as id_gudang,
                        tls.id_satuan as id_satuan,
                        tb.barang_nama AS nama_barang,
                        tls.unit_masuk AS unit_masuk,
                        tls.unit_keluar AS unit_keluar
                    FROM
                        tbl_log_stok AS tls
                        LEFT JOIN tbl_barang AS tb ON tls.id_barang = barang_id
                        LEFT JOIN ref_gudang AS rg ON tls.id_ref_gudang = rg.id
                        LEFT JOIN tbl_satuan AS ts ON tls.id_satuan = ts.satuan_id
                    -- WHERE  tls.id_ref_gudang = '$gudang' AND tls.tanggal < '$tanggal_awal' 
                    $where2
                ) q 
                GROUP BY 
                    q.id_barang, 
                    q.id_satuan
                ORDER BY
                q.nama_barang
            ) AS r ON q.id_barang=r.id_barang AND q.id_satuan=r.id_satuan AND q.id_gudang=r.id_gudang
            GROUP BY 
                q.id_barang, 
                q.id_satuan
            ORDER BY
            q.nama_barang");
        }else{
            $where = "WHERE tsb.id_gudang = '$gudang' AND tsb.tanggal >= '$tanggal_awal' AND tsb.tanggal <= '$tanggal_akhir'";
            $where2= "WHERE tls.id_ref_gudang = '$gudang' AND tls.tanggal < '$tanggal_awal' ";
            $html = DB::select("SELECT q.id_barang,
                q.kode_barang,
                q.nama_barang,
                q.id_gudang,
                q.nama_gudang,
                q.id_satuan,
                q.nama_satuan,
                sum(q.stok) as stok,
                sum(q.fisik) as fisik,
                sum(q.stok-q.fisik) as selisih,
                q.keterangan 
            FROM (                                              
                SELECT 
                    tsb.id_barang as id_barang,
                    tsb.id_gudang as id_gudang,
                    tsb.id_satuan as id_satuan,
                    tb.barang_kode AS kode_barang,
                    tb.barang_nama AS nama_barang,
                    rg.nama AS nama_gudang,     
                    ts.satuan_nama AS nama_satuan,
                    '0' AS unit_masuk,
                    '0' AS unit_keluar,
                    tsb.fisik AS fisik,
                    tsb.stok AS stok,
                    CASE WHEN tsb.keterangan IS NULL THEN '' ELSE tsb.keterangan END AS keterangan
                FROM tbl_stokopname_baru as tsb
                LEFT JOIN tbl_barang AS tb ON tsb.id_barang = barang_id
                LEFT JOIN ref_gudang AS rg ON tsb.id_gudang = rg.id
                LEFT JOIN tbl_satuan AS ts ON tsb.id_satuan = ts.satuan_id
                -- WHERE tsb.id_gudang = '$gudang' AND tsb.tanggal >= '$tanggal_awal' AND tsb.tanggal <= '$tanggal_akhir'
                $where
            ) q 
            JOIN(
                SELECT  
                    q.id_barang,
                    q.id_gudang,
                    q.id_satuan,
                    q.nama_barang,
                    sum(q.unit_masuk-q.unit_keluar) as stok
                FROM (                                              
                    SELECT
                        tls.id_barang as id_barang,
                        tls.id_ref_gudang as id_gudang,
                        tls.id_satuan as id_satuan,
                        tb.barang_nama AS nama_barang,
                        tls.unit_masuk AS unit_masuk,
                        tls.unit_keluar AS unit_keluar
                    FROM
                        tbl_log_stok AS tls
                        LEFT JOIN tbl_barang AS tb ON tls.id_barang = barang_id
                        LEFT JOIN ref_gudang AS rg ON tls.id_ref_gudang = rg.id
                        LEFT JOIN tbl_satuan AS ts ON tls.id_satuan = ts.satuan_id
                    -- WHERE  tls.id_ref_gudang = '$gudang' AND tls.tanggal < '$tanggal_awal' 
                    $where2
                ) q 
                GROUP BY 
                    q.id_barang,
                    q.id_gudang, 
                    q.id_satuan
                ORDER BY
                q.nama_barang
            ) AS r ON q.id_barang=r.id_barang AND q.id_satuan=r.id_satuan AND q.id_gudang=r.id_gudang
            GROUP BY 
                q.id_barang,
                q.id_gudang, 
                q.id_satuan
            ORDER BY
            q.nama_barang");
        }

        return $html;
    }

    public function excel($d_gudang,$d_tanggal_awal,$d_tanggal_akhir){
        $gudang = $d_gudang;
        $tanggal_awal = tgl_full($d_tanggal_awal,'99');  
        $tanggal_akhir = tgl_full($d_tanggal_akhir,'99');  

        $d_data = $this->get_selisihopname($gudang,tgl_full($tanggal_awal,'99'),tgl_full($tanggal_akhir,'99'));
        if($gudang == 'all'){
           $nama_gudang = 'Semua Gudang';
        }else{
           $nama_gudang = DB::table('ref_gudang')->where('id',$gudang)->first()->nama;
        }

        $no = 1;
        $arr = array();
            foreach ($d_data as $key => $d){
                $arr[] = array('NO.'=>$no++,
                                'KODE BARANG'           => $d->kode_barang,
                                'NAMA PARFURM'          => $d->nama_barang,
                                'SATUAN'                => $d->nama_satuan,
                                'FISIK'                 => $d->fisik,
                                'STOK'                  => $d->stok,
                                'SELISIH'               => $d->selisih,
                                'KETERANGAN'            => $d->keterangan);
            }
        trigger_log(NULL, "Cetak Laporan Stokopname Excel", 7);
        return Excel::download(new LaporanViewExport($arr), 'Laporan_Stokopname_'.$nama_gudang.'_'.$tanggal_awal.'-'.$tanggal_akhir.'.xlsx');
    }

    public function hasil($d_gudang,$d_tanggal_awal,$d_tanggal_akhir){
        ini_set('max_execution_time', '0');
        $gudang = $d_gudang;
        $tanggal_awal = tgl_full($d_tanggal_awal,'99');  
        $tanggal_akhir = tgl_full($d_tanggal_akhir,'99');  

        $d_data = $this->get_selisihopname($gudang,tgl_full($tanggal_awal,'99'),tgl_full($tanggal_akhir,'99'));
        if($gudang == 'all'){
           $nama_gudang = 'Semua Gudang';
        }else{
           $nama_gudang = DB::table('ref_gudang')->where('id',$gudang)->first()->nama;
        }

        $no = 1;
        $arr = array();
            foreach ($d_data as $key => $d){
                $arr[] = array('NO.'=>$no++,
                                'KODE BARANG'           => $d->kode_barang,
                                'NAMA PARFURM'          => $d->nama_barang,
                                'SATUAN'                => $d->nama_satuan,
                                'FISIK'                 => format_angka($d->fisik),
                                'STOK'                  => format_angka($d->stok),
                                'SELISIH'               => format_angka($d->selisih),
                                'KETERANGAN'            => $d->keterangan);
            }

        if(count($arr) > 0){
        $data = $arr;
        }else{
        $data = array("0"=>['NO.'                   =>"",
                            'KODE BARANG'           =>"",
                            'NAMA PARFURM'          =>"",
                            'SATUAN'                =>"",
                            'FISIK'                 =>"",
                            'STOK'                  =>"",
                            'SELISIH'               =>"",
                            'KETERANGAN'            =>""]);
        }

        return view('admin.laporan.hasil_laporan',compact('data'));
    }
}
