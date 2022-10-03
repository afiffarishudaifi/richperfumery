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

class LaporanPembelianController extends Controller
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
	    $data['group']	= Auth::user()->group_id;
    	return view('admin.laporan.index_laporan_pembelian')->with('data',$data);
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

    public function cetaklaporan_pembelian(Request $request){
    	require(public_path('fpdf1813/Mc_table.php'));
    	$gudang = $request->get('gudang');
    	$tanggal = tgl_full($request->get('tanggalAwal'),'99');
    	$tanggal2 = tgl_full($request->get('tanggalAkhir'),'99'); 
    	$kategori = $request->get('kategori');   	
        $d_gudang = DB::table('ref_gudang')->where('id',$gudang)->select(DB::Raw('id as id_gudang,nama as nama_gudang'))->first();
        trigger_log(NULL, "Cetak Laporan Pembelian PDF", 7);
    	switch ($kategori) {
			case '1':
			if($gudang != "" && $tanggal != ""){
	        $where_stokawal = "WHERE t.id_ref_gudang = '".$gudang."' AND t.tanggal < '".$tanggal."'";
	        $where_pembelian = "WHERE t.id_ref_gudang = '".$gudang."' AND t.tanggal >= '".$tanggal."' AND t.tanggal <= '".$tanggal2."' AND t.status='P1'";
	        $where_retur = "WHERE t.id_ref_gudang = '".$gudang."' AND t.tanggal >= '".$tanggal."' AND t.tanggal <= '".$tanggal2."' AND t.status='P3'";
	        }elseif($gudang == "" && $tanggal == ""){
	        	$where_stokawal = "WHERE t.id_ref_gudang = '".$gudang."'";
	        	$where_pembelian = "WHERE t.id_ref_gudang = '".$gudang."' AND t.status='P1'";
	        	$where_retur = "WHERE t.id_ref_gudang = '".$gudang."' AND t.status='P3'";
	        }else{
	        	$where_stokawal = "";
	        	$where_pembelian = "WHERE t.status='P1'";
	        	$where_retur = "WHERE t.status='P3'";
	        }
			$data['data']	= DB::select("
						SELECT q.*, 
							SUM( q.unit_masuk ) AS jumlah_masuk,
							SUM( q.unit_keluar ) AS jumlah_keluar,
							CASE WHEN SUM( r.jumlah_stokawal )<0 THEN '0' 
							WHEN SUM( r.jumlah_stokawal ) IS NULL THEN '0' 
							ELSE SUM( r.jumlah_stokawal ) END AS jumlah_stokawal,
							CASE WHEN SUM( q.unit_pembelian )<0 THEN '0' 
							WHEN SUM( q.unit_pembelian ) IS NULL THEN '0' 
							ELSE SUM( q.unit_pembelian ) END AS jumlah_pembelian,
							CASE WHEN SUM( q.unit_retur )<0 THEN '0' 
							WHEN SUM( q.unit_retur ) IS NULL THEN '0' 
							ELSE SUM( q.unit_retur ) END AS jumlah_retur,
							CASE WHEN SUM( (r.jumlah_stokawal) + (r.jumlah_stokawal))<0 THEN '0'
							WHEN SUM( (r.jumlah_stokawal) + (q.unit_pembelian-q.unit_retur)) IS NULL THEN '0'
							ELSE SUM( (r.jumlah_stokawal) + (q.unit_pembelian-q.unit_retur)) 
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
											'0' as unit_masuk,
											'0' as unit_keluar,
											t.unit_masuk as unit_pembelian,
											'0' as unit_retur,
											t.status
										FROM
											tbl_log_stok AS t										
										JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
										$where_pembelian
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
											t.tanggal,
											'0' as unit_masuk,
											'0' as unit_keluar,
											'0' AS unit_pembelian,
											t.unit_keluar AS unit_retur,
											t.status
										FROM
											tbl_log_stok AS t										
										JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
										$where_retur
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
	    $data['gudang']	 = $d_gudang->nama_gudang;
	    // print_r($data);exit();
	    $html = view('admin.laporan.cetak_laporan_pembelian')->with('data',$data);
      	return response($html)->header('Content-Type', 'application/pdf');
			break;
			case '2':
		if($gudang != "" && $tanggal != ""){
    		$where = "WHERE tp.id_gudang = '".$gudang."' AND tp.tanggal_faktur >= '".$tanggal."' AND tp.tanggal_faktur <= '".$tanggal2."'";
	    }elseif($gudang != "" && $tanggal == ""){
	        $where = "WHERE tp.id_gudang = '".$gudang."'";
	    }else{
	        $where = "";
	    }
		$data['data']	= DB::select("SELECT tp.id_pembelian, tp.tanggal_faktur as tanggal, tp.no_faktur, 
        	tsu.supplier_nama AS nama_supplier, tb.barang_nama AS nama_barang, tb.barang_kode AS kode_barang, tpd.jumlah, 
        	ts.satuan_satuan AS nama_satuan, tpd.harga, tpd.total
			FROM tbl_pembelian_detail AS tpd
			JOIN tbl_pembelian AS tp ON tpd.id_pembelian=tp.id_pembelian
			JOIN tbl_barang AS tb ON tpd.id_barang=tb.barang_id
			JOIN tbl_satuan AS ts ON tpd.id_satuan=ts.satuan_id
			JOIN ref_gudang AS rg ON tp.id_gudang=rg.id
			LEFT JOIN tbl_supplier AS tsu ON tp.id_supplier=tsu.supplier_id
			$where ");

	    $data['tanggal_awal'] = tgl_full($request->tanggalAwal,'');
	    $data['tanggal_akhir'] = tgl_full($request->tanggalAkhir,'');
	    $data['hari']    = tgl_full($request->tanggal,'hari');
	    $data['kasir']   = Auth::user()->name;
	    $data['gudang']	 = $d_gudang->nama_gudang;
	    // print_r($data);exit();
	    $html = view('admin.laporan.cetak_laporan_pembelian_pernota2')->with('data',$data);
      	return response($html)->header('Content-Type', 'application/pdf');
			break;
			case '3':
		$add_where = "WHERE ";
		$count = 0;
		if(isset($gudang)){
			$count += 1;
			$where[] = " tp.id_gudang = '".$gudang."'";
		}
		if(isset($tanggal)){
			$count += 1;
			$where[] = " tp.tanggal_faktur >= '".$tanggal."' ";
		}
		if(isset($tanggal2)){
			$count += 1;
			$where[] = " tp.tanggal_faktur <= '".$tanggal2."' ";
		}
		if(isset($barang)){
			if($barang != 0){
				$count += 0;
				$where[] = " tpd.id_barang = '".$barang."'";
			}
		}

		if($count > 0){
			$where = $add_where.implode(" AND ",$where);
		}

		$data['data']	= DB::select("SELECT tp.id_pembelian, tp.tanggal_faktur as tanggal, tp.no_faktur, 
        	tsu.supplier_nama AS nama_supplier, tb.barang_nama AS nama_barang, tb.barang_kode AS kode_barang, tpd.jumlah, 
        	ts.satuan_satuan AS nama_satuan, tpd.harga, tpd.total
			FROM tbl_pembelian_detail AS tpd
			JOIN tbl_pembelian AS tp ON tpd.id_pembelian=tp.id_pembelian
			JOIN tbl_barang AS tb ON tpd.id_barang=tb.barang_id
			JOIN tbl_satuan AS ts ON tpd.id_satuan=ts.satuan_id
			JOIN ref_gudang AS rg ON tp.id_gudang=rg.id
			LEFT JOIN tbl_supplier AS tsu ON tp.id_supplier=tsu.supplier_id
			$where ");

	    $data['tanggal_awal'] = tgl_full($request->tanggalAwal,'');
	    $data['tanggal_akhir'] = tgl_full($request->tanggalAkhir,'');
	    $data['hari']    = tgl_full($request->tanggal,'hari');
	    $data['kasir']   = Auth::user()->name;
	    $data['gudang']	 = $d_gudang->nama_gudang;
	    $html = view('admin.laporan.cetak_laporan_pembelian_pernota2')->with('data',$data);
	    return response($html)->header('Content-Type', 'application/pdf');
			break;
			default:
				# code...
			break;
		}
    }

    public function excel($d_gudang,$d_tanggalAwal,$d_tanggalAkhir,$d_kategori,$d_barang=''){
    	$gudang = $d_gudang;
    	$tanggal = tgl_full($d_tanggalAwal,'99');
    	$tanggal2 = tgl_full($d_tanggalAkhir,'99');  
    	$kategori = $d_kategori;
    	$barang = $d_barang;
        trigger_log(NULL, "Cetak Laporan Pengiriman Excel", 7);
    	switch ($kategori) {
    		case '1':
    			if($gudang != "" && $tanggal != ""){
        $where_stokawal = "WHERE t.id_ref_gudang = '".$gudang."' AND t.tanggal < '".$tanggal."'";
        $where_pembelian = "WHERE t.id_ref_gudang = '".$gudang."' AND t.tanggal >= '".$tanggal."' AND t.tanggal <= '".$tanggal2."' AND t.status='P1'";
        $where_retur = "WHERE t.id_ref_gudang = '".$gudang."' AND t.tanggal >= '".$tanggal."' AND t.tanggal <= '".$tanggal2."' AND t.status='P3'";
        }elseif($gudang == "" && $tanggal == ""){
        	$where_stokawal = "WHERE t.id_ref_gudang = '".$gudang."'";
        	$where_pembelian = "WHERE t.id_ref_gudang = '".$gudang."' AND t.status='P1'";
        	$where_retur = "WHERE t.id_ref_gudang = '".$gudang."' AND t.status='P3'";
        }else{
        	$where_stokawal = "";
        	$where_pembelian = "WHERE t.status='P1'";
        	$where_retur = "WHERE t.status='P3'";
        }

        $d_data = DB::select("SELECT q.*, 
							SUM( q.unit_masuk ) AS jumlah_masuk,
							SUM( q.unit_keluar ) AS jumlah_keluar,
							CASE WHEN SUM( r.jumlah_stokawal )<0 THEN '0' 
							WHEN SUM( r.jumlah_stokawal ) IS NULL THEN '0' 
							ELSE SUM( r.jumlah_stokawal ) END AS jumlah_stokawal,
							CASE WHEN SUM( q.unit_pembelian )<0 THEN '0' 
							WHEN SUM( q.unit_pembelian ) IS NULL THEN '0' 
							ELSE SUM( q.unit_pembelian ) END AS jumlah_pembelian,
							CASE WHEN SUM( q.unit_retur )<0 THEN '0' 
							WHEN SUM( q.unit_retur ) IS NULL THEN '0' 
							ELSE SUM( q.unit_retur ) END AS jumlah_retur,
							CASE WHEN SUM( (r.jumlah_stokawal) + (r.jumlah_stokawal))<0 THEN '0'
							WHEN SUM( (r.jumlah_stokawal) + (q.unit_pembelian-q.unit_retur)) IS NULL THEN '0'
							ELSE SUM( (r.jumlah_stokawal) + (q.unit_pembelian-q.unit_retur)) 
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
											'0' as unit_masuk,
											'0' as unit_keluar,
											t.unit_masuk as unit_pembelian,
											'0' as unit_retur,
											t.status
										FROM
											tbl_log_stok AS t										
										JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
										$where_pembelian
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
											t.tanggal,
											'0' as unit_masuk,
											'0' as unit_keluar,
											'0' AS unit_pembelian,
											t.unit_keluar AS unit_retur,
											t.status
										FROM
											tbl_log_stok AS t										
										JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
										$where_retur
							) q
							LEFT JOIN(
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
        $nama_gudang = DB::table('ref_gudang')->where('id',$gudang)->first()->nama;

		$no = 1;
		$arr = array();
			foreach ($d_data as $key => $d){
				$arr[] = array('NO.'=>$no++,
								'KODE BARANG' 			=> $d->barang_kode,
								'NAMA PARFURM'			=> $d->barang_nama,
								'STOCK AWAL'			=> $d->jumlah_stokawal,
								'PEMBELIAN HARI INI'	=> $d->jumlah_pembelian,
								'RETUR HARI INI'		=> $d->jumlah_retur,
								'STOK AKHIR'			=> $d->jumlah_terakhir);
			}

		if(count($arr) > 0){
		$data = $arr;
		}else{
		$data = array("0"=>['NO.'			=> '',
						 'KODE BARANG' 			=> '',
						 'NAMA PARFURM'			=> '',
						 'STOCK AWAL'			=> '',
						 'PEMBELIAN HARI INI'	=> '',
						 'RETUR HARI INI'		=> '',
						 'STOK AKHIR'			=> '']);
		}
		
    	return Excel::download(new LaporanViewExport($data), 'Laporan_Pembelian_'.$nama_gudang.'_'.$tanggal.'-'.$tanggal2.'.xlsx');
    			break;
    		case '2':
    	if($gudang != "" && $tanggal != ""){
    		$where = "WHERE tp.id_gudang = '".$gudang."' AND tp.tanggal_faktur >= '".$tanggal."' AND tp.tanggal_faktur <= '".$tanggal2."'";
        }elseif($gudang != "" && $tanggal == ""){
        	$where = "WHERE tp.id_gudang = '".$gudang."'";
        }else{
        	$where = "";
        }

        $nama_gudang = DB::table('ref_gudang')->where('id',$gudang)->first()->nama;

        $d_data = DB::SELECT("SELECT tp.id_pembelian, tp.tanggal_faktur as tanggal, tp.no_faktur, 
        	tsu.supplier_nama AS nama_supplier, tb.barang_nama AS nama_barang, tb.barang_kode AS kode_barang, tpd.jumlah, 
        	ts.satuan_satuan AS nama_satuan, tpd.harga, tpd.total
			FROM tbl_pembelian_detail AS tpd
			JOIN tbl_pembelian AS tp ON tpd.id_pembelian=tp.id_pembelian
			JOIN tbl_barang AS tb ON tpd.id_barang=tb.barang_id
			JOIN tbl_satuan AS ts ON tpd.id_satuan=ts.satuan_id
			JOIN ref_gudang AS rg ON tp.id_gudang=rg.id
			LEFT JOIN tbl_supplier AS tsu ON tp.id_supplier=tsu.supplier_id
			$where
			");

        $no = 1;
		$arr = array();
			foreach ($d_data as $key => $d){
				$arr[] = array('NO.'=>$no++,
								'TANGGAL BELI' 			=> tgl_full($d->tanggal,'0'),
								'NAMA SUPPLIER'			=> $d->nama_supplier,
								'NOMER NOTA'			=> $d->no_faktur,
								'KODE BARANG'			=> $d->kode_barang,
								'NAMA BARANG'			=> $d->nama_barang,
								'JUMLAH'				=> $d->jumlah,
								'HARGA'					=> $d->harga,
								'TOTAL'					=> $d->total);
			}

    	if(count($arr) > 0){
		$data = $arr;
		}else{
		$data = array("0"=>['NO.'			=> "",
							'TANGGAL BELI' 	=> "",							
							'NAMA SUPPLIER'	=> "",
							'NOMER NOTA'	=> "",
							'KODE BARANG'	=> "",
							'NAMA BARANG'	=> "",
							'JUMLAH'		=> "",
							'HARGA'			=> "",
							'TOTAL'			=> ""]);
		}
		return Excel::download(new LaporanViewExport($data), 'Laporan_Pembelian_PerNota_'.$nama_gudang.'_'.$tanggal.'-'.$tanggal2.'.xlsx');
    			break;
    	        case '3':
    	$add_where = "WHERE ";
		$count = 0;
		if(isset($gudang)){
			$count += 1;
			$where[] = " tp.id_gudang = '".$gudang."'";
		}
		if(isset($tanggal)){
			$count += 1;
			$where[] = " tp.tanggal_faktur >= '".$tanggal."' ";
		}
		if(isset($tanggal2)){
			$count += 1;
			$where[] = " tp.tanggal_faktur <= '".$tanggal2."' ";
		}
		if(isset($barang)){
			if($barang != 0){
				$count += 0;
				$where[] = " tpd.id_barang = '".$barang."'";
			}
		}

		if($count > 0){
			$where = $add_where.implode(" AND ",$where);
		}

        $nama_gudang = DB::table('ref_gudang')->where('id',$gudang)->first()->nama;

        $d_data = DB::SELECT("SELECT tp.id_pembelian, tp.tanggal_faktur as tanggal, tp.no_faktur, 
        	tsu.supplier_nama AS nama_supplier, tb.barang_nama AS nama_barang, tb.barang_kode AS kode_barang, tpd.jumlah, 
        	ts.satuan_satuan AS nama_satuan, tpd.harga, tpd.total
			FROM tbl_pembelian_detail AS tpd
			JOIN tbl_pembelian AS tp ON tpd.id_pembelian=tp.id_pembelian
			JOIN tbl_barang AS tb ON tpd.id_barang=tb.barang_id
			JOIN tbl_satuan AS ts ON tpd.id_satuan=ts.satuan_id
			JOIN ref_gudang AS rg ON tp.id_gudang=rg.id
			LEFT JOIN tbl_supplier AS tsu ON tp.id_supplier=tsu.supplier_id
			$where
			");

        $no = 1;
		$arr = array();
			foreach ($d_data as $key => $d){
				$arr[] = array('NO.'=>$no++,
								'TANGGAL BELI' 			=> tgl_full($d->tanggal,'0'),
								'NAMA SUPPLIER'			=> $d->nama_supplier,
								'NOMER NOTA'			=> $d->no_faktur,
								'KODE BARANG'			=> $d->kode_barang,
								'NAMA BARANG'			=> $d->nama_barang,
								'JUMLAH'				=> $d->jumlah,
								'HARGA'					=> $d->harga,
								'TOTAL'					=> $d->total);
			}

    	if(count($arr) > 0){
		$data = $arr;
		}else{
		$data = array("0"=>['NO.'			=> "",
							'TANGGAL BELI' 	=> "",							
							'NAMA SUPPLIER'	=> "",
							'NOMER NOTA'	=> "",
							'KODE BARANG'	=> "",
							'NAMA BARANG'	=> "",
							'JUMLAH'		=> "",
							'HARGA'			=> "",
							'TOTAL'			=> ""]);
		}
		return Excel::download(new LaporanViewExport($data), 'Laporan_Pembelian_PerBarang_'.$nama_gudang.'_'.$tanggal.'-'.$tanggal2.'.xlsx');
    			break;
    		default:
    			# code...
    			break;
    	}
    	
    }

    public function hasil($d_gudang,$d_tanggalAwal,$d_tanggalAkhir,$d_kategori,$d_barang=''){
    	$gudang = $d_gudang;
    	$tanggal = tgl_full($d_tanggalAwal,'99');  
    	$tanggal2 = tgl_full($d_tanggalAkhir,'99');
    	$kategori = $d_kategori;
    	$barang = $d_barang;

    	switch ($kategori) {
    		case '1':
    			if($gudang != "" && $tanggal != "" && $tanggal2 != ""){
        $where_stokawal = "WHERE t.id_ref_gudang = '".$gudang."' AND t.tanggal < '".$tanggal."'";
        $where_pembelian = "WHERE t.id_ref_gudang = '".$gudang."' AND t.tanggal >= '".$tanggal."' AND t.tanggal <= '".$tanggal2."' AND t.status='P1'";
        $where_retur = "WHERE t.id_ref_gudang = '".$gudang."' AND t.tanggal >= '".$tanggal."' AND t.tanggal <= '".$tanggal2."' AND t.status='P3'";
        }elseif($gudang == "" && $tanggal == ""){
        	$where_stokawal = "WHERE t.id_ref_gudang = '".$gudang."'";
        	$where_pembelian = "WHERE t.id_ref_gudang = '".$gudang."' AND t.status='P1'";
        	$where_retur = "WHERE t.id_ref_gudang = '".$gudang."' AND t.status='P3'";
        }else{
        	$where_stokawal = "";
        	$where_pembelian = "WHERE t.status='P1'";
        	$where_retur = "WHERE t.status='P3'";
        }

        $d_data = DB::select("SELECT q.*, 
							SUM( q.unit_masuk ) AS jumlah_masuk,
							SUM( q.unit_keluar ) AS jumlah_keluar,
							CASE WHEN SUM( r.jumlah_stokawal )<0 THEN '0' 
							WHEN SUM( r.jumlah_stokawal ) IS NULL THEN '0' 
							ELSE SUM( r.jumlah_stokawal ) END AS jumlah_stokawal,
							CASE WHEN SUM( q.unit_pembelian )<0 THEN '0' 
							WHEN SUM( q.unit_pembelian ) IS NULL THEN '0' 
							ELSE SUM( q.unit_pembelian ) END AS jumlah_pembelian,
							CASE WHEN SUM( q.unit_retur )<0 THEN '0' 
							WHEN SUM( q.unit_retur ) IS NULL THEN '0' 
							ELSE SUM( q.unit_retur ) END AS jumlah_retur,
							CASE WHEN SUM( (r.jumlah_stokawal) + (r.jumlah_stokawal))<0 THEN '0'
							WHEN SUM( (r.jumlah_stokawal) + (q.unit_pembelian-q.unit_retur)) IS NULL THEN '0'
							ELSE SUM( (r.jumlah_stokawal) + (q.unit_pembelian-q.unit_retur)) 
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
											'0' as unit_masuk,
											'0' as unit_keluar,
											t.unit_masuk as unit_pembelian,
											'0' as unit_retur,
											t.status
										FROM
											tbl_log_stok AS t										
										JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
										$where_pembelian
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
											t.tanggal,
											'0' as unit_masuk,
											'0' as unit_keluar,
											'0' AS unit_pembelian,
											t.unit_keluar AS unit_retur,
											t.status
										FROM
											tbl_log_stok AS t										
										JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
										$where_retur
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
        $nama_gudang = DB::table('ref_gudang')->where('id',$gudang)->first()->nama;

		$no = 1;
		$arr = array();
			foreach ($d_data as $key => $d){
				$arr[] = array('NO.'=>$no++,
								'KODE BARANG' 			=> $d->barang_kode,
								'NAMA PARFURM'			=> $d->barang_nama,
								'STOCK AWAL'			=> format_angka($d->jumlah_stokawal).' '.$d->satuan_satuan,
								'PEMBELIAN HARI INI'	=> format_angka($d->jumlah_pembelian).' '.$d->satuan_satuan,
								'RETUR HARI INI'		=> format_angka($d->jumlah_retur).' '.$d->satuan_satuan,
								'STOK AKHIR'			=> format_angka($d->jumlah_terakhir).' '.$d->satuan_satuan);
			}
		if(count($arr) > 0){
		$data = $arr;
		}else{
		$data = array("0"=>['NO.'=>$no++,
							'KODE BARANG' 			=> "",
							'NAMA PARFURM'			=> "",
							'STOCK AWAL'			=> "",
							'PEMBELIAN HARI INI'	=> "",
							'RETUR HARI INI'		=> "",
							'STOK AKHIR'			=> ""]);
		}
		// print_r($data);exit();
		return view('admin.laporan.hasil_laporan',compact('data'));
    			break;
    	case '2':
    	if($gudang != "" && $tanggal != ""){
    		$where = "WHERE tp.id_gudang = '".$gudang."' AND tp.tanggal_faktur >= '".$tanggal."' AND tp.tanggal_faktur <= '".$tanggal2."'";
        }elseif($gudang != "" && $tanggal == ""){
        	$where = "WHERE tp.id_gudang = '".$gudang."'";
        }else{
        	$where = "";
        }

        $d_data = DB::SELECT("SELECT tp.id_pembelian, tp.tanggal_faktur as tanggal, tp.no_faktur, 
        	tsu.supplier_nama AS nama_supplier, tb.barang_nama AS nama_barang, tb.barang_kode AS kode_barang, tpd.jumlah, 
        	ts.satuan_satuan AS nama_satuan, tpd.harga, tpd.total
			FROM tbl_pembelian_detail AS tpd
			JOIN tbl_pembelian AS tp ON tpd.id_pembelian=tp.id_pembelian
			JOIN tbl_barang AS tb ON tpd.id_barang=tb.barang_id
			JOIN tbl_satuan AS ts ON tpd.id_satuan=ts.satuan_id
			JOIN ref_gudang AS rg ON tp.id_gudang=rg.id
			LEFT JOIN tbl_supplier AS tsu ON tp.id_supplier=tsu.supplier_id
			$where
			");

        $no = 1;
		$arr = array();
			foreach ($d_data as $key => $d){
				$arr[] = array('NO.'=>$no++,
								'TANGGAL BELI' 			=> tgl_full($d->tanggal,'0'),
								'NAMA SUPPLIER'			=> $d->nama_supplier,
								'NOMER NOTA'			=> $d->no_faktur,
								'KODE BARANG'			=> $d->kode_barang,
								'NAMA BARANG'			=> $d->nama_barang,
								'JUMLAH'				=> format_angka($d->jumlah).' '.$d->nama_satuan,
								'HARGA'					=> 'Rp. '.format_angka($d->harga),
								'TOTAL'					=> 'Rp. '.format_angka($d->total));
			}

    	if(count($arr) > 0){
		$data = $arr;
		}else{
		$data = array("0"=>['NO.'			=> "",
							'TANGGAL BELI' 	=> "",							
							'NAMA SUPPLIER'	=> "",
							'NOMER NOTA'	=> "",
							'KODE BARANG'	=> "",
							'NAMA BARANG'	=> "",
							'JUMLAH'		=> "",
							'HARGA'			=> "",
							'TOTAL'			=> ""]);
		}

		return view('admin.laporan.hasil_laporan',compact('data'));
    			break;
    		    case '3':
    	$add_where = "WHERE ";
		$count = 0;
		if(isset($gudang)){
			$count += 1;
			$where[] = " tp.id_gudang = '".$gudang."'";
		}
		if(isset($tanggal)){
			$count += 1;
			$where[] = " tp.tanggal_faktur >= '".$tanggal."' ";
		}
		if(isset($tanggal2)){
			$count += 1;
			$where[] = " tp.tanggal_faktur <= '".$tanggal2."' ";
		}
		if(isset($barang)){
			if($barang > 0){
				$count += 0;
				$where[] = " tpd.id_barang = '".$barang."'";
			}
		}

		if($count > 0){
			$where = $add_where.implode(" AND ",$where);
		}

        $d_data = DB::SELECT("SELECT tp.id_pembelian, tp.tanggal_faktur as tanggal, tp.no_faktur, 
        	tsu.supplier_nama AS nama_supplier, tb.barang_nama AS nama_barang, tb.barang_kode AS kode_barang, tpd.jumlah, 
        	ts.satuan_satuan AS nama_satuan, tpd.harga, tpd.total
			FROM tbl_pembelian_detail AS tpd
			JOIN tbl_pembelian AS tp ON tpd.id_pembelian=tp.id_pembelian
			JOIN tbl_barang AS tb ON tpd.id_barang=tb.barang_id
			JOIN tbl_satuan AS ts ON tpd.id_satuan=ts.satuan_id
			JOIN ref_gudang AS rg ON tp.id_gudang=rg.id
			LEFT JOIN tbl_supplier AS tsu ON tp.id_supplier=tsu.supplier_id
			$where
			");

        $no = 1;
		$arr = array();
			foreach ($d_data as $key => $d){
				$arr[] = array('NO.'=>$no++,
								'TANGGAL BELI' 			=> tgl_full($d->tanggal,'0'),
								'NAMA SUPPLIER'			=> $d->nama_supplier,
								'NOMER NOTA'			=> $d->no_faktur,
								'KODE BARANG'			=> $d->kode_barang,
								'NAMA BARANG'			=> $d->nama_barang,
								'JUMLAH'				=> format_angka($d->jumlah).' '.$d->nama_satuan,
								'HARGA'					=> 'Rp. '.format_angka($d->harga),
								'TOTAL'					=> 'Rp. '.format_angka($d->total));
			}

    	if(count($arr) > 0){
		$data = $arr;
		}else{
		$data = array("0"=>['NO.'			=> "",
							'TANGGAL BELI' 	=> "",							
							'NAMA SUPPLIER'	=> "",
							'NOMER NOTA'	=> "",
							'KODE BARANG'	=> "",
							'NAMA BARANG'	=> "",
							'JUMLAH'		=> "",
							'HARGA'			=> "",
							'TOTAL'			=> ""]);
		}

		return view('admin.laporan.hasil_laporan',compact('data'));
    			break;
    		default:
    			# code...
    			break;
    	}
    	
    }
    
}
