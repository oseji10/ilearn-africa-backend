<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Payments;
use NumberToWords\NumberToWords;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use App\Models\Admissions;

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
        $data = [
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
            // Add other necessary fields
        ];

        $numberToWords = new NumberToWords();

        $numberTransformer = $numberToWords->getNumberTransformer('en');
        $word_amount = $numberTransformer->toWords($data['amount'], 'NGN');
        
        // Optionally, add the word amount back into the $data array
        $data['amount_in_words'] = $word_amount;  
        
        $verificationUrl = route('pdf.verify', ['reference' => $payment->transaction_reference]);
        $qrCode = Builder::create()
        ->writer(new PngWriter())
        ->data(route('pdf.verify', ['reference' => $payment->transaction_reference]))
        ->size(200)
        ->build();

    // Save the QR code to a file or directly use it in the PDF
    $data['qr_code'] = $qrCode->getDataUri();

        // Load the view file and pass in the data
        $pdf = Pdf::loadView('pdf.receipt', $data);
    
        
        // Return the generated PDF
        return $pdf->download("invoice-{$payment->transaction_reference}.pdf");
    }

public function verify(){
    return view('pdf.verify');
}



public function generateAdmissionLetter(Request $request)
    {
        // Validate the request
        $request->validate([
            'admission_number' => 'required',
        ]);
    
        // Fetch the payment data based on the transaction_reference
        $admission = Admissions::with("clients",  "users", "payments.courses.centers")->where('admission_number', $request->admission_number)->first();
        // return $admission->payments->courses->centers->center_name;
        // return response()->json([
        //     'message' => 'Payments retrieved successfully',
        //     'payments' => $admission,
        // ]);
        
        // Check if payment exists
        if (!$admission) {
            return response()->json(['error' => 'Admission not found'], 404);
        }
    
        // Prepare the data for the PDF
        $data = [
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
            'center_name' => $admission->payments->courses->centers->center_name,
            // Add other necessary fields
        ];

        

        // Load the view file and pass in the data
        $pdf = Pdf::loadView('pdf.admission_letter', $data);
    
        
        // Return the generated PDF
        return $pdf->download("admission-{$admission->admission_number}.pdf");
    }


    
    
}
