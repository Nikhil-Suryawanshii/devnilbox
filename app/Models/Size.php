<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Size extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function translations(): HasMany
    {
        return $this->hasMany(TranslateUtility::class);
    }

    /**
     * Scope a query to only include active records.
     */
    public function scopeIsActive($query)
    {
        return $query->where('is_active', true);
    }
    
     public function subcategory()
    {
        return $this->belongsTo(SubCategory::class,'subcategory_id');
    }

    public function subcategories()
    {
        return $this->belongsToMany(
            SubCategory::class,
            'size_subcategory',
            'size_id',
            'subcategory_id'
        );
    }
}
