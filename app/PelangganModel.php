<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PelangganModel extends Model
{
    //
	protected $table = 'm_pelanggan';
	protected $primaryKey = 'id';

	public function dataGudang(){
  	return $this->belongsTo("App\RefGudang","id_gudang","id");
  }
	
}
