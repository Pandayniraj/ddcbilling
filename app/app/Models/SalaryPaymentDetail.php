<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;

class SalaryPaymentDetail extends Model
{
    /**
     * @var array
     */
    protected $table = 'payroll_salary_payment_details';

    /**
     * @var array
     */
    protected $fillable = ['salary_payment_id', 'salary_payment_details_label', 'salary_payment_details_value'];

    /**
     * @return bool
     */
    public function isEditable()
    {
        // Prevent user from deleting his own account.
        if (Auth::check() && (Auth::user()->id == $this->user_id || Auth::user()->id == $this->assigned_by || Auth::user()->id == 1)) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isDeletable()
    {
        // Prevent user from deleting his own account.
        if (Auth::check() && (Auth::user()->id == $this->user_id || Auth::user()->id == $this->assigned_by || Auth::user()->id == 1)) {
            return true;
        }

        return false;
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
