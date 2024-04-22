<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrashedData extends Model
{
    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'TrashedData';

    /**
     * Kolom-kolom yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'price', 'deleted_at'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}

