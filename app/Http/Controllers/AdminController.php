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

    // public function getCustomerStatusData() {
    //     // Get today's date
    //     $today = Carbon::today();

    //     //only get pickups where the pickup date is today or later
    //     $data = Customer::with('customerPrompt.customer', 'driver')
    //         ->get();

    //     return $data;
    // }

    public function getCustomerStatusData()
    {
        $customers = Customer::select('customers.*')
            ->with([
                'prompts' => function ($query) {
                    $query->orderBy('created_at', 'desc')->first();
                },
                'prompts.driverPickups' => function ($query) {
                    $query->orderBy('created_at', 'desc')->first();
                }
            ])
            ->get()
            ->map(function ($customer) {
                $latestPrompt = $customer->prompts->first();
                $latestPickup = $latestPrompt ? $latestPrompt->driverPickups->first() : null;
                return [
                    'customer' => $customer,
                    'latestPrompt' => $latestPrompt,
                    'latestPickup' => $latestPickup
                ];
            });

        return response()->json($customers);
    }

    public function sendReminder($customerId)
    {
        // Logic to send reminder (email or text)
        return response()->json(['message' => 'Reminder sent!']);
    }
}
