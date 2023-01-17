<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use YoHang88\LetterAvatar\LetterAvatar;

class Department extends Model
{
    /**
     * @var array
     */
    protected $table = 'tbl_departments';

    /**
     * @var array
     */
    protected $fillable = ['deptname', 'department_head_id', 'org_id','division_id'];

    public function designation()
    {
        return $this->hasMany(\App\Models\Designation::class, 'departments_id', 'departments_id');
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

    public function division()
    {
        return $this->belongsTo(\App\Models\Division::class,'division_id');
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

     public function getAvatarAttribute()
    {
        return new LetterAvatar($this->deptname);
    }


    
}
