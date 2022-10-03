<?php
	function tanggal_indonesia($tgl, $tampil_hari=true){
		$nama_hari = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu");
		$nama_bulan = array(1=>"Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");
		$tahun = substr($tgl, 0, 4);
		$bulan = $nama_bulan[(int)substr($tgl,5,2)];
		$tanggal = substr($tgl, 8,2);

		$text="";
		if($tampil_hari){
			$urutan_hari = date('w', mktime(0,0,0, substr($tgl, 5,2), $tanggal, $tahun));
			$hari = $nama_hari[$urutan_hari];
			$text .= $hari.", ";
		}
		$text .= $tanggal ." ". $bulan ." ". $tahun;
		return $text;
	}
	
	function get_poinku($d_kasir='',$d_pelanggan=''){
		$where = array();
		if($d_kasir != ''){
			$where[] = 'tpp.id_kasir = $d_kasir';
		}
		if($d_pelanggan != ''){
			$where[] = 'tpp.id_pelanggan = $d_pelanggan';
		}
		$add_where = '';
		if(count($where) > 0){
		$add_Where = "WHERE ".implode(' AND ', $where);
		}

		// print_r($add_where);exit();
		// $d_data = DB::table('tbl_transaksi_poin as tpp')->join('m_pelanggan as mp','tpp.id_pelanggan','mp.id_pelanggan')->join('tbl_kasir as tk','tpp.id_kasir','tk.id_kasir')->whereraw($add_where)->groupBy('tpp.id_pelanggan')->select(DB::Raw('CASE WHEN SUM(tpp.unit_masuk-tpp.unit_keluar) THEN SUM(tpp.unit_masuk-tpp.unit_keluar) ELSE 0 END AS poin'))->first()->poin;

		$d_data = DB::SELECT("SELECT CASE WHEN SUM(tpp.unit_masuk-tpp.unit_keluar) THEN SUM(tpp.unit_masuk-tpp.unit_keluar) 
							ELSE 0 END AS poin 
							from tbl_transaksi_poin as tpp 
							JOIN m_pelanggan as mp ON tpp.id_pelanggan=mp.id
							JOIn tbl_kasir as tk On tpp.id_kasir = tk.id_kasir
							$add_where
							GROUP BY  tpp.id_pelanggan");
		$d_data = 
		$poin = $d_data[0]->poin;
		return $poin;
	}

	function get_produk_group($arr = array()){
		$where = "";
		$orderBy = "";
		$groupBy = "";
		if(isset($arr['where'])){
			$where = $arr['where'];
		}
		if(isset($arr['orderBy'])){
			$orderBy = $arr['orderBy'];
		}
		if(isset($arr['groupBy'])){
			$groupBy = $arr['groupBy'];
		}

		$data['total'] 	= DB::SELECT("SELECT COUNT(*) AS total FROM m_produk as mp join m_produkpoin as mpp ON mp.id=mpp.id_produk $where $groupBy"); 
		$data['data'] 	= DB::SELECT("SELECT mp.* FROM m_produk as mp join m_produkpoin as mpp ON mp.id=mpp.id_produk $where $groupBy $orderBy");

		return $data;
	}
 ?>
