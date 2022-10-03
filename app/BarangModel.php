<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BarangModel extends Model
{
  protected $table = 'tbl_barang';
  protected $primaryKey = 'barang_id';
  protected $searchableColumns = ['kode_barang'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
  protected $fillable = ['kode_barang'];
}
