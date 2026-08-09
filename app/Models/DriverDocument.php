<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'license_path', 
        'insurance_path', 
        'vehicle_license_path',
        'road_worthiness_path',
        'hackney_permit_path',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
