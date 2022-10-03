<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailPembelianModel extends Model
{
  protected $table = 'tbl_pembelian_detail';
  protected $primaryKey = 'pembelian_detail_id';
  protected $fillable = ['pembelian_detail_qty','pembelian_detail_harga'];
}
