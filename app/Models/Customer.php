<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'pickup_day',
        'pickup_frequency',
        'contact_method',
        'contact_email',
        'contact_phone',
    ];

    public function prompts()
    {
        return $this->hasMany(CustomerPrompt::class);
    }
}
