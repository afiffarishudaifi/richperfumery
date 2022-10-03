<?php
include "connection.php";

// $id_kasir = $_GET['id_kasir'];
$id_kasir = $_POST['id_kasir'];
//$id_kasir = 1530;
$query_kasir = "SELECT ks.no_faktur, ks.tanggal_faktur, ks.id_pelanggan, ks.total_tagihan, ks.total_potongan, ks.ongkos_kirim,ks.uang_muka, mm.nama as nama_metodebayar, ks.total_metodebayar, mm2.nama as nama_metodebayar2, ks.total_metodebayar2, ks.metodebayar, ks.metodebayar2, ks.carabayar FROM tbl_kasir ks LEFT JOIN m_metode as mm ON ks.metodebayar=mm.id LEFT JOIN m_metode as mm2 ON ks.metodebayar2=mm2.id WHERE id_kasir='$id_kasir'";
$result_kasir = mysqli_query($connect,$query_kasir);

$arraydata = array();
if ($result_kasir->num_rows>0) {

	$query_produk = "SELECT tbp.id,tbp.nama,tbkdp.jumlah,tbs.satuan_satuan,tbkdp.harga,tbkdp.total
	FROM tbl_kasir_detail_produk tbkdp, m_produk tbp,tbl_satuan tbs
	WHERE tbkdp.id_produk=tbp.id
	AND tbkdp.id_satuan=tbs.satuan_id
	AND id_kasir = $id_kasir";
	$result_produk = mysqli_query($connect,$query_produk);
	// $post = array();
	$produk_array = array();
	// print_r(mysqli_num_rows($result_produk));exit();
	if(mysqli_num_rows($result_produk) > 0){
		while($row = mysqli_fetch_assoc($result_produk))
		{
			$produk_array[] = $row;
		}
	}
	$q_barang = "SELECT tb.barang_kode AS kode_barang,tb.barang_nama AS nama_barang,ts.satuan_satuan AS satuan_barang,tkd.jumlah AS jml_barang,tkd.harga AS harga_barang,tkd.total AS total_barang FROM tbl_kasir_detail AS tkd INNER JOIN tbl_barang AS tb ON tkd.id_barang = tb.barang_id INNER JOIN tbl_satuan AS ts ON tkd.id_satuan = ts.satuan_id
	WHERE tkd.id_kasir = $id_kasir AND tkd.id_detail_kasir_produk = 0";
	$r_barang = mysqli_query($connect,$q_barang);
	// $post = array();
	$barang_array= array();
	// print_r(mysqli_num_rows($r_barang));exit();
	if(mysqli_num_rows($r_barang) > 0){
		while($row_barang = mysqli_fetch_assoc($r_barang))
		{
			$barang_array[] = $row_barang;
		}
	}
	// print_r($barang_array);exit();
	while ($baris = mysqli_fetch_assoc($result_kasir)) {
		$w_pelanggan = $baris['id_pelanggan'];
		$q_pelanggan = "SELECT pl.nama, pl.no_member FROM m_pelanggan pl WHERE pl.id = '$w_pelanggan'";
		$r_pelanggan = mysqli_query($connect,$q_pelanggan);
		while ($baris_pelanggan = mysqli_fetch_assoc($r_pelanggan)) {
			$nama_pelanggan = $baris_pelanggan['nama'];
			$no_member = $baris_pelanggan['no_member'];
		}

		$q_qty = "SELECT CASE WHEN SUM(tbkdp.jumlah) IS NULL THEN 0 ELSE SUM(tbkdp.jumlah) END AS total_qty FROM tbl_kasir_detail_produk tbkdp WHERE id_kasir = '$id_kasir'";
		$b_qty = "SELECT CASE WHEN SUM(tbkdp.jumlah) IS NULL THEN 0 ELSE SUM(tbkdp.jumlah) END AS total_qty FROM tbl_kasir_detail tbkdp WHERE id_kasir = '$id_kasir' AND id_detail_kasir_produk = 0";
		$r_qty = mysqli_query($connect,$q_qty);
		$s_qty = mysqli_query($connect,$b_qty);

		$total_qty = (mysqli_fetch_assoc($r_qty)['total_qty'])+(mysqli_fetch_assoc($s_qty)['total_qty']);

		$s_metodebayar = 1;
		if($baris['metodebayar2']==null){
			$s_metodebayar = 2;
		}

		$total_metodebayar = $baris['total_metodebayar'];
		if($baris['carabayar']==1 && ($baris['metodebayar2']==null||$baris['metodebayar2']=="")){
			$total_metodebayar = $baris['total_tagihan']-($baris['total_potongan']-$baris['ongkos_kirim'])-$baris['uang_muka'];
		}

		$total_metodebayar2 = 0;
		if($baris['metodebayar2']!=null||$baris['metodebayar2']!=""){
			$total_metodebayar2 = $baris['total_metodebayar2']; 
		}

		$total_tagihan = $baris['total_tagihan'];
		/*$total_tagihan = $baris['total_tagihan']-($baris['total_potongan']-$baris['ongkos_kirim'])-$baris['uang_muka']-($total_metodebayar+$total_metodebayar2);*/


		$arraydata = array(
			"error"=>1,
			"message"=>"Data Ditemukan",
			"no_faktur" => $baris['no_faktur'],
			"nama_pelanggan" => $nama_pelanggan,
			"no_member" => $no_member,
			"tanggal_faktur" => date("d-m-Y", strtotime($baris['tanggal_faktur'])),
			"subtotal" => format_angka($baris['total_tagihan']),
			"total_tagihan" => format_angka($total_tagihan-($baris['total_potongan']-$baris['ongkos_kirim'])),
			"total_potongan" => format_angka($baris['total_potongan']),
			"ongkos_kirim" => format_angka($baris['ongkos_kirim']),
			"uang_muka" => format_angka($baris['uang_muka']),
			"total_qty" => isset(mysqli_fetch_assoc($r_qty)['total_qty']) ? count(mysqli_fetch_assoc($r_qty)['total_qty']):0,
			"nama_metodebayar" => $baris['nama_metodebayar'],
			"nama_metodebayar2"=> ($baris['nama_metodebayar2']==null)?"0":$baris['nama_metodebayar2'],
			"total_metodebayar"=> format_angka($total_metodebayar),
			"total_metodebayar2"=> format_angka($total_metodebayar2),
			"status_metode" => $s_metodebayar,
			"detail_produk" => $produk_array,
			"detail_barang" => $barang_array		
		);
	}
}else{
	$arraydata['error']=0;
	$arraydata['message']="Data Tidak Ditemukan";
}

echo json_encode($arraydata);

?>

<?php
function format_angka($var){
	if(is_numeric($var)){
		$var = number_format($var, 0, '.', '.');
	}else{
		$var = 0;
	}

	return $var;
}
?>
