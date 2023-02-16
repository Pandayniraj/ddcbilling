<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutletUser extends Model
{
    protected $table = 'outlet_users';

    /**
     * @var array
     */
    protected $fillable = ['outlet_id', 'user_id'];

    public function outlet() : BelongsTo
    {
        return $this->belongsTo(PosOutlets::class, 'outlet_id');
    }
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isEditable()
    {
        // Protect the admins and users Leads from editing changes
        if (\Auth::user()->id != $this->user_id && \Auth::user()->id != 1 && \Auth::user()->id != 5 && \Auth::user()->id != 4 && \Auth::user()->id != 3) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isDeletable()
    {
        // Protect the admins and users Leads from deletion
        /*if ( (\Auth::user()->id != $this->user_id  && \Auth::user()->id != 1 && \Auth::user()->id != 5 && \Auth::user()->id != 4 && \Auth::user()->id != 3)) {
            return false;
        } */

        if (!\Auth::user()->hasRole('admins'))
            return false;

        return true;
    }
}
