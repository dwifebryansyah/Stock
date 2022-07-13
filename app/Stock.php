<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{

    protected $table = 'stocks';

    protected $fillable =[
        'kode_trx',
        'barang_id',
        'date',
        'type',
        'harga',
        'jumlah',
        'admin_id',
        'created_at',
        'updated_at'
    ];
}
