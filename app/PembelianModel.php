<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PembelianModel extends Model
{
  protected $table = 'tbl_pembelian';
  protected $primaryKey = 'id_pembelian';

  public function dataPenyedia(){
  	return $this->belongsTo("App\SupplierModel", "id_supplier","supplier_id");
  }

  public function dataGudang(){
  	return $this->belongsTo("App\RefGudang","id_gudang","id");
  }

  public function dataDetail(){
  	return $this->hasMany("App\KonversiPersediaanDetailModel", "id_stokopname","id_stokopname");
  }
  
}
