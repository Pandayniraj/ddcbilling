<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    /**
     * @var array
     */
    protected $table = 'invoice';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 'client_id', 'order_type', 'name', 'position', 'address', 'credit_limit','remaning_amount','deposit_amount',
        'bank_deposit','bill_type','client_type', 'ship_date', 'require_date', 'sales_tax', 'status', 'paid',
        'bill_date', 'due_date', 'subtotal', 'discount_amount', 'discount_percent', 'discount_note', 'total_amount',
        'comment', 'org_id', 'customer_pan', 'customer_name', 'tax_amount', 'terms', 'taxable_amount', 'bill_no', 'fiscal_year',
        'is_renewal', 'fiscal_year_id', 'from_stock_location', 'entry_id','outlet_id', 'payment_status'
    ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function client() : BelongsTo
    {
        return $this->belongsTo(\App\Models\Client::class, 'client_id');
    }
    public function posOutlet() : BelongsTo
    {
        return $this->belongsTo(PosOutlets::class, 'outlet_id');
    }

    public function entry()
    {
        return $this->belongsTo(\App\Models\Entry::class, 'entry_id');
    }

    public function outlet()
    {
        return $this->belongsTo(\App\Models\PosOutlets::class);
    }

    public function organization()
    {
        return $this->belongsTo('\App\Models\organization');
    }

    public function lead()
    {
        return $this->belongsTo(\App\Models\Lead::class, 'client_id');
    }

    public function invoicemeta()
    {
        return $this->hasOne(\App\Models\InvoiceMeta::class, 'invoice_id', 'id');
    }
    public function invoicedetails()
    {
        return $this->hasMany(\App\Models\InvoiceDetail::class, 'invoice_id', 'id');
    }
    public function invoicePayments() : HasMany
    {
        return $this->hasMany(InvoicePayment::class, 'invoice_id');
    }
    public function followupInvoice(){
        return $this->hasOne(FollowupInvoice::class, 'invoice_id');
    }

    /**
     * @return bool
     */
    public function isEditable()
    {
        // Protect the admins and users Intakes from editing changes
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
        // Protect the admins and users Intakes from deletion
        if (('admins' == $this->name) || ('users' == $this->name)) {
            return false;
        }

        return true;
    }

    public function hasPerm(Permission $perm)
    {
        // perm 'basic-authenticated' is always checked.
        if ('basic-authenticated' == $perm->name) {
            return true;
        }
        // Return true if the Intake has is assigned the given permission.
        if ($this->perms()->where('id', $perm->id)->first()) {
            return true;
        }
        // Otherwise
        return false;
    }
}
