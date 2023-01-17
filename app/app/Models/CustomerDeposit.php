<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerDeposit extends Model
{
    use HasFactory;
    protected $table="customerdeposit";
    protected $fillable=['date','amount','type','closing','client_id','user_id','remarks','reference_no'];
}
