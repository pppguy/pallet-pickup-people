<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverPickup extends Model
{
    use HasFactory;

    protected $fillable = [
        'pickup_status', // 0 pending, 1 complete, -1 cancelled
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
