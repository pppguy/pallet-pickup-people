<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverPickup extends Model
{
    use HasFactory;

    protected $fillable = [
        'pickup_status',
        'driver_id',
        'customer_prompt_id',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function customerPrompt()
    {
        return $this->belongsTo(CustomerPrompt::class, 'customer_prompt_id');
    }
}
