<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerDeposit extends Model
{
    use HasFactory;
    protected $table="customerdeposit";
    protected $fillable=['date','amount','type','closing','client_id','user_id','remarks','reference_no', 'balance', 'entry_id'];
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    public function deducts()
    {
        return $this->hasMany(DepositDeduct::class, 'deposit_id');
    }
    public function entry()
    {
        return $this->belongsTo(\App\Models\Entry::class, 'entry_id');
    }
}
