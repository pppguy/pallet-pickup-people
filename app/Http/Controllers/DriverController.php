<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerPrompt;
use App\Models\DriverPickup;
use Carbon\Carbon;
use Inertia\Inertia;

class DriverController extends Controller
{
    public function dashboard()
    {
        // $customers = CustomerPrompt::all();
        // return Inertia::render('DriverDashboard', [
        //     'customers' => $customers,
        // ]);

        //$this->getCustomersYES();

        //$pickups = DriverPickup::all();


    }

    public function claimPickup($pickupId)
    {
        // Logic to claim the pickup
        $pickup = DriverPickup::find($pickupId);
        $pickup->status = 'claimed'; // Update status as needed
        $pickup->save();
        return response()->json(['message' => 'Pickup claimed!']);
    }

    public function confirmPickup($pickupId)
    {
    }

    public function getCustomersYES()
    {
        $customers = CustomerPrompt::where('response', 'YES')->get();
        return Inertia::render('DriverDashboard', [
            'customers' => $customers,
        ]);
    }

    public function getUpcomingPickups()
    {
        // Get today's date
        $today = Carbon::today();

        //only get pickups where the pickup date is today or later
        $pickups = DriverPickup::where('pickup_date', '>=', $today)->get();

        return $pickups;
    }
}
