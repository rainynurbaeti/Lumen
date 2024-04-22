<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stuff extends Model
{
    use SoftDeletes;

    //protected $primarykey = 'no';
   //Set kolom primarykey jika kolom primary key bukan lah kolom id,karena default primary key pada suatu  
    protected $fillable = ["name","category"];

    public function stuffStock()
    {
        return $this->hasOne(StuffStock::class);
    }

    //one to many

    public function inboundStuffs()
    {
        return $this->hasMany(InboundStuff::class);
    }

    public function lendings()
    {
        return  $this->hasMany(Lending::class);
    }
}
