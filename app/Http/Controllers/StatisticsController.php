<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Admissions;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Payments;

class StatisticsController extends Controller
{
    public function incompleteApplications()
    {
        $incompleteApplications = Client::with('user')->where('status', 'profile_created')
        ->whereHas('user', function ($query) {$query->where('role_id', 3);})
        ->get();
        return response()->json([
            'message' => 'Incomplete applications retrieved successfully',
            'incompleteApplications' => $incompleteApplications,

        ]);
    }


    public function registeredClients()
    {
        $registeredClients = Client::with('user')->where('status', 'registered')
        ->whereHas('user', function ($query) {$query->where('role_id', 3);})
        ->get();
        return response()->json([
            'message' => 'Registered clients retrieved successfully',
            'registeredClients' => $registeredClients,
            
        ]);
    }

    public function pendingAdmissions()
    {
        $pendingAdmissions = Admissions::where('status', 'pending')
        ->whereHas('payments', function ($query) {
            $query->where('status', 1);
        })
        ->get();
        return response()->json([
            'message' => 'Pending admissions retrieved successfully',
            'pendingAdmissions' => $pendingAdmissions,
            
        ]);
    }

    public function currentlyAdmitted()
    {
        $currentlyAdmitted = Admissions::with('users', 'clients', 'payments.courses')->where('status', 'ADMITTED')->get();
        return response()->json([
            'message' => 'Currently Admitted applications retrieved successfully',
            'currentlyAdmitted' => $currentlyAdmitted,
            
        ]);
    }
    

    public function graduated()
    {
        $graduated = Admissions::with('users', 'clients', 'payments.courses')->where('status', 'COMPLETED')->get();
        return response()->json([
            'message' => 'Currently Admitted applications retrieved successfully',
            'graduated' => $graduated,
            
        ]);
    }


    public function paymentsToday(){
        $today = Carbon::today();

        $paymentsToday = Payments::with('users', 'clients')->where('status', 1)->whereDate('created_at', $today)->get();

        return response()->json([
            'message' => 'Payments for today retrived succesfully',
            'paymentsToday' => $paymentsToday,
        ]);
    
}


public function paymentsThisWeek(){
    $startOfWeek = Carbon::now()->startOfWeek(); // Defaults to Monday
    $endOfWeek = Carbon::now()->endOfWeek(); 
    $paymentsThisWeek = Payments::with('users', 'clients', 'courses')->where('status', 1)->whereBetween('created_at', [$startOfWeek, $endOfWeek])->get();

    return response()->json([
        'message' => 'Payments for this week retrieved successfully',
        'paymentsThisWeek' => $paymentsThisWeek,
    ]);

}

public function paymentsThisMonth(){
    $startOfMonth = Carbon::now()->startOfMonth();
    $endOfMonth = Carbon::now()->endOfMonth();
    $paymentsThisMonth = Payments::with('users', 'clients', 'courses')->where('status', 1)->whereBetween('created_at', [$startOfMonth, $endOfMonth])->get();

    return response()->json([
        'message' => 'Payment for this month retrieved successfully',
        'paymentsThisMonth' => $paymentsThisMonth,
    ]);

}


public function allPayments(){
    $allPayments = Payments::with('users', 'clients', 'courses')->where('status', 1)->get();

    return response()->json([
        'message' => 'All payments retrieved successfully',
        'allPayments' => $allPayments,
    ]);

}

}