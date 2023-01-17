<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spareparts extends Model
{
    use HasFactory;

    protected $table="spareparts";

    protected $fillable =['product_id','brand_id','name','price','qty','remarks','description'];

    public function products(){
        return $this->belongsTo(\App\Models\Product::class, 'product_id','id');
    }
    public function brands(){
        return $this->belongsTo(\App\Models\Brand::class, 'brand_id','id');
    }
}
