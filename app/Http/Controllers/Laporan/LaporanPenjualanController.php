<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\Exports\LaporanViewExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPenjualanController extends Controller
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
	    return view('admin.laporan.index_laporan_penjualan')->with('data',$data);
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

    public function cetaklaporan_outlet(Request $request){
    	$gudang = $request->gudang;
    	$tanggal = tgl_full($request->tanggal,'99');
	    $data['gudang'] = $request->gudang;
	    $data['tanggal'] = tgl_full($request->tanggal,'');
	    $data['hari']    = tgl_full($request->tanggal,'hari');
	    $data['kasir']   = Auth::user()->name;
	    $kumpul_barang = 'AND t.id_barang IN (569,571,610,587,594,600,601,602,606,605,607,631,632,633,634)';

	    if($gudang != "" && $tanggal != ""){
        	$where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."'";
        	$where_stokawal = "WHERE t.id_ref_gudang = '".$gudang."' AND t.tanggal < '".$tanggal."'";
        	$where_penjualan = "WHERE (t.id_ref_gudang = '".$gudang."' AND t.tanggal = '".$tanggal."' AND t.status='J1') ".$kumpul_barang."";
        	$where_transfer = "WHERE k.id_gudang = '".$gudang."' AND k.tanggal = '".$tanggal."' AND k.carabayar IN (1,2) AND k.metodebayar IN (5,7)";
        	$where_hutang = "WHERE k.id_gudang = '".$gudang."' AND k.tanggal = '".$tanggal."' AND k.carabayar='3'";
        }elseif($gudang == "" && $tanggal == ""){
        	$where = "WHERE k.id_gudang = '".$gudang."'";
        	$where_stokawal = "WHERE t.id_ref_gudang = '".$gudang."'";
        	$where_penjualan = "WHERE t.id_ref_gudang = '".$gudang."' AND t.status='J1' ".$kumpul_barang."";
        	$where_transfer = "WHERE k.id_gudang = '".$gudang."' AND k.carabayar IN (1,2) AND k.metodebayar IN (5,7)";
        	$where_hutang = "WHERE k.id_gudang = '".$gudang."' AND k.carabayar='3'";
        }else{
        	$where = "";
        	$where_stokawal = "";
        	$where_penjualan = "WHERE t.status='J1' ".$kumpul_barang."";
        	$where_transfer = "WHERE k.carabayar IN (1,2) AND k.metodebayar IN (5,7)";
        	$where_hutang = "WHERE k.carabayar='3'";
        }

        

	    $d_stokawal	 = DB::select("SELECT
									tbb.barang_id,
									tbb.barang_nama,
									tbb.barang_kode,
									q.satuan_id,
									q.satuan_nama,
									q.gudang_id as id_gudang,
									q.gudang_nama as nama_gudang,
									q.tanggal,
									CASE WHEN SUM( q.jumlah_stok ) IS NULL THEN '' 
									WHEN SUM( q.jumlah_stok ) <= 0 THEN '' ELSE SUM( q.jumlah_stok ) END AS total_barang
								FROM
									tbl_barang as tbb
								LEFT JOIN (
										SELECT
											t.id_barang,
											b.barang_nama,											
											t.id_satuan AS satuan_id,
											s.satuan_nama,
											s.satuan_satuan,
											t.id_ref_gudang AS gudang_id,
											g.nama AS gudang_nama,											
											t.tanggal,
											SUM( t.unit_masuk ) AS jumlah_masuk,
											SUM( t.unit_keluar ) AS jumlah_keluar,
											SUM( t.unit_masuk - t.unit_keluar ) AS jumlah_stok,
											t.status
										FROM
											tbl_log_stok AS t
										LEFT JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										LEFT JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
										$where_stokawal
										GROUP BY
											t.id_barang,
											t.id_satuan,
											t.id_ref_gudang,
											t.tanggal,
											t.status
										ORDER BY
											t.tanggal DESC
							) AS q ON q.id_barang = tbb.barang_id 
								WHERE 
										tbb.barang_id IN (569,571,610,587,594,600,601,602,606,605,607,631,632,633,634)
								GROUP BY
										tbb.barang_id,
										q.satuan_id,
										q.gudang_id
								ORDER BY
									tbb.barang_nama ASC");
	    $d_penjualan 	 = DB::select("SELECT
									tbb.barang_id,
									tbb.barang_nama,
									tbb.barang_kode,
									q.satuan_id,
									q.satuan_nama,
									q.gudang_id as id_gudang,
									q.gudang_nama as nama_gudang,
									q.tanggal,
									CASE WHEN SUM( q.jumlah_keluar ) IS NULL THEN '' 
									WHEN SUM( q.jumlah_keluar ) <= 0 THEN '' ELSE SUM( q.jumlah_keluar ) END AS total_barang
								FROM
									tbl_barang as tbb
								LEFT JOIN (
										SELECT
											t.id_barang,
											b.barang_nama,											
											t.id_satuan AS satuan_id,
											s.satuan_nama,
											s.satuan_satuan,
											t.id_ref_gudang AS gudang_id,
											g.nama AS gudang_nama,											
											t.tanggal,
											SUM( t.unit_masuk ) AS jumlah_masuk,
											SUM( t.unit_keluar ) AS jumlah_keluar,
											SUM( t.unit_masuk - t.unit_keluar ) AS jumlah_stok,
											t.status
										FROM
											tbl_log_stok AS t
										LEFT JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										LEFT JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
										$where_penjualan
										GROUP BY
											t.id_barang,
											t.id_satuan,
											t.id_ref_gudang,
											t.tanggal,
											t.status
										ORDER BY
											t.tanggal DESC
								) AS q ON q.id_barang = tbb.barang_id 
								WHERE 
										tbb.barang_id IN (569,571,610,587,594,600,601,602,606,605,607,631,632,633,634)
								GROUP BY
										tbb.barang_id,
										q.satuan_id,
										q.gudang_id
								ORDER BY
									tbb.barang_nama ASC");
	    
	    foreach($d_stokawal as $key => $value){
	    	$data['stokawal'][$value->barang_id]['total_barang'] = $value->total_barang; 
	    }
	    foreach($d_penjualan as $key => $value){
	    	$data['penjualan'][$value->barang_id]['total_barang'] = $value->total_barang; 
	    }

        $data['omset'] =DB::select("SELECT
						            me.id,
						            me.nama as name,
						            CASE WHEN su.jumlah IS NULL THEN '0' ELSE su.jumlah END AS data
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
						            ) AS su ON su.metodebayar = me.id");
        $data['transfer']	= DB::select("SELECT
										k.no_faktur,
										k.metodebayar,
										m.nama AS metode,
										k.tanggal,
										k.total_tagihan,
										k.total_potongan,
										k.uang_muka,
										k.ongkos_kirim,
										k.carabayar,
										k.metodebayar
									FROM
										tbl_kasir AS k
										LEFT JOIN m_metode AS m ON k.metodebayar = m.id
									$where_transfer");
        $data['hutang']	= DB::select("SELECT
										k.no_faktur,
										k.metodebayar,
										m.nama AS metode,
										k.tanggal,
										k.total_tagihan,
										k.total_potongan,
										k.uang_muka,
										k.ongkos_kirim,
										k.carabayar
									FROM
										tbl_kasir AS k
										LEFT JOIN m_metode AS m ON k.metodebayar = m.id
									$where_hutang");

	    return view('admin.laporan.cetak_laporan_penjualan')->with('data',$data);
    }

    public function cetaklaporan_grosir(Request $request){
    	require(public_path('fpdf1813/Mc_table.php'));
    	$gudang = $request->get('gudang');
    	$tanggal = tgl_full($request->get('tanggal'),'99');    	

    	if($gudang != "" && $tanggal != ""){
        $where_stokawal = "WHERE t.id_ref_gudang = '".$gudang."' AND t.tanggal < '".$tanggal."'";
        $where_penjualan = "WHERE t.id_ref_gudang = '".$gudang."' AND t.tanggal = '".$tanggal."' AND t.status IN ('J1','J4')";
        }elseif($gudang == "" && $tanggal == ""){
        	$where_stokawal = "WHERE t.id_ref_gudang = '".$gudang."'";
        	$where_penjualan = "WHERE t.id_ref_gudang = '".$gudang."' AND t.status IN ('J1','J4')";
        }else{
        	$where_stokawal = "";
        	$where_penjualan = "WHERE t.status IN ('J1','J4')";
        }

        $gudang = DB::table('ref_gudang')->where('id',$gudang)->select(DB::Raw('id as id_gudang,nama as nama_gudang'))->first();
        /*$d_saldoawal = DB::SELECT("SELECT q.*, 
											SUM( q.unit_masuk ) AS jumlah_masuk,
											SUM( q.unit_keluar ) AS jumlah_keluar,
											SUM( q.unit_masuk - q.unit_keluar ) AS jumlah_stok,
											'' AS jumlah_terjual 
											from (	
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
											t.status
										FROM
											tbl_log_stok AS t										
										LEFT JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										LEFT JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
										$where_stokawal
  										) q
									GROUP BY
										q.id_barang,
										q.satuan_id,
										q.gudang_id
									ORDER BY
										q.barang_nama ASC");*/
       /* $d_penjualan = DB::SELECT("SELECT q.*, 
											'0' AS jumlah_masuk,
											'0' AS jumlah_keluar,
											'0' AS jumlah_stok,
											SUM( q.unit_keluar ) AS jumlah_terjual 
											from (	
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
											t.status
										FROM
											tbl_log_stok AS t										
										LEFT JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										LEFT JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
										$where_penjualan
  										) q
									GROUP BY
										q.id_barang,
										q.satuan_id,
										q.gudang_id
									ORDER BY
										q.barang_nama ASC");*/

       
        /*foreach ($d_penjualan as $key => $val) {
        	// $dat[$val->id_barang]['id_barang']	=$val->id_barang;
        	// $dat[$val->id_barang]['barang_nama']	=$val->barang_nama;
        	// $dat[$val->id_barang]['id_barang']	=$val->id_barang;
        	$dat[$val->id_barang]['jumlah_terjual']	= $val->jumlah_terjual;
        }
         foreach($d_saldoawal as $key=>$value){

         	if(array_key_exists($value->id_barang, $dat)){
         		if (is_null($dat[$value->id_barang])) {
          	 	$jual = '0';
		        } else {
		          $jual = $dat[$value->id_barang]['jumlah_terjual'];
		        }
		    $data[$value->id_barang]['id_barang']	=$value->id_barang;
        	$data[$value->id_barang]['barang_nama']	=$value->barang_nama;
        	$data[$value->id_barang]['barang_kode']	=$value->barang_kode;
        	$data[$value->id_barang]['saldoawal']	=$value->jumlah_stok;
        	$data[$value->id_barang]['jumlah_terjual']	= $jual;
         	}
        	
        }*/
        //print_r($data); exit;

	    $data['data'] 	= DB::select("SELECT q.*, 
											SUM( q.unit_masuk ) AS jumlah_masuk,
											SUM( q.unit_keluar ) AS jumlah_keluar,
											CASE WHEN SUM( q.unit_masuk - q.unit_keluar )< 0 THEN '0' 
											WHEN SUM( q.unit_masuk - q.unit_keluar ) IS NULL THEN '0' 
											ELSE SUM( q.unit_masuk - q.unit_keluar ) END AS jumlah_stokawal,
											CASE WHEN SUM( q.unit_terjual )<0 THEN '0' 
											WHEN SUM( q.unit_terjual ) IS NULL THEN '0' 
											ELSE SUM( q.unit_terjual ) END AS jumlah_terjual,
											CASE WHEN SUM( (q.unit_masuk - q.unit_keluar) - q.unit_terjual)<0 THEN '0'
											WHEN SUM( (q.unit_masuk - q.unit_keluar) - q.unit_terjual) IS NULL THEN '0'
											ELSE SUM( (q.unit_masuk - q.unit_keluar) - q.unit_terjual) 
											END AS jumlah_terakhir
											/*SUM( q.unit_masuk - q.unit_keluar ) AS jumlah_stok,
											SUM( q.unit_terjual ) AS jumlah_terjual,
											SUM( (q.unit_masuk - q.unit_keluar) - q.unit_terjual) AS jumlah_terakhir*/
											from (	
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
											'0' as unit_terjual,
											t.status
										FROM
											tbl_log_stok AS t										
										JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id						
										$where_stokawal
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
											t.unit_keluar,
											t.status
										FROM
											tbl_log_stok AS t										
										JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
										$where_penjualan
  										) q
									GROUP BY
										q.id_barang,
										q.satuan_id,
										q.gudang_id
									ORDER BY
										q.barang_nama ASC
								");
	    $data['tanggal'] = tgl_full($request->tanggal,'');
	    $data['hari']    = tgl_full($request->tanggal,'hari');
	    $data['kasir']   = Auth::user()->name;
	    $data['gudang']	 = $gudang->nama_gudang;

	    $html = view('admin.laporan.cetak_laporan_penjualangrosir')->with('data',$data);
	    trigger_log(NULL, "Cetak Laporan Penjualan PDF", 7);
      	return response($html)->header('Content-Type', 'application/pdf');
    }

    public function excel_grosir($d_gudang,$d_tanggal){
    	$gudang = $d_gudang;
    	$tanggal = tgl_full($d_tanggal,'99');  

    	if($gudang != "" && $tanggal != ""){
        $where_stokawal = "WHERE t.id_ref_gudang = '".$gudang."' AND t.tanggal < '".$tanggal."'";
        $where_penjualan = "WHERE t.id_ref_gudang = '".$gudang."' AND t.tanggal = '".$tanggal."' AND t.status IN ('J1','J4')";
        }elseif($gudang == "" && $tanggal == ""){
        	$where_stokawal = "WHERE t.id_ref_gudang = '".$gudang."'";
        	$where_penjualan = "WHERE t.id_ref_gudang = '".$gudang."' AND t.status IN ('J1','J4')";
        }else{
        	$where_stokawal = "";
        	$where_penjualan = "WHERE t.status IN ('J1','J4')";
        }

        $d_data = DB::select("
	    								SELECT q.*, 
											SUM( q.unit_masuk ) AS jumlah_masuk,
											SUM( q.unit_keluar ) AS jumlah_keluar,
											CASE WHEN SUM( q.unit_masuk - q.unit_keluar )< 0 THEN '0' 
											WHEN SUM( q.unit_masuk - q.unit_keluar ) IS NULL THEN '0' 
											ELSE SUM( q.unit_masuk - q.unit_keluar ) END AS jumlah_stokawal,
											CASE WHEN SUM( q.unit_terjual )<0 THEN '0' 
											WHEN SUM( q.unit_terjual ) IS NULL THEN '0' 
											ELSE SUM( q.unit_terjual ) END AS jumlah_terjual,
											CASE WHEN SUM( (q.unit_masuk - q.unit_keluar) - q.unit_terjual)<0 THEN '0'
											WHEN SUM( (q.unit_masuk - q.unit_keluar) - q.unit_terjual) IS NULL THEN '0'
											ELSE SUM( (q.unit_masuk - q.unit_keluar) - q.unit_terjual) 
											END AS jumlah_terakhir
											from (	
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
											'0' as unit_terjual,
											t.status
										FROM
											tbl_log_stok AS t										
										JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id						
										$where_stokawal
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
											t.unit_keluar,
											t.status
										FROM
											tbl_log_stok AS t										
										JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
										$where_penjualan
  										) q
									GROUP BY
										q.id_barang,
										q.satuan_id,
										q.gudang_id
									ORDER BY
										q.barang_nama ASC
								");
        $nama_gudang = DB::table('ref_gudang')->where('id',$gudang)->first()->nama;

		$no = 1;
		$arr = array();
			foreach ($d_data as $key => $d){
				$arr[] = array('NO.'=>$no++,
								'KODE BARANG' 			=> $d->barang_kode,
								'NAMA PARFURM'			=> $d->barang_nama,
								'STOCK AWAL'			=> format_angka($d->jumlah_stokawal).' '.$d->satuan_satuan,
								'PENJUALAN HARI INI'	=> format_angka($d->jumlah_terjual).' '.$d->satuan_satuan,
								'STOK AKHIR'			=> format_angka($d->jumlah_terakhir).' '.$d->satuan_satuan);
			}
        trigger_log(NULL, "Cetak Laporan Penjualan Excel", 7);
    	return Excel::download(new LaporanViewExport($arr), 'Laporan_Penjualan_'.$nama_gudang.'_'.$tanggal.'.xlsx');
    }
    
    public function cetaklaporan_penjualan(Request $request){
    	require(public_path('fpdf1813/Mc_table.php'));
    	$gudang = $request->get('gudang_penjualan');
    	$tanggal = tgl_full($request->get('tanggalAwal'),'99');
    	$tanggal2 = tgl_full($request->get('tanggalAkhir'),'99'); 
    	$kategori = $request->get('kategori'); 
    	$barang = 0;
    	if(isset($request['barang'])){
    		$barang = $request->get('barang');
    	}

        $d_gudang = DB::table('ref_gudang')->where('id',$gudang)->select(DB::Raw('id as id_gudang,nama as nama_gudang'))->first();
        trigger_log(NULL, "Cetak Laporan Penjualan PDF", 7);
    	switch ($kategori) { 
    	    case '1':
    	if($gudang != "" && $tanggal != ""){
    		$where = "WHERE k.id_gudang = '".$gudang."' AND k.tanggal_faktur >= '".$tanggal."' AND k.tanggal_faktur <= '".$tanggal2."'";
    		$where_ongkir = "WHERE k.id_gudang = '".$gudang."' AND k.tanggal_faktur >= '".$tanggal."' AND k.tanggal_faktur <= '".$tanggal2."' AND k.ongkos_kirim > 0";
        }elseif($gudang != "" && $tanggal == ""){
        	$where = "WHERE k.id_gudang = '".$gudang."'";
        	$where_ongkir = "WHERE k.id_gudang = '".$gudang."' AND k.ongkos_kirim > 0";
        }else{
        	$where = "";
        	$where_ongkir = "WHERE k.ongkos_kirim > 0";
        }
        $data['data'] 	= DB::SELECT("SELECT p.tanggal, sum(p.tunai) as tunai, sum(p.debit_bca) as debit_bca, 
        	sum(p.debit_mandiri) as debit_mandiri, sum(p.kredit_bca) as kredit_bca, sum(p.kredit_mandiri) as kredit_mandiri, 
        	sum(p.transfer_bca) as transfer_bca, sum(p.transfer_mandiri) as transfer_mandiri, sum(p.ovo) as ovo, 
        	sum(p.hutang) as hutang, sum(p.ongkos_kirim) as ongkos_kirim,
        	sum(p.tunai+p.debit_bca+p.debit_mandiri+p.kredit_bca+p.kredit_mandiri+p.transfer_bca+p.transfer_mandiri+p.ovo+p.hutang) as total,
        	sum(p.tunai+p.debit_bca+p.debit_mandiri+p.kredit_bca+p.kredit_mandiri+p.transfer_bca+p.transfer_mandiri+p.ovo+p.hutang+p.ongkos_kirim) as total_ongkir
		FROM (
		SELECT su.tanggal, 
			CASE WHEN su.metodebayar = 1 THEN su.jumlah ELSE 0 END AS tunai,
			CASE WHEN su.metodebayar = 2 THEN su.jumlah ELSE 0 END AS debit_bca,
			CASE WHEN su.metodebayar = 3 THEN su.jumlah ELSE 0 END AS debit_mandiri,
			CASE WHEN su.metodebayar = 4 THEN su.jumlah ELSE 0 END AS kredit_bca,
			CASE WHEN su.metodebayar = 9 THEN su.jumlah ELSE 0 END AS kredit_mandiri,
			CASE WHEN su.metodebayar = 5 THEN su.jumlah ELSE 0 END AS transfer_bca,
			CASE WHEN su.metodebayar = 8 THEN su.jumlah ELSE 0 END AS transfer_mandiri,
			CASE WHEN su.metodebayar = 6 THEN su.jumlah ELSE 0 END AS ovo,
			CASE WHEN su.metodebayar = 7 THEN su.jumlah ELSE 0 END AS hutang,
			CASE WHEN su.metodebayar = 99 THEN su.jumlah ELSE 0 END AS ongkos_kirim
		FROM (
		SELECT q.no_faktur, q.tanggal, q.metodebayar, q.metode, SUM(q.jumlah) as jumlah FROM (
        		SELECT
					k.id_kasir,
					k.tanggal_faktur as tanggal,
					k.no_faktur as no_faktur,
					k.metodebayar,
					m.nama AS metode,
					k.created_at,
					CASE WHEN k.metodebayar2 IS NOT NULL 
					THEN 0 ELSE SUM( k.total_tagihan - k.total_potongan ) 
					END AS jumlah
					FROM tbl_kasir AS k
					LEFT JOIN m_metode AS m ON k.metodebayar = m.id
					$where
					GROUP BY
					k.tanggal_faktur, k.metodebayar, k.metodebayar2, m.nama
				UNION
				SELECT
					k.id_kasir,
					k.tanggal_faktur as tanggal,
					k.no_faktur as no_faktur,
          			k.metodebayar,
          			m.nama AS metode,
					k.created_at,
          			CASE WHEN k.metodebayar2 IS NOT NULL 
          			THEN SUM(k.total_metodebayar) ELSE 0 
          			END AS jumlah
          			FROM tbl_kasir AS k
          			LEFT JOIN m_metode AS m ON k.metodebayar = m.id
          			$where
					GROUP BY
					k.tanggal_faktur, k.metodebayar, k.metodebayar2, m.nama
				UNION 
				SELECT
					k.id_kasir,
					k.tanggal_faktur as tanggal,
					k.no_faktur as no_faktur,
          		k.metodebayar2 as metodebayar,
          		m.nama AS metode,k.created_at,
          		CASE WHEN k.metodebayar2 IS NOT NULL 
          		THEN SUM(k.total_metodebayar2) ELSE 0 
          		END AS jumlah
          		FROM tbl_kasir AS k
          		JOIN m_metode AS m ON k.metodebayar2 = m.id
          		$where
          		GROUP BY
				k.tanggal_faktur, k.metodebayar, k.metodebayar2, m.nama
				UNION ALL
				SELECT 
					k.id_kasir,
					k.tanggal_faktur as tanggal,
					k.no_faktur as no_faktur,
					'99' AS urutan, 
					'Ongkos Kirim' AS metode,
					k.created_at,
					SUM(k.ongkos_kirim) AS jumlah 
				FROM tbl_kasir AS k 
				$where_ongkir
				AND k.ongkos_kirim > 0
				GROUP BY 
				k.tanggal_faktur, k.ongkos_kirim
				) AS q 
					GROUP BY
					q.tanggal, q.metodebayar
			) AS su
		) AS p	
		group by p.tanggal");
	    $data['tanggal_awal'] = tgl_full($request->tanggalAwal,'');
	    $data['tanggal_akhir'] = tgl_full($request->tanggalAkhir,'');
	    $data['hari']    = tgl_full($request->tanggal,'hari');
	    $data['kasir']   = Auth::user()->name;
	    $data['gudang']	 = $d_gudang->nama_gudang;
	    $html = view('admin.laporan.cetak_laporan_penjualangrosir_rekap')->with('data',$data);
	    return response($html)->header('Content-Type', 'application/pdf');
	    
    		break; 
    	    case '2':
    	/*if($gudang != "" && $tanggal != ""){
    		$where_tk = "WHERE tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."'";
    		$where_tkd = "WHERE tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."' AND tkd.id_detail_kasir_produk = 0";
        }elseif($gudang != "" && $tanggal == ""){
        	$where_tk = "WHERE tk.id_gudang = '".$gudang."'";
    		$where_tkd = "WHERE tk.id_gudang = '".$gudang."' AND tkd.id_detail_kasir_produk = 0";
        }else{
        	$where_tk = "";
    		$where_tkd = "WHERE tkd.id_detail_kasir_produk = 0";
        }*/
        
        $add_where = " WHERE ";
        $where_tkd[] = "( tkd.id_detail_kasir_produk = 0 OR tkd.id_detail_kasir_produk IS NULL) ";
        if(isset($gudang)){
        	if($gudang != ''){
        		$where_tk[] = "tk.id_gudang = '".$gudang."'";
        		$where_tkd[] = "tk.id_gudang = '".$gudang."'";
        	}
        }

        if(isset($tanggal)){
        	if($tanggal != ''){
        		$where_tk[] = " tk.tanggal_faktur >= '".$tanggal."'";
        		$where_tkd[] = " tk.tanggal_faktur >= '".$tanggal."'";
        	}
        }

        if(isset($tanggal2)){
        	if($tanggal2 != ''){
        		$where_tk[] = " tk.tanggal_faktur <= '".$tanggal2."'";
        		$where_tkd[] = " tk.tanggal_faktur <= '".$tanggal2."'";
        	}
        }

        $where_tk = $add_where.implode(" AND ", $where_tk);
        $where_tkd = $add_where.implode(" AND ", $where_tkd);
        // print_r("<br>".$where_tk."<br>".$where_tkd);exit();
	    $data['data'] 	= DB::SELECT("SELECT q.*, tkp.jumlah_cetak FROM (
			SELECT tk.id_kasir, tk.tanggal_faktur as tanggal, tk.no_faktur, mpe.nama AS nama_pelanggan, mpe.telp as telp_pelanggan, tk.keterangan, tk.total_potongan as potongan, tk.ongkos_kirim as ongkir,
			CASE WHEN tk.metodebayar2 IS NOT NULL THEN CONCAT(mm.nama,', ',mm2.nama) ELSE mm.nama END AS metodebayar,
			mp.nama AS nama_produk, mp.kode_produk AS kode_produk, SUM(tkp.jumlah) as jumlah, ts.satuan_satuan AS nama_satuan, tkp.harga, tkp.total, '1' AS urutan, CASE WHEN SUBSTRING(mp.kode_produk,5,1) = 'R' THEN SUBSTRING(mp.kode_produk,1,5) WHEN SUBSTRING(mp.kode_produk,5,2) = 'aR' THEN SUBSTRING(mp.kode_produk,1,6) ELSE SUBSTRING(mp.kode_produk,1,4) END AS jenis_produk
			FROM tbl_kasir_detail_produk AS tkp
			JOIN tbl_kasir AS tk ON tkp.id_kasir=tk.id_kasir
			JOIN m_produk AS mp ON tkp.id_produk=mp.id
			JOIN tbl_satuan AS ts ON tkp.id_satuan=ts.satuan_id
			JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
			LEFT JOIN m_pelanggan AS mpe ON tk.id_pelanggan=mpe.id
			LEFT JOIN m_metode AS mm ON tk.metodebayar=mm.id
			LEFT JOIN m_metode AS mm2 ON tk.metodebayar2=mm2.id
			$where_tk
			GROUP BY tk.id_kasir, tk.tanggal, tk.no_faktur, tk.id_pelanggan, tkp.id_produk, tkp.id_satuan
			UNION

			SELECT tk.id_kasir, tk.tanggal_faktur as tanggal, tk.no_faktur, mp.nama AS nama_pelanggan, mp.telp as telp_pelanggan, tk.keterangan, tk.total_potongan as potongan, tk.ongkos_kirim as ongkir,
			CASE WHEN tk.metodebayar2 IS NOT NULL THEN CONCAT(mm.nama,', ',mm2.nama) ELSE mm.nama END AS metodebayar,
			tb.barang_nama AS nama_produk, tb.barang_kode as kode_produk, SUM(tkd.jumlah) as jumlah, ts.satuan_satuan AS nama_satuan, tkd.harga, tkd.total, '2' AS urutan, tb.barang_kode as jenis_produk
			FROM tbl_kasir_detail AS tkd
			JOIN tbl_kasir AS tk ON tkd.id_kasir=tk.id_kasir
			JOIN tbl_barang AS tb ON tkd.id_barang=tb.barang_id
			JOIN tbl_satuan AS ts ON tkd.id_satuan=ts.satuan_id
			JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
			LEFT JOIN m_pelanggan AS mp ON tk.id_pelanggan=mp.id
			LEFT JOIN m_metode AS mm ON tk.metodebayar=mm.id
			LEFT JOIN m_metode AS mm2 ON tk.metodebayar2=mm2.id
			$where_tkd
			GROUP BY tk.id_kasir, tk.tanggal, tk.no_faktur, tk.id_pelanggan, tkd.id_barang, tkd.id_satuan
			) AS q 
			LEFT JOIN (SELECT id_kasir, COUNT(id_print) as jumlah_cetak FROM tbl_kasir_print GROUP BY id_kasir) AS tkp ON q.id_kasir = tkp.id_kasir
			ORDER BY q.id_kasir, q.tanggal, q.urutan, q.nama_produk ");
	    $data['tanggal_awal'] = tgl_full($request->tanggalAwal,'');
	    $data['tanggal_akhir'] = tgl_full($request->tanggalAkhir,'');
	    $data['hari']    = tgl_full($request->tanggal,'hari');
	    $data['kasir']   = Auth::user()->name;
	    $data['gudang']	 = $d_gudang->nama_gudang;
	    $html = view('admin.laporan.cetak_laporan_penjualangrosir_pernota')->with('data',$data);
	    return response($html)->header('Content-Type', 'application/pdf');
    		break;
    		case '3':
    			# code...
    	if($gudang != "" && $tanggal != ""){
        $where_stokawal = "WHERE t.id_ref_gudang = '".$gudang."' AND t.tanggal < '".$tanggal."'";
        $where_penjualan = "WHERE t.id_ref_gudang = '".$gudang."' AND t.tanggal >= '".$tanggal."' AND t.tanggal <= '".$tanggal2."' AND t.status IN ('J1','J4')";
        }elseif($gudang == "" && $tanggal == ""){
        	$where_stokawal = "WHERE t.id_ref_gudang = '".$gudang."'";
        	$where_penjualan = "WHERE t.id_ref_gudang = '".$gudang."' AND t.status IN ('J1','J4')";
        }else{
        	$where_stokawal = "";
        	$where_penjualan = "WHERE t.status IN ('J1','J4')";
        }

	    $data['data'] 	= DB::select("SELECT q.*, 
											SUM( q.unit_masuk ) AS jumlah_masuk,
											SUM( q.unit_keluar ) AS jumlah_keluar,
											CASE WHEN SUM( r.jumlah_stokawal )< 0 THEN '0' 
											WHEN SUM( r.jumlah_stokawal ) IS NULL THEN '0' 
											ELSE SUM( r.jumlah_stokawal ) END AS jumlah_stokawal,
											CASE WHEN SUM( q.unit_terjual )<0 THEN '0' 
											WHEN SUM( q.unit_terjual ) IS NULL THEN '0' 
											ELSE SUM( q.unit_terjual ) END AS jumlah_terjual,
											CASE WHEN SUM( (r.jumlah_stokawal) - q.unit_terjual)<0 THEN '0'
											WHEN SUM( (r.jumlah_stokawal) - q.unit_terjual) IS NULL THEN '0'
											ELSE SUM( (r.jumlah_stokawal) - q.unit_terjual) 
											END AS jumlah_terakhir,
											CASE WHEN SUM( q.harga )<0 THEN '0' 
											WHEN SUM( q.harga ) IS NULL THEN '0' 
											ELSE SUM( q.harga ) END AS harga_penjualan
											from (										
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
											t.unit_keluar as unit_terjual,
											tkd.total as harga,
											t.status
										FROM
											tbl_log_stok AS t										
										JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
										JOIN tbl_kasir_detail AS tkd ON t.log_stok_id=tkd.id_log_stok
										$where_penjualan
  										) q
										JOIN (
											SELECT q.id_barang as barang, q.satuan_id as satuan, q.gudang_id as gudang,
											CASE WHEN SUM( q.unit_masuk - q.unit_keluar )< 0 THEN '0' 
											WHEN SUM( q.unit_masuk - q.unit_keluar ) IS NULL THEN '0' 
											ELSE SUM( q.unit_masuk - q.unit_keluar ) END AS jumlah_stokawal
											from (	
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
											'0' as unit_terjual,
											'0' as harga,
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
										q.barang_nama ASC
								");
	    $data['tanggal_awal'] = tgl_full($request->tanggalAwal,'');
	    $data['tanggal_akhir'] = tgl_full($request->tanggalAkhir,'');
	    $data['hari']    = tgl_full($request->tanggal,'hari');
	    $data['kasir']   = Auth::user()->name;
	    $data['gudang']	 = $d_gudang->nama_gudang;
	    $html = view('admin.laporan.cetak_laporan_penjualangrosir')->with('data',$data);
	    return response($html)->header('Content-Type', 'application/pdf');
	    break;
	        case '4':
	    if($gudang != "" && $tanggal != ""){
    		if($barang == 0){
    		$where_tkd = "WHERE tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."'";
	    	}else{
    		$where_tkd = "WHERE tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."' AND tkd.id_barang = '".$barang."' ";
	    	}
        }elseif($gudang != "" && $tanggal == ""){
        	$where_tkd = "WHERE tk.id_gudang = '".$gudang."'";
        }else{
        	$where_tkd = "";
        }

	    $data['data'] 	= DB::SELECT("SELECT * FROM (
			SELECT tk.id_kasir, tk.tanggal_faktur as tanggal, tk.no_faktur, mp.nama AS nama_pelanggan, mp.telp as telp_pelanggan,
			CASE WHEN tk.metodebayar2 IS NOT NULL THEN CONCAT(mm.nama,', ',mm2.nama) ELSE mm.nama END AS metodebayar,
			tb.barang_nama AS nama_produk, tkd.jumlah, ts.satuan_satuan AS nama_satuan, tkd.harga, tkd.total, tk.keterangan
			FROM tbl_kasir_detail AS tkd
			JOIN tbl_kasir AS tk ON tkd.id_kasir=tk.id_kasir
			JOIN tbl_barang AS tb ON tkd.id_barang=tb.barang_id
			JOIN tbl_satuan AS ts ON tkd.id_satuan=ts.satuan_id
			JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
			LEFT JOIN m_pelanggan AS mp ON tk.id_pelanggan=mp.id
			LEFT JOIN m_metode AS mm ON tk.metodebayar=mm.id
			LEFT JOIN m_metode AS mm2 ON tk.metodebayar2=mm2.id
			$where_tkd
			) AS q 
			ORDER BY q.id_kasir, q.tanggal, q.nama_produk ");
	    $data['tanggal_awal'] = tgl_full($request->tanggalAwal,'');
	    $data['tanggal_akhir'] = tgl_full($request->tanggalAkhir,'');
	    $data['hari']    = tgl_full($request->tanggal,'hari');
	    $data['kasir']   = Auth::user()->name;
	    $data['gudang']	 = $d_gudang->nama_gudang;
	    $html = view('admin.laporan.cetak_laporan_penjualangrosir_perbarang')->with('data',$data);
	    return response($html)->header('Content-Type', 'application/pdf');
	    	break;
	    	case '5':
	    if($gudang != "" && $tanggal != ""){
    		$where_tkd = "WHERE tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."' AND tk.carabayar='3'";
        }elseif($gudang != "" && $tanggal == ""){
        	$where_tkd = "WHERE tk.id_gudang = '".$gudang."' AND tk.carabayar='3'";
        }else{
        	$where_tkd = "AND tk.carabayar='3'";
        }

	    $data['data'] 	= DB::SELECT("
	    	SELECT tk.id_kasir, tk.tanggal_faktur as tanggal, tk.tanggal_bayar as tanggal_bayar, tk.no_faktur, 
	    	mp.nama AS nama_pelanggan, mp.telp as telp_pelanggan, tk.keterangan,
			CASE WHEN tk.metodebayar2 IS NOT NULL THEN CONCAT(mm.nama,', ',mm2.nama) ELSE mm.nama END AS metodebayar,
			(tk.total_tagihan-(tk.total_potongan+tk.ongkos_kirim)) as total,
			CASE WHEN tk.status=2 THEN 'LUNAS' ELSE 'BELUM BAYAR' END AS status
			FROM tbl_kasir AS tk 
			JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
			LEFT JOIN m_pelanggan AS mp ON tk.id_pelanggan=mp.id
			LEFT JOIN m_metode AS mm ON tk.metodebayar=mm.id
			LEFT JOIN m_metode AS mm2 ON tk.metodebayar2=mm2.id
			$where_tkd
			ORDER BY tk.tanggal_faktur ");
	    $data['tanggal_awal'] = tgl_full($request->tanggalAwal,'');
	    $data['tanggal_akhir'] = tgl_full($request->tanggalAkhir,'');
	    $data['hari']    = tgl_full($request->tanggal,'hari');
	    $data['kasir']   = Auth::user()->name;
	    $data['gudang']	 = $d_gudang->nama_gudang;
	    $html = view('admin.laporan.cetak_laporan_penjualangrosir_piutang')->with('data',$data);
	    return response($html)->header('Content-Type', 'application/pdf');
	    	break;
    		
    		default:
    			# code...
    			break;
    	}
    }

    public function excel_penjualan($d_gudang,$d_tanggalAwal, $d_tanggalAkhir, $d_kategori, $d_barang){
    	$gudang 	= $d_gudang;
    	$tanggal 	= tgl_full($d_tanggalAwal,'99');
    	$tanggal2 	= tgl_full($d_tanggalAkhir,'99');
    	$kategori 	= $d_kategori;
    	$barang 	= $d_barang;
    	trigger_log(NULL, "Cetak Laporan Penjualan Excel", 7);
    	switch ($kategori) {
    		case '1':
    	if($gudang != "" && $tanggal != ""){
    		$where = "WHERE k.id_gudang = '".$gudang."' AND k.tanggal_faktur >= '".$tanggal."' AND k.tanggal_faktur <= '".$tanggal2."'";
    		$where_ongkir = "WHERE k.id_gudang = '".$gudang."' AND k.tanggal_faktur >= '".$tanggal."' AND k.tanggal_faktur <= '".$tanggal2."' AND k.ongkos_kirim > 0";
        }elseif($gudang != "" && $tanggal == ""){
        	$where = "WHERE k.id_gudang = '".$gudang."'";
        	$where_ongkir = "WHERE k.id_gudang = '".$gudang."' AND k.ongkos_kirim > 0";
        }else{
        	$where = "";
        	$where_ongkir = "WHERE k.ongkos_kirim > 0";
        }

        $nama_gudang = DB::table('ref_gudang')->where('id',$gudang)->first()->nama;

        $d_data = DB::SELECT("SELECT p.tanggal, sum(p.tunai) as tunai, sum(p.debit_bca) as debit_bca, 
        	sum(p.debit_mandiri) as debit_mandiri, sum(p.kredit_bca) as kredit_bca, sum(p.kredit_mandiri) as kredit_mandiri, 
        	sum(p.transfer_bca) as transfer_bca, sum(p.transfer_mandiri) as transfer_mandiri, sum(p.ovo) as ovo, 
        	sum(p.hutang) as hutang, sum(p.ongkos_kirim) as ongkos_kirim,
        	sum(p.tunai+p.debit_bca+p.debit_mandiri+p.kredit_bca+p.kredit_mandiri+p.transfer_bca+p.transfer_mandiri+p.ovo+p.hutang) as total,
        	sum(p.tunai+p.debit_bca+p.debit_mandiri+p.kredit_bca+p.kredit_mandiri+p.transfer_bca+p.transfer_mandiri+p.ovo+p.hutang+p.ongkos_kirim) as total_ongkir
		FROM (
		SELECT su.tanggal, 
			CASE WHEN su.metodebayar = 1 THEN su.jumlah ELSE 0 END AS tunai,
			CASE WHEN su.metodebayar = 2 THEN su.jumlah ELSE 0 END AS debit_bca,
			CASE WHEN su.metodebayar = 3 THEN su.jumlah ELSE 0 END AS debit_mandiri,
			CASE WHEN su.metodebayar = 4 THEN su.jumlah ELSE 0 END AS kredit_bca,
			CASE WHEN su.metodebayar = 9 THEN su.jumlah ELSE 0 END AS kredit_mandiri,
			CASE WHEN su.metodebayar = 5 THEN su.jumlah ELSE 0 END AS transfer_bca,
			CASE WHEN su.metodebayar = 8 THEN su.jumlah ELSE 0 END AS transfer_mandiri,
			CASE WHEN su.metodebayar = 6 THEN su.jumlah ELSE 0 END AS ovo,
			CASE WHEN su.metodebayar = 7 THEN su.jumlah ELSE 0 END AS hutang,
			CASE WHEN su.metodebayar = 99 THEN su.jumlah ELSE 0 END AS ongkos_kirim
		FROM (
		SELECT q.no_faktur, q.tanggal, q.metodebayar, q.metode, SUM(q.jumlah) as jumlah FROM (
        		SELECT
					k.id_kasir,
					k.tanggal_faktur as tanggal,
					k.no_faktur as no_faktur,
					k.metodebayar,
					m.nama AS metode,
					k.created_at,
					CASE WHEN k.metodebayar2 IS NOT NULL 
					THEN 0 ELSE SUM( k.total_tagihan - k.total_potongan ) 
					END AS jumlah
					FROM tbl_kasir AS k
					LEFT JOIN m_metode AS m ON k.metodebayar = m.id
					$where
					GROUP BY
					k.tanggal_faktur, k.metodebayar, k.metodebayar2, m.nama
				UNION
				SELECT
					k.id_kasir,
					k.tanggal_faktur as tanggal,
					k.no_faktur as no_faktur,
          			k.metodebayar,
          			m.nama AS metode,
					k.created_at,
          			CASE WHEN k.metodebayar2 IS NOT NULL 
          			THEN SUM(k.total_metodebayar) ELSE 0 
          			END AS jumlah
          			FROM tbl_kasir AS k
          			LEFT JOIN m_metode AS m ON k.metodebayar = m.id
          			$where
					GROUP BY
					k.tanggal_faktur, k.metodebayar, k.metodebayar2, m.nama
				UNION 
				SELECT
					k.id_kasir,
					k.tanggal_faktur as tanggal,
					k.no_faktur as no_faktur,
          		k.metodebayar2 as metodebayar,
          		m.nama AS metode,k.created_at,
          		CASE WHEN k.metodebayar2 IS NOT NULL 
          		THEN SUM(k.total_metodebayar2) ELSE 0 
          		END AS jumlah
          		FROM tbl_kasir AS k
          		JOIN m_metode AS m ON k.metodebayar2 = m.id
          		$where
          		GROUP BY
				k.tanggal_faktur, k.metodebayar, k.metodebayar2, m.nama
				UNION ALL
				SELECT 
					k.id_kasir,
					k.tanggal_faktur as tanggal,
					k.no_faktur as no_faktur,
					'99' AS urutan, 
					'Ongkos Kirim' AS metode,
					k.created_at,
					SUM(k.ongkos_kirim) AS jumlah 
				FROM tbl_kasir AS k 
				$where_ongkir
				AND k.ongkos_kirim > 0
				GROUP BY 
				k.tanggal_faktur, k.ongkos_kirim
				) AS q 
					GROUP BY
					q.tanggal, q.metodebayar
			) AS su
		) AS p	
		group by p.tanggal
				");
        $no = 1;
		$arr = array();
			foreach ($d_data as $key => $d){
				$arr[] = array('NO.'=>$no++,
								'TANGGAL' 				=> tgl_full($d->tanggal,'0'),
								'TUNAI'					=> $d->tunai,
								'DEBIT BCA'				=> $d->debit_bca,
								'DEBIT MANDIRI'			=> $d->debit_mandiri,
								'KREDIT BCA'			=> $d->kredit_bca,
								'KREDIT MANDIRI'		=> $d->kredit_mandiri,
								'TRANSFER BCA'			=> $d->transfer_bca,
								'TRANSFER MANDIRI'		=> $d->transfer_mandiri,
								'OVO'					=> $d->ovo,
								'HUTANG'				=> $d->hutang,
								'ONGKOS KIRIM'			=> $d->ongkos_kirim,
								'TOTAL + ONGKOS KIRIM'	=> $d->total_ongkir,
								'TOTAL'					=> $d->total);
			}

    	if(count($arr) > 0){
		$data = $arr;
		}else{
		$data = array("0"=>['NO.'					=> '',
							'TANGGAL' 				=> '',
							'TUNAI'					=> '',
							'DEBIT BCA'				=> '',
							'DEBIT MANDIRI'			=> '',
							'KREDIT BCA'			=> '',
							'KREDIT MANDIRI'		=> '',
							'TRANSFER BCA'			=> '',
							'TRANSFER MANDIRI'		=> '',
							'OVO'					=> '',
							'HUTANG'				=> '',
							'ONGKOS KIRIM'			=> '',
							'TOTAL + ONGKOS KIRIM'	=> '',
							'TOTAL'					=> '']);
		}
		return Excel::download(new LaporanViewExport($data), 'Laporan_Penjualan_Rekap_'.$nama_gudang.'_'.$tanggal.'-'.$tanggal2.'.xlsx');

    		break;
    		case '2':
    	/*if($gudang != "" && $tanggal != ""){
    		$where = "WHERE k.id_gudang = '".$gudang."' AND k.tanggal_faktur >= '".$tanggal."' AND k.tanggal_faktur <= '".$tanggal2."'";
    		$where_ongkir = "WHERE k.id_gudang = '".$gudang."' AND k.tanggal_faktur >= '".$tanggal."' AND k.tanggal_faktur <= '".$tanggal2."' AND k.ongkos_kirim > 0";
    		$where_tk = "WHERE tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."'";
    		$where_tkd = "WHERE tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."' AND tkd.id_detail_kasir_produk = 0";
        }elseif($gudang != "" && $tanggal == ""){
        	$where = "WHERE k.id_gudang = '".$gudang."'";
        	$where_ongkir = "WHERE k.id_gudang = '".$gudang."' AND k.ongkos_kirim > 0";
        	$where_tk = "WHERE tk.id_gudang = '".$gudang."'";
        	$where_tkd = "WHERE tk.id_gudang = '".$gudang."' AND tkd.id_detail_kasir_produk = 0";
        }else{
        	$where = "";
        	$where_ongkir = "WHERE k.ongkos_kirim > 0";
        	$where_tk = "";
        	$where_tkd = "WHERE tkd.id_detail_kasir_produk = 0";
        }*/

        $add_where = " WHERE ";
        $where_tkd[] = "( tkd.id_detail_kasir_produk = 0 OR tkd.id_detail_kasir_produk IS NULL) ";
        if(isset($gudang)){
        	if($gudang != ''){
        		$where_tk[] = "tk.id_gudang = '".$gudang."'";
        		$where_tkd[] = "tk.id_gudang = '".$gudang."'";
        	}
        }

        if(isset($tanggal)){
        	if($tanggal != ''){
        		$where_tk[] = " tk.tanggal_faktur >= '".$tanggal."'";
        		$where_tkd[] = " tk.tanggal_faktur >= '".$tanggal."'";
        	}
        }

        if(isset($tanggal2)){
        	if($tanggal2 != ''){
        		$where_tk[] = " tk.tanggal_faktur <= '".$tanggal2."'";
        		$where_tkd[] = " tk.tanggal_faktur <= '".$tanggal2."'";
        	}
        }

        $where_tk = $add_where.implode(" AND ", $where_tk);
        $where_tkd = $add_where.implode(" AND ", $where_tkd);

        $nama_gudang = DB::table('ref_gudang')->where('id',$gudang)->first()->nama;

        $d_data = DB::SELECT("SELECT q.*, tkp.jumlah_cetak FROM (
			SELECT tk.id_kasir, tk.tanggal_faktur as tanggal, tk.no_faktur, mpe.nama AS nama_pelanggan, mpe.telp as telp_pelanggan, tk.keterangan, tk.total_potongan as potongan, tk.ongkos_kirim as ongkir,
			CASE WHEN tk.metodebayar2 IS NOT NULL THEN CONCAT(mm.nama,', ',mm2.nama) ELSE mm.nama END AS metodebayar,
			mp.nama AS nama_produk, mp.kode_produk AS kode_produk, SUM(tkp.jumlah) as jumlah, ts.satuan_satuan AS nama_satuan, tkp.harga, tkp.total, '1' AS urutan, CASE WHEN SUBSTRING(mp.kode_produk,5,1) = 'R' THEN SUBSTRING(mp.kode_produk,1,5) WHEN SUBSTRING(mp.kode_produk,5,2) = 'aR' THEN SUBSTRING(mp.kode_produk,1,6) ELSE SUBSTRING(mp.kode_produk,1,4) END AS jenis_produk
			FROM tbl_kasir_detail_produk AS tkp
			JOIN tbl_kasir AS tk ON tkp.id_kasir=tk.id_kasir
			JOIN m_produk AS mp ON tkp.id_produk=mp.id
			JOIN tbl_satuan AS ts ON tkp.id_satuan=ts.satuan_id
			JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
			LEFT JOIN m_pelanggan AS mpe ON tk.id_pelanggan=mpe.id
			LEFT JOIN m_metode AS mm ON tk.metodebayar=mm.id
			LEFT JOIN m_metode AS mm2 ON tk.metodebayar2=mm2.id
			$where_tk
			GROUP BY tk.id_kasir, tk.tanggal, tk.no_faktur, tk.id_pelanggan, tkp.id_produk, tkp.id_satuan
			UNION

			SELECT tk.id_kasir, tk.tanggal_faktur as tanggal, tk.no_faktur, mp.nama AS nama_pelanggan, mp.telp as telp_pelanggan, tk.keterangan, tk.total_potongan as potongan, tk.ongkos_kirim as ongkir,
			CASE WHEN tk.metodebayar2 IS NOT NULL THEN CONCAT(mm.nama,', ',mm2.nama) ELSE mm.nama END AS metodebayar,
			tb.barang_nama AS nama_produk, tb.barang_kode as kode_produk, SUM(tkd.jumlah) as jumlah, ts.satuan_satuan AS nama_satuan, tkd.harga, tkd.total, '2' AS urutan, tb.barang_kode as jenis_produk
			FROM tbl_kasir_detail AS tkd
			JOIN tbl_kasir AS tk ON tkd.id_kasir=tk.id_kasir
			JOIN tbl_barang AS tb ON tkd.id_barang=tb.barang_id
			JOIN tbl_satuan AS ts ON tkd.id_satuan=ts.satuan_id
			JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
			LEFT JOIN m_pelanggan AS mp ON tk.id_pelanggan=mp.id
			LEFT JOIN m_metode AS mm ON tk.metodebayar=mm.id
			LEFT JOIN m_metode AS mm2 ON tk.metodebayar2=mm2.id
			$where_tkd
			GROUP BY tk.id_kasir, tk.tanggal, tk.no_faktur, tk.id_pelanggan, tkd.id_barang, tkd.id_satuan
			) AS q 
			LEFT JOIN (SELECT id_kasir, COUNT(id_print) as jumlah_cetak FROM tbl_kasir_print GROUP BY id_kasir) AS tkp ON q.id_kasir = tkp.id_kasir
			ORDER BY q.id_kasir, q.tanggal, q.urutan, q.nama_produk
			");
        $no = 1;
		$arr = array();
			foreach ($d_data as $key => $d){
				if($d->telp_pelanggan != '' || $d->telp_pelanggan != null){
				$pelanggan = $d->nama_pelanggan.'/'.$d->telp_pelanggan;
				}else{
				$pelanggan = $d->nama_pelanggan;
				}
				$arr[] = array('NO.'=>$no++,
								'TANGGAL JUAL' 			=> tgl_full($d->tanggal,'0'),
								'NOMER NOTA'			=> $d->no_faktur,
								'KODE PARFUM' 			=> $d->kode_produk,
								'JENIS PARFUM'			=> $d->jenis_produk,
								'NAMA PARFUM'			=> $d->nama_produk,
								'NAMA CUSTOMER'			=> $pelanggan,
								'CARA BAYAR'			=> $d->metodebayar,
								'JUMLAH'				=> $d->jumlah,
								'HARGA'					=> $d->harga,
								'DISKON'				=> $d->potongan,
								'ONGKIR'				=> $d->ongkir,
								'TOTAL'					=> $d->total+$d->ongkir-$d->potongan,
								'KETERANGAN'			=> $d->keterangan,
								'CETAK'					=> $d->jumlah_cetak ?? 0);
			}

    	if(count($arr) > 0){
		$data = $arr;
		}else{
		$data = array("0"=>['NO.'			=> "",
							'TANGGAL JUAL' 	=> "",
							'NOMER NOTA'	=> "",
							'KODE PARFUM' 	=> "",
							'JENIS PARFUM'	=> "",
							'NAMA PARFUM'	=> "",
							'NAMA CUSTOMER'	=> "",
							'CARA BAYAR'	=> "",
							'JUMLAH'		=> "",
							'HARGA'			=> "",
							'DISKON'		=> "",
							'ONGKIR'		=> "",
							'TOTAL'			=> "",
							'KETERANGAN'	=> "",
							'CETAK'			=> ""]);
		}

		return Excel::download(new LaporanViewExport($data), 'Laporan_Penjualan_PerNota_'.$nama_gudang.'_'.$tanggal.'-'.$tanggal2.'.xlsx');
    		break;
    		case '3':
    	if($gudang != "" && $tanggal != ""){
        $where_stokawal = "WHERE t.id_ref_gudang = '".$gudang."' AND t.tanggal < '".$tanggal."'";
        $where_penjualan = "WHERE t.id_ref_gudang = '".$gudang."' AND t.tanggal >= '".$tanggal."' AND t.tanggal <= '".$tanggal2."' AND t.status IN ('J1','J4')";
        }elseif($gudang == "" && $tanggal == ""){
        	$where_stokawal = "WHERE t.id_ref_gudang = '".$gudang."'";
        	$where_penjualan = "WHERE t.id_ref_gudang = '".$gudang."' AND t.status IN ('J1','J4')";
        }else{
        	$where_stokawal = "";
        	$where_penjualan = "WHERE t.status IN ('J1','J4')";
        }

        $d_data = DB::select("SELECT q.*, 
											SUM( q.unit_masuk ) AS jumlah_masuk,
											SUM( q.unit_keluar ) AS jumlah_keluar,
											CASE WHEN SUM( r.jumlah_stokawal )< 0 THEN '0' 
											WHEN SUM( r.jumlah_stokawal ) IS NULL THEN '0' 
											ELSE SUM( r.jumlah_stokawal ) END AS jumlah_stokawal,
											CASE WHEN SUM( q.unit_terjual )<0 THEN '0' 
											WHEN SUM( q.unit_terjual ) IS NULL THEN '0' 
											ELSE SUM( q.unit_terjual ) END AS jumlah_terjual,
											CASE WHEN SUM( (r.jumlah_stokawal) - q.unit_terjual)<0 THEN '0'
											WHEN SUM( (r.jumlah_stokawal) - q.unit_terjual) IS NULL THEN '0'
											ELSE SUM( (r.jumlah_stokawal) - q.unit_terjual) 
											END AS jumlah_terakhir,
											CASE WHEN SUM( q.harga )<0 THEN '0' 
											WHEN SUM( q.harga ) IS NULL THEN '0' 
											ELSE SUM( q.harga ) END AS harga_penjualan
											from (										
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
											t.unit_keluar as unit_terjual,
											tkd.total as harga,
											t.status
										FROM
											tbl_log_stok AS t										
										JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
										JOIN tbl_kasir_detail AS tkd ON t.log_stok_id=tkd.id_log_stok
										$where_penjualan
  										) q
										JOIN (
											SELECT q.id_barang as barang, q.satuan_id as satuan, q.gudang_id as gudang,
											CASE WHEN SUM( q.unit_masuk - q.unit_keluar )< 0 THEN '0' 
											WHEN SUM( q.unit_masuk - q.unit_keluar ) IS NULL THEN '0' 
											ELSE SUM( q.unit_masuk - q.unit_keluar ) END AS jumlah_stokawal
											from (	
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
											'0' as unit_terjual,
											'0' as harga,
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
        $arr = array();
		$no = 1;
			foreach ($d_data as $key => $d){
				$arr[] = array('NO.'=>$no++,
								'KODE BARANG' 			=> $d->barang_kode,
								'NAMA BARANG'			=> $d->barang_nama,
								'STOCK AWAL'			=> $d->jumlah_stokawal,
								'PENJUALAN HARI INI'	=> $d->jumlah_terjual,
								'STOK AKHIR'			=> $d->jumlah_terakhir,
								'HARGA'					=> $d->harga_penjualan);
			}
		if(count($arr) > 0){
		$data = $arr;
		}else{
		$data = array(['NO.'=>$no++,
							'KODE BARANG' 			=> "",
							'NAMA BARANG'			=> "",
							'STOCK AWAL'			=> "",
							'PENJUALAN HARI INI'	=> "",
							'STOK AKHIR'			=> "",
							'HARGA'					=> ""]);
		}

    	return Excel::download(new LaporanViewExport($data), 'Laporan_Penjualan_'.$nama_gudang.'_'.$tanggal.'-'.$tanggal2.'.xlsx');
    			break;
    			case '4':
    	if($gudang != "" && $tanggal != ""){
    		if($barang == 0){
    		$where_tkd = "WHERE tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."'";
	    	}else{
    		$where_tkd = "WHERE tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."' AND tkd.id_barang = '".$barang."' ";
	    	}
        }elseif($gudang != "" && $tanggal == ""){
        	$where_tkd = "WHERE tk.id_gudang = '".$gudang."'";
        }else{
        	$where_tkd = "";
        }

        $d_data = DB::SELECT("SELECT * FROM (
			SELECT tk.id_kasir, tk.tanggal_faktur as tanggal, tk.no_faktur, mp.nama AS nama_pelanggan, mp.telp as telp_pelanggan, tk.keterangan,
			CASE WHEN tk.metodebayar2 IS NOT NULL THEN CONCAT(mm.nama,', ',mm2.nama) ELSE mm.nama END AS metodebayar,
			tb.barang_nama AS nama_produk, tb.barang_kode AS kode_produk, tkd.jumlah, ts.satuan_satuan AS nama_satuan, tkd.harga, tkd.total
			FROM tbl_kasir_detail AS tkd
			JOIN tbl_kasir AS tk ON tkd.id_kasir=tk.id_kasir
			JOIN tbl_barang AS tb ON tkd.id_barang=tb.barang_id
			JOIN tbl_satuan AS ts ON tkd.id_satuan=ts.satuan_id
			JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
			LEFT JOIN m_pelanggan AS mp ON tk.id_pelanggan=mp.id
			LEFT JOIN m_metode AS mm ON tk.metodebayar=mm.id
			LEFT JOIN m_metode AS mm2 ON tk.metodebayar2=mm2.id
			$where_tkd
			) AS q 
			ORDER BY q.id_kasir, q.tanggal, q.nama_produk
			");
        $nama_gudang = DB::table('ref_gudang')->where('id',$gudang)->first()->nama;
        $no = 1;
		$arr = array();
			foreach ($d_data as $key => $d){
				if($d->telp_pelanggan != '' || $d->telp_pelanggan != null){
				$pelanggan = $d->nama_pelanggan.'/'.$d->telp_pelanggan;
				}else{
				$pelanggan = $d->nama_pelanggan;
				}
				$arr[] = array('NO.'=>$no++,
								'TANGGAL JUAL' 			=> tgl_full($d->tanggal,'0'),
								'NOMER NOTA'			=> $d->no_faktur,
								'KODE PARFUM' 			=> $d->kode_produk,
								'NAMA PARFUM'			=> $d->nama_produk,
								'CARA BAYAR'			=> $d->metodebayar,
								'JUMLAH'				=> format_angka($d->jumlah),
								'HARGA'					=> 'Rp. '.format_angka($d->harga),
								'TOTAL'					=> 'Rp. '.format_angka($d->total),
								'KETERANGAN'			=> $d->keterangan);
			}

    	if(count($arr) > 0){
		$data = $arr;
		}else{
		$data = array("0"=>['NO.'			=> "",
							'TANGGAL JUAL' 	=> "",
							'NOMER NOTA'	=> "",
							'KODE PARFUM' 	=> "",
							'NAMA PARFUM'	=> "",
							'CARA BAYAR'	=> "",
							'JUMLAH'		=> "",
							'HARGA'			=> "",
							'TOTAL'			=> "",
							'KETERANGAN'	=> ""]);
		}
		return Excel::download(new LaporanViewExport($data), 'Laporan_Penjualan_PerNota_'.$nama_gudang.'_'.$tanggal.'-'.$tanggal2.'.xlsx');
    			break;
    			case '5':
    	if($gudang != "" && $tanggal != ""){
    		$where_tkd = "WHERE tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."' AND tk.carabayar='3'";
        }elseif($gudang != "" && $tanggal == ""){
        	$where_tkd = "WHERE tk.id_gudang = '".$gudang."' AND tk.carabayar='3'";
        }else{
        	$where_tkd = "WHERE tk.carabayar='3'";
        }
        //DB::enableQueryLog();
        $d_data = DB::SELECT("
			SELECT tk.id_kasir, tk.tanggal_faktur as tanggal, tk.tanggal_bayar as tanggal_bayar, tk.no_faktur, mp.nama AS nama_pelanggan, mp.telp as telp_pelanggan, tk.keterangan,
			CASE WHEN tk.metodebayar2 IS NOT NULL THEN CONCAT(mm.nama,', ',mm2.nama) ELSE mm.nama END AS metodebayar,
			(tk.total_tagihan-(tk.total_potongan+tk.ongkos_kirim)) as total,
			CASE WHEN tk.status=2 THEN 'LUNAS' ELSE 'BELUM BAYAR' END AS status
			FROM tbl_kasir AS tk 
			JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
			LEFT JOIN m_pelanggan AS mp ON tk.id_pelanggan=mp.id
			LEFT JOIN m_metode AS mm ON tk.metodebayar=mm.id
			LEFT JOIN m_metode AS mm2 ON tk.metodebayar2=mm2.id
			$where_tkd
			ORDER BY tk.tanggal_faktur
			");
        $nama_gudang = DB::table('ref_gudang')->where('id',$gudang)->first()->nama;
        //dd(DB::getQueryLog());
        //print_r($d_data);exit();
        $no = 1;
		$arr = array();
			foreach ($d_data as $key => $d){
				if($d->telp_pelanggan != '' || $d->telp_pelanggan != null){
				$pelanggan = $d->nama_pelanggan.'/'.$d->telp_pelanggan;
				}else{
				$pelanggan = $d->nama_pelanggan;
				}
				$arr[] = array('NO.'=>$no++,
								'TANGGAL JUAL' 			=> tgl_full($d->tanggal,'0'),
								'TANGGAL BAYAR' 		=> tgl_full($d->tanggal_bayar,'0'),
								'NOMER NOTA'			=> $d->no_faktur,
								'NAMA CUSTOMER'			=> $pelanggan,
								'TOTAL'					=> 'Rp. '.format_angka($d->total),
								'STATUS'				=> $d->status,
								'KETERANGAN'			=> $d->keterangan);
			}

    	if(count($arr) > 0){
		$data = $arr;
		}else{
		$data = array("0"=>['NO.'			=> "",
							'TANGGAL JUAL' 	=> "",
							'TANGGAL Bayar' => "",
							'NOMER NOTA'	=> "",
							'NAMA CUSTOMER'	=> "",
							'TOTAL'			=> "",
							'STATUS'		=> "",
							'KETERANGAN'	=> ""]);
		}
		return Excel::download(new LaporanViewExport($data), 'Laporan_Penjualan_PerNota_'.$nama_gudang.'_'.$tanggal.'-'.$tanggal2.'.xlsx');
    			break;
    		
    		default:
    			# code...
    			break;
    	}
    	
    }

    public function hasil_penjualan($d_gudang,$d_tanggalAwal, $d_tanggalAkhir, $d_kategori, $d_barang){
    	ini_set('max_execution_time', '0');
    	$gudang 	= $d_gudang;
    	$tanggal 	= tgl_full($d_tanggalAwal,'99');
    	$tanggal2 	= tgl_full($d_tanggalAkhir,'99');  
    	$kategori 	= $d_kategori;
    	$barang 	= $d_barang;

    	switch ($kategori) {
    		case '1':
    	if($gudang != "" && $tanggal != ""){
    		$where = "WHERE k.id_gudang = '".$gudang."' AND k.tanggal_faktur >= '".$tanggal."' AND k.tanggal_faktur <= '".$tanggal2."'";
    		$where_ongkir = "WHERE k.id_gudang = '".$gudang."' AND k.tanggal_faktur >= '".$tanggal."' AND k.tanggal_faktur <= '".$tanggal2."' AND k.ongkos_kirim > 0";
        }elseif($gudang != "" && $tanggal == ""){
        	$where = "WHERE k.id_gudang = '".$gudang."'";
        	$where_ongkir = "WHERE k.id_gudang = '".$gudang."' AND k.ongkos_kirim > 0";
        }else{
        	$where = "";
        	$where_ongkir = "WHERE k.ongkos_kirim > 0";
        }

        $d_data = DB::SELECT("SELECT p.tanggal, sum(p.tunai) as tunai, sum(p.debit_bca) as debit_bca, 
        	sum(p.debit_mandiri) as debit_mandiri, sum(p.kredit_bca) as kredit_bca, sum(p.kredit_mandiri) as kredit_mandiri, 
        	sum(p.transfer_bca) as transfer_bca, sum(p.transfer_mandiri) as transfer_mandiri, sum(p.ovo) as ovo, 
        	sum(p.hutang) as hutang, sum(p.ongkos_kirim) as ongkos_kirim,
        	sum(p.tunai+p.debit_bca+p.debit_mandiri+p.kredit_bca+p.kredit_mandiri+p.transfer_bca+p.transfer_mandiri+p.ovo+p.hutang) as total,
        	sum(p.tunai+p.debit_bca+p.debit_mandiri+p.kredit_bca+p.kredit_mandiri+p.transfer_bca+p.transfer_mandiri+p.ovo+p.hutang+p.ongkos_kirim) as total_ongkir
		FROM (
		SELECT su.tanggal, 
			CASE WHEN su.metodebayar = 1 THEN su.jumlah ELSE 0 END AS tunai,
			CASE WHEN su.metodebayar = 2 THEN su.jumlah ELSE 0 END AS debit_bca,
			CASE WHEN su.metodebayar = 3 THEN su.jumlah ELSE 0 END AS debit_mandiri,
			CASE WHEN su.metodebayar = 4 THEN su.jumlah ELSE 0 END AS kredit_bca,
			CASE WHEN su.metodebayar = 9 THEN su.jumlah ELSE 0 END AS kredit_mandiri,
			CASE WHEN su.metodebayar = 5 THEN su.jumlah ELSE 0 END AS transfer_bca,
			CASE WHEN su.metodebayar = 8 THEN su.jumlah ELSE 0 END AS transfer_mandiri,
			CASE WHEN su.metodebayar = 6 THEN su.jumlah ELSE 0 END AS ovo,
			CASE WHEN su.metodebayar = 7 THEN su.jumlah ELSE 0 END AS hutang,
			CASE WHEN su.metodebayar = 99 THEN su.jumlah ELSE 0 END AS ongkos_kirim
		FROM (
		SELECT q.no_faktur, q.tanggal, q.metodebayar, q.metode, SUM(q.jumlah) as jumlah FROM (
        		SELECT
					k.id_kasir,
					k.tanggal_faktur as tanggal,
					k.no_faktur as no_faktur,
					k.metodebayar,
					m.nama AS metode,
					k.created_at,
					CASE WHEN k.metodebayar2 IS NOT NULL 
					THEN 0 ELSE SUM( k.total_tagihan - k.total_potongan ) 
					END AS jumlah
					FROM tbl_kasir AS k
					LEFT JOIN m_metode AS m ON k.metodebayar = m.id
					$where
					GROUP BY
					k.tanggal_faktur, k.metodebayar, k.metodebayar2, m.nama
				UNION
				SELECT
					k.id_kasir,
					k.tanggal_faktur as tanggal,
					k.no_faktur as no_faktur,
          			k.metodebayar,
          			m.nama AS metode,
					k.created_at,
          			CASE WHEN k.metodebayar2 IS NOT NULL 
          			THEN SUM(k.total_metodebayar) ELSE 0 
          			END AS jumlah
          			FROM tbl_kasir AS k
          			LEFT JOIN m_metode AS m ON k.metodebayar = m.id
          			$where
					GROUP BY
					k.tanggal_faktur, k.metodebayar, k.metodebayar2, m.nama
				UNION 
				SELECT
					k.id_kasir,
					k.tanggal_faktur as tanggal,
					k.no_faktur as no_faktur,
          		k.metodebayar2 as metodebayar,
          		m.nama AS metode,k.created_at,
          		CASE WHEN k.metodebayar2 IS NOT NULL 
          		THEN SUM(k.total_metodebayar2) ELSE 0 
          		END AS jumlah
          		FROM tbl_kasir AS k
          		JOIN m_metode AS m ON k.metodebayar2 = m.id
          		$where
          		GROUP BY
				k.tanggal_faktur, k.metodebayar, k.metodebayar2, m.nama
				UNION ALL
				SELECT 
					k.id_kasir,
					k.tanggal_faktur as tanggal,
					k.no_faktur as no_faktur,
					'99' AS urutan, 
					'Ongkos Kirim' AS metode,
					k.created_at,
					SUM(k.ongkos_kirim) AS jumlah 
				FROM tbl_kasir AS k 
				$where_ongkir
				AND k.ongkos_kirim > 0
				GROUP BY 
				k.tanggal_faktur, k.ongkos_kirim
				) AS q 
					GROUP BY
					q.tanggal, q.metodebayar
			) AS su
		) AS p	
		group by p.tanggal
				");
        $no = 1;
		$arr = array();
			foreach ($d_data as $key => $d){
				$arr[] = array('NO.'=>$no++,
								'TANGGAL' 				=> tgl_full($d->tanggal,'0'),
								'TUNAI'					=> 'Rp. '.format_angka($d->tunai),
								'DEBIT BCA'				=> 'Rp. '.format_angka($d->debit_bca),
								'DEBIT MANDIRI'			=> 'Rp. '.format_angka($d->debit_mandiri),
								'KREDIT BCA'			=> 'Rp. '.format_angka($d->kredit_bca),
								'KREDIT MANDIRI'		=> 'Rp. '.format_angka($d->kredit_mandiri),
								'TRANSFER BCA'			=> 'Rp. '.format_angka($d->transfer_bca),
								'TRANSFER MANDIRI'		=> 'Rp. '.format_angka($d->transfer_mandiri),
								'OVO'					=> 'Rp. '.format_angka($d->ovo),
								'HUTANG'				=> 'Rp. '.format_angka($d->hutang),
								'ONGKOS KIRIM'			=> 'Rp. '.format_angka($d->ongkos_kirim),
								'TOTAL + ONGKOS KIRIM'	=> 'Rp. '.format_angka($d->total_ongkir),
								'TOTAL'					=> 'Rp. '.format_angka($d->total));
			}

    	if(count($arr) > 0){
		$data = $arr;
		}else{
		$data = array("0"=>['NO.'					=> '',
							'TANGGAL' 				=> '',
							'TUNAI'					=> '',
							'DEBIT BCA'				=> '',
							'DEBIT MANDIRI'			=> '',
							'KREDIT BCA'			=> '',
							'KREDIT MANDIRI'		=> '',
							'TRANSFER BCA'			=> '',
							'TRANSFER MANDIRI'		=> '',
							'OVO'					=> '',
							'HUTANG'				=> '',
							'ONGKOS KIRIM'			=> '',
							'TOTAL + ONGKOS KIRIM'	=> '',
							'TOTAL'					=> '']);
		}

		return view('admin.laporan.hasil_laporan',compact('data'));

    		break;
    		case '2':

    	/*if($gudang != "" && $tanggal != ""){
    		$where = "WHERE k.id_gudang = '".$gudang."' AND k.tanggal_faktur >= '".$tanggal."' AND k.tanggal_faktur <= '".$tanggal2."'";
    		$where_ongkir = "WHERE k.id_gudang = '".$gudang."' AND k.tanggal_faktur >= '".$tanggal."' AND k.tanggal_faktur <= '".$tanggal2."' AND k.ongkos_kirim > 0";
    		$where_tk = "WHERE tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."'";
    		$where_tkd = "WHERE tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."' AND tkd.id_detail_kasir_produk = 0";
        }elseif($gudang != "" && $tanggal == ""){
        	$where = "WHERE k.id_gudang = '".$gudang."'";
        	$where_ongkir = "WHERE k.id_gudang = '".$gudang."' AND k.ongkos_kirim > 0";
        	$where_tk = "WHERE tk.id_gudang = '".$gudang."'";
        	$where_tkd = "WHERE tk.id_gudang = '".$gudang."' AND tkd.id_detail_kasir_produk = 0";
        }else{
        	$where = "";
        	$where_ongkir = "WHERE k.ongkos_kirim > 0";
        	$where_tk = "";
        	$where_tkd = "WHERE tkd.id_detail_kasir_produk = 0";
        }*/
        /*$d_data = DB::SELECT("SELECT q.tanggal, q.metodebayar, q.metode, SUM(q.jumlah) as jumlah FROM (
        		SELECT
					k.tanggal_faktur as tanggal,
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
                k.tanggal_faktur, k.metodebayar
				UNION
				SELECT
					k.tanggal_faktur as tanggal,
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
                k.tanggal_faktur, k.metodebayar
				UNION 
				SELECT
					k.tanggal_faktur as tanggal,
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
                k.tanggal_faktur, k.metodebayar2
				UNION ALL
				SELECT 
					k.tanggal_faktur as tanggal,
					'99' AS urutan, 
					'Ongkos Kirim' AS metode,
					k.created_at,
					SUM(k.ongkos_kirim) AS jumlah 
				FROM tbl_kasir AS k 
				$where_ongkir
				GROUP BY 
				k.tanggal_faktur, k.ongkos_kirim
				) AS q 
					GROUP BY
					q.tanggal, q.metodebayar
                ");*/
        /*$d_data = DB::SELECT("SELECT q.no_faktur, q.tanggal, q.metodebayar, q.metode, SUM(q.jumlah) as jumlah FROM (
        		SELECT
					k.id_kasir,
					k.tanggal_faktur as tanggal,
					k.no_faktur as no_faktur,
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
                k.id_kasir, k.metodebayar
				UNION
				SELECT
					k.id_kasir,
					k.tanggal_faktur as tanggal,
					k.no_faktur as no_faktur,
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
                k.id_kasir, k.metodebayar
				UNION 
				SELECT
					k.id_kasir,
					k.tanggal_faktur as tanggal,
					k.no_faktur as no_faktur,
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
               k.id_kasir, k.metodebayar2
				UNION ALL
				SELECT 
					k.id_kasir,
					k.tanggal_faktur as tanggal,
					k.no_faktur as no_faktur,
					'99' AS urutan, 
					'Ongkos Kirim' AS metode,
					k.created_at,
					SUM(k.ongkos_kirim) AS jumlah 
				FROM tbl_kasir AS k 
				$where_ongkir
				GROUP BY 
				k.id_kasir, k.ongkos_kirim
				) AS q 
					GROUP BY
					q.id_kasir, q.metodebayar
                ");
        */
        //DB::enableQueryLog();

        $add_where = " WHERE ";
        $where_tkd[] = "( tkd.id_detail_kasir_produk = 0 OR tkd.id_detail_kasir_produk IS NULL) ";
        if(isset($gudang)){
        	if($gudang != ''){
        		$where_tk[] = "tk.id_gudang = '".$gudang."'";
        		$where_tkd[] = "tk.id_gudang = '".$gudang."'";
        	}
        }

        if(isset($tanggal)){
        	if($tanggal != ''){
        		$where_tk[] = " tk.tanggal_faktur >= '".$tanggal."'";
        		$where_tkd[] = " tk.tanggal_faktur >= '".$tanggal."'";
        	}
        }

        if(isset($tanggal2)){
        	if($tanggal2 != ''){
        		$where_tk[] = " tk.tanggal_faktur <= '".$tanggal2."'";
        		$where_tkd[] = " tk.tanggal_faktur <= '".$tanggal2."'";
        	}
        }

        $where_tk = $add_where.implode(" AND ", $where_tk);
        $where_tkd = $add_where.implode(" AND ", $where_tkd);

        $d_data = DB::SELECT("SELECT q.*, tkp.jumlah_cetak FROM (
			SELECT tk.id_kasir, tk.tanggal_faktur as tanggal, tk.no_faktur, mpe.nama AS nama_pelanggan, mpe.telp as telp_pelanggan, tk.keterangan, tk.total_potongan as potongan, tk.ongkos_kirim as ongkir,
			CASE WHEN tk.metodebayar2 IS NOT NULL THEN CONCAT(mm.nama,', ',mm2.nama) ELSE mm.nama END AS metodebayar,
			mp.nama AS nama_produk, mp.kode_produk AS kode_produk, SUM(tkp.jumlah) as jumlah, ts.satuan_satuan AS nama_satuan, tkp.harga, tkp.total, '1' AS urutan, CASE WHEN SUBSTRING(mp.kode_produk,5,1) = 'R' THEN SUBSTRING(mp.kode_produk,1,5) WHEN SUBSTRING(mp.kode_produk,5,2) = 'aR' THEN SUBSTRING(mp.kode_produk,1,6) ELSE SUBSTRING(mp.kode_produk,1,4) END AS jenis_produk
			FROM tbl_kasir_detail_produk AS tkp
			JOIN tbl_kasir AS tk ON tkp.id_kasir=tk.id_kasir
			JOIN m_produk AS mp ON tkp.id_produk=mp.id
			JOIN tbl_satuan AS ts ON tkp.id_satuan=ts.satuan_id
			JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
			LEFT JOIN m_pelanggan AS mpe ON tk.id_pelanggan=mpe.id
			LEFT JOIN m_metode AS mm ON tk.metodebayar=mm.id
			LEFT JOIN m_metode AS mm2 ON tk.metodebayar2=mm2.id
			$where_tk
			GROUP BY tk.id_kasir, tk.tanggal, tk.no_faktur, tk.id_pelanggan, tkp.id_produk, tkp.id_satuan
			UNION

			SELECT tk.id_kasir, tk.tanggal_faktur as tanggal, tk.no_faktur, mp.nama AS nama_pelanggan, mp.telp as telp_pelanggan, tk.keterangan, tk.total_potongan as potongan, tk.ongkos_kirim as ongkir,
			CASE WHEN tk.metodebayar2 IS NOT NULL THEN CONCAT(mm.nama,', ',mm2.nama) ELSE mm.nama END AS metodebayar,
			tb.barang_nama AS nama_produk, tb.barang_kode as kode_produk, SUM(tkd.jumlah) as jumlah, ts.satuan_satuan AS nama_satuan, tkd.harga, tkd.total, '2' AS urutan, tb.barang_kode as jenis_produk
			FROM tbl_kasir_detail AS tkd
			JOIN tbl_kasir AS tk ON tkd.id_kasir=tk.id_kasir
			JOIN tbl_barang AS tb ON tkd.id_barang=tb.barang_id
			JOIN tbl_satuan AS ts ON tkd.id_satuan=ts.satuan_id
			JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
			LEFT JOIN m_pelanggan AS mp ON tk.id_pelanggan=mp.id
			LEFT JOIN m_metode AS mm ON tk.metodebayar=mm.id
			LEFT JOIN m_metode AS mm2 ON tk.metodebayar2=mm2.id
			$where_tkd
			GROUP BY tk.id_kasir, tk.tanggal, tk.no_faktur, tk.id_pelanggan, tkd.id_barang, tkd.id_satuan
			) AS q 
			LEFT JOIN (SELECT id_kasir, COUNT(id_print) as jumlah_cetak FROM tbl_kasir_print GROUP BY id_kasir) AS tkp ON q.id_kasir = tkp.id_kasir
			ORDER BY q.id_kasir, q.tanggal, q.urutan, q.nama_produk
			");
        //dd(DB::getQueryLog());
        //print_r($d_data);exit();
        $no = 1;
		$arr = array();
			foreach ($d_data as $key => $d){
				if($d->telp_pelanggan != '' || $d->telp_pelanggan != null){
				$pelanggan = $d->nama_pelanggan.'/'.$d->telp_pelanggan;
				}else{
				$pelanggan = $d->nama_pelanggan;
				}
				$arr[] = array('NO.'=>$no++,
								'TANGGAL JUAL' 			=> tgl_full($d->tanggal,'0'),
								'NOMER NOTA'			=> $d->no_faktur,
								'KODE PARFUM' 			=> $d->kode_produk,
								'JENIS PARFUM'			=> $d->jenis_produk,
								'NAMA PARFUM'			=> $d->nama_produk,
								'NAMA CUSTOMER'			=> $pelanggan,
								'CARA BAYAR'			=> $d->metodebayar,
								'JUMLAH'				=> format_angka($d->jumlah),
								'HARGA'					=> 'Rp. '.format_angka($d->harga),
								'DISKON'				=> 'Rp. '.format_angka($d->potongan),
								'ONGKIR'				=> 'Rp. '.format_angka($d->ongkir),
								'TOTAL'					=> 'Rp. '.format_angka($d->total+$d->ongkir-$d->potongan),
								'KETERANGAN'			=> $d->keterangan,
								'CETAK'					=> $d->jumlah_cetak ?? 0);
			}

    	if(count($arr) > 0){
		$data = $arr;
		}else{
		$data = array("0"=>['NO.'			=> "",
							'TANGGAL JUAL' 	=> "",
							'NOMER NOTA'	=> "",
							'KODE PARFUM' 	=> "",
							'JENIS PARFUM'	=> "",
							'NAMA PARFUM'	=> "",
							'NAMA CUSTOMER'	=> "",
							'CARA BAYAR'	=> "",
							'JUMLAH'		=> "",
							'HARGA'			=> "",
							'DISKON'		=> "",
							'ONGKIR'		=> "",
							'TOTAL'			=> "",
							'KETERANGAN'	=> "",
							'CETAK'			=> ""]);
		}

		return view('admin.laporan.hasil_laporan',compact('data'));
    		break;
    		case '3':
    			
    	if($gudang != "" && $tanggal != ""){
        $where_stokawal = "WHERE t.id_ref_gudang = '".$gudang."' AND t.tanggal < '".$tanggal."'";
        $where_penjualan = "WHERE t.id_ref_gudang = '".$gudang."' AND t.tanggal >= '".$tanggal."' AND t.tanggal <= '".$tanggal2."' AND t.status IN ('J1','J4')";
        }elseif($gudang == "" && $tanggal == ""){
        	$where_stokawal = "WHERE t.id_ref_gudang = '".$gudang."'";
        	$where_penjualan = "WHERE t.id_ref_gudang = '".$gudang."' AND t.status IN ('J1','J4')";
        }else{
        	$where_stokawal = "";
        	$where_penjualan = "WHERE t.status IN ('J1','J4')";
        }

        $d_data = DB::select("SELECT q.*, 
								SUM( q.unit_masuk ) AS jumlah_masuk,
								SUM( q.unit_keluar ) AS jumlah_keluar,
								CASE WHEN SUM( r.jumlah_stokawal )< 0 THEN '0' 
								WHEN SUM( r.jumlah_stokawal ) IS NULL THEN '0' 
								ELSE SUM( r.jumlah_stokawal ) END AS jumlah_stokawal,
								CASE WHEN SUM( q.unit_terjual )<0 THEN '0' 
								WHEN SUM( q.unit_terjual ) IS NULL THEN '0' 
								ELSE SUM( q.unit_terjual ) END AS jumlah_terjual,
								CASE WHEN SUM( (r.jumlah_stokawal) - q.unit_terjual)<0 THEN '0'
								WHEN SUM( (r.jumlah_stokawal) - q.unit_terjual) IS NULL THEN '0'
								ELSE SUM( (r.jumlah_stokawal) - q.unit_terjual) 
								END AS jumlah_terakhir,
								CASE WHEN SUM( q.harga )<0 THEN '0' 
								WHEN SUM( q.harga ) IS NULL THEN '0' 
								ELSE SUM( q.harga ) END AS harga_penjualan
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
											t.unit_keluar as unit_terjual,
											tkd.total as harga,
											t.status
										FROM
											tbl_log_stok AS t										
										JOIN tbl_barang AS b ON t.id_barang = b.barang_id
										JOIN tbl_satuan AS s ON t.id_satuan = s.satuan_id 
										lEFT JOIN ref_gudang AS g ON t.id_ref_gudang = g.id
										JOIN tbl_kasir_detail AS tkd ON t.log_stok_id=tkd.id_log_stok
										$where_penjualan
  										) q
										JOIN (
											SELECT q.id_barang as barang, q.satuan_id as satuan, q.gudang_id as gudang,
											CASE WHEN SUM( q.unit_masuk - q.unit_keluar )< 0 THEN '0' 
											WHEN SUM( q.unit_masuk - q.unit_keluar ) IS NULL THEN '0' 
											ELSE SUM( q.unit_masuk - q.unit_keluar ) END AS jumlah_stokawal
											from (	
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
											'0' as unit_terjual,
											'0' as harga,
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
										q.barang_nama ASC ");
        $nama_gudang = DB::table('ref_gudang')->where('id',$gudang)->first()->nama;

		$no = 1;
		$arr = array();
			foreach ($d_data as $key => $d){
				$arr[] = array('NO.'=>$no++,
								'KODE BARANG' 			=> $d->barang_kode,
								'NAMA BARANG'			=> $d->barang_nama,
								'STOCK AWAL'			=> format_angka($d->jumlah_stokawal).' '.$d->satuan_satuan,
								'PENJUALAN HARI INI'	=> format_angka($d->jumlah_terjual).' '.$d->satuan_satuan,
								'STOK AKHIR'			=> format_angka($d->jumlah_terakhir).' '.$d->satuan_satuan,
								'HARGA'					=> "Rp. ".format_angka($d->harga_penjualan));
			}

    	if(count($arr) > 0){
		$data = $arr;
		}else{
		$data = array("0"=>['NO.'=>$no++,
							'KODE BARANG' 			=> "",
							'NAMA BARANG'			=> "",
							'STOCK AWAL'			=> "",
							'PENJUALAN HARI INI'	=> "",
							'STOK AKHIR'			=> "",
							'HARGA'					=> ""]);
		}
		return view('admin.laporan.hasil_laporan',compact('data'));

    			break;
    			case '4':

    	if($gudang != "" && $tanggal != ""){
    		if($barang == 0){
    		$where_tkd = "WHERE tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."'";
	    	}else{
    		$where_tkd = "WHERE tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."' AND tkd.id_barang = '".$barang."' ";
	    	}
        }elseif($gudang != "" && $tanggal == ""){
        	$where_tkd = "WHERE tk.id_gudang = '".$gudang."'";
        }else{
        	$where_tkd = "";
        }

        $d_data = DB::SELECT("SELECT * FROM (
			SELECT tk.id_kasir, tk.tanggal_faktur as tanggal, tk.no_faktur, mp.nama AS nama_pelanggan, mp.telp as telp_pelanggan, tk.keterangan,
			CASE WHEN tk.metodebayar2 IS NOT NULL THEN CONCAT(mm.nama,', ',mm2.nama) ELSE mm.nama END AS metodebayar,
			tb.barang_nama AS nama_produk, tb.barang_kode as kode_produk, tkd.jumlah, ts.satuan_satuan AS nama_satuan, tkd.harga, tkd.total
			FROM tbl_kasir_detail AS tkd
			JOIN tbl_kasir AS tk ON tkd.id_kasir=tk.id_kasir
			JOIN tbl_barang AS tb ON tkd.id_barang=tb.barang_id
			JOIN tbl_satuan AS ts ON tkd.id_satuan=ts.satuan_id
			JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
			LEFT JOIN m_pelanggan AS mp ON tk.id_pelanggan=mp.id
			LEFT JOIN m_metode AS mm ON tk.metodebayar=mm.id
			LEFT JOIN m_metode AS mm2 ON tk.metodebayar2=mm2.id
			$where_tkd
			) AS q 
			ORDER BY q.id_kasir, q.tanggal, q.nama_produk
			");
        $nama_gudang = DB::table('ref_gudang')->where('id',$gudang)->first()->nama;
        $no = 1;
		$arr = array();
			foreach ($d_data as $key => $d){
				if($d->telp_pelanggan != '' || $d->telp_pelanggan != null){
				$pelanggan = $d->nama_pelanggan.'/'.$d->telp_pelanggan;
				}else{
				$pelanggan = $d->nama_pelanggan;
				}
				$arr[] = array('NO.'=>$no++,
								'TANGGAL JUAL' 			=> tgl_full($d->tanggal,'0'),
								'NOMER NOTA'			=> $d->no_faktur,
								'KODE PARFUM' 			=> $d->kode_produk,
								'NAMA PARFUM'			=> $d->nama_produk,
								'CARA BAYAR'			=> $d->metodebayar,
								'JUMLAH'				=> format_angka($d->jumlah),
								'HARGA'					=> 'Rp. '.format_angka($d->harga),
								'TOTAL'					=> 'Rp. '.format_angka($d->total),
								'KETERANGAN'			=> $d->keterangan);
			}

    	if(count($arr) > 0){
		$data = $arr;
		}else{
		$data = array("0"=>['NO.'			=> "",
							'TANGGAL JUAL' 	=> "",
							'NOMER NOTA'	=> "",
							'NAMA PARFUM'	=> "",
							'CARA BAYAR'	=> "",
							'JUMLAH'		=> "",
							'HARGA'			=> "",
							'TOTAL'			=> "",
							'KETERANGAN'	=> ""]);
		}

		return view('admin.laporan.hasil_laporan',compact('data'));
    			break;
    			case '5':
    	if($gudang != "" && $tanggal != ""){
    		$where_tkd = "WHERE tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur >= '".$tanggal."' AND tk.tanggal_faktur <= '".$tanggal2."' AND tk.carabayar='3'";
        }elseif($gudang != "" && $tanggal == ""){
        	$where_tkd = "WHERE tk.id_gudang = '".$gudang."' AND tk.carabayar='3'";
        }else{
        	$where_tkd = "WHERE tk.carabayar='3'";
        }
        //DB::enableQueryLog();
        $d_data = DB::SELECT("
			SELECT tk.id_kasir, tk.tanggal_faktur as tanggal, tk.tanggal_bayar as tanggal_bayar, tk.no_faktur, mp.nama AS nama_pelanggan, mp.telp as telp_pelanggan, tk.keterangan,
			CASE WHEN tk.metodebayar2 IS NOT NULL THEN CONCAT(mm.nama,', ',mm2.nama) ELSE mm.nama END AS metodebayar,
			(tk.total_tagihan-(tk.total_potongan+tk.ongkos_kirim)) as total,
			CASE WHEN tk.status=2 THEN 'LUNAS' ELSE 'BELUM BAYAR' END AS status
			FROM tbl_kasir AS tk 
			JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
			LEFT JOIN m_pelanggan AS mp ON tk.id_pelanggan=mp.id
			LEFT JOIN m_metode AS mm ON tk.metodebayar=mm.id
			LEFT JOIN m_metode AS mm2 ON tk.metodebayar2=mm2.id
			$where_tkd
			ORDER BY tk.tanggal_faktur
			");
        //dd(DB::getQueryLog());
        //print_r($d_data);exit();
        $no = 1;
		$arr = array();
			foreach ($d_data as $key => $d){
				if($d->telp_pelanggan != '' || $d->telp_pelanggan != null){
				$pelanggan = $d->nama_pelanggan.'/'.$d->telp_pelanggan;
				}else{
				$pelanggan = $d->nama_pelanggan;
				}
				$arr[] = array('NO.'=>$no++,
								'TANGGAL JUAL' 			=> tgl_full($d->tanggal,'0'),
								'TANGGAL BAYAR' 		=> tgl_full($d->tanggal_bayar,'0'),
								'NOMER NOTA'			=> $d->no_faktur,
								'NAMA CUSTOMER'			=> $pelanggan,
								'TOTAL'					=> 'Rp. '.format_angka($d->total),
								'STATUS'				=> $d->status,
								'KETERANGAN'			=> $d->keterangan);
			}

    	if(count($arr) > 0){
		$data = $arr;
		}else{
		$data = array("0"=>['NO.'			=> "",
							'TANGGAL JUAL' 	=> "",
							'TANGGAL BAYAR' => "",
							'NOMER NOTA'	=> "",
							'NAMA CUSTOMER'	=> "",
							'TOTAL'			=> "",
							'STATUS'		=> "",
							'KETERANGAN'	=> ""]);
		}

		return view('admin.laporan.hasil_laporan',compact('data'));
    			break;
    		
    		default:
    			# code...
    			break;
    	}

    	
		// print_r($data);exit();
		
    }

    
}
