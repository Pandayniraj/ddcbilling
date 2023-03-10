<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    /**
     * @var array
     */
    protected $table = 'fin_orders';

    /**
     * @var array
     */
    protected $fillable = ['user_id', 'client_id', 'source', 'order_type', 'name', 'position', 
                            'address',
                            'ship_date', 'require_date', 'sales_tax', 'status', 'paid', 'bill_date', 'due_date',
                            'subtotal', 'discount_amount', 'discount_percent', 'discount_note', 'total_amount',
                            'comment', 'org_id', 'customer_pan', 'tax_amount', 'terms', 'taxable_amount', 'order_id', 'from_stock_location', 'payment_status', 'bill_no', 'is_renewal', 'fiscal_year_id', 'entry_id', ];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class);
    }
    public function product_details(){

        return $this->hasMany(\App\Models\OrderDetail::class, 'order_id', 'id');
    }

    public function lead()
    {
        return $this->belongsTo(\App\Models\Lead::class, 'client_id');
    }

    public function leadname()
    {
        return $this->belongsTo(\App\Models\Lead::class, 'client_id');
    }

    public function organization()
    {
        return $this->belongsTo('\App\Models\organization');
    }

    public function location()
    {
        return $this->belongsTo(\App\Models\ProductLocation::class, 'from_stock_location');
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
