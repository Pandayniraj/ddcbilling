<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    /**
     * @var array
     */

    protected $table = 'expenses';

    protected $fillable = ['tax_type','tax_amount','user_id','expenses_account','amount','paid_through','vendor_id','date', 'reference', 'attachment','org_id','project_id','entry_id','expense_type','tag_id','currency_id','bill_no'];


    public function vendor()
    {
        return $this->belongsTo('App\Models\Client','vendor_id');
    }

    public function paidledger()
    {
        return $this->belongsTo('App\Models\COALedgers','paid_through','id');
    }

    public function ledger()
    {
        return $this->belongsTo('App\Models\COALedgers','expenses_account','id');
    }

    public function entry()
    {
        return $this->belongsTo(\App\Models\Entry::class, 'entry_id');
    }


    public function tag()
    {
        return $this->belongsTo(\App\Models\IncomeExpenseCategory::class, 'tag_id');
    }

    public function currency()
    {
        return $this->belongsTo(\App\Models\Country::class,'currency_id');
    }


    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

    /**
     * @return bool
     */
    public function isEditable()
    {
        // Protect the admins and users Communication from editing changes
        if ( ('admins' == $this->name) || ('users' == $this->name) ) {
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
        if ( ('admins' == $this->name) || ('users' == $this->name) ) {
            return false;
        }

        return true;
    }

}
