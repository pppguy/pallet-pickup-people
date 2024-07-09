<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPrompt extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'response',
        'pickup_date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function driverPickups()
    {
        return $this->hasMany(DriverPickup::class);
    }
}
