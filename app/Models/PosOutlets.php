<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosOutlets extends Model
{

    protected $table = 'pos_outlets';

    /**
     * @var array
     */

    protected $fillable = ['outlet_code', 'name','bank_address_one','bank_address_two','bank_ac_name_one','bank_ac_name_two',
        'bank_name_one','bank_name_two','bank_account_one','bank_account_two', 'short_name','forrandomcustomer',
        'printformat','enabled', 'outlet_type', 'fnb_outlet', 'ledger_id', 'bill_printer', 'kot_printer', 'bill_printer_port',
        'kot_printer_port', 'bot_printer', 'bot_printer_port', 'project_id'];


    public function outlet()
    {
        return $this->belongsTo('\App\Models\PosOutlets');
    }
    public function project() : BelongsTo
    {
        return $this->belongsTo(Store::class, 'project_id');
    }
    public function invoices() : HasMany
    {
        return $this->hasMany(Invoice::class, 'outlet_id');
    }


    public function isEditable()
    {

        if (!\Auth::user()->hasRole('admins')) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isDeletable()
    {

        if (!\Auth::user()->hasRole('admins'))
            return false;

        return true;
    }
}
