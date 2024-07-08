<?php

namespace App\Http\Controllers;

use App\Mail\CustomerReminder;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerPrompt;
use App\Models\DriverPickup;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Carbon\Carbon;


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
        // Retrieve the customer by ID
        $customer = Customer::find($customerId);


        // if (!$customer) {
        //     return response()->json(['message' => 'Customer not found'], 404);
        // }

        // Create a CustomerPrompt object
        $customerPrompt = new CustomerPrompt();
        $customerPrompt->customer_id = $customerId; // Associate with the customer ID
        $customerPrompt->response = Null; // Initial status
        $customerPrompt->save();

        // Send the email
        Mail::to($customer->contact_email)->send(new CustomerReminder($customerPrompt));

        $test = $customer->contact_email;

        return response()->json(['message' => 'Reminder sent!']);
    }

    // YES or NO from customer
    public function handleResponse($response, $id)
    {
        // Calculate the pickupdate (today + 2 days)
        $pickupdate = Carbon::now()->addDays(2);

        // Logic to handle the response based on $response and $id
        if ($response === 'YES') {
            // Handle approval logic

            $customerPrompt = CustomerPrompt::find($id);

            $customerPrompt->response = $response;
            $customerPrompt->save();

            DriverPickup::create([
                'customer_prompt_id' => $id,
                'pickup_status' => 0,
                'pickup_date' => $pickupdate,
            ]);
        } elseif ($response === 'NO') {
            // Handle rejection logic
        } else {
            // Handle other cases
        }

        // Optionally, redirect to another route or return a view
        //return redirect()->route('your.redirect.route');
    }
}
