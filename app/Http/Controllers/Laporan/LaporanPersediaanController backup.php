<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\Exports\LaporanViewExport;
use App\Exports\LaporanPembelianViewExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanPersediaanController extends Controller
{
    //
    public function index(){
    	$id_profil = Auth::user()->id_profil;
	    $group = Auth::user()->group_id;
	    $where = "";
	        if($group == 5){
	          $where = "WHERE id_profil='$id_profil'";
	        }
	    $data['gudang'] = DB::select(base_gudang($where));
	    $data['outlet'] = DB::table('ref_gudang')->where('id','!=','8')->select(DB::raw('id as id_gudang, nama as nama_gudang'))->get();
	    $data['group']	= Auth::user()->group_id;
    	return view('admin.laporan.index_laporan_persediaan')->with('data',$data);
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
                WHERE (b.barang_kode LIKE '%$search%' OR b.barang_nama LIKE '%$search%' OR b.barang_alias LIKE '%$search%') AND b.barang_id_parent != '0'");

        return \Response::json($barangs);
    }

    public function cetaklaporan_persediaan(Request $request){
    	require(public_path('fpdf1813/Mc_table.php'));
    	$gudang = $request->get('gudang');
    	$tanggal = tgl_full($request->get('tanggalAwal'),'99');
    	$tanggal2 = tgl_full($request->get('tanggalAkhir'),'99');
    	$barang = $request->get('barang');
	    if($gudang == 'all'){
	    $data['gudang']	 = 'Semua Gudang';
	    }else{
	    $d_gudang = DB::table('ref_gudang')->where('id',$gudang)->select(DB::Raw('id as id_gudang,nama as nama_gudang'))->first();
	    $data['gudang']	 = $d_gudang->nama_gudang;
	    }
	    
	    $add_where = "where ";
	    $count = 0;
	    if(isset($gudang)){
	    	if($gudang != 'all'){
	    		$count += 1;
	    		$where_stokawal[] = " t.id_ref_gudang = '".$gudang."'";
	    		$where_persediaan[] = " t.id_ref_gudang = '".$gudang."'";
	    	}
	    }

	    if(isset($tanggal)){
	    	if($tanggal != ''){
	    		$where_stokawal[] = " t.tanggal < '".$tanggal."'";
	    		$where_persediaan[] = " t.tanggal >= '".$tanggal."'";
	    	}
	    }

	    if(isset($tanggal2)){
	    	if($tanggal2 != ''){
	    		$where_persediaan[] = " t.tanggal <= '".$tanggal2."'";
	    	}
	    }

	    if(isset($barang)){
	    	if($barang != 0){
	    		$where_stokawal[] = " t.id_barang = '".$barang."'";
	    		$where_persediaan[] = " t.id_barang = '".$barang."'";
	    	}
	    }
		
		$where_stokawal = $add_where.implode(" AND ",$where_stokawal);
		$where_persediaan = $add_where.implode(" AND ",$where_persediaan);
		
		$data['data']	= DB::select("
						SELECT q.*, 
							SUM( q.unit_masuk ) AS jumlah_masuk,
							SUM( q.unit_keluar ) AS jumlah_keluar,
							CASE WHEN r.jumlah_stokawal < 0 THEN '0' 
							WHEN r.jumlah_stokawal  IS NULL THEN '0' 
							ELSE r.jumlah_stokawal  END AS jumlah_stokawal,
							CASE WHEN ((r.jumlah_stokawal + SUM(q.unit_masuk)) - SUM(q.unit_keluar)) < 0 THEN '0'
							WHEN ((r.jumlah_stokawal + SUM(q.unit_masuk)) - SUM(q.unit_keluar)) IS NULL THEN '0'
							ELSE ((r.jumlah_stokawal + SUM(q.unit_masuk)) - SUM(q.unit_keluar))
							END AS jumlah_terakhir
							FROM (		
										SELECT
											t.id_barang,
											b.barang_nama,
											b.barang_kode,											
											t.id_satuan AS satuan_id,
											s.satuan_nama,
											s.satuan_satuan,
											t.id_ref_gudang AS gudang_id,
											g.nama AS gudang_nama,											
											t.tanggal,
											t.unit_masuk as unit_masuk,
											t.unit_keluar as unit_keluar
										FROM
											tbl_log_stok AS t										
										JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
										$where_persediaan
							) q
							LEFT JOIN (
										SELECT q.id_barang as barang, q.satuan_id as satuan, q.gudang_id as gudang,
											CASE WHEN SUM( q.unit_masuk - q.unit_keluar )< 0 THEN '0' 
											WHEN SUM( q.unit_masuk - q.unit_keluar ) IS NULL THEN '0' 
											ELSE SUM( q.unit_masuk - q.unit_keluar ) END AS jumlah_stokawal
											FROM (	
										SELECT
											t.id_barang,
											b.barang_nama,
											b.barang_kode,											
											t.id_satuan AS satuan_id,
											s.satuan_nama,
											s.satuan_satuan,
											t.id_ref_gudang AS gudang_id,
											g.nama AS gudang_nama,											
											t.tanggal,
											t.unit_masuk,
											t.unit_keluar,
											'0' as unit_pembelian,
											'0' as unit_retur,
											t.status
										FROM
											tbl_log_stok AS t										
										JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id						
										$where_stokawal
									) q
									GROUP BY
										q.id_barang,
										q.satuan_id,
										q.gudang_id
									ORDER BY
										q.barang_nama ASC
							) r ON q.id_barang=r.barang AND q.satuan_id=r.satuan AND q.gudang_id=r.gudang
							GROUP BY
								q.id_barang,
								q.satuan_id,
								q.gudang_id
							ORDER BY
							q.barang_nama ASC");

	    $data['tanggal_awal'] = tgl_full($request->tanggalAwal,'');
	    $data['tanggal_akhir'] = tgl_full($request->tanggalAkhir,'');
	    $data['hari']    = tgl_full($request->tanggal,'hari');
	    $data['kasir']   = Auth::user()->name;
	    // print_r($data);exit();
	    $html = view('admin.laporan.cetak_laporan_persediaan')->with('data',$data);
	    trigger_log(NULL, "Cetak Laporan Persediaan PDF", 7);
      	return response($html)->header('Content-Type', 'application/pdf');
		
    }

    public function excel($d_gudang,$d_tanggalAwal, $d_tanggalAkhir, $d_barang){
    	$gudang = $d_gudang;
    	$tanggal = tgl_full($d_tanggalAwal,'99');
    	$tanggal2 = tgl_full($d_tanggalAkhir,'99');  
    	$barang = $d_barang;

    	$add_where = "where ";
	    $count = 0;
	    if(isset($gudang)){
	    	if($gudang != 'all'){
	    		$count += 1;
	    		$where_stokawal[] = " t.id_ref_gudang = '".$gudang."'";
	    		$where_persediaan[] = " t.id_ref_gudang = '".$gudang."'";
	    	}
	    }

	    if(isset($tanggal)){
	    	if($tanggal != ''){
	    		$where_stokawal[] = " t.tanggal < '".$tanggal."'";
	    		$where_persediaan[] = " t.tanggal >= '".$tanggal."'";
	    	}
	    }

	    if(isset($tanggal2)){
	    	if($tanggal2 != ''){
	    		$where_persediaan[] = " t.tanggal <= '".$tanggal2."'";
	    	}
	    }

	    if(isset($barang)){
	    	if($barang != 0){
	    		$where_stokawal[] = " t.id_barang = '".$barang."'";
	    		$where_persediaan[] = " t.id_barang = '".$barang."'";
	    	}
	    }
		
		$where_stokawal = $add_where.implode(" AND ",$where_stokawal);
		$where_persediaan = $add_where.implode(" AND ",$where_persediaan);

        $d_data = DB::select("SELECT q.*, 
							SUM( q.unit_masuk ) AS jumlah_masuk,
							SUM( q.unit_keluar ) AS jumlah_keluar,
							CASE WHEN r.jumlah_stokawal < 0 THEN '0' 
							WHEN r.jumlah_stokawal  IS NULL THEN '0' 
							ELSE r.jumlah_stokawal  END AS jumlah_stokawal,
							CASE WHEN ((r.jumlah_stokawal + SUM(q.unit_masuk)) - SUM(q.unit_keluar)) < 0 THEN '0'
							WHEN ((r.jumlah_stokawal + SUM(q.unit_masuk)) - SUM(q.unit_keluar)) IS NULL THEN '0'
							ELSE ((r.jumlah_stokawal + SUM(q.unit_masuk)) - SUM(q.unit_keluar))
							END AS jumlah_terakhir
							FROM (		
										SELECT
											t.id_barang,
											b.barang_nama,
											b.barang_kode,											
											t.id_satuan AS satuan_id,
											s.satuan_nama,
											s.satuan_satuan,
											t.id_ref_gudang AS gudang_id,
											g.nama AS gudang_nama,											
											t.tanggal,
											t.unit_masuk as unit_masuk,
											t.unit_keluar as unit_keluar
										FROM
											tbl_log_stok AS t										
										JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
										$where_persediaan
							) q
							LEFT JOIN (
										SELECT q.id_barang as barang, q.satuan_id as satuan, q.gudang_id as gudang,
											CASE WHEN SUM( q.unit_masuk - q.unit_keluar )< 0 THEN '0' 
											WHEN SUM( q.unit_masuk - q.unit_keluar ) IS NULL THEN '0' 
											ELSE SUM( q.unit_masuk - q.unit_keluar ) END AS jumlah_stokawal
											FROM (	
										SELECT
											t.id_barang,
											b.barang_nama,
											b.barang_kode,											
											t.id_satuan AS satuan_id,
											s.satuan_nama,
											s.satuan_satuan,
											t.id_ref_gudang AS gudang_id,
											g.nama AS gudang_nama,											
											t.tanggal,
											t.unit_masuk,
											t.unit_keluar,
											'0' as unit_pembelian,
											'0' as unit_retur,
											t.status
										FROM
											tbl_log_stok AS t										
										JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id						
										$where_stokawal
									) q
									GROUP BY
										q.id_barang,
										q.satuan_id,
										q.gudang_id
									ORDER BY
										q.barang_nama ASC
							) r ON q.id_barang=r.barang AND q.satuan_id=r.satuan AND q.gudang_id=r.gudang
							GROUP BY
								q.id_barang,
								q.satuan_id,
								q.gudang_id
							ORDER BY
							q.barang_nama ASC");
        if($gudang=='all'){
        $nama_gudang = 'Semua Gudang';
	    }else{
	    $nama_gudang = DB::table('ref_gudang')->where('id',$gudang)->first()->nama;
	    }
		$no = 1;
		$arr = array();
			foreach ($d_data as $key => $d){
				$arr[] = array('NO.'=>$no++,
								'KODE BARANG' 			=> $d->barang_kode,
								'NAMA PARFURM'			=> $d->barang_nama,
								'NAMA GUDANG'			=> $d->gudang_nama,
								'STOCK AWAL'			=> format_angka($d->jumlah_stokawal).' '.$d->satuan_satuan,
								'MASUK'					=> format_angka($d->jumlah_masuk).' '.$d->satuan_satuan,
								'KELUAR'				=> format_angka($d->jumlah_keluar).' '.$d->satuan_satuan,
								'STOK AKHIR'			=> format_angka($d->jumlah_terakhir).' '.$d->satuan_satuan);
			}
		if(count($arr) > 0){
		$data = $arr;
		}else{
		$data = array("0"=>['NO.'=>$no++,
							'KODE BARANG' 			=> "",
							'NAMA PARFURM'			=> "",
							'NAMA GUDANG'			=> "",
							'STOCK AWAL'			=> "",
							'PEMBELIAN HARI INI'	=> "",
							'RETUR HARI INI'		=> "",
							'STOK AKHIR'			=> ""]);
		}
        trigger_log(NULL, "Cetak Laporan Persediaan Excel", 7);
		return Excel::download(new LaporanViewExport($data), 'Laporan_persediaan_'.$nama_gudang.'_'.$tanggal.'-'.$tanggal2.'.xlsx');
    	
    }

    public function hasil($d_gudang,$d_tanggalAwal,$d_tanggalAkhir,$d_barang){
    	$gudang = $d_gudang;
    	$tanggal = tgl_full($d_tanggalAwal,'99');  
    	$tanggal2 = tgl_full($d_tanggalAkhir,'99');
    	$barang = $d_barang;

    	$add_where = "where ";
	    $count = 0;
	    if(isset($gudang)){
	    	if($gudang != 'all'){
	    		$count += 1;
	    		$where_stokawal[] = " t.id_ref_gudang = '".$gudang."'";
	    		$where_persediaan[] = " t.id_ref_gudang = '".$gudang."'";
	    	}
	    }

	    if(isset($tanggal)){
	    	if($tanggal != ''){
	    		$where_stokawal[] = " t.tanggal < '".$tanggal."'";
	    		$where_persediaan[] = " t.tanggal >= '".$tanggal."'";
	    	}
	    }

	    if(isset($tanggal2)){
	    	if($tanggal2 != ''){
	    		$where_persediaan[] = " t.tanggal <= '".$tanggal2."'";
	    	}
	    }

	    if(isset($barang)){
	    	if($barang != 0){
	    		$where_stokawal[] = " t.id_barang = '".$barang."'";
	    		$where_persediaan[] = " t.id_barang = '".$barang."'";
	    	}
	    }
		
		$where_stokawal = $add_where.implode(" AND ",$where_stokawal);
		$where_persediaan = $add_where.implode(" AND ",$where_persediaan);

        $d_data = DB::select("SELECT q.*, 
							SUM( q.unit_masuk ) AS jumlah_masuk,
							SUM( q.unit_keluar ) AS jumlah_keluar,
							CASE WHEN r.jumlah_stokawal < 0 THEN '0' 
							WHEN r.jumlah_stokawal  IS NULL THEN '0' 
							ELSE r.jumlah_stokawal  END AS jumlah_stokawal,
							CASE WHEN ((r.jumlah_stokawal + SUM(q.unit_masuk)) - SUM(q.unit_keluar)) < 0 THEN '0'
							WHEN ((r.jumlah_stokawal + SUM(q.unit_masuk)) - SUM(q.unit_keluar)) IS NULL THEN '0'
							ELSE ((r.jumlah_stokawal + SUM(q.unit_masuk)) - SUM(q.unit_keluar))
							END AS jumlah_terakhir
							FROM (		
										SELECT
											t.id_barang,
											b.barang_nama,
											b.barang_kode,											
											t.id_satuan AS satuan_id,
											s.satuan_nama,
											s.satuan_satuan,
											t.id_ref_gudang AS gudang_id,
											g.nama AS gudang_nama,											
											t.tanggal,
											t.unit_masuk as unit_masuk,
											t.unit_keluar as unit_keluar
										FROM
											tbl_log_stok AS t										
										JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
										$where_persediaan
							) q
							LEFT JOIN (
										SELECT q.id_barang as barang, q.satuan_id as satuan, q.gudang_id as gudang,
											CASE WHEN SUM( q.unit_masuk - q.unit_keluar )< 0 THEN '0' 
											WHEN SUM( q.unit_masuk - q.unit_keluar ) IS NULL THEN '0' 
											ELSE SUM( q.unit_masuk - q.unit_keluar ) END AS jumlah_stokawal
											FROM (	
										SELECT
											t.id_barang,
											b.barang_nama,
											b.barang_kode,											
											t.id_satuan AS satuan_id,
											s.satuan_nama,
											s.satuan_satuan,
											t.id_ref_gudang AS gudang_id,
											g.nama AS gudang_nama,											
											t.tanggal,
											t.unit_masuk,
											t.unit_keluar,
											'0' as unit_pembelian,
											'0' as unit_retur,
											t.status
										FROM
											tbl_log_stok AS t										
										JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id						
										$where_stokawal
									) q
									GROUP BY
										q.id_barang,
										q.satuan_id,
										q.gudang_id
									ORDER BY
										q.barang_nama ASC
							) r ON q.id_barang=r.barang AND q.satuan_id=r.satuan AND q.gudang_id=r.gudang
							GROUP BY
								q.id_barang,
								q.satuan_id,
								q.gudang_id
							ORDER BY
							q.barang_nama ASC");
        if($gudang=='all'){
        $nama_gudang = 'Semua Gudang';
	    }else{
	    $nama_gudang = DB::table('ref_gudang')->where('id',$gudang)->first()->nama;
	    }
		$no = 1;
		$arr = array();
			foreach ($d_data as $key => $d){
				$arr[] = array('NO.'=>$no++,
								'KODE BARANG' 			=> $d->barang_kode,
								'NAMA PARFURM'			=> $d->barang_nama,
								'NAMA GUDANG'			=> $d->gudang_nama,
								'STOCK AWAL'			=> format_angka($d->jumlah_stokawal).' '.$d->satuan_satuan,
								'MASUK'					=> format_angka($d->jumlah_masuk).' '.$d->satuan_satuan,
								'KELUAR'				=> format_angka($d->jumlah_keluar).' '.$d->satuan_satuan,
								'STOK AKHIR'			=> format_angka($d->jumlah_terakhir).' '.$d->satuan_satuan);
			}
		if(count($arr) > 0){
		$data = $arr;
		}else{
		$data = array("0"=>['NO.'=>$no++,
							'KODE BARANG' 			=> "",
							'NAMA PARFURM'			=> "",
							'NAMA GUDANG'			=> "",
							'STOCK AWAL'			=> "",
							'PEMBELIAN HARI INI'	=> "",
							'RETUR HARI INI'		=> "",
							'STOK AKHIR'			=> ""]);
		}
		// print_r($data);exit();
		return view('admin.laporan.hasil_laporan',compact('data'));
    			
    	
    }
    
}
