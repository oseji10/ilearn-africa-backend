<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Payments;
use NumberToWords\NumberToWords;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use App\Models\Admissions;
use App\Models\CourseModule;
use App\Mail\EmailReceipt;
use App\Mail\EmailAdmission;
use App\Mail\EmailCertificate;

class PdfController extends Controller
{

    public function generateReceipt(Request $request)
    {
        // Validate the request
        $request->validate([
            'transaction_reference' => 'required|string',
        ]);
    
        // Fetch the payment data based on the transaction_reference
        $payment = Payments::with("clients", "courses", "users")->where('transaction_reference', $request->transaction_reference)->first();
    
        // Check if payment exists
        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }
    
        // Prepare the data for the PDF
        $user_data = [
            'client_id' => $payment->client_id,
            'amount' => $payment->amount,
            'created_at' => $payment->created_at->format('Y-m-d'),
            'firstname' => $payment->clients->firstname,
            'surname' => $payment->clients->surname,
            'othernames' => $payment->clients->othernames,
            'phone_number' => $payment->users->phone_number,
            'email' => $payment->users->email,
            'payment_method' => $payment->payment_method,
            'transaction_reference' => $payment->transaction_reference,
            'course_name' => $payment->courses->course_name,
            'course_id' => $payment->courses->course_id,
            'transaction_date' => $payment->created_at,
            // Add other necessary fields
        ];

        $numberToWords = new NumberToWords();

        $numberTransformer = $numberToWords->getNumberTransformer('en');
        $word_amount = $numberTransformer->toWords($user_data['amount'], 'NGN');
        
        // Optionally, add the word amount back into the $data array
        $user_data['amount_in_words'] = $word_amount;  
        
        $verificationUrl = route('pdf.verify', ['reference' => $payment->transaction_reference]);
        $qrCode = Builder::create()
        ->writer(new PngWriter())
        ->data(route('pdf.verify', ['reference' => $payment->transaction_reference]))
        ->size(200)
        ->build();

    // Save the QR code to a file or directly use it in the PDF
    $user_data['qr_code'] = $qrCode->getDataUri();

        // Load the view file and pass in the data
        $pdf = Pdf::loadView('pdf.receipt', $user_data);

      
        
        Mail::to($payment->users->email)->send(new EmailReceipt($user_data));
        
        
        // Return the generated PDF
        return $pdf->download("invoice-{$payment->transaction_reference}.pdf");
    }


    public function downloadReceipt(Request $request)
    {
        // Validate the request
        $request->validate([
            'transaction_reference' => 'required|string',
        ]);
    
        // Fetch the payment data based on the transaction_reference
        $payment = Payments::with("clients", "courses", "users")->where('transaction_reference', $request->transaction_reference)->first();
    
        // Check if payment exists
        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }
    
        // Prepare the data for the PDF
        $user_data = [
            'client_id' => $payment->client_id,
            'amount' => $payment->amount,
            'created_at' => $payment->created_at->format('Y-m-d'),
            'firstname' => $payment->clients->firstname,
            'surname' => $payment->clients->surname,
            'othernames' => $payment->clients->othernames,
            'phone_number' => $payment->users->phone_number,
            'email' => $payment->users->email,
            'payment_method' => $payment->payment_method,
            'transaction_reference' => $payment->transaction_reference,
            'course_name' => $payment->courses->course_name,
            'course_id' => $payment->courses->course_id,
            'transaction_date' => $payment->created_at,
            // Add other necessary fields
        ];

        $numberToWords = new NumberToWords();

        $numberTransformer = $numberToWords->getNumberTransformer('en');
        $word_amount = $numberTransformer->toWords($user_data['amount'], 'NGN');
        
        // Optionally, add the word amount back into the $data array
        $user_data['amount_in_words'] = $word_amount;  
        
        // $verificationUrl = route('pdf.verify', ['reference' => $payment->transaction_reference]);
        $qrCode = Builder::create()
        ->writer(new PngWriter())
        // ->data(route('pdf.verify', ['reference' => $payment->transaction_reference]))
        ->data(env('PAYMENT_VERIFICATION_URL') . "?reference={$payment->transaction_reference}")
        ->size(200)
        ->build();

    // Save the QR code to a file or directly use it in the PDF
    $user_data['qr_code'] = $qrCode->getDataUri();

        // Load the view file and pass in the data
        $pdf = Pdf::loadView('pdf.receipt', $user_data);

    
        // Return the generated PDF
        return $pdf->download("invoice-{$payment->transaction_reference}.pdf");
    }

public function verify(){
    return view('pdf.verify');
}

public function verifyCertificate(Request $request){
    $admission = Admissions::with("clients",  "users", "payments.courses.centers")->where('admission_number', $request->admission_number)->first();
     
        
    // Check if payment exists
    if (!$admission) {
        return response()->json(['message' => 'Admission not found'], 404);
    }

    $admission_data = [
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
            'course_name' => $admission->payments->courses->course_name ?? '',
            'certification_name' => $admission->payments->courses->certification_name ?? '',
            'course_id' => $admission->payments->courses->course_id ?? '',
            'admission_date' => $admission->created_at,
            'admission_number' => $admission->admission_number,
    ];
    return view('pdf.verify_certificate', $admission_data);
}



public function generateAdmissionLetter(Request $request)
    {
        // Validate the request
        $request->validate([
            'admission_number' => 'required',
        ]);
    
        // Fetch the payment data based on the transaction_reference
        $admission = Admissions::with("clients",  "users", "payments.courses.centers", 'cohorts')->where('admission_number', $request->admission_number)->first();
     
        
        // Check if payment exists
        if (!$admission) {
            return response()->json(['error' => 'Admission not found'], 404);
        }
    
        // Prepare the data for the PDF
        $admission_data = [
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
            'course_name' => $admission->payments->courses->course_name ?? '',
            'certification_name' => $admission->payments->courses->certification_name ?? '',
            'professional_certification_name' => $admission->payments->courses->professional_certification_name ?? '',
            'course_id' => $admission->payments->courses->course_id ?? '',
            'admission_date' => $admission->created_at,
            'admission_number' => $admission->admission_number,
            'center_name' => $admission->payments->courses->centers->center_name ?? '',
            'start_date' => $admission->cohorts->start_date ?? '',
            // Add other necessary fields
        ];

        

      // Assuming you have the necessary data in $admission_data
$centerName = $admission->payments->courses->centers->center_name;

// Determine which PDF view to load based on the center name
if (strpos($centerName, 'iLearn Africa') !== false) {
    $pdf = Pdf::loadView('pdf.ilearn_admission_letter', $admission_data);
} else {
    $pdf = Pdf::loadView('pdf.partner_admission_letter', $admission_data);
}

// Optionally, return or output the PDF
return $pdf->download('admission_letter.pdf');


            
        
        // Mail::to($admission->users->email)->send(new EmailAdmission($admission_data));
        
        
        // Return the generated PDF
        return $pdf->download("admission-{$admission->admission_number}.pdf");
    }





    public function emailAdmissionLetter(Request $request)
    {
        // Validate the request
        $request->validate([
            'admission_number' => 'required',
        ]);
    
        // Fetch the payment data based on the transaction_reference
        $admission = Admissions::with("clients",  "users", "payments.courses.centers", 'cohorts')->where('admission_number', $request->admission_number)->first();
     
        
        // Check if payment exists
        if (!$admission) {
            return response()->json(['error' => 'Admission not found'], 404);
        }
    
        // Prepare the data for the PDF
        $admission_data = [
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
            'course_name' => $admission->payments->courses->course_name ?? '',
            'certification_name' => $admission->payments->courses->certification_name ?? '',
            'professional_certification_name' => $admission->payments->courses->professional_certification_name ?? '',
            'course_id' => $admission->payments->courses->course_id ?? '',
            'admission_date' => $admission->created_at,
            'admission_number' => $admission->admission_number,
            'center_name' => $admission->payments->courses->centers->center_name ?? '',
            'start_date' => $admission->cohorts->start_date ?? '',
            // Add other necessary fields
        ];

        

      // Assuming you have the necessary data in $admission_data
$centerName = $admission->payments->courses->centers->center_name;

// Determine which PDF view to load based on the center name
if (strpos($centerName, 'iLearn Africa') !== false) {
    $pdf = Pdf::loadView('pdf.ilearn_admission_letter', $admission_data);
} else {
    $pdf = Pdf::loadView('pdf.partner_admission_letter', $admission_data);
}

// // Optionally, return or output the PDF
// return $pdf->download('admission_letter.pdf');


            
        
        Mail::to($admission->users->email)->send(new EmailAdmission($admission_data));
        
        
        // Return the generated PDF
        return $pdf->download("admission-{$admission->admission_number}.pdf");
    }




    public function generateCertificate(Request $request)
    {
        // Validate the request
        $request->validate([
            'admission_number' => 'required',
        ]);
    
        // Fetch the payment data based on the transaction_reference
        $admission = Admissions::with("clients",  "users", "payments.courses.centers")->where('admission_number', $request->admission_number)->first();
     
        
        // Check if payment exists
        if (!$admission) {
            return response()->json(['error' => 'Admission not found'], 404);
        }
    
        // Prepare the data for the PDF
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
            'certification_name' => $admission->payments->courses->certification_name,
            'admission_date' => $admission->created_at,
            'admission_number' => $admission->admission_number,
            'admission_id' => $admission->id,
            'center_name' => $admission->payments->courses->centers->center_name,
            'name_on_certificate' => $admission->clients->name_on_certificate,
            // Add other necessary fields
        ];
        $status = "COMPLETED";
        $validated['status'] = $status;
        $validated['certificate_type'] = 1; // Default to 1 if not set
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
        


        // Mail::to($admission->users->email)->send(new EmailCertificate($certificate_data));
        return $pdf->download("admission-{$admission->admission_number}.pdf");
    }


    public function issueIlearnAfricaCertificate(Request $request)
    {
        // Validate the request
        $request->validate([
            'admission_number' => 'required',
        ]);
    
        // Fetch the payment data based on the transaction_reference
        $admission = Admissions::with("clients",  "users", "payments.courses.centers")->where('admission_number', $request->admission_number)->first();
     
        
        // Check if payment exists
        if (!$admission) {
            return response()->json(['error' => 'Admission not found'], 404);
        }
    
        // Prepare the data for the PDF
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
            'certification_name' => $admission->payments->courses->certification_name,
            'admission_date' => $admission->created_at,
            'admission_number' => $admission->admission_number,
            'admission_id' => $admission->id,
            'center_name' => $admission->payments->courses->centers->center_name,
            'name_on_certificate' => $admission->clients->name_on_certificate,
            // Add other necessary fields
        ];
        $status = "COMPLETED";
        $validated['status'] = $status;
        $validated['certificate_type'] = 2; // Assuming 2 is for iLearn Africa certificate
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
            'admission_date' => $admission->updated_at,
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
                ->setPaper('letter', 'landscape');
    
            // Mail::to($admission->users->email)->send(new EmailCertificate($certificate_data));
            return $pdf->download("ilearn-{$admission->admission_number}.pdf");
    
        } elseif ($certificate_type == 3) {
            // Generate both PDFs
            $pdf1 = Pdf::loadView('pdf.certificate', $certificate_data)
                ->setPaper('a4', 'landscape')
                ->output();
    
            $pdf2 = Pdf::loadView('pdf.ilearn_certificate', $certificate_data)
                ->setPaper('letter', 'landscape')
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
            // Mail::to($admission->users->email)->send(new EmailCertificate($certificate_data));
    
            // Return the ZIP download
            return response()->download($tempZipPath)->deleteFileAfterSend(true);
        }
    
        return response()->json(['error' => 'Invalid certificate type'], 400);
    }

    
    
}
