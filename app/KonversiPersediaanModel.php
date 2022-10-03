<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KonversiPersediaanModel extends Model
{
  protected $table = 'tbl_konversipersediaan';
  protected $primaryKey = 'id_konversi';

  public function dataBarang(){
  	return $this->belongsTo("App\BarangModel", "id_barang","barang_id");
  }

  public function dataGudang(){
  	return $this->belongsTo("App\RefGudang","id_gudang","id");
  }

  public function dataSatuan(){
  	return $this->belongsTo("App\SatuanModel","id_satuan","satuan_id");
  }
  
  public function dataSatuan_Konversi(){
  	return $this->belongsTo("App\SatuanModel","id_satuan_konversi","satuan_id");
  }

  public function dataDetail(){
  	return $this->hasMany("App\KonversiPersediaanDetailModel", "id_konversi","id_konversi");
  }

}
