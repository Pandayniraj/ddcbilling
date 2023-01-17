<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceChangeRequest extends Model
{
    protected $table = 'attendance_change_requests';

    protected $fillable = ['attendance_id', 'user_id','attendance_status','date','actual_time', 'requested_time', 'approved_by','shift_id', 'reason', 'status', 'is_forwarded'];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }


    public function approvedBy()
    {
        return $this->belongsTo(\App\User::class, 'approved_by');
    }

    public function isDeletable()
    {
        if (\Auth::user()->hasRole('admins') || \Auth::user()->hasRole('hr-manager') || \Auth::user()->id == $this->user_id || \Auth::user()->isAuthsupervisor()) {
            return true;
        }

        return false;
    }

}
