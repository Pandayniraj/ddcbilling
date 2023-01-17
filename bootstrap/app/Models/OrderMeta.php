<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class OrderMeta extends Model
{

     /**
      * @var array
      */

     protected $table = 'fin_orders_meta';

     /**
      * @var array
      */
     protected $fillable = ['order_id', 'sync_with_ird', 'is_bill_active', 'is_realtime', 'void_reason', 'posting_entry_id', 'settle_entry_id', 'cancel_date', 'credit_note_no', 'credit_user_id', 'settlement', 'is_posted', 'parent_id'];
}
