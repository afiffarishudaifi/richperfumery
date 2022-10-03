<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PembatalanModel extends Model
{
    //
	protected $table = 'tbl_kasir_batal';
	protected $primaryKey = 'id';

	public function dataPenyedia(){
		return $this->belongsTo("App\SupplierModel", "id_supplier","supplier_id");
	}

	public function dataPelanggan(){
		return $this->belongsTo("App\PelangganModel",'id_pelanggan','id');
	}

	public function dataGudang(){
		return $this->belongsTo("App\RefGudang",'id_gudang','id');
	}
	
}
