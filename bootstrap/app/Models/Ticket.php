<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';

    protected $fillable = ['user_id', 'cc_users', 'notice', 'source', 'help_topic', 'department_id', 'sla_plan', 'due_date', 'assign_to', 'issue_summary', 'detail_reason', 'ticket_status', 'internal_notes', 'from_user', 'ticket_number', 'from_email', 'from_ext', 'from_phone', 'form_source','company','position'];

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


    public function user()
    {
        return $this->belongsTo(\App\User::class,'assign_to');
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
}
