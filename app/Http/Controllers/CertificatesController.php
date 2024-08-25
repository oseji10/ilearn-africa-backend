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
}
