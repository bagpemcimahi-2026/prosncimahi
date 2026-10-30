<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verifikasi extends Model
{
    protected $table = 'verifikasi';

    protected $fillable = ['folder_id', 'terverifikasi'];

    protected $casts = ['terverifikasi' => 'boolean'];
}
