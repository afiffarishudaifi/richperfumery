<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KonversiPersediaanDetailModel extends Model
{
  protected $table = 'tbl_konversipersediaan_detail';
  protected $primaryKey = 'id_konversi_konversi';

  public function dataBarang(){
  	return $this->belongsTo("App\BarangModel", "id_barang","barang_id");
  }

  public function dataGudang(){
  	return $this->belongsTo("App\RefGudang","id_gudang","id");
  }

  public function dataSatuan(){
  	return $this->belongsTo("App\SatuanModel","id_satuan","satuan_id");
  }

}
