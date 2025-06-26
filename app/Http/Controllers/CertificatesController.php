<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admissions;
use App\Models\Client;
use App\Models\CourseList;
use App\Models\CourseModule;

use NumberToWords\NumberToWords;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Mail;
// use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\EmailCertificate;

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


public function downloadCertificate(Request $request, $admission_number)
    {
        $admission = Admissions::with("clients", "users", "payments.courses.centers", "payments.courses.modules")
            ->where('admission_number', $admission_number)
            ->first();
    
        if (!$admission) {
            return response()->json(['error' => 'Admission not found'], 404);
        }
    
        // Certificate type
        $certificate_type = $admission->certificate_type;
    
        $certificate_data = [
            'id' => $admission->id,
            'client_id' => $admission->client_id,
            'amount' => $admission->amount,
            'created_at' => $admission->created_at->format('Y-m-d'),
            'firstname' => $admission->clients->firstname,
            'surname' => $admission->clients->surname,
            'othernames' => $admission->clients->othernames,
            'phone_number' => $admission->users->phone_number,
            'email' => $admission->users->email,
            'payment_method' => $admission->payment_method,
            'transaction_reference' => $admission->transaction_reference,
            'course_name' => $admission->payments->courses->course_name,
            'course_id' => $admission->payments->courses->course_id,
            'admission_date' => $admission->created_at,
            'admission_number' => $admission->admission_number,
            'center_name' => $admission->payments->courses->centers->center_name,
            'certification_name' => $admission->payments->courses->certification_name,
            'admission_id' => $admission->id,
            'name_on_certificate' => $admission->clients->name_on_certificate,
            'modules' => $admission->payments->courses->modules->pluck('modules')->toArray(), // Add modules
        ];
    
        // Generate QR code
        $verificationUrl = route('pdf.verify_certificate', ['admission_number' => $admission->admission_number]);
        $qrCode = Builder::create()
            ->writer(new PngWriter())
            ->data($verificationUrl)
            ->size(200)
            ->build();
    
        $certificate_data['qr_code'] = $qrCode->getDataUri();
    
        // Update admission status
        Admissions::where('admission_number', $admission_number)->update(['status' => 'COMPLETED']);
    
        if ($certificate_type == 1) {
            $pdf = Pdf::loadView('pdf.certificate', $certificate_data)
                ->setPaper('a4', 'landscape');
    
            Mail::to($admission->users->email)->send(new EmailCertificate($certificate_data));
            return $pdf->download("admission-{$admission->admission_number}.pdf");
    
        } elseif ($certificate_type == 2) {
            $pdf = Pdf::loadView('pdf.ilearn_certificate', $certificate_data)
                ->setPaper('a4', 'landscape');
    
            Mail::to($admission->users->email)->send(new EmailCertificate($certificate_data));
            return $pdf->download("ilearn-{$admission->admission_number}.pdf");
    
        } elseif ($certificate_type == 3) {
            // Generate both PDFs
            $pdf1 = Pdf::loadView('pdf.certificate', $certificate_data)
                ->setPaper('a4', 'landscape')
                ->output();
    
            $pdf2 = Pdf::loadView('pdf.ilearn_certificate', $certificate_data)
                ->setPaper('a4', 'landscape')
                ->output();
    
            // Create a ZIP archive with both PDFs
            $zipFileName = "certificates_{$admission->admission_number}.zip";
            $tempZipPath = storage_path("app/public/{$zipFileName}");
    
            $zip = new \ZipArchive;
            if ($zip->open($tempZipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                $zip->addFromString("certificate.pdf", $pdf1);
                $zip->addFromString("ilearn_certificate.pdf", $pdf2);
                $zip->close();
            }
    
            // Mail one of the certificates (optional)
            Mail::to($admission->users->email)->send(new EmailCertificate($certificate_data));
    
            // Return the ZIP download
            return response()->download($tempZipPath)->deleteFileAfterSend(true);
        }
    
        return response()->json(['error' => 'Invalid certificate type'], 400);
    }
    

public function emailCertificate(Request $request, $admission_number)
{
      // Fetch the payment data based on the transaction_reference
    $admission = Admissions::with("clients",  "users", "payments.courses.centers")->where('admission_number', $admission_number)->first();
 
    
    // Check if payment exists
    if (!$admission) {
        return response()->json(['error' => 'Admission not found'], 404);
    }

    // Prepare the data for the PDF
    $certificate_data = [
        'id' => $admission->id ?? '',
        'client_id' => $admission->client_id ?? '',
        'amount' => $admission->amount ?? '',
        'created_at' => $admission->created_at->format('Y-m-d') ?? '',
        'firstname' => $admission->clients->firstname ?? '',
        'surname' => $admission->clients->surname ?? '',
        'othernames' => $admission->clients->othernames ?? '',
        'phone_number' => $admission->users->phone_number ?? '',
        'email' => $admission->users->email ?? '',
        'payment_method' => $admission->payment_method ?? '',
        'transaction_reference' => $admission->transaction_reference ?? '',
        'course_name' => $admission->payments->courses->course_name ?? '',
        'course_id' => $admission->payments->courses->course_id ?? '',
        'admission_date' => $admission->created_at  ?? '',
        'admission_number' => $admission->admission_number ?? '',
        'center_name' => $admission->payments->courses->centers->center_name ?? '',
        'certification_name' => $admission->payments->courses->certification_name ?? '',
        'admission_id' => $admission->id ?? '',
        'name_on_certificate' => $admission->clients->name_on_certificate ?? '',
        // Add other necessary fields
    ];
    $status = "COMPLETED";
    $validated['status'] = $status;
    $update_admission = Admissions::where('admission_number', $request->admission_number)->update($validated);


    $verificationUrl = route('pdf.verify_certificate', ['admission_number' => $request->admission_number]);
    $qrCode = Builder::create()
    ->writer(new PngWriter())
    ->data(route('pdf.verify_certificate', ['admission_number' => $request->admission_number]))
    ->size(200)
    ->build();

// Save the QR code to a file or directly use it in the PDF
$certificate_data['qr_code'] = $qrCode->getDataUri();

    $pdf = Pdf::loadView('pdf.certificate', $certificate_data)
      ->setPaper('a4', 'landscape');
    
    Mail::to($admission->users->email)->send(new EmailCertificate($certificate_data));
    // return $pdf->download("admission-{$admission->admission_number}.pdf");
}




public function processIlearnAfricaCertificate(Request $request, $admission_number)
{
      // Fetch the payment data based on the transaction_reference
    $admission = Admissions::with("clients",  "users", "payments.courses.centers")->where('admission_number', $admission_number)->first();
 
    
    // Check if payment exists
    if (!$admission) {
        return response()->json(['error' => 'Admission not found'], 404);
    }

    // Prepare the data for the PDF
    $certificate_data = [
        'id' => $admission->id ?? '',
        'client_id' => $admission->client_id ?? '',
        'amount' => $admission->amount ?? '',
        'created_at' => $admission->created_at->format('Y-m-d') ?? '',
        'firstname' => $admission->clients->firstname ?? '',
        'surname' => $admission->clients->surname ?? '',
        'othernames' => $admission->clients->othernames ?? '',
        'phone_number' => $admission->users->phone_number ?? '',
        'email' => $admission->users->email ?? '',
        'payment_method' => $admission->payment_method ?? '',
        'transaction_reference' => $admission->transaction_reference ?? '',
        'course_name' => $admission->payments->courses->course_name ?? '',
        'course_id' => $admission->payments->courses->course_id ?? '',
        'admission_date' => $admission->created_at  ?? '',
        'admission_number' => $admission->admission_number ?? '',
        'center_name' => $admission->payments->courses->centers->center_name ?? '',
        'certification_name' => $admission->payments->courses->certification_name ?? '',
        'admission_id' => $admission->id ?? '',
        'name_on_certificate' => $admission->clients->name_on_certificate ?? '',
        // Add other necessary fields
    ];
    $status = "COMPLETED";
    $validated['status'] = $status;
    $update_admission = Admissions::where('admission_number', $request->admission_number)->update($validated);


    $verificationUrl = route('pdf.verify_certificate', ['admission_number' => $request->admission_number]);
    $qrCode = Builder::create()
    ->writer(new PngWriter())
    ->data(route('pdf.verify_certificate', ['admission_number' => $request->admission_number]))
    ->size(200)
    ->build();

// Save the QR code to a file or directly use it in the PDF
$certificate_data['qr_code'] = $qrCode->getDataUri();

    $pdf = Pdf::loadView('pdf.ilearn_certificate', $certificate_data)
      ->setPaper('a4', 'landscape');
    
    // Mail::to($admission->users->email)->send(new EmailCertificate($certificate_data));
    return $pdf->download("admission-{$admission->admission_number}.pdf");
}

   
}
