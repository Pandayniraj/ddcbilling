<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositDeduct extends Model
{
    use HasFactory;
    protected $table = "deposit_deducts";
    protected $fillable = ['deposit_id', 'amount', 'deduct_from', 'invoice_deposit', 'batch'];
}
