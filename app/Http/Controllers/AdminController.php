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
use DateTime;

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
        $customers = Customer::with([
            'prompts' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'prompts.driverPickups' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ])
            ->get()
            ->map(function ($customer) {
                $latestPrompt = $customer->prompts->first();
                // If there are no prompts, set the latest pickup to null
                $latestPickup = $latestPrompt ? $latestPrompt->driverPickups->first() : null;
                $lastSuccessfulPickup = $this->getLastSuccessfulPickup($customer->last_successful_pickup_id);
                $currentStatus = $this->calculateStatus($latestPickup, $lastSuccessfulPickup, $latestPrompt);
                return [
                    'customer' => $customer,
                    'latestPrompt' => $latestPrompt,
                    'latestPickup' => $latestPickup,
                    'lastSuccessfulPickup' => $lastSuccessfulPickup,
                    'current_status' => $currentStatus,
                ];
            });

        return response()->json($customers);
    }


    public function getLastSuccessfulPickup($pickupId)
    {
        if ($pickupId == null) {
            return null;
        }

        $pickup = DriverPickup::findOrFail($pickupId);
        return $pickup;
    }

    public function calculateStatus($latestPickup, $lastSuccessfulPickup, $latestPrompt)
    {
        try {
            if ($latestPickup == null) {
                //if ($latestPrompt->created_at > )
                if ($latestPrompt != null) {
                    if ($latestPrompt->created_at > $lastSuccessfulPickup->updated_at) {
                        return "Reminder Sent";
                    } else {
                        return "Nothing yet";
                    }
                }
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

        $pickupDate = $this->calculateDateFromDayOfWeek($customer->pickup_day);

        // Create a CustomerPrompt object
        $customerPrompt = new CustomerPrompt();
        $customerPrompt->customer_id = $customerId; // Associate with the customer ID
        $customerPrompt->response = Null; // Initial status
        $customerPrompt->pickup_date = $pickupDate;
        $customerPrompt->save();

        // $formattedDate = date("F j, Y", strtotime($customer->pickup_day));
        //$formattedDate = $pickupDate->format('F j, Y');


        // Send the email
        Mail::to($customer->contact_email)->send(new CustomerReminder($customerPrompt, $customer->name));

        $test = $customer->contact_email;

        return response()->json(['message' => 'Reminder sent!']);
    }

    public function calculateDateFromDayOfWeek(int $day)
    {
        $today = new DateTime(); // Current date and time

        $currentDayOfWeek = (int) $today->format('w'); // Get current day of the week (0 for Sunday, 1 for Monday, ..., 6 for Saturday)

        // Calculate the number of days to add to get to the next occurrence of $targetDayOfWeek
        $daysToAdd = ($day - $currentDayOfWeek + 7) % 7;

        // Clone today's date and add the calculated days to find the next occurrence
        $nextDate = clone $today;
        $nextDate->modify("+$daysToAdd days");

        // // Format the next date as desired
        // $nextDateFormatted = $nextDate->format('F j, Y');

        //return $nextDateFormatted;
        return $nextDate;
    }

    // YES or NO from customer
    public function handleResponse($response, $id)
    {
        // Logic to handle the response based on $response and $id
        if ($response === 'YES') {
            // Handle approval logic

            $customerPrompt = CustomerPrompt::find($id);

            $customerPrompt->response = $response;
            $customerPrompt->save();

            DriverPickup::create([
                'customer_prompt_id' => $id,
                'pickup_status' => 0,
                'pickup_date' => $customerPrompt->pickup_date,
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
