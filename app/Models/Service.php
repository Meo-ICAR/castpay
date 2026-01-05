<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'is_active',
        'required_role_id',
        'stripe_product_id'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function requiredRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'required_role_id');
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    // Per ottenere il prezzo mensile predefinito
    public function monthlyPrice()
    {
        return $this->prices()->where('interval', 'month')->first();
    }
}
