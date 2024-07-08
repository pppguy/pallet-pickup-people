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
                    'latestPickup' => $latestPickup,
                    'lastSuccessfulPickup' => $this->getLastSuccessfulPickupDate($customer->last_successful_pickup_id),
                    'current_status' => $this->calculateStatus($latestPickup),
                ];
            });

        return response()->json($customers);
    }


    public function getLastSuccessfulPickupDate($pickupId)
    {
        if ($pickupId == null) {
            return null;
        }

        $pickup = DriverPickup::findOrFail($pickupId);
        return $pickup;
    }

    public function calculateStatus($latestPickup)
    {
        try {
            if ($latestPickup == null) {
                return "Reminder Sent";
            } else {
                if ($latestPickup->driver_id == null) {
                    return "Pickup Created";
                } else {
                    if ($latestPickup->pickup_status == 1) {
                        return "Pickup Complete";
                    } else {
                        return "Assigned to " . $latestPickup->driver->name;
                    }
                }
            }
            return "Nothing yet";
        } catch (\Exception $e) {
        }

        return "Nothing yet";
    }



    public function updateCustomer(Request $request, $customerId)
    {
        //TODO: validation

        try {
            $customer = Customer::findOrFail($customerId);

            // Update the customer attributes
            $customer->name = $request->input('name');
            $customer->address = $request->input('address');
            $customer->pickup_day = $request->input('pickup_day');
            $customer->pickup_frequency = $request->input('pickup_frequency');
            $customer->contact_method = $request->input('contact_method');
            $customer->contact_email = $request->input('contact_email');
            $customer->contact_phone = $request->input('contact_phone');

            $customer->save();

            return response()->json(['message' => 'Customer updated successfully', 'customer' => $customer]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating customer: ' . $e->getMessage()], 500);
        }
    }

    public function sendReminder($customerId)
    {
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

        // Redirect to the thank you page
        return redirect()->route('thank-you');
    }

    public function thankYouPage()
    {
        return view('thank-you');
    }
}
