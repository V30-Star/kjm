<?php
// app/Models/Purchase.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
  protected $table = 'transaksi_pembelian';
  protected $fillable = [
    'no_transaksi',
    'tanggal',
    'nama_pembeli',
    'total_qty',
    'total_harga',
    'diskon_percent',
    'keterangan',
    'created_user',
    'updated_user'
  ];
  public $timestamps = true;
  const CREATED_AT = 'created_date';
  const UPDATED_AT = 'updated_date';

  public function items()
  {
    return $this->hasMany(PurchaseItem::class, 'pembelian_id');
  }
}
