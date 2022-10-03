<?php
	//Bagian Tanggal

	function tgl_full($tgl, $jenis){
		$hari_h = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu");
		$tg = date("d", strtotime($tgl));
		$bln = date("m", strtotime($tgl));
		$bln2 = date("m", strtotime($tgl));
		$thn = date("Y", strtotime($tgl));
		$bln_h = array('01' => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "Nopember", "12" => "Desember");
		$bln = $bln_h[$bln];
		$hari = $hari_h[date("w", strtotime($tgl))];

		$jam = date('H');
		$menit = date('i');
		$detik = date('s');

		$get_jam = date("H", strtotime($tgl));	
		$get_menit = date("i", strtotime($tgl));
		$get_detik = date("s", strtotime($tgl));

		$zero_jam = '00';	
		$zero_menit = '00';
		$zero_detik = '00';

		if($jenis == '0'){
			$print = $tg.' '.$bln.' '.$thn;
		}else if($jenis == '1'){
			$print = $hari.', '.$tg.' '.$bln.' '.$thn;
		}else if($jenis == '2'){
			$print = $thn.'-'.$bln2.'-'.$tg;
		}else if($jenis == '3'){
			$print = $tg."/".$bln2;
		}else if($jenis == '4'){
			$print = strtotime($tgl);
		}else if($jenis == '5'){
			$print = $thn."-".$bln2."-".$tg." ".$jam.":".$menit.":".$detik;
		}else if($jenis == '6'){
			$print = $thn."-".$bln2."-".$tg." ".$get_jam.":".$get_menit.":".$get_detik;
		}else if($jenis == '7'){
			$print = $thn."-".$bln2."-".$tg." ".$zero_jam.":".$zero_menit.":".$zero_detik;
		}else if($jenis == '98'){
			$print = $tg."-".$bln2."-".$thn;
		}else if($jenis == '99'){
			$print = $thn."-".$bln2."-".$tg;
		}else if($jenis == 'hari'){
			$print = $hari;
		}
		else{
			$print = $tg.'-'.$bln2.'-'.$thn;
		}
		return $print;
	}

	function base_gudang($where){
	    $var = 'SELECT id as id_gudang, nama as nama_gudang, alamat as alamat_gudang, kode as kode FROM ref_gudang '.$where;
	    return $var;
	}

	function date_range($tgl_awal, $tgl_akhir){
		$hari = date('j', strtotime($tgl_awal));
		$hari2 = date('j', strtotime($tgl_akhir));

		$bln = date('m', strtotime($tgl_awal));
		$bln2 = date('m', strtotime($tgl_akhir));

		$thn = date('Y', strtotime($tgl_awal));
		$thn2 = date('Y', strtotime($tgl_akhir));

		if($thn == $thn2){
			if($bln == $bln2){
				if($hari == $hari2){
					$tahun = $hari.' '.bln_tostr($bln).' '.$thn;
				}else{
					$tahun = $hari.' - '.$hari2.' '.bln_tostr($bln).' '.$thn;
				}
			}else{
				$tahun = $hari.' '.bln_tostr($bln).' - '.$hari2.' '.bln_tostr($bln2).' '.$thn;
			}
		}else{
			$tahun = $hari.' '.bln_tostr($bln). ' '.$thn.' - '.$hari2.' '.bln_tostr($bln2).' '.$thn2;
		}

		return $tahun;
	}

	function bln_tostr($bln = ''){
		$bln_h = array('01' => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "Nopember", "12" => "Desember");

		return $bln_h[sprintf("%02d", $bln)];
	}

	function date_db($value, $reverse = false, $ver=1)
	{
		/* date format on $value d/m/Y */
		if($ver==1){
			if($reverse){ // Y-m-d to d/m/Y
				$date = explode("-", $value);
				return implode("/", array_reverse($date));

			}else{ // d/m/Y to Y-m-d
				$date = explode("/", $value);
				return implode("-", array_reverse($date));
				
			}
		}
		/* date format on $value d-m-Y */
		else if($ver==2){
			if($reverse){ // Y-m-d to d-m-Y
				$date = explode("-", $value);
				return implode("-", array_reverse($date));

			}else{ // d-m-Y to Y-m-d
				$date = explode("-", $value);
				return implode("-", array_reverse($date));
				
			}
			
		}
	}

	/* data format */
	function format_tampil_data_barang($data, $sparator=" ")
	{
		if(is_array($data)){
			return "$data[kode_barang] - ".$sparator.$data['nama_barang'];
		}else{
			if(!is_null($data)){
				return "$data->kode_barang - ".$sparator.$data->nama_barang;
			}
		}
		return "None";
	}

	function format_tampil_data_pelanggan($data, $sparator=" - ")
	{
		if(is_array($data)){
			if ($data['pic'] == "") {
				return "$data[nama]";
			} else {
				return "$data[nama]".$sparator.$data['pic'];
			}
		}else{
			if(!is_null($data)){
				if ($data->pic == "") {
					return $data->nama;
				} else {
					return $data->nama.$sparator.$data->pic;
				}
			}
		}
		return "None";
	}

	function nomor_pembelian($data, $sparator="")
	{
		if(is_array($data)){
			if(isset($data['no_order'])){
				return "$data[no_order]".$sparator.$data['sufix_no_faktur'];
			}
			return "$data[no_faktur]".$sparator.$data['sufix_no_faktur'];

		}else{
			if(!is_null($data)){

				if(isset($data->no_order)){
					return "$data->no_order".$sparator.$data->sufix_no_faktur;
				}
				return "$data->no_faktur".$sparator.$data->sufix_no_faktur;
			}
		}

		return "None";
	}

	function nomor_surat_jalan($data, $sparator="")
	{
		if(is_array($data)){
			return "$data[no_surat]".$sparator.$data['sufix_no_surat'];

		}else{
			if(!is_null($data)){
				return "$data->no_surat".$sparator.$data->sufix_no_surat;

			}
		}

		return "None";
	}

	function nomor_penjualan($data, $sparator="")
	{
		if(is_array($data)){
			return "$data[no_faktur]".$sparator.$data['sufix_no_faktur'];

		}else{
			if(!is_null($data)){
				return "$data->no_faktur".$sparator.$data->sufix_no_faktur;
				
			}
		}
		
		return "None";
	}

	function nomor_retur_penjualan($data, $sparator="")
	{
		if(is_array($data)){
			return "$data[no_retur]".$sparator.$data['sufix_no_retur'];

		}else{
			if(!is_null($data)){
				return "$data->no_retur".$sparator.$data->sufix_no_retur;
				
			}
		}
		
		return "None";
	}

	function nomor_orderservice($data, $sparator="")
	{
		if(is_array($data)){
			return "$data[no_nota]".$sparator.$data['sufix_no_nota'];

		}else{
			if(!is_null($data)){
				return "$data->no_nota".$sparator.$data->sufix_no_nota;
				
			}
		}
		
		return "None";
	}
	/* end : data format */

	// Bagian Uang
	function rupiah($uang){
		if(is_numeric($uang)){
			$uang = number_format($uang,0);
		}else{
			$uang = 0;
		}
		return $uang;
	}

	function rupiah2($uang, $decimal=0){
		if(is_numeric($uang)){
			$uang = number_format($uang,$decimal,",",".");
		}else{
			$uang = $uang;
		}

		return $uang;
	}

	function rounding($number, $decimal)
	{
		return round((float)$number, $decimal);
	}

	function format_angka($var){
		if(is_numeric($var)){
			$var = number_format($var, 0, '.', '.');
		}else{
			$var = 0;
		}

		return $var;
	}

	function format_angkav2($var){
		if(is_numeric($var)){
			
			if(floor($var) != $var){
				$var = number_format($var, 2, '.', '.');
			}else{
				$var = number_format($var, 0, '.', '.');
			}

		}else{
			$var = 0;
		}

		return $var;
	}

	function terbilang($number, $currency = 'Rupiah'){
		$before_comma = trim(to_word($number));
		$after_comma = trim(comma($number));
		return ucwords($results = $before_comma . " ".$currency);
	}

	function to_word($number){
		$words = "";
		$arr_number = array(
		"",
		"satu",
		"dua",
		"tiga",
		"empat",
		"lima",
		"enam",
		"tujuh",
		"delapan",
		"sembilan",
		"sepuluh",
		"sebelas");

		if($number<12)
		{
			$words = "".$arr_number[$number];
		}
		else if($number<20)
		{
			$words = to_word($number-10)." belas";
		}
		else if($number<100)
		{
			$words = to_word($number/10)." puluh ".to_word($number%10);
		}
		else if($number<200)
		{
			$words = "seratus ".to_word($number-100);
		}
		else if($number<1000)
		{
			$words = to_word($number/100)." ratus ".to_word($number%100);
		}
		else if($number<2000)
		{
			$words = "seribu ".to_word($number-1000);
		}
		else if($number<1000000)
		{
			$words = to_word($number/1000)." ribu ".to_word($number%1000);
		}
		else if($number<1000000000)
		{
			$words = to_word($number/1000000)." juta ".to_word($number%1000000);
		}
		else if($number<1000000000000)
		{
			$words = to_word($number/1000000000)." milyar ".to_word($number%1000000000);
		}
		else
		{
			$words = "undefined";
		}
		return $words;
	}

	function comma($number){
		$after_comma = stristr($number,',');
		$arr_number = array(
		"nol",
		"satu",
		"dua",
		"tiga",
		"empat",
		"lima",
		"enam",
		"tujuh",
		"delapan",
		"sembilan");

		$results = "";
		$length = strlen($after_comma);
		$i = 1;
		while($i<$length)
		{
			$get = substr($after_comma,$i,1);
			$results .= " ".$arr_number[$get];
			$i++;
		}
		return $results;
	}


	// ------------------------- Status -----------------------------

	function status_bayar($id){
        switch ($id) {
            case '0':
                $d = '<span class="label label-danger">Belum Bayar</span>';
                break;
            case '1':
                $d = '<span class="label label-warning">Proses Bayar</span>';
            break;
            case '2':
                $d = '<span class="label label-success">Lunas</span>';
            break;
            case '3':
                $d = '<span class="label label-info">Kelebihan Bayar</span>';
            break;
            default:
                $d = '<span class="label label-default"></span>';
                break;
        }

        return $d;
    }

	function status_penjualan($id){
        switch ($id) {
            case '0':
                $d = '<span class="label label-danger">Belum Dikeluarkan</span>';
                break;
            case '1':
                $d = '<span class="label label-warning">Sudah Dikeluarkan</span>';
            break;
            case '99':
                $d = '<span class="label label-primary">Faktur Belum Dibuat</span>';
            break;
            default:
                $d = '<span class="label label-default"></span>';
                break;
        }

        return $d;
    }

    function status_surat_jalan($value)
    {
        switch ($value) {
            case '0':
                $d = '<span class="label label-primary">Faktur Belum Dibuat</span>';
                break;
            case '1':
                $d = '<span class="label label-success">Faktur Dibuat</span>';
            break;
            default:
                $d = '<span class="label label-default">...</span>';
                break;
        }

        return $d;
    }

    function status_bon($id){
        switch ($id) {
            case '0':
                $d = '<span class="label label-warning bg-warning-600">Menunggu Gudang</span>';
                break;
            case '1':
                $d = '<span class="label label-warning">Dalam Proses</span>';
            	break;
            case '2':
                $d = '<span class="label label-success">Selesai</span>';
            	break;
            case '3';
            	$d = '<span class="label label-warning">Belum Selesai Terima</span>';
            	break;
            default:
                $d = '<span class="label label-default"></span>';
                break;
        }

        return $d;
    }  

    function status_transfer($id){
        switch ($id) {
            case '0':
                $d = '<span class="label label-danger">Menunggu Gudang</span>';
                break;
            case '1':
                $d = '<span class="label label-warning">Dalam Proses</span>';
            	break;
            case '2':
                $d = '<span class="label label-success">Selesai</span>';
            	break;
            default:
                $d = '<span class="label label-default"></span>';
                break;
        }

        return $d;
    } 

    function status_approve($id){
    	switch ($id) {
    		case '0':
    			$d = '<label class="label bg-warning-400">Belum Verifikasi</label>';
    			break;
    		case '1':
    			$d = '<label class="label bg-primary-400">Terverifikasi</label>';
    			break;
    		default:
    			$d = '<label class="label bg-primary-400"></label>';
    			break;
    	}

    	return $d;
    }

    function status_terima_barang($id){

    	switch ($id) {
    		case '0':
    			$d = '<label class="label bg-warning-400">Belum Diterima</label>';
    			break;
    		
    		default:
    			$d = '<label class="label bg-primary-400">Diterima</label>';
    			break;
    	}

		return $d;
	}

    function status_retur($id){
    	switch ($id) {
    		case '0':
    			$d = '<label class="label bg-warning-400">Belum Diterima</label>';
    			break;
    		case '1':
    			$d = '<label class="label bg-primary-400">Diterima</label>';
    			break;
    		default:
    			$d = '<label class="label bg-primary-400"></label>';
    			break;
    	}

    	return $d;
    }

    // ----------- Lain ----------------------


    function pembagi($var, $var2){
    	return round((float)$var/(float)$var2, 3);
    }

    function get_null($var){
    	return ($var == 0) ? "":$var;
    }

    function get_empty($var){
    	return ($var == '') ? "":" ".$var;
    }

    function get_stripe($var){
    	$count = count($var);
    	$list = "";
    	for ($i=1 ; $i <= $count; $i++) { 
    		if($i == $count){
    			$list .= ($var[$i-1] != '') ? $var[$i-1]:"";
    		}else{
    			$list .= ($var[$i-1] != '') ? $var[$i-1]." - ":"";
    		}
    		
    	}

    	return $list;
    }

    function status_checkbox($val, $id = '', $jenis = ''){
    	switch ($val) {
    		/*case '0':
    			$return = '<input type="checkbox" id="check_verifikasi" value="'.$id.'" jenis="'.$jenis.'">';
    			break;*/
    		case '1':
    			$return = '<input type="checkbox" id="check_verifikasi" value="'.$id.'" jenis="'.$jenis.'">';
    			break;
    		default:
    			$return = '';
    			break;
    	}

    	return $return;
    }

    function cew($data = ''){
		if(!empty($data)){
			$d = ucwords(strtolower($data));
		}

		return $d;
	}

	function get_rawquery($func){
		$PDO = DB::connection('mysql')->getPdo();

        $billingStmt = $PDO->prepare("".$func."");
        $billingStmt->execute();
        $usersBills = $billingStmt->fetchAll((\PDO::FETCH_ASSOC));

        return $usersBills;
	}


	function setup_corak(){
		return [22, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33];
	}

	function enc($n){
      $encript = base64_encode(base64_encode($n));
      return $encript;
    }
    function dec($n){
      $decrtip = base64_decode(base64_decode($n));
      return $decrtip;
    }


    function tombol_create($action='', $status, $jenis, $id_group = '1'){
    	if($jenis == 1){
    		switch ($status) {
    			case '2':
    				# code...
    				$tombol = '<a href="javascript:;" style="margin-bottom:20px;margin-left:10px;" class="card-body-title btn_tambah" id="btn_tambah"><button class="btn btn-primary"><i class="fa fa-plus-square-o"></i> Tambah</button></a>';
    				break;
    			case '1':
    				$tombol = "";
    				break;
    			default:
    				# code...
    				$tombol = '<a href="javascript:;" style="margin-bottom:20px;margin-left:10px;" class="card-body-title btn_tambah" id="btn_tambah"><button class="btn btn-primary"><i class="fa fa-plus-square-o"></i> Tambah</button></a>';
    				break;
    		}
    	}else if($jenis == 2){
    		switch ($status) {
    			case '2':
    				$tombol = '<a href="'.$action.'" style="margin-bottom:20px;margin-left:10px;" class="card-body-title"><button class="btn btn-primary"><i class="fa fa-plus-square-o"></i> Tambah</button></a>';
    				break;
    			case '1':
    				$tombol = "";
    				break;
    			default:
    				# code...
    				$tombol = '<a href="'.$action.'" style="margin-bottom:20px;margin-left:10px;" class="card-body-title"><button class="btn btn-primary"><i class="fa fa-plus-square-o"></i> Tambah</button></a>';
    				break;
    		}
    	}else if($jenis == 3){
    		$update = "";
    		if($id_group != 1){
    			$update = '<a href="'.url('stokopnamebaru_update').'" class="card-body-title btn_cekstokopname" id="btn_cekstokopname"><button class="btn btn-success"><i class="fa fa-refresh"></i> Update</button></a>';
    		}
    		switch ($status) {
    			case '2':
    				$tombol = '<a href="javascript:;" style="margin-bottom:20px;margin-left:10px;" class="card-body-title btn_tambah" id="btn_tambah"><button class="btn btn-primary"><i class="fa fa-plus-square-o"></i> Tambah</button></a> '.$update;
    				break;
    			case '1':
    				$tombol = '';
    				break;
    			default:
    				$tombol = '<a href="javascript:;" style="margin-bottom:20px;margin-left:10px;" class="card-body-title btn_tambah" id="btn_tambah"><button class="btn btn-primary"><i class="fa fa-plus-square-o"></i> Tambah</button></a> '.$update;
    				break;
    		}

    	}else if($jenis == 4){
    		switch ($status) {
    			case '2':
    				$tombol = '<div class="form-group"><div class="col-md-7"><select class="form-control" name="gudang" required="" style="width: 100%;"><option value=""> --- Pilih --- </option></select></div><button class="btn btn-primary col-md-4" type="button" id="btn_verifikasi">Verifikasi</button></div> ';
    				break;
    			case '1':
    				$tombol = '<div class="form-group hide"><div class="col-md-7"><select class="form-control" name="gudang" required="" style="width: 100%;"><option value=""> --- Pilih --- </option></select></div><button class="btn btn-primary col-md-4" type="button" id="btn_verifikasi">Verifikasi</button></div> ';
    				break;
    			default:
    				$tombol = '<div class="form-group"><div class="col-md-7"><select class="form-control" name="gudang" required="" style="width: 100%;"><option value=""> --- Pilih --- </option></select></div><button class="btn btn-primary col-md-4" type="button" id="btn_verifikasi">Verifikasi</button></div> ';
    				break;
    		}
    	}else if($jenis == 5){
    		switch ($status) {
    			case '2':
    				$tombol = '<button class="btn btn-primary" style="margin-bottom:20px;margin-left:10px;" type="button" id="btn_verifikasi"><i class="fa  fa-check-o"></i> Verifikasi</button>';
    				break;
    			case '1':
    				$tombol = '';
    				break;
    			default:
    				$tombol = '<button class="btn btn-primary" style="margin-bottom:20px;margin-left:10px;" type="button" id="btn_verifikasi"><i class="fa  fa-check-o"></i> Verifikasi</button>';
    				break;
    		}
    	}else if($jenis == 6){
    		switch ($status) {
    			case '2':
    				$tombol = '<a href="javascript:;" class="card-body-title btn_tambah" id="btn_tambah"><button class="btn btn-primary"><i class="fa fa-plus-square-o"></i> Tambah</button></a> 
           <a onclick="addBarcode()" href="#" style="margin-left:10px;" class="card-body-title"><button class="btn btn-primary"><i class="fa fa-qrcode"></i> Scan QR</button></a>';
    				break;
    			case '1':
    				$tombol = '';
    				break;
    			default:
    				$tombol = '<a href="javascript:;" class="card-body-title btn_tambah" id="btn_tambah"><button class="btn btn-primary"><i class="fa fa-plus-square-o"></i> Tambah</button></a> 
           <a onclick="addBarcode()" href="#" style="margin-left:10px;" class="card-body-title"><button class="btn btn-primary"><i class="fa fa-qrcode"></i> Scan QR</button></a>';
    				break;
    		}
    	}else if($jenis == 7){
    		switch (true) {
    			case ($status == '2' && $id_group == '1'):
    				$tombol = '<button class="btn btn-primary hide" type="button" id="btn_posting_check" style="margin-top: 25px;"><i class="fa fa-check-circle"></i> Closing</button> <button class="btn btn-warning hide" type="button" id="btn_posting_uncheck" style="margin-top: 25px;"><i class="fa fa-times-circle"></i> Closing</button>';
    				break;
    			case ($status == '1' &&  $id_group == '1'):
    				$tombol = '<button class="btn btn-primary hide" type="button" id="btn_posting_check" style="margin-top: 25px;"><i class="fa fa-check-circle"></i> Closing</button> <button class="btn btn-warning hide" type="button" id="btn_posting_uncheck" style="margin-top: 25px;"><i class="fa fa-times-circle"></i> Closing</button>';
    				break;
    			case ($status == '2' && $id_group != '1'):
    				$tombol = '<button class="btn btn-primary hide" type="button" id="btn_posting_check" style="margin-top: 25px;"><i class="fa fa-check-circle"></i> Closing</button>';
    				break;
    			case ($status == '1' && $id_group != '1'):
    				$tombol = '<button class="btn btn-primary hide" type="button" id="btn_posting_check" style="margin-top: 25px;"><i class="fa fa-check-circle"></i> Closing</button>';
    				break;
    			default:
    				$tombol = '';
    				break;
    		}
	    	
    	}

    	return $tombol;
    }
    
function trigger_log($id_tabel, $keterangan, $status, $url = NULL)
{
	if ($status == 1) {
		$c_status = 'Tambah';
	} elseif ($status == 2) {
		$c_status = 'Edit';
	} elseif ($status == 3) {
		$c_status = 'Hapus';
	} elseif ($status == 4) {
		$c_status = 'Scan';
	} elseif ($status == 5) {
		$c_status = 'Download';
	} elseif ($status == 6) {
		$c_status = 'Validasi';
	} elseif ($status == 7) {
		$c_status = 'Cetak';
	} elseif ($status == 8) {
		$c_status = 'Closing';
	} else {
		$c_status = '';
	}

	$log['id_tabel']   = $id_tabel;
	$log['id_user']    = Auth::id();
	$log['nama_user']  = Auth::user()->name;
	$log['tgl_log']    = date('Y-m-d H:i:s');
	$log['status']     = $c_status;
	$log['keterangan'] = $keterangan;
	$log['url']        = $url;

	// return $log;
	DB::table('log_history')->insert($log);
}

	

	