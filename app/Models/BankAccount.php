<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'country_code',
        'recipient_name',
        'bank_name',
        'iban',
        'swift_bic',
        'purpose_of_payment',
        'is_primary',
    ];

    /**
     * Get the user that owns the bank account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the country name from country code.
     */
    public function getCountryNameAttribute(): string
    {
        $countries = [
            'TR' => 'Turkey',
            'MK' => 'North Macedonia',
            'UA' => 'Ukraine',
            'XK' => 'Kosovo',
        ];

        return $countries[$this->country_code] ?? 'Unknown';
    }

    /**
     * Scope a query to only include accounts for a specific country.
     */
    public function scopeCountry($query, string $countryCode)
    {
        return $query->where('country_code', $countryCode);
    }
    
    /**
     * Scope a query to only include primary account.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

}
