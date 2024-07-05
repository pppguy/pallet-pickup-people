<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Inertia\Inertia;

class DriverController extends Controller
{
    public function dashboard()
    {
        $customers = Customer::where('status', 'pending')->get();
        return Inertia::render('DriverDashboard', [
            'customers' => $customers,
        ]);
    }

    public function claimPickup($customerId)
    {
        // Logic to claim the pickup
        $customer = Customer::find($customerId);
        $customer->status = 'claimed'; // Update status as needed
        $customer->save();
        return response()->json(['message' => 'Pickup claimed!']);
    }
}
