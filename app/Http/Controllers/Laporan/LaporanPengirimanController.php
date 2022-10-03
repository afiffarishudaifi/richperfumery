<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\Exports\LaporanViewExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPengirimanController extends Controller
{
    //
    public function index(){
    	$id_profil = Auth::user()->id_profil;
	    $group = Auth::user()->group_id;
	    $where = "";
	        if($group == 5){
	          $where = "WHERE id_profil='$id_profil'";
	        }
	    $data['gudang'] = DB::table('ref_gudang')->where('id','!=','8')->select('id as id_gudang','nama as nama_gudang')->get();
	    $data['group']	= Auth::user()->group_id;
    	return view('admin.laporan.index_laporan_pengiriman')->with('data',$data);
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

    public function cetaklaporan_pengiriman(Request $request){
    	require(public_path('fpdf1813/Mc_table.php'));
		ini_set('max_execution_time', '0');
		ini_set('memory_limit','512M');
		$gudang = implode(',', $request->get('gudang'));
    	$tanggal = tgl_full($request->get('tanggalAwal'),'99');    	
    	$tanggal2 = tgl_full($request->get('tanggalAkhir'),'99');
    	$kategori = $request->get('kategori');
    	$barang = $request->get('barang');
    	$id_barang = '';

        $add_where = " WHERE ";
    	$where_pengiriman[] = " t.status = 'K1' ";
    	$where_retur[] = "t.status = 'K4' ";

    	$where_gudang = "";
    	if(isset($gudang)){
    		if(in_array("all",$request->get('gudang'))){
    			$where_gudang = "";
    			$nama_gudang = "Semua Gudang Toko";
    		}else{
    			$where_gudang = " g2.id IN ($d_gudang) AND";
		    	$c_nmgudang = DB::table('ref_gudang')->whereIn('id',$gudang)->get();
		    	$d_nmgudang = array();
		    	foreach($c_nmgudang as $d){
		    		$d_nmgudang[] = $d->nama;
		    	}
    			$nama_gudang = join(' dan ', array_filter(array_merge(array(join(', ', array_slice($d_nmgudang, 0, -1))), array_slice($d_nmgudang, -1)), 'strlen'));
    		}

    		if($gudang != ''){
    			$where_stokawal[] = " t.id_ref_gudang IN ('8','18') ";
    			$where_pengiriman[] = " t.id_ref_gudang IN ('8','18') ";
    			$where_retur[] = " t.id_ref_gudang IN ('8','18') ";
    		}
    	}

    	if(isset($tanggal)){
    		if($tanggal != ''){
    			$where_stokawal[] = " t.tanggal < '".$tanggal."'";
    			$where_pengiriman[] = " t.tanggal >= '".$tanggal."'";
    			$where_retur[] = " t.tanggal >= '".$tanggal."'";
    		}
    	}

    	if(isset($tanggal2)){
    		if($tanggal2 !=''){
    			$where_pengiriman[] = " t.tanggal <= '".$tanggal2."'";
    			$where_retur[] = " t.tanggal <= '".$tanggal2."'";
    		}
    	}

    	if(isset($barang)){
    		if($barang != 0){
    			$where_stokawal[] = " t.id_barang = '".$barang."'";
    			$where_pengiriman[] = " t.id_barang = '".$barang."'";
    			$where_retur[] = " t.id_barang = '".$barang."'";
    			$id_barang  = $barang;
    		}
    	}

    	$where_stokawal = $add_where.implode(" AND ", $where_stokawal);
    	$where_pengiriman = $add_where.implode(" AND ", $where_pengiriman);
    	$where_retur = $add_where.implode(" AND ", $where_retur);

    	$sql = "SELECT q.*, 
				CASE WHEN SUM( r.stok )< 0 THEN '0' 
				WHEN SUM( r.stok ) IS NULL THEN '0' 
				ELSE SUM( r.stok ) END AS stok, 
				0 AS jumlah_masuk,
				0 AS jumlah_keluar,
				CASE WHEN SUM( r.stok )< 0 THEN '0' 
				WHEN SUM( r.stok ) IS NULL THEN '0' 
				ELSE SUM( r.stok ) END AS jumlah_stokawal,
				0 AS jumlah_pengiriman,
				0 AS jumlah_retur,
				0 AS jumlah_terakhir,
				0 AS harga_pengiriman,
				0 AS total_pengiriman
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
							pe.gudang_tujuan AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							'0' as unit_masuk,
							'0' as unit_keluar,
							t.unit_keluar as unit_pengiriman,
							'0' as unit_retur,
							pd.harga as harga,
							t.status,
							pe.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						LEFT JOIN pengiriman_detail AS pd ON t.log_stok_id=pd.id_log_stok
						JOIN pengiriman AS pe ON pd.id_inv_pengiriman=pe.id
						LEFT JOIN ref_gudang AS g2 ON pe.gudang_tujuan=g2.id
						$where_pengiriman
				UNION ALL
						SELECT
							t.id_barang,
							b.barang_nama,
							b.barang_kode,											
							t.id_satuan AS satuan_id,
							s.satuan_nama,
							s.satuan_satuan,
							t.id_ref_gudang AS gudang_id,
							g.nama AS gudang_nama,
							pr.id_gudang_outlet AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							'0' as unit_masuk,
							'0' as unit_keluar,
							'0' AS unit_pengiriman,
							t.unit_masuk AS unit_retur,
							'0' AS harga,
							t.status,
							pr.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						JOIN pengiriman_retur AS pr ON t.log_stok_id=pr.id_log_stok
						LEFT JOIN ref_gudang AS g2 ON pr.id_gudang_outlet=g2.id
						$where_retur
					) q
					JOIN (
						SELECT  
							q.id_barang,
							q.id_gudang,
							q.id_satuan,
							q.nama_barang,
							sum(q.unit_masuk-q.unit_keluar) as stok
						FROM (                                             
							SELECT
									t.id_barang as id_barang,
									t.id_ref_gudang as id_gudang,
									t.id_satuan as id_satuan,
									tb.barang_nama AS nama_barang,
									t.unit_masuk AS unit_masuk,
									t.unit_keluar AS unit_keluar
							FROM tbl_log_stok AS t
							LEFT JOIN tbl_barang AS tb ON t.id_barang = barang_id
							LEFT JOIN ref_gudang AS rg ON t.id_ref_gudang = rg.id
							LEFT JOIN tbl_satuan AS ts ON t.id_satuan = ts.satuan_id
							$where_stokawal
						) q 
						GROUP BY 
							q.id_barang,
							q.id_gudang, 
							q.id_satuan
						ORDER BY
						q.nama_barang
						) AS r ON q.id_barang=r.id_barang AND q.satuan_id=r.id_satuan AND q.gudang_id=r.id_gudang
						GROUP BY
							q.id_barang,
							q.satuan_id,
							q.gudang_id
						ORDER BY
						q.barang_nama
			 ";
        trigger_log(NULL, "Cetak Laporan Pengiriman PDF", 7);
    	switch ($kategori) {
    		case '1':
    	$d_query = DB::select("SELECT q.*, 
				CASE WHEN SUM( r.stok )< 0 THEN '0' 
				WHEN SUM( r.stok ) IS NULL THEN '0' 
				ELSE SUM( r.stok ) END AS stok, 
				0 AS jumlah_masuk,
				0 AS jumlah_keluar,
				CASE WHEN SUM( r.stok )< 0 THEN '0' 
				WHEN SUM( r.stok ) IS NULL THEN '0' 
				ELSE SUM( r.stok ) END AS jumlah_stokawal,
				0 AS jumlah_pengiriman,
				0 AS jumlah_retur,
				0 AS jumlah_terakhir,
				0 AS harga_pengiriman,
				0 AS total_pengiriman
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
							pe.gudang_tujuan AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							'0' as unit_masuk,
							'0' as unit_keluar,
							t.unit_keluar as unit_pengiriman,
							'0' as unit_retur,
							pd.harga as harga,
							t.status,
							pe.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						LEFT JOIN pengiriman_detail AS pd ON t.log_stok_id=pd.id_log_stok
						JOIN pengiriman AS pe ON pd.id_inv_pengiriman=pe.id
						LEFT JOIN ref_gudang AS g2 ON pe.gudang_tujuan=g2.id
						$where_pengiriman
				UNION ALL
						SELECT
							t.id_barang,
							b.barang_nama,
							b.barang_kode,											
							t.id_satuan AS satuan_id,
							s.satuan_nama,
							s.satuan_satuan,
							t.id_ref_gudang AS gudang_id,
							g.nama AS gudang_nama,
							pr.id_gudang_outlet AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							'0' as unit_masuk,
							'0' as unit_keluar,
							'0' AS unit_pengiriman,
							t.unit_masuk AS unit_retur,
							'0' AS harga,
							t.status,
							pr.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						JOIN pengiriman_retur AS pr ON t.log_stok_id=pr.id_log_stok
						LEFT JOIN ref_gudang AS g2 ON pr.id_gudang_outlet=g2.id
						$where_retur
					) q
					JOIN (
						SELECT  
							q.id_barang,
							q.id_gudang,
							q.id_satuan,
							q.nama_barang,
							sum(q.unit_masuk-q.unit_keluar) as stok
						FROM (                                             
							SELECT
									t.id_barang as id_barang,
									t.id_ref_gudang as id_gudang,
									t.id_satuan as id_satuan,
									tb.barang_nama AS nama_barang,
									t.unit_masuk AS unit_masuk,
									t.unit_keluar AS unit_keluar
							FROM tbl_log_stok AS t
							LEFT JOIN tbl_barang AS tb ON t.id_barang = barang_id
							LEFT JOIN ref_gudang AS rg ON t.id_ref_gudang = rg.id
							LEFT JOIN tbl_satuan AS ts ON t.id_satuan = ts.satuan_id
							$where_stokawal
						) q 
						GROUP BY 
							q.id_barang,
							q.id_gudang, 
							q.id_satuan
						ORDER BY
						q.nama_barang
						) AS r ON q.id_barang=r.id_barang AND q.satuan_id=r.id_satuan AND q.gudang_id=r.id_gudang
						GROUP BY
							q.id_barang,
							q.satuan_id,
							q.gudang_id
						ORDER BY
						q.barang_nama
			");	
        $wheres = array();
	    if(count($d_query) > 0){    	
	    foreach($d_query as $e){
	        		$wheres[] = array('id_barang'		=> $e->id_barang,
	        						'nama_barang'		=> $e->barang_nama,
	        						'kode_barang'		=> $e->barang_kode,
	        						'id_satuan'			=> $e->satuan_id,
	        						'nama_satuan'		=> $e->satuan_satuan,
	        						'id_gudang'			=> $e->gudang_id,
	        						'nama_gudang'		=> $e->gudang_nama,
	        						'id_gudangtujuan'	=> $e->gudangtujuan_id,
	        						'nama_gudangtujuan'	=> $e->gudangtujuan_nama,
	        						'jumlah_pengiriman' => $e->jumlah_pengiriman,
	        						'jumlah_retur'		=> $e->jumlah_retur,
	        						'jumlah_stokawal'	=> $e->jumlah_stokawal,
	        						'total_pengiriman'	=> $e->total_pengiriman,
	        						'status_pengiriman' => '',
	        						'keterangan'		=> $e->keterangan);
	        		}
	    }

	    $where_arr = array();
	    $where_arr2 = array();
	    foreach($wheres as $w){
	    	$wheres_stokawal = "WHERE t.id_ref_gudang = '".$w['id_gudang']."' AND ".$where_gudang." t.id_barang = '".$w['id_barang']."' AND t.id_satuan = '".$w['id_satuan']."' AND  t.tanggal < '".$tanggal."'";
	    	$wheres_pengiriman = "WHERE t.id_ref_gudang = '".$w['id_gudang']."' AND ".$where_gudang." t.id_barang = '".$w['id_barang']."' AND t.id_satuan = '".$w['id_satuan']."' AND  t.tanggal >= '".$tanggal."' AND t.tanggal <= '".$tanggal2."' AND t.status='K1'";
		    $wheres_retur = "WHERE t.id_ref_gudang = '".$w['id_gudang']."' AND ".$where_gudang." t.id_barang = '".$w['id_barang']."' AND t.id_satuan = '".$w['id_satuan']."' AND t.tanggal >= '".$tanggal."' AND t.tanggal <= '".$tanggal2."' AND t.status='K4'";
			$d_query2 = DB::SELECT("SELECT q.*,
					0 as stok,
					SUM( q.unit_masuk ) AS jumlah_masuk,
					SUM( q.unit_keluar ) AS jumlah_keluar,
					0 AS jumlah_stokawal,
					CASE WHEN SUM( q.unit_pengiriman )<0 THEN '0' 
					WHEN SUM( q.unit_pengiriman ) IS NULL THEN '0' 
					ELSE SUM( q.unit_pengiriman ) END AS jumlah_pengiriman,
					CASE WHEN SUM( q.unit_retur )<0 THEN '0' 
					WHEN SUM( q.unit_retur ) IS NULL THEN '0' 
					ELSE SUM( q.unit_retur ) END AS jumlah_retur,
					0 AS jumlah_terakhir,
					CASE WHEN SUM( q.harga )<0 THEN '0' 
					WHEN SUM( q.harga ) IS NULL THEN '0' 
					ELSE SUM( q.harga ) END AS harga_pengiriman,
					CASE WHEN SUM( q.harga*q.unit_pengiriman )<0 THEN '0' 
					WHEN SUM( q.harga*q.unit_pengiriman ) IS NULL THEN '0' 
					ELSE SUM( q.harga*q.unit_pengiriman ) END AS total_pengiriman
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
							pe.gudang_tujuan AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							'0' as unit_masuk,
							'0' as unit_keluar,
							t.unit_keluar as unit_pengiriman,
							'0' as unit_retur,
							pd.harga as harga,
							t.status,
							CASE WHEN pe.status_pengiriman = 1 THEN 'Sedang Dikirim'
							WHEN pe.status_pengiriman = 2 THEN 'Terkirim'
							WHEN pe.status_pengiriman = 3 THEN 'Diterima Sebagian'
							WHEN pe.status_pengiriman = 4 THEN 'Dikembalikan'
							ELSE '' END AS status_pengiriman,
							pe.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						LEFT JOIN pengiriman_detail AS pd ON t.log_stok_id=pd.id_log_stok
						JOIN pengiriman AS pe ON pd.id_inv_pengiriman=pe.id
						LEFT JOIN ref_gudang AS g2 ON pe.gudang_tujuan=g2.id
						$wheres_pengiriman
				UNION ALL
						SELECT
							t.id_barang,
							b.barang_nama,
							b.barang_kode,											
							t.id_satuan AS satuan_id,
							s.satuan_nama,
							s.satuan_satuan,
							t.id_ref_gudang AS gudang_id,
							g.nama AS gudang_nama,
							pr.id_gudang_outlet AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							'0' as unit_masuk,
							'0' as unit_keluar,
							'0' AS unit_pengiriman,
							t.unit_masuk AS unit_retur,
							'0' AS harga,
							t.status,
							CASE WHEN pr.status = 1 THEN 'Terkirim'
							WHEN pr.status = 2 THEN 'Diterima'
							WHEN pr.status = 3 THEN 'Diterima Sebagian'
							ELSE '' END AS status_pengiriman,
							pr.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						JOIN pengiriman_retur AS pr ON t.log_stok_id=pr.id_log_stok
						LEFT JOIN ref_gudang AS g2 ON pr.id_gudang_outlet=g2.id
						$wheres_retur
					) q
						GROUP BY
							q.id_barang,
							q.satuan_id,
							q.gudang_id,
							q.gudangtujuan_id
						ORDER BY
							q.barang_nama 
				");		
			$where_arr[] = $w+array("jumlah_terakhir"=>$w['jumlah_stokawal'],"jenis"=>'0');
			$sisa = $w['jumlah_stokawal'];
			foreach ($d_query2 as $key => $e) {
				$sisa = ($sisa+$e->jumlah_retur)-$e->jumlah_pengiriman;
				$stok = $sisa+$e->jumlah_pengiriman;
				$where_arr[] = array('id_barang'		=> $e->id_barang,
	        						'nama_barang'		=> $e->barang_nama,
	        						'kode_barang'		=> $e->barang_kode,
	        						'id_satuan'			=> $e->satuan_id,
	        						'nama_satuan'		=> $e->satuan_satuan,
	        						'id_gudang'			=> $e->gudang_id,
	        						'nama_gudang'		=> $e->gudang_nama,
	        						'id_gudangtujuan'	=> $e->gudangtujuan_id,
	        						'nama_gudangtujuan'	=> $e->gudangtujuan_nama,
	        						'jumlah_pengiriman' => $e->jumlah_pengiriman,
	        						'jumlah_retur'		=> $e->jumlah_retur,
	        						'jumlah_stokawal'	=> $stok,
	        						'total_pengiriman'	=> $e->total_pengiriman,
	        						'status_pengiriman'	=> $e->status_pengiriman,
	        						'keterangan'		=> $e->keterangan,
	        						'jumlah_terakhir'	=> $sisa,
	        						'jenis'				=> '1');
	        		
			}
	    }
	    $data['data'] 	= $where_arr;
	    $data['tanggal_awal'] = tgl_full($request->tanggalAwal,'');
	    $data['tanggal_akhir'] = tgl_full($request->tanggalAkhir,'');
	    $data['hari']    = tgl_full($request->tanggal,'hari');
	    $data['kasir']   = Auth::user()->name;
	    $data['gudang']	 = $nama_gudang;

	    $html = view('admin.laporan.cetak_laporan_pengiriman')->with('data',$data);
      	return response($html)->header('Content-Type', 'application/pdf');
    			break;
    			case '2':
    	$id_progress = '';
    	$d_query_awal = DB::select("SELECT q.*, 
				CASE WHEN SUM( r.stok )< 0 THEN '0' 
				WHEN SUM( r.stok ) IS NULL THEN '0' 
				ELSE SUM( r.stok ) END AS stok, 
				0 AS jumlah_masuk,
				0 AS jumlah_keluar,
				CASE WHEN SUM( r.stok )< 0 THEN '0' 
				WHEN SUM( r.stok ) IS NULL THEN '0' 
				ELSE SUM( r.stok ) END AS jumlah_stokawal,
				0 AS jumlah_pengiriman,
				0 AS jumlah_retur,
				0 AS jumlah_terakhir,
				0 AS harga_pengiriman,
				0 AS total_pengiriman
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
							pe.gudang_tujuan AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							pe.kode_pengiriman as no_nota,
							'0' as unit_masuk,
							'0' as unit_keluar,
							t.unit_keluar as unit_pengiriman,
							'0' as unit_retur,
							pd.harga as harga,
							t.status,
							pe.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						LEFT JOIN pengiriman_detail AS pd ON t.log_stok_id=pd.id_log_stok
						JOIN pengiriman AS pe ON pd.id_inv_pengiriman=pe.id
						LEFT JOIN ref_gudang AS g2 ON pe.gudang_tujuan=g2.id
						$where_pengiriman
				UNION ALL
						SELECT
							t.id_barang,
							b.barang_nama,
							b.barang_kode,											
							t.id_satuan AS satuan_id,
							s.satuan_nama,
							s.satuan_satuan,
							t.id_ref_gudang AS gudang_id,
							g.nama AS gudang_nama,
							pr.id_gudang_outlet AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							pr.kode_retur AS no_nota,
							'0' as unit_masuk,
							'0' as unit_keluar,
							'0' AS unit_pengiriman,
							t.unit_masuk AS unit_retur,
							'0' AS harga,
							t.status,
							pr.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						JOIN pengiriman_retur AS pr ON t.log_stok_id=pr.id_log_stok
						LEFT JOIN ref_gudang AS g2 ON pr.id_gudang_outlet=g2.id
						$where_retur
					) q
					JOIN (
						SELECT  
							q.id_barang,
							q.id_gudang,
							q.id_satuan,
							q.nama_barang,
							sum(q.unit_masuk-q.unit_keluar) as stok
						FROM (                                             
							SELECT
									t.id_barang as id_barang,
									t.id_ref_gudang as id_gudang,
									t.id_satuan as id_satuan,
									tb.barang_nama AS nama_barang,
									t.unit_masuk AS unit_masuk,
									t.unit_keluar AS unit_keluar
							FROM tbl_log_stok AS t
							LEFT JOIN tbl_barang AS tb ON t.id_barang = barang_id
							LEFT JOIN ref_gudang AS rg ON t.id_ref_gudang = rg.id
							LEFT JOIN tbl_satuan AS ts ON t.id_satuan = ts.satuan_id
							$where_stokawal
						) q 
						GROUP BY 
							q.id_barang,
							q.id_gudang, 
							q.id_satuan
						ORDER BY
						q.nama_barang
						) AS r ON q.id_barang=r.id_barang AND q.satuan_id=r.id_satuan AND q.gudang_id=r.id_gudang
						GROUP BY
							q.no_nota,
							q.id_barang,
							q.satuan_id,
							q.gudang_id
						ORDER BY
						q.barang_nama
			");	
    	$d_query_mutasi = DB::SELECT("
    			SELECT q.*,
					0 as stok,
					SUM( q.unit_masuk ) AS jumlah_masuk,
					SUM( q.unit_keluar ) AS jumlah_keluar,
					0 AS jumlah_stokawal,
					CASE WHEN SUM( q.unit_pengiriman )<0 THEN '0' 
					WHEN SUM( q.unit_pengiriman ) IS NULL THEN '0' 
					ELSE SUM( q.unit_pengiriman ) END AS jumlah_pengiriman,
					CASE WHEN SUM( q.unit_retur )<0 THEN '0' 
					WHEN SUM( q.unit_retur ) IS NULL THEN '0' 
					ELSE SUM( q.unit_retur ) END AS jumlah_retur,
					0 AS jumlah_terakhir,
					CASE WHEN SUM( q.harga )<0 THEN '0' 
					WHEN SUM( q.harga ) IS NULL THEN '0' 
					ELSE SUM( q.harga ) END AS harga_pengiriman,
					CASE WHEN SUM( q.harga*q.unit_pengiriman )<0 THEN '0' 
					WHEN SUM( q.harga*q.unit_pengiriman ) IS NULL THEN '0' 
					ELSE SUM( q.harga*q.unit_pengiriman ) END AS total_pengiriman
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
							pe.gudang_tujuan AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							pe.kode_pengiriman as no_nota,
							'0' as unit_masuk,
							'0' as unit_keluar,
							t.unit_keluar as unit_pengiriman,
							'0' as unit_retur,
							pd.harga as harga,
							t.status,
							CASE WHEN pe.status_pengiriman = 1 THEN 'Sedang Dikirim'
							WHEN pe.status_pengiriman = 2 THEN 'Terkirim'
							WHEN pe.status_pengiriman = 3 THEN 'Diterima Sebagian'
							WHEN pe.status_pengiriman = 4 THEN 'Dikembalikan'
							ELSE '' END AS status_pengiriman,
							pe.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						LEFT JOIN pengiriman_detail AS pd ON t.log_stok_id=pd.id_log_stok
						JOIN pengiriman AS pe ON pd.id_inv_pengiriman=pe.id
						LEFT JOIN ref_gudang AS g2 ON pe.gudang_tujuan=g2.id
						$where_pengiriman
				UNION ALL
						SELECT
							t.id_barang,
							b.barang_nama,
							b.barang_kode,											
							t.id_satuan AS satuan_id,
							s.satuan_nama,
							s.satuan_satuan,
							t.id_ref_gudang AS gudang_id,
							g.nama AS gudang_nama,
							pr.id_gudang_outlet AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							pr.kode_retur AS no_nota,
							'0' as unit_masuk,
							'0' as unit_keluar,
							'0' AS unit_pengiriman,
							t.unit_masuk AS unit_retur,
							'0' AS harga,
							t.status,
							CASE WHEN pr.status = 1 THEN 'Terkirim'
							WHEN pr.status = 2 THEN 'Diterima'
							WHEN pr.status = 3 THEN 'Diterima Sebagian'
							ELSE '' END AS status_pengiriman,
							pr.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						JOIN pengiriman_retur AS pr ON t.log_stok_id=pr.id_log_stok
						LEFT JOIN ref_gudang AS g2 ON pr.id_gudang_outlet=g2.id
						$where_retur
					) q
						GROUP BY
							q.no_nota,
							q.id_barang,
							q.satuan_id,
							q.gudang_id,
							q.gudangtujuan_id
						ORDER BY
							q.barang_nama 
		");	
        $wheres = array();
	    if(count($d_query_awal) > 0){    	
	    foreach($d_query_awal as $e){
	        		$wheres[] = array('id_barang'		=> $e->id_barang,
	        						'nama_barang'		=> $e->barang_nama,
	        						'kode_barang'		=> $e->barang_kode,
	        						'id_satuan'			=> $e->satuan_id,
	        						'nama_satuan'		=> $e->satuan_satuan,
	        						'id_gudang'			=> $e->gudang_id,
	        						'nama_gudang'		=> $e->gudang_nama,
	        						'id_gudangtujuan'	=> $e->gudangtujuan_id,
	        						'nama_gudangtujuan'	=> $e->gudangtujuan_nama,
	        						'jumlah_pengiriman' => $e->jumlah_pengiriman,
	        						'jumlah_retur'		=> $e->jumlah_retur,
	        						'jumlah_stokawal'	=> $e->jumlah_stokawal,
	        						'total_pengiriman'	=> $e->total_pengiriman,
	        						'status_pengiriman' => '',
	        						'keterangan'		=> $e->keterangan);
	        		}
	    }

	    $new = array();
	    $arr = array();
	    foreach($wheres as $w){
	    	$arr[] = $w+array("jumlah_terakhir"=>$w['jumlah_stokawal'],"jenis"=>'0');
			$sisa = $w['jumlah_stokawal'];

			$new = array_filter($d_query_mutasi, function ($var) use ($w, $id_barang) {
				/*return $var->id_barang==$w['id_barang'] && $var->satuan_id==$w['id_satuan'] && $var->gudang_id==$w['id_gudang'];*/

				/*return $var->id_barang==$w['id_barang'] && $var->satuan_id==$w['id_satuan'] && $var->gudang_id==$w['id_gudang'] && $var->gudangtujuan_id==$w['id_gudangtujuan'];*/
				if($id_barang != ''){
					return $var->id_barang==$w['id_barang'] && $var->satuan_id==$w['id_satuan'] && $var->gudang_id==$w['id_gudang'];
				}else{
					return true;
				}
			});
			
			$count = count($new);
			foreach($new as $key => $d){
				// $sisa = ($sisa+$d['keluar'])-$d['masuk'];
				$sisa = ($sisa+$d->jumlah_retur)-$d->jumlah_pengiriman;
				$stok = $sisa+$d->jumlah_pengiriman;
				// $arr[] = $d+array('jumlah_terakhir'	=> $sisa);
				if($key <= $count){
				$arr[] = array('id_barang'			=> $d->id_barang,
	        				   'nama_barang'		=> $d->barang_nama,
	        				   'kode_barang'		=> $d->barang_kode,
	        				   'id_satuan'			=> $d->satuan_id,
	        				   'nama_satuan'		=> $d->satuan_satuan,
	        				   'id_gudang'			=> $d->gudang_id,
	        				   'nama_gudang'		=> $d->gudang_nama,
	        				   'id_gudangtujuan'	=> $d->gudangtujuan_id,
	        				   'nama_gudangtujuan'	=> $d->gudangtujuan_nama,
	        				   'jumlah_pengiriman' 	=> $d->jumlah_pengiriman,
	        				   'jumlah_retur'		=> $d->jumlah_retur,
	        				   'jumlah_stokawal'	=> $stok,
	        				   'total_pengiriman'	=> $d->total_pengiriman,
	        				   'status_pengiriman'	=> $d->status_pengiriman,
	        				   'keterangan'			=> $d->keterangan,
	        				   'jumlah_terakhir'	=> $sisa,
	        				   'jenis'				=> '1',
	        				   'no_nota'			=> $d->no_nota,
	        				   'tanggal'			=> date('d-m-Y',strtotime($d->tanggal)),
	        				   'key'				=> $key);
				}
			}

			
	    }

	   	
	    $arr2 = array();
	    foreach($arr as $c){
	    	if($c['jenis'] != 0){
	    	$arr2[$c['key']] = $c;
	    	}
	    }

	    // $data['data'] = $where_arr;
	    if(count($arr2) > 0){
	    	/*if($id_barang != ''){
	    		$d_arr2 = $arr2;
	    	}else{
	    		$d_arr2 = array_values($arr2);
	    	}*/

	    	$d_arr2 = $arr2;
	    }else{
	    	$d_arr2 = array();
	    }
	    $data['data'] 	= $d_arr2;
	    $data['tanggal_awal'] = tgl_full($request->tanggalAwal,'');
	    $data['tanggal_akhir'] = tgl_full($request->tanggalAkhir,'');
	    $data['hari']    = tgl_full($request->tanggal,'hari');
	    $data['kasir']   = Auth::user()->name;
	    $data['gudang']	 = $nama_gudang;

	    $html = view('admin.laporan.cetak_laporan_pengiriman_pernota')->with('data',$data);
      	return response($html)->header('Content-Type', 'application/pdf');
    			break;
    		default:
    			# code...
    			break;
    	}
        
        
    }

    public function excel($d_gudang,$d_tanggal_awal,$d_tanggal_akhir,$d_kategori,$d_barang=''){
    	ini_set('max_execution_time', '0');
    	ini_set('memory_limit','512M');
    	$gudang = explode(',',$d_gudang);
    	$tanggal = tgl_full($d_tanggal_awal,'99');  
    	$tanggal2 = tgl_full($d_tanggal_akhir,'99'); 
    	$kategori = $d_kategori;
    	$barang = $d_barang;

    	$add_where = " WHERE ";
    	$where_pengiriman[] = " t.status = 'K1' ";
    	$where_retur[] = "t.status = 'K4' ";

    	$where_gudang = "";
    	if(isset($gudang)){
    		if(in_array("all",$gudang)){
    			$where_gudang = "";
    			$nama_gudang = "Semua Gudang Toko";
    		}else{
    			$where_gudang = " g2.id IN ($d_gudang) AND";
		    	$c_nmgudang = DB::table('ref_gudang')->whereIn('id',$gudang)->get();
		    	$d_nmgudang = array();
		    	foreach($c_nmgudang as $d){
		    		$d_nmgudang[] = $d->nama;
		    	}
    			$nama_gudang = join(' dan ', array_filter(array_merge(array(join(', ', array_slice($d_nmgudang, 0, -1))), array_slice($d_nmgudang, -1)), 'strlen'));
    		}

    		if($gudang != ''){
    			$where_stokawal[] = " t.id_ref_gudang IN ('8','18') ";
    			$where_pengiriman[] = " t.id_ref_gudang IN ('8','18') ";
    			$where_retur[] = " t.id_ref_gudang IN ('8','18') ";
    		}
    	}

    	if(isset($tanggal)){
    		if($tanggal != ''){
    			$where_stokawal[] = " t.tanggal < '".$tanggal."'";
    			$where_pengiriman[] = " t.tanggal >= '".$tanggal."'";
    			$where_retur[] = " t.tanggal >= '".$tanggal."'";
    		}
    	}

    	if(isset($tanggal2)){
    		if($tanggal2 !=''){
    			$where_pengiriman[] = " t.tanggal <= '".$tanggal2."'";
    			$where_retur[] = " t.tanggal <= '".$tanggal2."'";
    		}
    	}

    	if(isset($barang)){
    		if($barang != 0){
    			$where_stokawal[] = " t.id_barang = '".$barang."'";
    			$where_pengiriman[] = " t.id_barang = '".$barang."'";
    			$where_retur[] = " t.id_barang = '".$barang."'";
    		}
    	}

    	$where_stokawal = $add_where.implode(" AND ", $where_stokawal);
    	$where_pengiriman = $add_where.implode(" AND ", $where_pengiriman);
    	$where_retur = $add_where.implode(" AND ", $where_retur);
        trigger_log(NULL, "Cetak Laporan Pengiriman Excel", 7);
        switch ($kategori) {
        	case '1':
        $d_query = DB::select("SELECT q.*, 
				CASE WHEN SUM( r.stok )< 0 THEN '0' 
				WHEN SUM( r.stok ) IS NULL THEN '0' 
				ELSE SUM( r.stok ) END AS stok, 
				0 AS jumlah_masuk,
				0 AS jumlah_keluar,
				CASE WHEN SUM( r.stok )< 0 THEN '0' 
				WHEN SUM( r.stok ) IS NULL THEN '0' 
				ELSE SUM( r.stok ) END AS jumlah_stokawal,
				0 AS jumlah_pengiriman,
				0 AS jumlah_retur,
				0 AS jumlah_terakhir,
				0 AS harga_pengiriman,
				0 AS total_pengiriman
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
							pe.gudang_tujuan AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							'0' as unit_masuk,
							'0' as unit_keluar,
							t.unit_keluar as unit_pengiriman,
							'0' as unit_retur,
							pd.harga as harga,
							t.status,
							pe.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						LEFT JOIN pengiriman_detail AS pd ON t.log_stok_id=pd.id_log_stok
						JOIN pengiriman AS pe ON pd.id_inv_pengiriman=pe.id
						LEFT JOIN ref_gudang AS g2 ON pe.gudang_tujuan=g2.id
						$where_pengiriman
				UNION ALL
						SELECT
							t.id_barang,
							b.barang_nama,
							b.barang_kode,											
							t.id_satuan AS satuan_id,
							s.satuan_nama,
							s.satuan_satuan,
							t.id_ref_gudang AS gudang_id,
							g.nama AS gudang_nama,
							pr.id_gudang_outlet AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							'0' as unit_masuk,
							'0' as unit_keluar,
							'0' AS unit_pengiriman,
							t.unit_masuk AS unit_retur,
							'0' AS harga,
							t.status,
							pr.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						JOIN pengiriman_retur AS pr ON t.log_stok_id=pr.id_log_stok
						LEFT JOIN ref_gudang AS g2 ON pr.id_gudang_outlet=g2.id
						$where_retur
					) q
					JOIN (
						SELECT  
							q.id_barang,
							q.id_gudang,
							q.id_satuan,
							q.nama_barang,
							sum(q.unit_masuk-q.unit_keluar) as stok
						FROM (                                             
							SELECT
									t.id_barang as id_barang,
									t.id_ref_gudang as id_gudang,
									t.id_satuan as id_satuan,
									tb.barang_nama AS nama_barang,
									t.unit_masuk AS unit_masuk,
									t.unit_keluar AS unit_keluar
							FROM tbl_log_stok AS t
							LEFT JOIN tbl_barang AS tb ON t.id_barang = barang_id
							LEFT JOIN ref_gudang AS rg ON t.id_ref_gudang = rg.id
							LEFT JOIN tbl_satuan AS ts ON t.id_satuan = ts.satuan_id
							$where_stokawal
						) q 
						GROUP BY 
							q.id_barang,
							q.id_gudang, 
							q.id_satuan
						ORDER BY
						q.nama_barang
						) AS r ON q.id_barang=r.id_barang AND q.satuan_id=r.id_satuan AND q.gudang_id=r.id_gudang
						GROUP BY
							q.id_barang,
							q.satuan_id,
							q.gudang_id
						ORDER BY
						q.barang_nama
			");	
        $wheres = array();
	    if(count($d_query) > 0){    	
	    foreach($d_query as $e){
	        		$wheres[] = array('id_barang'		=> $e->id_barang,
	        						'nama_barang'		=> $e->barang_nama,
	        						'kode_barang'		=> $e->barang_kode,
	        						'id_satuan'			=> $e->satuan_id,
	        						'nama_satuan'		=> $e->satuan_satuan,
	        						'id_gudang'			=> $e->gudang_id,
	        						'nama_gudang'		=> $e->gudang_nama,
	        						'id_gudangtujuan'	=> $e->gudangtujuan_id,
	        						'nama_gudangtujuan'	=> $e->gudangtujuan_nama,
	        						'jumlah_pengiriman' => $e->jumlah_pengiriman,
	        						'jumlah_retur'		=> $e->jumlah_retur,
	        						'jumlah_stokawal'	=> $e->jumlah_stokawal,
	        						'total_pengiriman'	=> $e->total_pengiriman,
	        						'status_pengiriman' => '',
	        						'keterangan'		=> $e->keterangan);
	        		}
	    }

	    $where_arr = array();
	    $where_arr2 = array();
	    foreach($wheres as $w){
	    	$wheres_stokawal = "WHERE t.id_ref_gudang = '".$w['id_gudang']."' AND ".$where_gudang." t.id_barang = '".$w['id_barang']."' AND t.id_satuan = '".$w['id_satuan']."' AND  t.tanggal < '".$tanggal."'";
	    	$wheres_pengiriman = "WHERE t.id_ref_gudang = '".$w['id_gudang']."' AND ".$where_gudang." t.id_barang = '".$w['id_barang']."' AND t.id_satuan = '".$w['id_satuan']."' AND  t.tanggal >= '".$tanggal."' AND t.tanggal <= '".$tanggal2."' AND t.status='K1'";
		    $wheres_retur = "WHERE t.id_ref_gudang = '".$w['id_gudang']."' AND ".$where_gudang." t.id_barang = '".$w['id_barang']."' AND t.id_satuan = '".$w['id_satuan']."' AND t.tanggal >= '".$tanggal."' AND t.tanggal <= '".$tanggal2."' AND t.status='K4'";
			$d_query2 = DB::SELECT("SELECT q.*,
					0 as stok,
					SUM( q.unit_masuk ) AS jumlah_masuk,
					SUM( q.unit_keluar ) AS jumlah_keluar,
					0 AS jumlah_stokawal,
					CASE WHEN SUM( q.unit_pengiriman )<0 THEN '0' 
					WHEN SUM( q.unit_pengiriman ) IS NULL THEN '0' 
					ELSE SUM( q.unit_pengiriman ) END AS jumlah_pengiriman,
					CASE WHEN SUM( q.unit_retur )<0 THEN '0' 
					WHEN SUM( q.unit_retur ) IS NULL THEN '0' 
					ELSE SUM( q.unit_retur ) END AS jumlah_retur,
					0 AS jumlah_terakhir,
					CASE WHEN SUM( q.harga )<0 THEN '0' 
					WHEN SUM( q.harga ) IS NULL THEN '0' 
					ELSE SUM( q.harga ) END AS harga_pengiriman,
					CASE WHEN SUM( q.harga*q.unit_pengiriman )<0 THEN '0' 
					WHEN SUM( q.harga*q.unit_pengiriman ) IS NULL THEN '0' 
					ELSE SUM( q.harga*q.unit_pengiriman ) END AS total_pengiriman
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
							pe.gudang_tujuan AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							'0' as unit_masuk,
							'0' as unit_keluar,
							t.unit_keluar as unit_pengiriman,
							'0' as unit_retur,
							pd.harga as harga,
							t.status,
							CASE WHEN pe.status_pengiriman = 1 THEN 'Sedang Dikirim'
							WHEN pe.status_pengiriman = 2 THEN 'Terkirim'
							WHEN pe.status_pengiriman = 3 THEN 'Diterima Sebagian'
							WHEN pe.status_pengiriman = 4 THEN 'Dikembalikan'
							ELSE '' END AS status_pengiriman,
							pe.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						LEFT JOIN pengiriman_detail AS pd ON t.log_stok_id=pd.id_log_stok
						JOIN pengiriman AS pe ON pd.id_inv_pengiriman=pe.id
						LEFT JOIN ref_gudang AS g2 ON pe.gudang_tujuan=g2.id
						$wheres_pengiriman
				UNION ALL
						SELECT
							t.id_barang,
							b.barang_nama,
							b.barang_kode,											
							t.id_satuan AS satuan_id,
							s.satuan_nama,
							s.satuan_satuan,
							t.id_ref_gudang AS gudang_id,
							g.nama AS gudang_nama,
							pr.id_gudang_outlet AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							'0' as unit_masuk,
							'0' as unit_keluar,
							'0' AS unit_pengiriman,
							t.unit_masuk AS unit_retur,
							'0' AS harga,
							t.status,
							CASE WHEN pr.status = 1 THEN 'Terkirim'
							WHEN pr.status = 2 THEN 'Diterima'
							WHEN pr.status = 3 THEN 'Diterima Sebagian'
							ELSE '' END AS status_pengiriman,
							pr.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						JOIN pengiriman_retur AS pr ON t.log_stok_id=pr.id_log_stok
						LEFT JOIN ref_gudang AS g2 ON pr.id_gudang_outlet=g2.id
						$wheres_retur
					) q
						GROUP BY
							q.id_barang,
							q.satuan_id,
							q.gudang_id,
							q.gudangtujuan_id
						ORDER BY
							q.barang_nama 
				");		
			$where_arr[] = $w+array("jumlah_terakhir"=>$w['jumlah_stokawal'],"jenis"=>'0');
			$sisa = $w['jumlah_stokawal'];
			foreach ($d_query2 as $key => $e) {
				# code...
				$sisa = ($sisa+$e->jumlah_retur)-$e->jumlah_pengiriman;
				$stok = $sisa+$e->jumlah_pengiriman;
				$where_arr[] = array('id_barang'		=> $e->id_barang,
	        						'nama_barang'		=> $e->barang_nama,
	        						'kode_barang'		=> $e->barang_kode,
	        						'id_satuan'			=> $e->satuan_id,
	        						'nama_satuan'		=> $e->satuan_satuan,
	        						'id_gudang'			=> $e->gudang_id,
	        						'nama_gudang'		=> $e->gudang_nama,
	        						'id_gudangtujuan'	=> $e->gudangtujuan_id,
	        						'nama_gudangtujuan'	=> $e->gudangtujuan_nama,
	        						'jumlah_pengiriman' => $e->jumlah_pengiriman,
	        						'jumlah_retur'		=> $e->jumlah_retur,
	        						'jumlah_stokawal'	=> $stok,
	        						'total_pengiriman'	=> $e->total_pengiriman,
	        						'status_pengiriman'	=> $e->status_pengiriman,
	        						'keterangan'		=> $e->keterangan,
	        						'jumlah_terakhir'	=> $sisa,
	        						'jenis'				=> '1');
	        		
			}

			
	    	

	    }

        $no = 1;
		$arr = array();
			foreach ($where_arr as $key => $d){
				if($d['jenis'] != 0){
				$arr[] = array('NO.'=>$no++,
								'KODE BARANG' 			=> $d['kode_barang'],
								'NAMA PARFURM'			=> $d['nama_barang'],
								'NAMA GUDANG'			=> $d['nama_gudangtujuan'],
								'SATUAN'				=> $d['nama_satuan'],
								'STOCK AWAL'			=> $d['jumlah_stokawal'],
								'PENGIRIMAN HARI INI'	=> $d['jumlah_pengiriman'],
								'RETUR HARI INI'		=> $d['jumlah_retur'],
								'STOK AKHIR'			=> $d['jumlah_terakhir'],
								'HARGA (Rp)'			=> $d['total_pengiriman'],
								'KETERANGAN'			=> $d['keterangan'],
								'STATUS'				=> $d['status_pengiriman']);
				}
			}
		if(count($arr) > 0){
        $data = $arr;
        }else{
        $data = array("0"=>['NO.'					=> "",
							'KODE BARANG' 			=> "",
							'NAMA PARFURM'			=> "",
							'NAMA GUDANG'			=> "",
							'SATUAN'				=> "",
							'STOCK AWAL'			=> "",
							'PENGIRIMAN HARI INI'	=> "",
							'RETUR HARI INI'		=> "",
							'STOK AKHIR'			=> "",
							'HARGA (Rp)'			=> "",
							'KETERANGAN'			=> "",
							'STATUS'				=> ""]);
        }

    	return Excel::download(new LaporanViewExport($data), 'Laporan_Pengiriman_'.$nama_gudang.'_'.$tanggal.'-'.$tanggal2.'.xlsx');
        		break;
        		case '2';
        $d_query_awal = DB::select("SELECT q.*, 
				CASE WHEN SUM( r.stok )< 0 THEN '0' 
				WHEN SUM( r.stok ) IS NULL THEN '0' 
				ELSE SUM( r.stok ) END AS stok, 
				0 AS jumlah_masuk,
				0 AS jumlah_keluar,
				CASE WHEN SUM( r.stok )< 0 THEN '0' 
				WHEN SUM( r.stok ) IS NULL THEN '0' 
				ELSE SUM( r.stok ) END AS jumlah_stokawal,
				0 AS jumlah_pengiriman,
				0 AS jumlah_retur,
				0 AS jumlah_terakhir,
				0 AS harga_pengiriman,
				0 AS total_pengiriman
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
							pe.gudang_tujuan AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							pe.kode_pengiriman as no_nota,
							'0' as unit_masuk,
							'0' as unit_keluar,
							t.unit_keluar as unit_pengiriman,
							'0' as unit_retur,
							pd.harga as harga,
							t.status,
							pe.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						LEFT JOIN pengiriman_detail AS pd ON t.log_stok_id=pd.id_log_stok
						JOIN pengiriman AS pe ON pd.id_inv_pengiriman=pe.id
						LEFT JOIN ref_gudang AS g2 ON pe.gudang_tujuan=g2.id
						$where_pengiriman
				UNION ALL
						SELECT
							t.id_barang,
							b.barang_nama,
							b.barang_kode,											
							t.id_satuan AS satuan_id,
							s.satuan_nama,
							s.satuan_satuan,
							t.id_ref_gudang AS gudang_id,
							g.nama AS gudang_nama,
							pr.id_gudang_outlet AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							pr.kode_retur AS no_nota,
							'0' as unit_masuk,
							'0' as unit_keluar,
							'0' AS unit_pengiriman,
							t.unit_masuk AS unit_retur,
							'0' AS harga,
							t.status,
							pr.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						JOIN pengiriman_retur AS pr ON t.log_stok_id=pr.id_log_stok
						LEFT JOIN ref_gudang AS g2 ON pr.id_gudang_outlet=g2.id
						$where_retur
					) q
					JOIN (
						SELECT  
							q.id_barang,
							q.id_gudang,
							q.id_satuan,
							q.nama_barang,
							sum(q.unit_masuk-q.unit_keluar) as stok
						FROM (                                             
							SELECT
									t.id_barang as id_barang,
									t.id_ref_gudang as id_gudang,
									t.id_satuan as id_satuan,
									tb.barang_nama AS nama_barang,
									t.unit_masuk AS unit_masuk,
									t.unit_keluar AS unit_keluar
							FROM tbl_log_stok AS t
							LEFT JOIN tbl_barang AS tb ON t.id_barang = barang_id
							LEFT JOIN ref_gudang AS rg ON t.id_ref_gudang = rg.id
							LEFT JOIN tbl_satuan AS ts ON t.id_satuan = ts.satuan_id
							$where_stokawal
						) q 
						GROUP BY 
							q.id_barang,
							q.id_gudang, 
							q.id_satuan
						ORDER BY
						q.nama_barang
						) AS r ON q.id_barang=r.id_barang AND q.satuan_id=r.id_satuan AND q.gudang_id=r.id_gudang
						GROUP BY
							q.no_nota,
							q.id_barang,
							q.satuan_id,
							q.gudang_id
						ORDER BY
						q.barang_nama
			");	
        $d_query_mutasi = DB::SELECT("
    			SELECT q.*,
					0 as stok,
					SUM( q.unit_masuk ) AS jumlah_masuk,
					SUM( q.unit_keluar ) AS jumlah_keluar,
					0 AS jumlah_stokawal,
					CASE WHEN SUM( q.unit_pengiriman )<0 THEN '0' 
					WHEN SUM( q.unit_pengiriman ) IS NULL THEN '0' 
					ELSE SUM( q.unit_pengiriman ) END AS jumlah_pengiriman,
					CASE WHEN SUM( q.unit_retur )<0 THEN '0' 
					WHEN SUM( q.unit_retur ) IS NULL THEN '0' 
					ELSE SUM( q.unit_retur ) END AS jumlah_retur,
					0 AS jumlah_terakhir,
					CASE WHEN SUM( q.harga )<0 THEN '0' 
					WHEN SUM( q.harga ) IS NULL THEN '0' 
					ELSE SUM( q.harga ) END AS harga_pengiriman,
					CASE WHEN SUM( q.harga*q.unit_pengiriman )<0 THEN '0' 
					WHEN SUM( q.harga*q.unit_pengiriman ) IS NULL THEN '0' 
					ELSE SUM( q.harga*q.unit_pengiriman ) END AS total_pengiriman
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
							pe.gudang_tujuan AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							pe.kode_pengiriman as no_nota,
							'0' as unit_masuk,
							'0' as unit_keluar,
							t.unit_keluar as unit_pengiriman,
							'0' as unit_retur,
							pd.harga as harga,
							t.status,
							CASE WHEN pe.status_pengiriman = 1 THEN 'Sedang Dikirim'
							WHEN pe.status_pengiriman = 2 THEN 'Terkirim'
							WHEN pe.status_pengiriman = 3 THEN 'Diterima Sebagian'
							WHEN pe.status_pengiriman = 4 THEN 'Dikembalikan'
							ELSE '' END AS status_pengiriman,
							pe.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						LEFT JOIN pengiriman_detail AS pd ON t.log_stok_id=pd.id_log_stok
						JOIN pengiriman AS pe ON pd.id_inv_pengiriman=pe.id
						LEFT JOIN ref_gudang AS g2 ON pe.gudang_tujuan=g2.id
						$where_pengiriman
				UNION ALL
						SELECT
							t.id_barang,
							b.barang_nama,
							b.barang_kode,											
							t.id_satuan AS satuan_id,
							s.satuan_nama,
							s.satuan_satuan,
							t.id_ref_gudang AS gudang_id,
							g.nama AS gudang_nama,
							pr.id_gudang_outlet AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							pr.kode_retur AS no_nota,
							'0' as unit_masuk,
							'0' as unit_keluar,
							'0' AS unit_pengiriman,
							t.unit_masuk AS unit_retur,
							'0' AS harga,
							t.status,
							CASE WHEN pr.status = 1 THEN 'Terkirim'
							WHEN pr.status = 2 THEN 'Diterima'
							WHEN pr.status = 3 THEN 'Diterima Sebagian'
							ELSE '' END AS status_pengiriman,
							pr.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						JOIN pengiriman_retur AS pr ON t.log_stok_id=pr.id_log_stok
						LEFT JOIN ref_gudang AS g2 ON pr.id_gudang_outlet=g2.id
						$where_retur
					) q
						GROUP BY
							q.no_nota,
							q.id_barang,
							q.satuan_id,
							q.gudang_id,
							q.gudangtujuan_id
						ORDER BY
							q.barang_nama 
		");	
        $wheres = array();
	    if(count($d_query_awal) > 0){    	
	    foreach($d_query_awal as $e){
	        		$wheres[] = array('id_barang'		=> $e->id_barang,
	        						'nama_barang'		=> $e->barang_nama,
	        						'kode_barang'		=> $e->barang_kode,
	        						'id_satuan'			=> $e->satuan_id,
	        						'nama_satuan'		=> $e->satuan_satuan,
	        						'id_gudang'			=> $e->gudang_id,
	        						'nama_gudang'		=> $e->gudang_nama,
	        						'id_gudangtujuan'	=> $e->gudangtujuan_id,
	        						'nama_gudangtujuan'	=> $e->gudangtujuan_nama,
	        						'jumlah_pengiriman' => $e->jumlah_pengiriman,
	        						'jumlah_retur'		=> $e->jumlah_retur,
	        						'jumlah_stokawal'	=> $e->jumlah_stokawal,
	        						'total_pengiriman'	=> $e->total_pengiriman,
	        						'status_pengiriman' => '',
	        						'keterangan'		=> $e->keterangan);
	        		}
	    }
	    
	    $new = array();
	    $arr = array();
	    $id_barang = "";
	    if($barang != 0){
	    	$id_barang = $barang;
	    }
	    // dd($id_barang);
	    foreach($wheres as $w){
	    	$arr[] = $w+array("jumlah_terakhir"=>$w['jumlah_stokawal'],"jenis"=>'0');
			$sisa = $w['jumlah_stokawal'];

			$new = array_filter($d_query_mutasi, function ($var) use ($w, $id_barang) {
				if($id_barang != ''){
					return $var->id_barang==$w['id_barang'] && $var->satuan_id==$w['id_satuan'] && $var->gudang_id==$w['id_gudang'];
				}else{
					return true;
				}
			});
			// dd($barang);
			//print_r($new);exit();
			$count = count($new);
			foreach($new as $key => $d){
				$sisa = ($sisa+$d->jumlah_retur)-$d->jumlah_pengiriman;
				$stok = $sisa+$d->jumlah_pengiriman;
				if($key <= $count){
				$arr[] = array('id_barang'			=> $d->id_barang,
	        				   'nama_barang'		=> $d->barang_nama,
	        				   'kode_barang'		=> $d->barang_kode,
	        				   'id_satuan'			=> $d->satuan_id,
	        				   'nama_satuan'		=> $d->satuan_satuan,
	        				   'id_gudang'			=> $d->gudang_id,
	        				   'nama_gudang'		=> $d->gudang_nama,
	        				   'id_gudangtujuan'	=> $d->gudangtujuan_id,
	        				   'nama_gudangtujuan'	=> $d->gudangtujuan_nama,
	        				   'jumlah_pengiriman' 	=> $d->jumlah_pengiriman,
	        				   'jumlah_retur'		=> $d->jumlah_retur,
	        				   'jumlah_stokawal'	=> $stok,
	        				   'total_pengiriman'	=> $d->total_pengiriman,
	        				   'status_pengiriman'	=> $d->status_pengiriman,
	        				   'keterangan'			=> $d->keterangan,
	        				   'jumlah_terakhir'	=> $sisa,
	        				   'jenis'				=> '1',
	        				   'no_nota'			=> $d->no_nota,
	        				   'tanggal'			=> date('d-m-Y',strtotime($d->tanggal)),
	        				   'key'				=> $key);
				}
			}

			
			
	    }

	   	
	    $arr2 = array();
	    foreach($arr as $c){
	    	if($c['jenis'] != 0){
	    	$arr2[$c['key']] = $c;
	    	}
	    }

		
		$no = 1;
		$d_endarr = array();
		if(count($arr2) > 0){
			foreach($arr2 as $d){
				$d_endarr[] = array('NO.'				=> $no++,
								'NO NOTA'				=> $d['no_nota'],
								'TANGGAL'				=> $d['tanggal'],
								'KODE PARFURM' 			=> $d['kode_barang'],
								'NAMA PARFURM'			=> $d['nama_barang'],
								'NAMA GUDANG'			=> $d['nama_gudangtujuan'],
								'SATUAN'				=> $d['nama_satuan'],
								'STOCK AWAL'			=> $d['jumlah_stokawal'],
								'PENGIRIMAN HARI INI'	=> $d['jumlah_pengiriman'],
								'RETUR HARI INI'		=> $d['jumlah_retur'],
								'STOK AKHIR'			=> $d['jumlah_terakhir'],
								'HARGA (Rp)'			=> $d['total_pengiriman'],	
								'KETERANGAN'			=> $d['keterangan'],
								'STATUS'				=> $d['status_pengiriman']);
			}
		}
		if(count($d_endarr) > 0){
        $data = $d_endarr;
        }else{
        $data = array("0"=>['NO.'					=> "",
        					'NO NOTA'				=> "",
							'TANGGAL'				=> "",
							'KODE PARFURM' 			=> "",
							'NAMA PARFURM'			=> "",
							'NAMA GUDANG'			=> "",
							'SATUAN'				=> "",
							'STOCK AWAL'			=> "",
							'PENGIRIMAN HARI INI'	=> "",
							'RETUR HARI INI'		=> "",
							'STOK AKHIR'			=> "",
							'HARGA (Rp)'			=> "",
							'KETERANGAN'			=> "",
							'STATUS'				=> ""]);
        }

    	return Excel::download(new LaporanViewExport($data), 'Laporan_Pengiriman_'.$nama_gudang.'_'.$tanggal.'-'.$tanggal2.'.xlsx');
        		break;
        	default:
        		# code...
        		break;
        }

        
        
    }

    public function hasil($d_gudang,$d_tanggal_awal, $d_tanggal_akhir,$d_kategori,$d_barang=''){
    	ini_set('max_execution_time', '0');
    	ini_set('memory_limit','512M');
    	$gudang = explode(',',$d_gudang);
    	$tanggal = tgl_full($d_tanggal_awal,'99');  
    	$tanggal2 = tgl_full($d_tanggal_akhir,'99'); 
    	$kategori = $d_kategori;
    	$barang = $d_barang;

    	$add_where = " WHERE ";
    	$where_pengiriman[] = " t.status = 'K1' ";
    	$where_retur[] = "t.status = 'K4' ";

    	$where_gudang = "";
    	if(isset($gudang)){
    		if(in_array("all",$gudang)){
    			$where_gudang = "";
    			$nama_gudang = "Semua Gudang Toko";
    		}else{
    			$where_gudang = " g2.id IN ($d_gudang) AND";
		    	$c_nmgudang = DB::table('ref_gudang')->whereIn('id',$gudang)->get();
		    	$d_nmgudang = array();
		    	foreach($c_nmgudang as $d){
		    		$d_nmgudang[] = $d->nama;
		    	}
    			$nama_gudang = join(' dan ', array_filter(array_merge(array(join(', ', array_slice($d_nmgudang, 0, -1))), array_slice($d_nmgudang, -1)), 'strlen'));
    		}

    		if($gudang != ''){
    			$where_stokawal[] = " t.id_ref_gudang IN ('8','18') ";
    			$where_pengiriman[] = " t.id_ref_gudang IN ('8','18') ";
    			$where_retur[] = " t.id_ref_gudang IN ('8','18') ";
    		}
    	}

    	if(isset($tanggal)){
    		if($tanggal != ''){
    			$where_stokawal[] = " t.tanggal < '".$tanggal."'";
    			$where_pengiriman[] = " t.tanggal >= '".$tanggal."'";
    			$where_retur[] = " t.tanggal >= '".$tanggal."'";
    		}
    	}

    	if(isset($tanggal2)){
    		if($tanggal2 !=''){
    			$where_pengiriman[] = " t.tanggal <= '".$tanggal2."'";
    			$where_retur[] = " t.tanggal <= '".$tanggal2."'";
    		}
    	}

    	if(isset($barang)){
    		if($barang != 0){
    			$where_stokawal[] = " t.id_barang = '".$barang."'";
    			$where_pengiriman[] = " t.id_barang = '".$barang."'";
    			$where_retur[] = " t.id_barang = '".$barang."'";
    		}
    	}

    	$where_stokawal = $add_where.implode(" AND ", $where_stokawal);
    	$where_pengiriman = $add_where.implode(" AND ", $where_pengiriman);
    	$where_retur = $add_where.implode(" AND ", $where_retur);

        switch ($kategori) {
        	case '1':
        $d_query = DB::select("SELECT q.*, 
				CASE WHEN SUM( r.stok )< 0 THEN '0' 
				WHEN SUM( r.stok ) IS NULL THEN '0' 
				ELSE SUM( r.stok ) END AS stok, 
				0 AS jumlah_masuk,
				0 AS jumlah_keluar,
				CASE WHEN SUM( r.stok )< 0 THEN '0' 
				WHEN SUM( r.stok ) IS NULL THEN '0' 
				ELSE SUM( r.stok ) END AS jumlah_stokawal,
				0 AS jumlah_pengiriman,
				0 AS jumlah_retur,
				0 AS jumlah_terakhir,
				0 AS harga_pengiriman,
				0 AS total_pengiriman
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
							pe.gudang_tujuan AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							'0' as unit_masuk,
							'0' as unit_keluar,
							t.unit_keluar as unit_pengiriman,
							'0' as unit_retur,
							pd.harga as harga,
							t.status,
							pe.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						LEFT JOIN pengiriman_detail AS pd ON t.log_stok_id=pd.id_log_stok
						JOIN pengiriman AS pe ON pd.id_inv_pengiriman=pe.id
						LEFT JOIN ref_gudang AS g2 ON pe.gudang_tujuan=g2.id
						$where_pengiriman
				UNION ALL
						SELECT
							t.id_barang,
							b.barang_nama,
							b.barang_kode,											
							t.id_satuan AS satuan_id,
							s.satuan_nama,
							s.satuan_satuan,
							t.id_ref_gudang AS gudang_id,
							g.nama AS gudang_nama,
							pr.id_gudang_outlet AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							'0' as unit_masuk,
							'0' as unit_keluar,
							'0' AS unit_pengiriman,
							t.unit_masuk AS unit_retur,
							'0' AS harga,
							t.status,
							pr.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						JOIN pengiriman_retur AS pr ON t.log_stok_id=pr.id_log_stok
						LEFT JOIN ref_gudang AS g2 ON pr.id_gudang_outlet=g2.id
						$where_retur
					) q
					JOIN (
						SELECT  
							q.id_barang,
							q.id_gudang,
							q.id_satuan,
							q.nama_barang,
							sum(q.unit_masuk-q.unit_keluar) as stok
						FROM (                                             
							SELECT
									t.id_barang as id_barang,
									t.id_ref_gudang as id_gudang,
									t.id_satuan as id_satuan,
									tb.barang_nama AS nama_barang,
									t.unit_masuk AS unit_masuk,
									t.unit_keluar AS unit_keluar
							FROM tbl_log_stok AS t
							LEFT JOIN tbl_barang AS tb ON t.id_barang = barang_id
							LEFT JOIN ref_gudang AS rg ON t.id_ref_gudang = rg.id
							LEFT JOIN tbl_satuan AS ts ON t.id_satuan = ts.satuan_id
							$where_stokawal
						) q 
						GROUP BY 
							q.id_barang,
							q.id_gudang, 
							q.id_satuan
						ORDER BY
						q.nama_barang
						) AS r ON q.id_barang=r.id_barang AND q.satuan_id=r.id_satuan AND q.gudang_id=r.id_gudang
						GROUP BY
							q.id_barang,
							q.satuan_id,
							q.gudang_id
						ORDER BY
						q.barang_nama
			");	
        $wheres = array();
	    if(count($d_query) > 0){    	
	    foreach($d_query as $e){
	        		$wheres[] = array('id_barang'		=> $e->id_barang,
	        						'nama_barang'		=> $e->barang_nama,
	        						'kode_barang'		=> $e->barang_kode,
	        						'id_satuan'			=> $e->satuan_id,
	        						'nama_satuan'		=> $e->satuan_satuan,
	        						'id_gudang'			=> $e->gudang_id,
	        						'nama_gudang'		=> $e->gudang_nama,
	        						'id_gudangtujuan'	=> $e->gudangtujuan_id,
	        						'nama_gudangtujuan'	=> $e->gudangtujuan_nama,
	        						'jumlah_pengiriman' => $e->jumlah_pengiriman,
	        						'jumlah_retur'		=> $e->jumlah_retur,
	        						'jumlah_stokawal'	=> $e->jumlah_stokawal,
	        						'total_pengiriman'	=> $e->total_pengiriman,
	        						'status_pengiriman' => '',
	        						'keterangan'		=> $e->keterangan);
	        		}
	    }

	    $where_arr = array();
	    $where_arr2 = array();
	    foreach($wheres as $w){
	    	$wheres_stokawal = "WHERE t.id_ref_gudang = '".$w['id_gudang']."' AND ".$where_gudang." t.id_barang = '".$w['id_barang']."' AND t.id_satuan = '".$w['id_satuan']."' AND  t.tanggal < '".$tanggal."'";
	    	$wheres_pengiriman = "WHERE t.id_ref_gudang = '".$w['id_gudang']."' AND ".$where_gudang." t.id_barang = '".$w['id_barang']."' AND t.id_satuan = '".$w['id_satuan']."' AND  t.tanggal >= '".$tanggal."' AND t.tanggal <= '".$tanggal2."' AND t.status='K1'";
		    $wheres_retur = "WHERE t.id_ref_gudang = '".$w['id_gudang']."' AND ".$where_gudang." t.id_barang = '".$w['id_barang']."' AND t.id_satuan = '".$w['id_satuan']."' AND t.tanggal >= '".$tanggal."' AND t.tanggal <= '".$tanggal2."' AND t.status='K4'";
			$d_query2 = DB::SELECT("SELECT q.*,
					0 as stok,
					SUM( q.unit_masuk ) AS jumlah_masuk,
					SUM( q.unit_keluar ) AS jumlah_keluar,
					0 AS jumlah_stokawal,
					CASE WHEN SUM( q.unit_pengiriman )<0 THEN '0' 
					WHEN SUM( q.unit_pengiriman ) IS NULL THEN '0' 
					ELSE SUM( q.unit_pengiriman ) END AS jumlah_pengiriman,
					CASE WHEN SUM( q.unit_retur )<0 THEN '0' 
					WHEN SUM( q.unit_retur ) IS NULL THEN '0' 
					ELSE SUM( q.unit_retur ) END AS jumlah_retur,
					0 AS jumlah_terakhir,
					CASE WHEN SUM( q.harga )<0 THEN '0' 
					WHEN SUM( q.harga ) IS NULL THEN '0' 
					ELSE SUM( q.harga ) END AS harga_pengiriman,
					CASE WHEN SUM( q.harga*q.unit_pengiriman )<0 THEN '0' 
					WHEN SUM( q.harga*q.unit_pengiriman ) IS NULL THEN '0' 
					ELSE SUM( q.harga*q.unit_pengiriman ) END AS total_pengiriman
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
							pe.gudang_tujuan AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							'0' as unit_masuk,
							'0' as unit_keluar,
							t.unit_keluar as unit_pengiriman,
							'0' as unit_retur,
							pd.harga as harga,
							t.status,
							CASE WHEN pe.status_pengiriman = 1 THEN 'Sedang Dikirim'
							WHEN pe.status_pengiriman = 2 THEN 'Terkirim'
							WHEN pe.status_pengiriman = 3 THEN 'Diterima Sebagian'
							WHEN pe.status_pengiriman = 4 THEN 'Dikembalikan'
							ELSE '' END AS status_pengiriman,
							pe.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						LEFT JOIN pengiriman_detail AS pd ON t.log_stok_id=pd.id_log_stok
						JOIN pengiriman AS pe ON pd.id_inv_pengiriman=pe.id
						LEFT JOIN ref_gudang AS g2 ON pe.gudang_tujuan=g2.id
						$wheres_pengiriman
				UNION ALL
						SELECT
							t.id_barang,
							b.barang_nama,
							b.barang_kode,											
							t.id_satuan AS satuan_id,
							s.satuan_nama,
							s.satuan_satuan,
							t.id_ref_gudang AS gudang_id,
							g.nama AS gudang_nama,
							pr.id_gudang_outlet AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							'0' as unit_masuk,
							'0' as unit_keluar,
							'0' AS unit_pengiriman,
							t.unit_masuk AS unit_retur,
							'0' AS harga,
							t.status,
							CASE WHEN pr.status = 1 THEN 'Terkirim'
							WHEN pr.status = 2 THEN 'Diterima'
							WHEN pr.status = 3 THEN 'Diterima Sebagian'
							ELSE '' END AS status_pengiriman,
							pr.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						JOIN pengiriman_retur AS pr ON t.log_stok_id=pr.id_log_stok
						LEFT JOIN ref_gudang AS g2 ON pr.id_gudang_outlet=g2.id
						$wheres_retur
					) q
						GROUP BY
							q.id_barang,
							q.satuan_id,
							q.gudang_id,
							q.gudangtujuan_id
						ORDER BY
							q.barang_nama 
				");		
			$where_arr[] = $w+array("jumlah_terakhir"=>$w['jumlah_stokawal'],"jenis"=>'0');
			$sisa = $w['jumlah_stokawal'];
			foreach ($d_query2 as $key => $e) {
				# code...
				$sisa = ($sisa+$e->jumlah_retur)-$e->jumlah_pengiriman;
				$stok = $sisa+$e->jumlah_pengiriman;
				$where_arr[] = array('id_barang'		=> $e->id_barang,
	        						'nama_barang'		=> $e->barang_nama,
	        						'kode_barang'		=> $e->barang_kode,
	        						'id_satuan'			=> $e->satuan_id,
	        						'nama_satuan'		=> $e->satuan_satuan,
	        						'id_gudang'			=> $e->gudang_id,
	        						'nama_gudang'		=> $e->gudang_nama,
	        						'id_gudangtujuan'	=> $e->gudangtujuan_id,
	        						'nama_gudangtujuan'	=> $e->gudangtujuan_nama,
	        						'jumlah_pengiriman' => $e->jumlah_pengiriman,
	        						'jumlah_retur'		=> $e->jumlah_retur,
	        						'jumlah_stokawal'	=> $stok,
	        						'total_pengiriman'	=> $e->total_pengiriman,
	        						'status_pengiriman'	=> $e->status_pengiriman,
	        						'keterangan'		=> $e->keterangan,
	        						'jumlah_terakhir'	=> $sisa,
	        						'jenis'				=> '1');
	        		
			}

			
	    	

	    }

        $no = 1;
		$arr = array();
			foreach ($where_arr as $key => $d){
				if($d['jenis'] != 0){
				$arr[] = array('NO.'=>$no++,
								'KODE BARANG' 			=> $d['kode_barang'],
								'NAMA PARFURM'			=> $d['nama_barang'],
								'NAMA GUDANG'			=> $d['nama_gudangtujuan'],
								'STOCK AWAL'			=> format_angka($d['jumlah_stokawal']).' '.$d['nama_satuan'],
								'PENGIRIMAN HARI INI'	=> format_angka($d['jumlah_pengiriman']).' '.$d['nama_satuan'],
								'RETUR HARI INI'		=> format_angka($d['jumlah_retur']).' '.$d['nama_satuan'],
								'STOK AKHIR'			=> format_angka($d['jumlah_terakhir']).' '.$d['nama_satuan'],
								'HARGA'					=> 'Rp. '.format_angka($d['total_pengiriman']),
								'KET.'					=> $d['keterangan'],
								'STATUS'				=> $d['status_pengiriman']);
				}
			}
		if(count($arr) > 0){
        $data = $arr;
        }else{
        $data = array("0"=>['NO.'					=> "",
							'KODE BARANG' 			=> "",
							'NAMA PARFURM'			=> "",
							'NAMA GUDANG'			=> "",
							'STOCK AWAL'			=> "",
							'PENGIRIMAN HARI INI'	=> "",
							'RETUR HARI INI'		=> "",
							'STOK AKHIR'			=> "",
							'HARGA'					=> "",
							'KET.'					=> "",
							'STATUS'				=> ""]);
        }

    	 return view('admin.laporan.hasil_laporan',compact('data'));
        		break;
        		case '2':
        
        $d_query_awal = DB::select("SELECT q.*, 
				CASE WHEN SUM( r.stok )< 0 THEN '0' 
				WHEN SUM( r.stok ) IS NULL THEN '0' 
				ELSE SUM( r.stok ) END AS stok, 
				0 AS jumlah_masuk,
				0 AS jumlah_keluar,
				CASE WHEN SUM( r.stok )< 0 THEN '0' 
				WHEN SUM( r.stok ) IS NULL THEN '0' 
				ELSE SUM( r.stok ) END AS jumlah_stokawal,
				0 AS jumlah_pengiriman,
				0 AS jumlah_retur,
				0 AS jumlah_terakhir,
				0 AS harga_pengiriman,
				0 AS total_pengiriman
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
							pe.gudang_tujuan AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							pe.kode_pengiriman as no_nota,
							'0' as unit_masuk,
							'0' as unit_keluar,
							t.unit_keluar as unit_pengiriman,
							'0' as unit_retur,
							pd.harga as harga,
							t.status,
							pe.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						LEFT JOIN pengiriman_detail AS pd ON t.log_stok_id=pd.id_log_stok
						JOIN pengiriman AS pe ON pd.id_inv_pengiriman=pe.id
						LEFT JOIN ref_gudang AS g2 ON pe.gudang_tujuan=g2.id
						$where_pengiriman
				UNION ALL
						SELECT
							t.id_barang,
							b.barang_nama,
							b.barang_kode,											
							t.id_satuan AS satuan_id,
							s.satuan_nama,
							s.satuan_satuan,
							t.id_ref_gudang AS gudang_id,
							g.nama AS gudang_nama,
							pr.id_gudang_outlet AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							pr.kode_retur AS no_nota,
							'0' as unit_masuk,
							'0' as unit_keluar,
							'0' AS unit_pengiriman,
							t.unit_masuk AS unit_retur,
							'0' AS harga,
							t.status,
							pr.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						JOIN pengiriman_retur AS pr ON t.log_stok_id=pr.id_log_stok
						LEFT JOIN ref_gudang AS g2 ON pr.id_gudang_outlet=g2.id
						$where_retur
					) q
					JOIN (
						SELECT  
							q.id_barang,
							q.id_gudang,
							q.id_satuan,
							q.nama_barang,
							sum(q.unit_masuk-q.unit_keluar) as stok
						FROM (                                             
							SELECT
									t.id_barang as id_barang,
									t.id_ref_gudang as id_gudang,
									t.id_satuan as id_satuan,
									tb.barang_nama AS nama_barang,
									t.unit_masuk AS unit_masuk,
									t.unit_keluar AS unit_keluar
							FROM tbl_log_stok AS t
							LEFT JOIN tbl_barang AS tb ON t.id_barang = barang_id
							LEFT JOIN ref_gudang AS rg ON t.id_ref_gudang = rg.id
							LEFT JOIN tbl_satuan AS ts ON t.id_satuan = ts.satuan_id
							$where_stokawal
						) q 
						GROUP BY 
							q.id_barang,
							q.id_gudang, 
							q.id_satuan
						ORDER BY
						q.nama_barang
						) AS r ON q.id_barang=r.id_barang AND q.satuan_id=r.id_satuan AND q.gudang_id=r.id_gudang
						GROUP BY
							q.no_nota,
							q.id_barang,
							q.satuan_id,
							q.gudang_id
						ORDER BY
						q.barang_nama
		");
		
		$d_query_mutasi = DB::SELECT("SELECT q.*,
					0 as stok,
					SUM( q.unit_masuk ) AS jumlah_masuk,
					SUM( q.unit_keluar ) AS jumlah_keluar,
					0 AS jumlah_stokawal,
					CASE WHEN SUM( q.unit_pengiriman )<0 THEN '0' 
					WHEN SUM( q.unit_pengiriman ) IS NULL THEN '0' 
					ELSE SUM( q.unit_pengiriman ) END AS jumlah_pengiriman,
					CASE WHEN SUM( q.unit_retur )<0 THEN '0' 
					WHEN SUM( q.unit_retur ) IS NULL THEN '0' 
					ELSE SUM( q.unit_retur ) END AS jumlah_retur,
					0 AS jumlah_terakhir,
					CASE WHEN SUM( q.harga )<0 THEN '0' 
					WHEN SUM( q.harga ) IS NULL THEN '0' 
					ELSE SUM( q.harga ) END AS harga_pengiriman,
					CASE WHEN SUM( q.harga*q.unit_pengiriman )<0 THEN '0' 
					WHEN SUM( q.harga*q.unit_pengiriman ) IS NULL THEN '0' 
					ELSE SUM( q.harga*q.unit_pengiriman ) END AS total_pengiriman
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
							pe.gudang_tujuan AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							pe.kode_pengiriman as no_nota,
							'0' as unit_masuk,
							'0' as unit_keluar,
							t.unit_keluar as unit_pengiriman,
							'0' as unit_retur,
							pd.harga as harga,
							t.status,
							CASE WHEN pe.status_pengiriman = 1 THEN 'Sedang Dikirim'
							WHEN pe.status_pengiriman = 2 THEN 'Terkirim'
							WHEN pe.status_pengiriman = 3 THEN 'Diterima Sebagian'
							WHEN pe.status_pengiriman = 4 THEN 'Dikembalikan'
							ELSE '' END AS status_pengiriman,
							pe.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						LEFT JOIN pengiriman_detail AS pd ON t.log_stok_id=pd.id_log_stok
						JOIN pengiriman AS pe ON pd.id_inv_pengiriman=pe.id
						LEFT JOIN ref_gudang AS g2 ON pe.gudang_tujuan=g2.id
						$where_pengiriman
				UNION ALL
						SELECT
							t.id_barang,
							b.barang_nama,
							b.barang_kode,											
							t.id_satuan AS satuan_id,
							s.satuan_nama,
							s.satuan_satuan,
							t.id_ref_gudang AS gudang_id,
							g.nama AS gudang_nama,
							pr.id_gudang_outlet AS gudangtujuan_id,
							g2.nama AS gudangtujuan_nama,	
							t.tanggal,
							pr.kode_retur AS no_nota,
							'0' as unit_masuk,
							'0' as unit_keluar,
							'0' AS unit_pengiriman,
							t.unit_masuk AS unit_retur,
							'0' AS harga,
							t.status,
							CASE WHEN pr.status = 1 THEN 'Terkirim'
							WHEN pr.status = 2 THEN 'Diterima'
							WHEN pr.status = 3 THEN 'Diterima Sebagian'
							ELSE '' END AS status_pengiriman,
							pr.keterangan
						FROM
							tbl_log_stok AS t										
						JOIN tbl_barang AS b ON t.id_barang = b.barang_id
						JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
						LEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
						JOIN pengiriman_retur AS pr ON t.log_stok_id=pr.id_log_stok
						LEFT JOIN ref_gudang AS g2 ON pr.id_gudang_outlet=g2.id
						$where_retur
					) q
						GROUP BY
							q.no_nota,
							q.id_barang,
							q.satuan_id,
							q.gudang_id,
							q.gudangtujuan_id
						ORDER BY
							q.barang_nama 
		");
        
        $wheres = array();
	    if(count($d_query_awal) > 0){    	
	    foreach($d_query_awal as $e){
	        		$wheres[] = array('id_barang'		=> $e->id_barang,
	        						'nama_barang'		=> $e->barang_nama,
	        						'kode_barang'		=> $e->barang_kode,
	        						'id_satuan'			=> $e->satuan_id,
	        						'nama_satuan'		=> $e->satuan_satuan,
	        						'id_gudang'			=> $e->gudang_id,
	        						'nama_gudang'		=> $e->gudang_nama,
	        						'id_gudangtujuan'	=> $e->gudangtujuan_id,
	        						'nama_gudangtujuan'	=> $e->gudangtujuan_nama,
	        						'jumlah_pengiriman' => $e->jumlah_pengiriman,
	        						'jumlah_retur'		=> $e->jumlah_retur,
	        						'jumlah_stokawal'	=> $e->jumlah_stokawal,
	        						'total_pengiriman'	=> $e->total_pengiriman,
	        						'status_pengiriman' => '',
	        						'keterangan'		=> $e->keterangan);
	        		}
	    }

	    $new = array();
	    $arr = array();
	    $id_barang = "";
	    if($barang != 0){
	    	$id_barang = $barang;
	    }

	    foreach($wheres as $w){
	    	$arr[] = $w+array("jumlah_terakhir"=>$w['jumlah_stokawal'],"jenis"=>'0');
			$sisa = $w['jumlah_stokawal'];

			$new = array_filter($d_query_mutasi, function ($var) use ($w, $id_barang) {
				if($id_barang != ''){
					return $var->id_barang==$w['id_barang'] && $var->satuan_id==$w['id_satuan'] && $var->gudang_id==$w['id_gudang'];
				}else{
					return true;
				}
			});

			$count = count($new);
			foreach($new as $key => $d){
				$sisa = ($sisa+$d->jumlah_retur)-$d->jumlah_pengiriman;
				$stok = $sisa+$d->jumlah_pengiriman;
				if($key <= $count){
				$arr[] = array('id_barang'			=> $d->id_barang,
	        				   'nama_barang'		=> $d->barang_nama,
	        				   'kode_barang'		=> $d->barang_kode,
	        				   'id_satuan'			=> $d->satuan_id,
	        				   'nama_satuan'		=> $d->satuan_satuan,
	        				   'id_gudang'			=> $d->gudang_id,
	        				   'nama_gudang'		=> $d->gudang_nama,
	        				   'id_gudangtujuan'	=> $d->gudangtujuan_id,
	        				   'nama_gudangtujuan'	=> $d->gudangtujuan_nama,
	        				   'jumlah_pengiriman' 	=> $d->jumlah_pengiriman,
	        				   'jumlah_retur'		=> $d->jumlah_retur,
	        				   'jumlah_stokawal'	=> $stok,
	        				   'total_pengiriman'	=> $d->total_pengiriman,
	        				   'status_pengiriman'	=> $d->status_pengiriman,
	        				   'keterangan'			=> $d->keterangan,
	        				   'jumlah_terakhir'	=> $sisa,
	        				   'jenis'				=> '1',
	        				   'no_nota'			=> $d->no_nota,
	        				   'tanggal'			=> date('d-m-Y',strtotime($d->tanggal)),
	        				   'key'				=> $key);
				}
			}

			
			
	    }

	   	
	    $arr2 = array();
	    foreach($arr as $c){
	    	if($c['jenis'] != 0){
	    	$arr2[$c['key']] = $c;
	    	}
	    }

        $no = 1;
		$d_arr = array();
			foreach ($arr2 as $key => $d){
				$d_arr[] = array(
								'no_nota'				=> $d["no_nota"],
								'tanggal'				=> $d["tanggal"],
								'kode_barang' 			=> $d['kode_barang'],
								'nama_barang'			=> $d['nama_barang'],
								'nama_gudangtujuan'		=> $d['nama_gudangtujuan'],
								'jumlah_stokawal'		=> format_angka($d['jumlah_stokawal']).' '.$d['nama_satuan'],
								'jumlah_pengiriman'		=> format_angka($d['jumlah_pengiriman']).' '.$d['nama_satuan'],
								'jumlah_retur'			=> format_angka($d['jumlah_retur']).' '.$d['nama_satuan'],
								'jumlah_terakhir'		=> format_angka($d['jumlah_terakhir']).' '.$d['nama_satuan'],
								'total_pengiriman'		=> 'Rp. '.format_angka($d['total_pengiriman']),
								'keterangan'			=> $d['keterangan'],
								'status_pengiriman'		=> $d['status_pengiriman']);
				}
			
		// dd($d_arr);
		$d_endarr = array();
		foreach($d_arr as $key => $d){			
			$d_endarr[] =  array('NO.'=>$no++,
								'NO NOTA'				=> $d["no_nota"],
								'TANGGAL'				=> $d["tanggal"],
								'KODE BARANG' 			=> $d['kode_barang'],
								'NAMA PARFURM'			=> $d['nama_barang'],
								'NAMA GUDANG'			=> $d['nama_gudangtujuan'],
								'STOCK AWAL'			=> $d['jumlah_stokawal'],
								'PENGIRIMAN HARI INI'	=> $d['jumlah_pengiriman'],
								'RETUR HARI INI'		=> $d['jumlah_retur'],
								'STOK AKHIR'			=> $d['jumlah_terakhir'],
								'HARGA'					=> $d['total_pengiriman'],
								'KET.'					=> $d['keterangan'],
								'STATUS'				=> $d['status_pengiriman']);
		}
		if(count($d_endarr) > 0){
        $data = $d_endarr;
        }else{
        $data = array("0"=>['NO.'					=> "",
        					'NO NOTA'				=> "",
							'TANGGAL'				=> "",
							'KODE BARANG' 			=> "",
							'NAMA PARFURM'			=> "",
							'NAMA GUDANG'			=> "",
							'STOCK AWAL'			=> "",
							'PENGIRIMAN HARI INI'	=> "",
							'RETUR HARI INI'		=> "",
							'STOK AKHIR'			=> "",
							'HARGA'					=> "",
							'KET.'					=> "",
							'STATUS'				=> ""]);
        }
        // dd($data);
    	 return view('admin.laporan.hasil_laporan',compact('data'));
        		break;
        	
        	default:
        		# code...
        		break;
        }

        
        
    }

}
