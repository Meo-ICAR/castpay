<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Cashier\Billable;

class Customer extends Model
{
    use HasFactory, Billable;

    protected $fillable = ['company_id', 'name', 'email', 'stripe_id', 'pm_type', 'pm_last_four', 'trial_ends_at'];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
