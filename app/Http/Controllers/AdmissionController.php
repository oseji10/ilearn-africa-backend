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

    public function approval(Request $request)
    {
        $ids = $request->input('ids'); // Array of admission IDs to update
    
        $updatedCount = Admissions::whereIn('id', $ids)->update(['status' => 'ADMITTED']);
    
        return response()->json([
            'message' => 'Successfully admitted',
            'updated_count' => $updatedCount
        ]);
    }
    
}
