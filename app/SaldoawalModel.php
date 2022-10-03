<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaldoawalModel extends Model
{
  protected $table = 'tbl_saldoawal';
  protected $primaryKey = 'id_saldoawal';

  public function dataGudang(){
  	return $this->belongsTo("App\RefGudang","id_gudang","id");
  }

  public function dataBarang(){
  	return $this->belongsTo("App\BarangModel","id_barang","barang_id");
  }

  public function dataSatuan(){
  	return $this->belongsTo("App\SatuanModel","id_satuan","satuan_id");
  }
  
}
