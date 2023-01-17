<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Payroll extends Model
{
    /**
     * @var array
     */

	protected $table = 'payroll';

	/**
     * @var array
     */
    protected $fillable = ['created_by','departments_id','project_id','date'];


    public function department()
    {
        return $this->belongsTo('\App\Models\Department','departments_id','departments_id');
    }


    public function payrollDetails()
    {
        return $this->hasMany('App\Models\PayrollDetails');
    }




/**
     * @return bool
     */
    public function isEditable()
    {
        // Protect the admins and users Intakes from editing changes
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
        // Protect the admins and users Intakes from deletion
        if ( ('admins' == $this->name) || ('users' == $this->name) ) {
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
        if ( $this->perms()->where('id' , $perm->id)->first() ) {
            return true;
        }
        // Otherwise
        return false;
    }

    public function getTotalEmployeeAttribute(){
        return $this->payrollDetails()->count();
    }
    public function getTotalAmountAttribute(){
        return $this->payrollDetails()->sum('net_salary');
    }




}
