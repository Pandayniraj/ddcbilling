<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePayment extends Model
{
    /**
     * @var array
     */
    protected $table = 'invoice_payment';

    /**
     * @var array
     */
    protected $fillable = ['date', 'invoice_id', 'return_id', 'purchase_id', 'reference_no', 'transaction_id', 'paid_by',
        'cheque_no', 'cc_no', 'cc_holder', 'cc_month', 'cc_year', 'cc_type', 'amount', 'currency', 'attachment', 'type',
        'note', 'pos_paid', 'pos_balance', 'approval_code', 'created_by', 'client_id', 'entry_id'];


    public function user() : BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }
    public function paidby() : BelongsTo
    {
        return $this->belongsTo(\App\Models\COALedgers::class, 'paid_by');
    }
    public function client(){
        return $this->belongsTo(Client::class, 'client_id');
    }
    public function purchase() : BelongsTo
    {
        return $this->belongsTo(\App\Models\PurchaseOrder::class, 'purchase_id');
    }
    public function invoice() : BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }


    /**
     * @return bool
     */
    public function isEditable()
    {
        // Protect the admins and users Leadtypes from editing changes
        if (('admins' == $this->name) || ('users' == $this->name)) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isDeletable()
    {
        // Protect the admins and users Leadtypes from deletion
        if (('admins' == $this->name) || ('users' == $this->name)) {
            return false;
        }

        return true;
    }
    public function entry()
    {
        return $this->belongsTo(\App\Models\Entry::class, 'entry_id');
    }
}
