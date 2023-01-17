<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryRoute extends Model
{
    use HasFactory;
    protected $table="deliveryroutes";
    protected $fillable=['org_id', 'distributor_id','user_id','route_name','route_code'];
}
