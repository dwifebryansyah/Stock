<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barangs';

    protected $fillable =[
        'nama_barang',
        'harga',
        'stock',
        'admin_id',
        'created_at',
        'updated_at'
    ];
}
