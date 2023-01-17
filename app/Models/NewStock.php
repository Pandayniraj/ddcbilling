<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewStock extends Model
{
    use HasFactory;
    protected $table="stocks";
    protected $fillable=['date','user_id','org_id','store_id'];


    public function users(){
        return $this->belongsTo(\App\User::class,'user_id','id');
    }

    public function org(){
        return $this->belongsTo(\App\Models\Organization::class,'org_id','id');
    }
    public function location(){
        return $this->belongsTo(\App\Models\PosOutlets::class,'store_id','id');
    }
}
