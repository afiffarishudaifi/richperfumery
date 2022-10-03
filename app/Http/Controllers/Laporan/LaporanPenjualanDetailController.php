<?php

namespace App\Http\Controllers\Laporan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\Exports\LaporanViewExport;
use App\Exports\LaporanPenjualanExcelExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanPenjualanDetailController extends Controller
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
	    return view('admin.laporan.index_laporan_penjualan_detail')->with('data',$data);
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

        $d_gudang = DB::table('ref_gudang')->where('id',$gudang)->select(DB::Raw('id as id_gudang,nama as nama_gudang'))
        			->first();
        trigger_log(NULL, "Cetak Laporan Penjualan Detail PDF", 7);
    	switch ($kategori) {
    		case '2':
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
	    $data_d =  DB::SELECT("SELECT * FROM (
			SELECT tk.id_kasir, tk.tanggal_faktur as tanggal, tk.no_faktur, mpe.nama AS nama_pelanggan, mpe.telp as telp_pelanggan,
			CASE WHEN tk.metodebayar2 IS NOT NULL THEN CONCAT(mm.nama,', ',mm2.nama) ELSE mm.nama END AS metodebayar,
			mp.nama AS nama_produk, mp.id AS id_produk, tkp.jumlah, ts.satuan_satuan AS nama_satuan, tkp.harga, tkp.total, '1' AS urutan, 
			tk.keterangan,tk.total_potongan as potongan, tk.ongkos_kirim as ongkir
			FROM tbl_kasir_detail_produk AS tkp
			JOIN tbl_kasir AS tk ON tkp.id_kasir=tk.id_kasir
			JOIN m_produk AS mp ON tkp.id_produk=mp.id
			JOIN tbl_satuan AS ts ON tkp.id_satuan=ts.satuan_id
			JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
			LEFT JOIN m_pelanggan AS mpe ON tk.id_pelanggan=mpe.id
			LEFT JOIN m_metode AS mm ON tk.metodebayar=mm.id
			LEFT JOIN m_metode AS mm2 ON tk.metodebayar2=mm2.id
			$where_tk

			UNION

			SELECT tk.id_kasir, tk.tanggal_faktur as tanggal, tk.no_faktur, mp.nama AS nama_pelanggan, mp.telp as telp_pelanggan,
			CASE WHEN tk.metodebayar2 IS NOT NULL THEN CONCAT(mm.nama,', ',mm2.nama) ELSE mm.nama END AS metodebayar,
			tb.barang_nama AS nama_produk, tb.barang_id as id_produk, tkd.jumlah, ts.satuan_satuan AS nama_satuan, tkd.harga, tkd.total, 
			'2' AS urutan, tk.keterangan, tk.total_potongan as potongan, tk.ongkos_kirim as ongkir
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
			ORDER BY q.id_kasir, q.tanggal, q.urutan, q.nama_produk
			");


	    $data['tanggal_awal'] = tgl_full($request->tanggalAwal,'');
	    $data['tanggal_akhir'] = tgl_full($request->tanggalAkhir,'');
	    $data['hari']    = tgl_full($request->tanggal,'hari');
	    $data['kasir']   = Auth::user()->name;
		$data['gudang']	 = $d_gudang->nama_gudang;
        $data['data'] = [];


		foreach($data_d as $d){

			$detail =  DB::table("m_detail_produk")->where('id_produk',$d->id_produk)->get();

			if($d->telp_pelanggan != '' || $d->telp_pelanggan != null){
				$pelanggan = $d->nama_pelanggan.'/'.$d->telp_pelanggan;
				}else{
				$pelanggan = $d->nama_pelanggan;
				}
			
				
				$data['data'][$d->id_produk] = array(
								'tgl_jual' 			=> tgl_full($d->tanggal,'0'),
								'nomer_nota'			=> $d->no_faktur,
								'nama_parfum'			=> $d->nama_produk,
								'nama_customer'			=> $pelanggan,
								'jumlah'				=> $d->jumlah,
								'ket'					=> $d->keterangan,
								'urutan'				=> $d->urutan
							);

			if($d->urutan==1){
				foreach($detail as $e){
					$barang =  DB::table("tbl_barang")->where('barang_id',$e->id_barang)->get();
				
					foreach($barang as $key =>$value){
						$data['data'][$d->id_produk][$key][] = array(
							'nama_barang' => $value->barang_nama,
							'volume_barang' => $e->jumlah * $d->jumlah
							
						);
					}
				}
			}
		}

				
	    $html = view('admin.laporan.cetak_laporan_penjualan_detail_nota')->with('data',$data);
	    return response($html)->header('Content-Type', 'application/pdf');
    		break;
    		default:
    			# code...
    			break;
    	}
    	
      	
    }

	public function excel_penjualan($d_gudang,$d_tanggalAwal, $d_tanggalAkhir, $d_kategori){
		ini_set('max_execution_time', '0');
		$gudang 	= $d_gudang;
    	$tanggal 	= tgl_full($d_tanggalAwal,'99');
    	$tanggal2 	= tgl_full($d_tanggalAkhir,'99');
    	$kategori 	= $d_kategori;

		$nama_gudang = DB::table('ref_gudang')->where('id',$gudang)->first()->nama;
		trigger_log(NULL, "Cetak Laporan Penjualan Detail Per Nota Excel", 7);
    	switch ($kategori) {
			case '2':
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
		
				$d_data = DB::SELECT("SELECT * FROM (
			SELECT tk.id_kasir, tk.tanggal_faktur as tanggal, tk.no_faktur, mpe.nama AS nama_pelanggan, mpe.telp as telp_pelanggan,
			CASE WHEN tk.metodebayar2 IS NOT NULL THEN CONCAT(mm.nama,', ',mm2.nama) ELSE mm.nama END AS metodebayar,
			mp.nama AS nama_produk, mp.id AS id_produk, tkp.jumlah, ts.satuan_satuan AS nama_satuan, tkp.harga, tkp.total, '1' AS urutan, 
			tk.keterangan,tk.total_potongan as potongan, tk.ongkos_kirim as ongkir
			FROM tbl_kasir_detail_produk AS tkp
			JOIN tbl_kasir AS tk ON tkp.id_kasir=tk.id_kasir
			JOIN m_produk AS mp ON tkp.id_produk=mp.id
			JOIN tbl_satuan AS ts ON tkp.id_satuan=ts.satuan_id
			JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
			LEFT JOIN m_pelanggan AS mpe ON tk.id_pelanggan=mpe.id
			LEFT JOIN m_metode AS mm ON tk.metodebayar=mm.id
			LEFT JOIN m_metode AS mm2 ON tk.metodebayar2=mm2.id
			$where_tk

			UNION

			SELECT tk.id_kasir, tk.tanggal_faktur as tanggal, tk.no_faktur, mp.nama AS nama_pelanggan, mp.telp as telp_pelanggan,
			CASE WHEN tk.metodebayar2 IS NOT NULL THEN CONCAT(mm.nama,', ',mm2.nama) ELSE mm.nama END AS metodebayar,
			tb.barang_nama AS nama_produk, tb.barang_id as id_produk, tkd.jumlah, ts.satuan_satuan AS nama_satuan, tkd.harga, tkd.total, 
			'2' AS urutan, tk.keterangan, tk.total_potongan as potongan, tk.ongkos_kirim as ongkir
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
			ORDER BY q.id_kasir, q.tanggal, q.urutan, q.nama_produk
					");

				$no = 1;
				$arr = array();
					foreach ($d_data as $key => $d){
						$detail =  DB::table("m_detail_produk")->where('id_produk',$d->id_produk)->get();
						if($d->telp_pelanggan != '' || $d->telp_pelanggan != null){
						$pelanggan = $d->nama_pelanggan.'/'.$d->telp_pelanggan;
						}else{
						$pelanggan = $d->nama_pelanggan;
						}

						$arr[$d->id_produk] = array('no'=>$no++,
										'tgl_jual' 			    => tgl_full($d->tanggal,'0'),
										'nomer_nota'			=> $d->no_faktur,
										'nama_parfum'			=> $d->nama_produk,
										'nama_customer'			=> $pelanggan,
										'jumlah'				=> $d->jumlah,
										'total'					=> 'Rp. '.format_angka($d->total+$d->ongkir-$d->potongan),
										'ket'					=> $d->keterangan,
										'urutan'				=> $d->urutan,
									);

						if($d->urutan==1){
							$sub = [];
							foreach($detail as $e){
								$barang =  DB::table("tbl_barang")->where('barang_id',$e->id_barang)->get();
								foreach($barang as $key =>$value){
									$sub[] = array(
										'nama_barang' => $value->barang_nama,
										'volume_barang' => $e->jumlah * $d->jumlah
									);
								}

								$arr[$d->id_produk]['sub'] = $sub;
							}
						}
						else{
							$arr[$d->id_produk][] = null;
						}
					}
		
				if(count($arr) > 0){
					$data = $arr;
				}else{
					die('Data Kosong tidak dapat digenerate ke Excel Silahkan Kembali');
				}

				// dd($data);


				// dd($data);

				return Excel::download(new LaporanPenjualanExcelExport($data), 'Laporan_Penjualan_PerNota_'. $nama_gudang.'_'.$tanggal.'-'.$tanggal2.'.xlsx');
    		default:
    			break;
    	}
    	
    }



    public function hasil_penjualan($d_gudang,$d_tanggalAwal, $d_tanggalAkhir, $d_kategori){
    	ini_set('max_execution_time', '0');
    	$gudang 	= $d_gudang;
    	$tanggal 	= tgl_full($d_tanggalAwal,'99');
    	$tanggal2 	= tgl_full($d_tanggalAkhir,'99');  
    	$kategori 	= $d_kategori;

    	switch ($kategori) {
    		case '2':
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

        $d_data = DB::SELECT("SELECT * FROM (
			SELECT tk.id_kasir, tk.tanggal_faktur as tanggal, tk.no_faktur, mpe.nama AS nama_pelanggan, mpe.telp as telp_pelanggan,
			CASE WHEN tk.metodebayar2 IS NOT NULL THEN CONCAT(mm.nama,', ',mm2.nama) ELSE mm.nama END AS metodebayar,
			mp.nama AS nama_produk, mp.id AS id_produk, tkp.jumlah, ts.satuan_satuan AS nama_satuan, tkp.harga, tkp.total, '1' AS urutan, 
			tk.keterangan,tk.total_potongan as potongan, tk.ongkos_kirim as ongkir
			FROM tbl_kasir_detail_produk AS tkp
			JOIN tbl_kasir AS tk ON tkp.id_kasir=tk.id_kasir
			JOIN m_produk AS mp ON tkp.id_produk=mp.id
			JOIN tbl_satuan AS ts ON tkp.id_satuan=ts.satuan_id
			JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
			LEFT JOIN m_pelanggan AS mpe ON tk.id_pelanggan=mpe.id
			LEFT JOIN m_metode AS mm ON tk.metodebayar=mm.id
			LEFT JOIN m_metode AS mm2 ON tk.metodebayar2=mm2.id
			$where_tk

			UNION

			SELECT tk.id_kasir, tk.tanggal_faktur as tanggal, tk.no_faktur, mp.nama AS nama_pelanggan, mp.telp as telp_pelanggan,
			CASE WHEN tk.metodebayar2 IS NOT NULL THEN CONCAT(mm.nama,', ',mm2.nama) ELSE mm.nama END AS metodebayar,
			tb.barang_nama AS nama_produk, tb.barang_id as id_produk, tkd.jumlah, ts.satuan_satuan AS nama_satuan, tkd.harga, tkd.total, 
			'2' AS urutan, tk.keterangan, tk.total_potongan as potongan, tk.ongkos_kirim as ongkir
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
			ORDER BY q.id_kasir, q.tanggal, q.urutan, q.nama_produk
			");



        
        $no = 1;
		$arr = array();
			foreach ($d_data as $key => $d){
				$detail =  DB::table("m_detail_produk")->where('id_produk',$d->id_produk)->get();
				if($d->telp_pelanggan != '' || $d->telp_pelanggan != null){
				$pelanggan = $d->nama_pelanggan.'/'.$d->telp_pelanggan;
				}else{
				$pelanggan = $d->nama_pelanggan;
				}
			
				
				$arr[$d->id_produk] = array('no'=>$no++,
								'tgl_jual' 			=> tgl_full($d->tanggal,'0'),
								'nomer_nota'			=> $d->no_faktur,
								'nama_parfum'			=> $d->nama_produk,
								'nama_customer'			=> $pelanggan,
								'jumlah'				=> format_angka($d->jumlah),
								'ket'					=> $d->keterangan,
								'urutan'				=> $d->urutan
							);

				
							if($d->urutan==1){
								foreach($detail as $e){
									$barang =  DB::table("tbl_barang")->where('barang_id',$e->id_barang)->get();
								
									foreach($barang as $key =>$value){
										$arr[$d->id_produk][$key][] = array(
											'nama_barang' => $value->barang_nama,
											'volume_barang' => $e->jumlah * $d->jumlah
											
										);
									}
								}

							}
							else{
								$arr[$d->id_produk][0] = null;
							}		
								
			}

    	if(count($arr) > 0){
		$data = $arr;
		}else{
		$data = null;
		}

		return view('admin.laporan.hasil_laporan_detail',compact('data'));
    		break;
    		default:
    			# code...
    			break;
    	}

    	
		// print_r($data);exit();
		
    }

    public function hasil_penjualan2($d_gudang,$d_tanggalAwal, $d_tanggalAkhir, $d_kategori){
    	ini_set('max_execution_time', '0');
    	$gudang 	= $d_gudang;
    	$tanggal 	= tgl_full($d_tanggalAwal,'99');
    	$tanggal2 	= tgl_full($d_tanggalAkhir,'99');  
    	$kategori 	= $d_kategori;

    	switch ($kategori) {
    		case '2':
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

		$d_data = DB::SELECT("SELECT * FROM (
			SELECT tk.id_kasir, tk.tanggal_faktur as tanggal, tk.no_faktur, mpe.nama AS nama_pelanggan, mpe.telp as telp_pelanggan,
			CASE WHEN tk.metodebayar2 IS NOT NULL THEN CONCAT(mm.nama,', ',mm2.nama) ELSE mm.nama END AS metodebayar,
			mp.nama AS nama_produk, mp.id AS id_produk, tkp.jumlah, ts.satuan_satuan AS nama_satuan, tkp.harga, tkp.total, '2' AS urutan, 
			tk.keterangan,tk.total_potongan as potongan, tk.ongkos_kirim as ongkir, mpd.id_barang, tb.barang_nama as nama_barang, mpd.jumlah as jumlah_barang,
			(SELECT count(*) FROM m_produk AS smp LEFT JOIN m_detail_produk as smpd ON smp.id=smpd.id_produk WHERE smp.id=mp.id) as count_row
			FROM tbl_kasir_detail_produk AS tkp
			JOIN tbl_kasir AS tk ON tkp.id_kasir=tk.id_kasir
			JOIN m_produk AS mp ON tkp.id_produk=mp.id
			JOIN m_detail_produk AS mpd ON mp.id = mpd.id_produk
			JOIN tbl_barang AS tb ON mpd.id_barang = tb.barang_id
			JOIN tbl_satuan AS ts ON tkp.id_satuan=ts.satuan_id
			JOIN ref_gudang AS rg ON tk.id_gudang=rg.id
			LEFT JOIN m_pelanggan AS mpe ON tk.id_pelanggan=mpe.id
			LEFT JOIN m_metode AS mm ON tk.metodebayar=mm.id
			LEFT JOIN m_metode AS mm2 ON tk.metodebayar2=mm2.id
			$where_tk
			
			UNION ALL

			SELECT tk.id_kasir, tk.tanggal_faktur as tanggal, tk.no_faktur, mp.nama AS nama_pelanggan, mp.telp as telp_pelanggan,
			CASE WHEN tk.metodebayar2 IS NOT NULL THEN CONCAT(mm.nama,', ',mm2.nama) ELSE mm.nama END AS metodebayar,
			tb.barang_nama AS nama_produk, tb.barang_id as id_produk, tkd.jumlah, ts.satuan_satuan AS nama_satuan, tkd.harga, tkd.total, 
			'1' AS urutan, tk.keterangan, tk.total_potongan as potongan, tk.ongkos_kirim as ongkir, '' as id_barang, '' as nama_barang, '' as jumlah_barang, '0' as count_row
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
			ORDER BY q.id_kasir, q.tanggal, q.urutan, q.nama_produk");
        
        $arr = array();
        $no = 1;
        if(count($d_data) > 0){	
        	foreach($d_data as $d){
        		$arr[] = $d;
        	}
        }

        $data = $arr;
        print_r($data);
		return view('admin.laporan.hasil_laporan_detail',compact('data'));
    		break;
    		
    		default:
    			# code...
    			break;
    	}

    	
		// print_r($data);exit();
		
    }

}
