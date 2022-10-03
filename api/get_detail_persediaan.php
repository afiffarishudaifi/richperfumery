<?php
include "connection.php";

$id_barang = $_POST['id_barang'];
$id_satuan = $_POST['id_satuan'];
$id_gudang = $_POST['id_gudang'];
$where_gudang = "";
if($id_gudang!=""){
    $where_gudang = "AND rg.id IN ($id_gudang)";
}
/*
        $id_profil = Auth::user()->id_profil;
        $group = Auth::user()->group_id;
        $where = "";
        if($group == 5 || $group == 6){
          $where = "WHERE id_profil='$id_profil'";
        }
        $d_gudang = DB::select(base_gudang($where));
        $id_gudang = array();
        foreach($d_gudang as $d){
          $id_gudang[] = $d->id_gudang;
        }
        $where_gudang = "";
        if(sizeof($id_gudang) > 0){
          $gudang = implode(',',$id_gudang);
          $where_gudang = "AND b.id IN ($gudang)";
        }

print_r($where_gudang);exit();*/

$query_barang = "SELECT
    *
FROM
    (
    SELECT
        tls.*,
        tb.barang_kode AS kode_barang,
        tb.barang_nama AS nama_barang,
        rg.nama AS nama_gudang,
        SUM( tls.unit_masuk - tls.unit_keluar ) AS stok,
        ts.satuan_nama AS nama_satuan,
        SUM( (tls.unit_masuk * ts.konversi) - (tls.unit_keluar * ts.konversi) ) AS konversi
    FROM
        tbl_log_stok AS tls
        LEFT JOIN tbl_barang AS tb ON tls.id_barang = barang_id
        LEFT JOIN ref_gudang AS rg ON tls.id_ref_gudang = rg.id
        LEFT JOIN tbl_satuan AS ts ON tls.id_satuan = ts.satuan_id
	    WHERE tb.`barang_id` = '$id_barang' AND ts.`satuan_id` = '$id_satuan'
    GROUP BY
        id_barang,
        id_ref_gudang, id_satuan
    ORDER BY
        tb.barang_nama
    ) b
WHERE
    b.stok NOT LIKE '%-%' AND id_ref_gudang='$id_gudang'";
$result_barang = mysqli_query($connect,$query_barang);
// echo json_encode($result_barang);
// exit;

$response = array();
if ($result_barang->num_rows>0) {
  $response['error'] = 1;
  $response['message'] = "Data Ada";
  $response['result'] = mysqli_fetch_assoc($result_barang);
}else {
  $response['error'] = 0;
  $response['message'] = "Data Tidak Ada";
}
echo json_encode($response);
exit;
