<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        $customers = Customer::all();
        return Inertia::render('AdminDashboard', [
            'customers' => $customers,
        ]);
    }

    public function sendReminder($customerId)
    {
        // Logic to send reminder (email or text)
        return response()->json(['message' => 'Reminder sent!']);
    }
}
