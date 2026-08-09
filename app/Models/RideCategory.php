<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RideCategory extends Model
{
    protected $guarded = [];

    /**
     * Scope a query to filter by service type.
     */
    public function scopeForServiceType($query, ?string $serviceType)
    {
        if ($serviceType) {
            return $query->where('service_type', $serviceType);
        }
        return $query;
    }
}
