<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrashedData extends Model
{
    protected $table = 'tras'; // Ganti dengan nama tabel trash Anda
    protected $fillable = ['data', 'deleted_at']; // Atur kolom yang ingin ditampilkan
}
