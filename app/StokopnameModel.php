<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StokopnameModel extends Model
{
  protected $table = 'tbl_stokopname';
  protected $primaryKey = 'id_stokopname';

  public function dataDetail(){
  	return $this->hasMany("App\StokopnameDetailModel", "id_stokopname","id_stokopname");
  }

  public function dataGudang(){
  	return $this->belongsTo("App\RefGudang", "id_gudang","id");
  }

}
