<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
  protected $table = 'transaksi_pembelian_detail';
  protected $fillable = ['pembelian_id', 'product_id', 'qty', 'harga_modal', 'subtotal', 'diskon_percent'];
  public $timestamps = false;
  protected $casts = ['diskon_percent' => 'decimal:2'];

  public function product()
  {
    return $this->belongsTo(Product::class, 'product_id');
  }
}
