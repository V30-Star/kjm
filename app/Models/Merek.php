<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merek extends Model
{
  protected $table = 'merek';
  protected $fillable = [
    'kode_merek',
    'name_merek',
    'status',
    'created_user',
    'updated_user'
  ];

  public $timestamps = true;
  const CREATED_AT = 'created_date';
  const UPDATED_AT = 'updated_date';

  protected $casts = [
    'created_date' => 'datetime',
    'updated_date' => 'datetime',
  ];
}
