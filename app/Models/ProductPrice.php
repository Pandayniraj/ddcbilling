<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrice extends Model
{
    protected $table = 'product_prices';
    protected $fillable=['project_id', 'product_id', 'distributor_price', 'retailer_price', 'customer_price'];


    public function project(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'project_id');
    }
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function getProjectNameAttribute()
    {
        return $this->project->name??'';
    }


    public function isEditable()
    {
        if (('admins' == $this->name) || ('users' == $this->name)) return false;
        return true;
    }

    /**
     * @return bool
     */
    public function isDeletable()
    {
        // Protect the admins and users Courses from deletion
        if (('admins' == $this->name) || ('users' == $this->name)) return false;

        return true;
    }
}
