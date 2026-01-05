<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Price extends Model
{
    use HasFactory;

    protected $fillable = ['service_id', 'name', 'amount', 'currency', 'stripe_price_id', 'type', 'interval'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
