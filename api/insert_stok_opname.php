<?php
include "connection.php";

if (!empty($_POST)) {
  $id_stokopname = null;
  $tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : '';
  $id_barang = isset($_POST['id_barang']) ? $_POST['id_barang'] : '';
  $id_gudang = isset($_POST['id_gudang']) ? $_POST['id_gudang'] : '';
  $stok = isset($_POST['stok']) ? $_POST['stok'] : '';
  $fisik = isset($_POST['fisik']) ? $_POST['fisik'] : '';
  $selisih = isset($_POST['selisih']) ? $_POST['selisih'] : '';
  $id_satuan = isset($_POST['id_satuan']) ? $_POST['id_satuan'] : '';
  $keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';
  $id_log_stok = null;
  $status = "1";
  $created_at = date("Y-m-d H:i:s");
  $updated_at = date("Y-m-d H:i:s");
  $tgl = _tglEng($tanggal);
  $id_user = isset($_POST['id_user']) ? $_POST['id_user']:'';
  $nama_user = isset($_POST['nama_user']) ? $_POST['nama_user']:'';

  if($selisih > 0){
    $input_unit_masuk    = "0";
    $input_unit_keluar   = $selisih;
    $input_status        = 'S2';
  }else{
    $input_unit_masuk    = abs($selisih);
    $input_unit_keluar   = "0";
    $input_status        = 'S1';
  }
    $input = null;
    $input_id_barang     = $id_barang;
    $input_id_ref_gudang = $id_gudang;
    $input_tanggal       = $tgl;
    $input_id_satuan     = $id_satuan;
    
  $i_simpan = "INSERT INTO tbl_log_stok VALUES ('$input','$input_id_barang','$input_id_ref_gudang','$input_id_satuan','$input_tanggal','$input_unit_masuk','$input_unit_keluar','$input_status','$keterangan','$created_at','$updated_at')";
  if(mysqli_query($connect,$i_simpan)){
    $id_log_stok = mysqli_insert_id($connect);
  }else{
    $id_log_stok = null;
  }

// $q_simpan = "INSERT INTO tbl_stokopname_baru VALUES ('$id_stokopname','$tgl','$id_barang','$id_gudang','$stok','$fisik','$selisih','$id_satuan','$keterangan','$id_log_stok','$status','$created_at','$updated_at')";
  $q_simpan = "INSERT INTO tbl_stokopname_baru (id_stokopname, tanggal, id_barang, id_gudang, stok, fisik, selisih, id_satuan,keterangan,id_log_stok, status, created_at, created_by, created_iduser) VALUES ('$id_stokopname','$tgl','$id_barang','$id_gudang','$stok','$fisik','$selisih','$id_satuan','$keterangan','$id_log_stok','$status','$created_at','$nama_user','$id_user')"; 
  $result_ = mysqli_query($connect,$q_simpan);


  $response = array();
  if ($result_) {
    $response['error'] = false;
    $response['message'] = "Success Insert Data";
  }else {
    $response['error'] = true;
    $response['message'] = "Error Insert Data";
  }
  echo json_encode($response);
  exit;
}
function _tglEng($tgl){
  // 17-11-2018
  $tgl=explode("-", $tgl);
  $tanggal = $tgl[0];
  $bulan = $tgl[1];
  $tahun = $tgl[2];
  return $tahun.'-'.$bulan.'-'.$tanggal;
}
