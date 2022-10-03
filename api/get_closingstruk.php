<?php
include "connection.php";

$gudang = $_POST['id_gudang'];
$tanggal = date('Y-m-d',strtotime($_POST['tanggal']));
/*$gudang = $_GET['id_gudang'];
$tanggal = date('Y-m-d',strtotime($_GET['tanggal']));*/
//$id_kasir = 1530;
if($gudang != "" && $tanggal != ""){
        	//$where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."'";
            $where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."'";
        }elseif($gudang == "" && $tanggal == ""){
        	$where = "WHERE k.id_gudang = '".$gudang."'";
        }else{
        	$where = "";
        }
$query_closing = "SELECT COUNT(k.id_kasir) jumlah_nota, k.status_posting as status FROM tbl_kasir AS k ".$where." ";
$result_closing = mysqli_query($connect,$query_closing);

$db_query_barangclosing = "SELECT * FROM m_barang_closing";
$result_query_barangclosing = mysqli_query($connect, $db_query_barangclosing);
$arr_barang = array();
if(mysqli_num_rows($result_query_barangclosing) > 0){
    while($row_barangclosing = mysqli_fetch_assoc($result_query_barangclosing))
    {
        $arr_barang[] = $row_barangclosing['id_barang']; 
    }
}
$arr_barangclosing = "";
$barang_closing = '0';
if(count($arr_barang) > 0){
    $barang_closing = implode(',',$arr_barang);
    $arr_barangclosing = " AND tkd.id_barang IN ($barang_closing)";
}

$arraydata = array();
if ($result_closing->num_rows>0) {

	if($gudang != "" && $tanggal != ""){
        	//$where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."'";
            $where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."' and m.status = '1'";
            $where2 = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."'";
            /*$where_tambahan = "WHERE tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur = '".$tanggal."' AND tkd.id_barang IN (612,662,587,585,586,630,577,570,569,575,576,573,574,572,571,599,613,610,609,594,608,614,598,600,601,602)";*/
            $where_tambahan = "WHERE tk.id_gudang = '".$gudang."' AND tk.tanggal_faktur = '".$tanggal."' ".$arr_barangclosing."";
        }elseif($gudang == "" && $tanggal == ""){
        	$where = "WHERE k.id_gudang = '".$gudang."' and m.status = '1'";
        	$where2 = "WHERE k.id_gudang = '".$gudang."'";
            /*$where_tambahan = "WHERE tk.id_gudang = '".$gudang."' AND tkd.id_barang IN (612,662,587,585,586,630,577,570,569,575,576,573,574,572,571,599,613,610,609,594,608,614,598,600,601,602)";*/
            $where_tambahan = "WHERE tk.id_gudang = '".$gudang."' ".$arr_barangclosing."";
        }else{
        	$where = "WHERE m.status = '1'";
        	$where2 = "";
            /*$where_tambahan = "WHERE tkd.id_barang IN (612,662,587,585,586,630,577,570,569,575,576,573,574,572,571,599,613,610,609,594,608,614,598,600,601,602)";*/
            $where_tambahan = "WHERE tkd.id_barang IN (".$barang_closing.")";
        }
	$q_closing = "
        SELECT * FROM (
            SELECT
            me.id,
            me.nama as name,
            me.urutan as urutan,
            CASE WHEN su.jumlah IS NULL THEN '0' ELSE su.jumlah END AS data
                FROM
                    m_metode AS me
                    LEFT JOIN (
                    SELECT q.metodebayar,
                    q.metode AS metode,q.created_at, SUM(q.jumlah) AS jumlah FROM (
                SELECT
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
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
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
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
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
                k.metodebayar, k.metodebayar2, m.nama
                        ) q 
                        GROUP BY
                q.metodebayar, q.metode
                ) AS su ON su.metodebayar = me.id
                where me.status = 1
                UNION ALL
                SELECT '99' AS id, 'Ongkos Kirim' AS name, '99' AS urutan, SUM(k.ongkos_kirim) AS data FROM tbl_kasir AS k $where2
                ) AS p ORDER BY p.urutan ASC
                ";

    $r_closing = mysqli_query($connect,$q_closing);
	$closing_array= array();
	if(mysqli_num_rows($r_closing) > 0){
		while($row_closing = mysqli_fetch_assoc($r_closing))
		{
			// $closing_array[] = $row_closing;
			$closing_array[] = array('id'=>$row_closing['id'],
								'name'=>$row_closing['name'],
								'data'=>format_angka($row_closing['data']));
		}
	}

    $q_tambahan = "SELECT tb.barang_nama AS nama_barang, tb.barang_kode as kode_barang, 
    CASE WHEN SUM(jumlah) IS NULL THEN 0 ELSE SUM(jumlah) END AS jumlah 
    FROM tbl_kasir_detail AS tkd 
    JOIN tbl_kasir AS tk ON tkd.id_kasir=tk.id_kasir
    JOIN tbl_barang AS tb ON tkd.id_barang=tb.barang_id
    $where_tambahan
    GROUP BY
    tkd.id_barang, tkd.id_satuan";
    $r_tambahan = mysqli_query($connect,$q_tambahan);
    $tambahan_array = array();
    if(mysqli_num_rows($r_tambahan) > 0){   
        while ($row_tambahan = mysqli_fetch_assoc($r_tambahan)) {
            $tambahan_array[] = array("nama_barang"=>$row_tambahan['nama_barang'],
                                        "kode_barang"=>$row_tambahan['kode_barang'],
                                        "jumlah"=>$row_tambahan['jumlah']);
        }
    }
	//print_r($barang_array);exit();
	while ($baris = mysqli_fetch_assoc($result_closing)) {
		$nota_jumlah = $baris['jumlah_nota'];
		$nota_status = $baris['status'];

		if($gudang != "" && $tanggal != ""){
        	//$where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."'";
            $where = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."' and m.status = '1'";
            $where2 = "WHERE k.id_gudang = '".$gudang."' and k.tanggal_faktur = '".$tanggal."'";
        }elseif($gudang == "" && $tanggal == ""){
        	$where = "WHERE k.id_gudang = '".$gudang."' and m.status = '1'";
        	$where2 = "WHERE k.id_gudang = '".$gudang."'";
        }else{
        	$where = "WHERE m.status = '1'";
        	$where2 = "";
        }

		$q_total = "SELECT 
                        id,
                        CASE WHEN sum(data) IS NULL THEN '0' ELSE sum(data) END AS data,
                        CASE WHEN tanggal_bayar IS NULL THEN DATE_FORMAT(NOW(), '%d-%m-%Y') 
                        ELSE DATE_FORMAT(tanggal_bayar, '%d-%m-%Y') END AS tanggal_bayar FROM (
            SELECT
            me.id,
            me.nama as name,
            me.urutan as urutan,
            CASE WHEN su.jumlah IS NULL THEN '0' ELSE su.jumlah END AS data,
            su.tanggal_bayar
                FROM
                    m_metode AS me
                    LEFT JOIN (
                    SELECT q.metodebayar,
                    q.metode AS metode,
                    q.created_at, 
                    SUM(q.jumlah) AS jumlah,
                    q.tanggal_bayar FROM (
                SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN 0 ELSE SUM( k.total_tagihan - k.total_potongan ) 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN SUM(k.total_metodebayar) ELSE 0 
                    END AS jumlah,
                                        k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar2 as metodebayar,
                    m.nama AS metode,k.created_at,
                                    CASE WHEN k.metodebayar2 IS NOT NULL 
                                    THEN SUM(k.total_metodebayar2) ELSE 0 
                                    END AS jumlah,
                                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    JOIN m_metode AS m ON k.metodebayar2 = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                        ) q 
                        GROUP BY
                q.metodebayar, q.metode
                ) AS su ON su.metodebayar = me.id
                
                ) AS p ORDER BY p.urutan ASC ";
        //print_r($q_total);
		$r_total = mysqli_query($connect,$q_total);

        $q_totalongkir = "SELECT 
                        id,
                        CASE WHEN sum(data) IS NULL THEN '0' ELSE sum(data) END AS data,
                        CASE WHEN tanggal_bayar IS NULL THEN DATE_FORMAT(NOW(), '%d-%m-%Y') 
                        ELSE DATE_FORMAT(tanggal_bayar, '%d-%m-%Y') END AS tanggal_bayar FROM (
            SELECT
            me.id,
            me.nama as name,
            me.urutan as urutan,
            CASE WHEN su.jumlah IS NULL THEN '0' ELSE su.jumlah END AS data,
            su.tanggal_bayar
                FROM
                    m_metode AS me
                    LEFT JOIN (
                    SELECT q.metodebayar,
                    q.metode AS metode,
                    q.created_at, 
                    SUM(q.jumlah) AS jumlah,
                    q.tanggal_bayar FROM (
                SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN 0 ELSE SUM( k.total_tagihan - k.total_potongan ) 
                    END AS jumlah,
                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar,
                    m.nama AS metode,k.created_at,
                    CASE WHEN k.metodebayar2 IS NOT NULL 
                    THEN SUM(k.total_metodebayar) ELSE 0 
                    END AS jumlah,
                                        k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    LEFT JOIN m_metode AS m ON k.metodebayar = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                            UNION ALL
                            SELECT
                    k.metodebayar2 as metodebayar,
                    m.nama AS metode,k.created_at,
                                    CASE WHEN k.metodebayar2 IS NOT NULL 
                                    THEN SUM(k.total_metodebayar2) ELSE 0 
                                    END AS jumlah,
                                    k.tanggal_bayar
                FROM
                    tbl_kasir AS k
                    JOIN m_metode AS m ON k.metodebayar2 = m.id
                $where
                GROUP BY
                k.metodebayar, k.metodebayar2, m.nama
                        ) q 
                        GROUP BY
                q.metodebayar, q.metode
                ) AS su ON su.metodebayar = me.id
                UNION ALL
                SELECT '99' AS id, 'Ongkos Kirim' AS name, '99' AS urutan, SUM(k.ongkos_kirim) AS data, 
                k.tanggal_faktur as tanggak_bayar FROM tbl_kasir AS k 
                $where2
                ) AS p ORDER BY p.urutan ASC 
                ";
        $r_totalongkir = mysqli_query($connect,$q_totalongkir);

		$arraydata = array(
			"error"=>1,
			"message"=>"Data Ditemukan",
			"jumlah_nota" => $nota_jumlah,
			"status" => $nota_status,
			"total" => format_angka(mysqli_fetch_assoc($r_total)['data']),
            "total_ongkir" => format_angka(mysqli_fetch_assoc($r_totalongkir)['data']),
			"detail_closing"=> $closing_array,
            "detail_tambahan"=>	$tambahan_array,	
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
