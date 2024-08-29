<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admissions;
use App\Models\Payments;
use App\Models\Clients;

class AdmissionController extends Controller
{
    public function show(){
        $admissions = Admissions::with(['clients.user', 'payments.courses'])
        ->where('status', 'pending')
        ->orderBy(
            Payments::select('created_at')
                ->whereColumn('payments.admission_number', 'admissions.admission_number')
                ->latest()
                ->take(1)
        )
        ->get();
    
        return response()->json([
            'message' => 'Admissions retrieved successfully',
            'admissions' => $admissions,
        ]);
    }

    public function admittedClients(){
        $admissions = Admissions::with(['clients.user', 'payments.courses'])
        ->where('status', 'ADMITTED')
        ->orWhere('status', 'COMPLETED')
        ->orderBy(
            Payments::select('created_at')
                ->whereColumn('payments.admission_number', 'admissions.admission_number')
                ->latest()
                ->take(1)
        )
        ->get();
    
        return response()->json([
            'message' => 'Admissions retrieved successfully',
            'admissions' => $admissions,
        ]);
    }

    public function myAdmissions(Request $request, $client_id){
        $admissions = Admissions::with(['clients.user', 'payments.courses'])
        ->where('client_id', $client_id)
        ->orderBy(
            Payments::select('created_at')
                ->whereColumn('payments.admission_number', 'admissions.admission_number')
                ->latest()
                ->take(1)
        )
        ->get();
    
        return response()->json([
            'message' => 'Admissions retrieved successfully',
            'admissions' => $admissions,
        ]);
    }

    

    public function approval(Request $request, $admission_number)
    {
        // $admission_number = $request->input('admission_number'); // Array of admission IDs to update
    
        $updatedCount = Admissions::where('admission_number', $admission_number)->update(['status' => 'ADMITTED', 'created_by' => auth()->id()]);
    
        return response()->json([
            'message' => 'Successfully admitted',
            'updated_count' => $updatedCount
        ]);
    }
    
}
