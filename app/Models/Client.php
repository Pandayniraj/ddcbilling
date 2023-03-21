<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    /**
     * @var array
     */
    protected $table = 'clients';

    protected $fillable = ['route_id','name', 'location', 'vat', 'phone', 'email', 'website', 'industry', 'stock_symbol',
        'type', 'enabled', 'org_id', 'ledger_id', 'notes', 'reminder', 'bank_name', 'bank_branch', 'bank_account',
        'relation_type', 'physical_address', 'customer_group','image','parent_distributor','deposit_amount', 'outlet_id', 'threshold_amount', 'threshold_time'];

    public function paidby() : HasMany
    {
        return $this->hasMany(\App\Models\InvoicePayment::class, 'paid_by');
    }
    public function contact() : HasOne
    {
        return $this->hasOne(self::class);
    }
    public function locations() : BelongsTo
    {
        return $this->belongsTo(\App\Models\CityMaster::class, 'location');
    }
    public function ledger()
    {
        return $this->belongsTo(\App\Models\COALedgers::class, 'ledger_id');
    }
    public function getledgerNameAttribute()
    {
        return $this->ledger->name??'';
    }
    public function groups()
    {
        return $this->belongsTo(\App\Models\CustomerGroup::class, 'customer_group');
    }
    public function invoices() : HasMany
    {
        return $this->hasMany(\App\Models\Invoice::class, 'client_id');
    }
    public function payments() : HasMany
    {
        return $this->hasMany(\App\Models\InvoicePayment::class, 'client_id');
    }

    /**
     * @return bool
     */
    public function isEditable()
    {
        // Protect the admins and users Communication from editing changes
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
        // Protect the admins and users Communication from deletion
        if (('admins' == $this->name) || ('users' == $this->name)) {
            return false;
        }

        return true;
    }
    public function deposit()
    {
        return $this->hasMany(CustomerDeposit::class, 'client_id');
    }
    public function depositClosing()
    {
        return $this->deposit()->latest()->first()->closing??0;
    }

}
