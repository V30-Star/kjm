<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  protected $table = 'product';
  protected $fillable = [
    'name_barang',
    'qty',
    'harga_modal',
    'harga_akhir',
    'groupproduct_id',
    'merek_id',
    'created_user',
    'updated_user'
  ];

  public $timestamps = true;
  const CREATED_AT = 'created_date';
  const UPDATED_AT = 'updated_date';

  protected $casts = [
    'created_date' => 'datetime',
    'updated_date' => 'datetime',
    'harga_modal' => 'decimal:2',
    'harga_akhir' => 'decimal:2',
  ];

  // Relasi opsional
  public function groupproduct()
  {
    return $this->belongsTo(GroupProduct::class, 'groupproduct_id');
  }
  public function merek()
  {
    return $this->belongsTo(Merek::class, 'merek_id');
  }
}
