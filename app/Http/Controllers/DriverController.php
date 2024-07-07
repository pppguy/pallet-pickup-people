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
        $pickups = $this->getUpcomingPickups();
        $user = auth()->user();

        return Inertia::render('DriverDashboard', [
            'pickups' => $pickups,
            'user' => $user,
        ]);

        //$this->getCustomersYES();

        //$pickups = DriverPickup::all();


    }

    public function claimPickup($pickupId)
    {
        $pickup = DriverPickup::find($pickupId);

        if (!is_null($pickup->driver_id)) {
            return response()->json(['message' => 'Pickup has already been claimed by ' . $pickup->driver->name]);
        }


        $pickup->driver_id = auth()->id();
        $pickup->save();
        return response()->json(['message' => 'Pickup claimed!']);
    }

    public function completePickup($pickupId)
    {
        $pickup = DriverPickup::find($pickupId);
        $pickup->pickup_status = 1; //complete
        $pickup->save();
        return response()->json(['message' => 'Pallets collected!']);
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
        $pickups = DriverPickup::where('pickup_date', '>=', $today)
            ->where('pickup_status', 0)
            ->with('customerPrompt.customer', 'driver')
            ->get();

        return $pickups;
    }
}
