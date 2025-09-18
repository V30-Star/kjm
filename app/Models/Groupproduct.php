<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupProduct extends Model
{
  protected $table = 'groupproduct';
  protected $fillable = [
    'kode_groupproduct',
    'name_groupproduct',
    'status',
    'created_user',
    'updated_user',
  ];

  public $timestamps = true;
  const CREATED_AT = 'created_date';
  const UPDATED_AT = 'updated_date';

  protected $casts = [
    'created_date' => 'datetime',
    'updated_date' => 'datetime',
  ];
}
