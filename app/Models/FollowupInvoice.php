<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowupInvoice extends Model
{
    use HasFactory;
    protected $table  = "followup_invoices";
    protected $fillable = ['invoice_id', 'followup_date'];
}
