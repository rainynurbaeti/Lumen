<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use function PHPUnit\Framework\returnSelf;

class Restoration extends Model
{
    use SoftDeletes;
    protected $fillable = ["user_id","lending_id","date_time","total_good_stuff","total_defec_stuff"];

    public function user()
    {
        return $this->hasMany(User::class);
    }

    public function lending()
    {
        return $this->belongsTo(Lending::class);
    }

}

