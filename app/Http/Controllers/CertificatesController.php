<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admissions;
use App\Models\Client;
use App\Models\CourseList;

class CertificatesController extends Controller
{
    public function myCertificates($client_id){
        $my_certificates = Admissions::with('clients', 'payments.courses')
        ->where('client_id', $client_id)
        ->where('status', 'COMPLETED')
        ->get();
        return response()->json([
            'message' => 'Certificates retrieved successfully',
            'certificates' => $my_certificates
        ]);
    }

    public function clientCertificates(){
        $certificates = Admissions::with("clients",  "users", "payments.courses.centers")->where('status', 'COMPLETED')->get();
        return response()->json([
            'message' => 'Certificates retrieved successfuly',
            'certificates' => $certificates
        ]);
    }


    public function batchProcess(Request $request)
{
    $ids = $request->input('admission_number');

    // Validate input
    if (empty($ids) || !is_array($ids)) {
        return response()->json(['message' => 'Invalid input'], 400);
    }

    // Check if IDs are valid
    $admissions = Admissions::whereIn('admission_number', $ids)->get();
    if ($admissions->isEmpty()) {
        return response()->json(['message' => 'No valid admissions found'], 404);
    }

    // Update status
    try {
        Admissions::whereIn('admission_number', $ids)->update(['status' => 'COMPLETED']);
        return response()->json(['message' => 'Certificates generated successfully'], 200);
    } catch (\Exception $e) {
        Log::error('Error updating admissions: ' . $e->getMessage());
        return response()->json(['message' => 'Failed to process certificates'], 500);
    }
}

   
}
