<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admissions;
use App\Models\Payments;
use App\Models\Clients;
use Illuminate\Support\Facades\Log;

class AdmissionController extends Controller
{
    public function show(){
        $admissions = Admissions::with(['clients.user', 'payments.courses'])
            // ->where('status', 'pending')
            ->whereHas('payments', function ($query) {
                $query->where('status', 1);
            })
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


public function processCertificate(){
    $admissions = Admissions::with(['clients.user', 'payments.courses'])
    ->where('status', 'ADMITTED')
    // ->orWhere('status', 'COMPLETED')
    ->whereHas('payments', function ($query) {
        $query->where('status', 1);
    })
    ->orderBy(
        Payments::select('created_at')
            ->whereColumn('payments.admission_number', 'admissions.admission_number')
            ->latest()
            ->take(1)
    )
    ->get();

return response()->json([
    'message' => 'Certificates processed successfully',
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



    public function admitAll(Request $request)
    {
        $ids = $request->input('ids');

        // Validate input
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['message' => 'Invalid input'], 400);
        }

        // Check if IDs are valid
        $admissions = Admissions::whereIn('id', $ids)->get();
        if ($admissions->isEmpty()) {
            return response()->json(['message' => 'No valid admissions found'], 404);
        }

        // Update status
        try {
            Admissions::whereIn('id', $ids)->update(['status' => 'ADMITTED']);
            return response()->json(['message' => 'Admissions updated successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error updating admissions: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update admissions'], 500);
        }
    }

    
}
