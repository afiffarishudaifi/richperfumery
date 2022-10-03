<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StokopnameDetailModel extends Model
{
    //
  protected $table = 'tbl_stokopname_detail';
  protected $primaryKey = 'id_detail_stokopname';

  public function dataBarang(){
  	return $this->belongsTo("App\BarangModel", "id_barang","barang_id");
  }

  public function dataGudang(){
  	return $this->belongsTo("App\RefGudang", "id_gudang","id");
  }
  
}
