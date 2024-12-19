<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payments;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\CourseList;
use App\Models\Admissions;
use App\Models\User;
use App\Models\ProofOfPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use NumberToWords\NumberToWords;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailReceipt;
use App\Models\PartPayments;
use App\Models\Cohorts;
use Illuminate\Support\Facades\Http;

class PaymentsController extends Controller
{
    public function show()
    {
       
        // $payments = Payments::with(['clients'])->orderBy('updated_at', 'desc')->get();
        $payments = Payments::with(['clients', 'proof', 'part_payments'])->where('status', '1')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Payments retrieved successfully',
            'payments' => $payments,
        ]);
    }


    public function myPaymentPartPaymentHistory(Request $request, $id)
    {
      $part_payments = Payments::with(['part_payments'])->where('id', $id)->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Part Payments retrieved successfully',
            'part_payments' => $part_payments,
        ]);
    }

    public function pendingPayments()
    {
       
        $payments = Payments::with(['clients', 'proof'])->where('status', '0')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Payments retrieved successfully',
            'payments' => $payments,
        ]);
    }


    public function rejectedPayments()
    {
       
        $payments = Payments::with(['clients', 'proof'])->where('status', '2')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Payments retrieved successfully',
            'payments' => $payments,
        ]);
    }


    public function myPayments()
    {
       
        $payments = Payments::with(['clients', 'courses'])->where('client_id', auth()->user()->client_id)->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Payments retrieved successfully',
            'payments' => $payments,
        ]);
    }

    public function verifiedPayments()
    {
       
        $payments = Payments::with(['clients', 'courses', 'users'])->orderBy('created_at', 'desc')
        ->where('status', "1")
        ->get();

        return response()->json([
            'message' => 'Payments retrieved successfully',
            'payments' => $payments,
        ]);
    }


    public function storePayment(Request $request)
{
    // Validate the incoming request data
    $validated = $request->validate([
        'payment_for' => 'nullable|string',
        'course_id' => 'nullable|string',
        'payment_gateway' => 'nullable|string',
        'amount' => 'nullable',
        'transaction_reference' => 'nullable|string',
        'other_reference' => 'nullable|string',
        'status' => 'nullable|integer',
        'payment_method' => 'nullable|string',
        'cohort_id' => 'nullable|string',
        'part_payment' => 'nullable|string',
    ]);
    
    // Start a database transaction
    DB::beginTransaction();

    try {
        // Check if the transaction_reference already exists
        $existingPayment = Payments::where('transaction_reference', $validated['transaction_reference'])->first();

        if ($existingPayment) {
            // If the payment already exists, roll back the transaction
            DB::rollBack();

            // Proceed as if the transaction was successful
            return response()->json([
                'message' => 'Payment already exists, proceeding as successful.',
                'payments' => $existingPayment,
            ], 200); // HTTP status code 200: OK
        }

        // Generate a 7-digit random number for payment_id
        $validated['payment_id'] = mt_rand(1000000, 9999999);

        // Add the created_by field with the authenticated user's ID
        $validated['created_by'] = auth()->id();
        $validated['client_id'] = auth()->user()->client_id;
        $validated['part_payment'] = $request->amount;

        // Create a new payment with the validated data
        $admission_number = mt_rand(1000000, 9999999);
        
        $admissions = new Admissions();
        $admissions->client_id = auth()->user()->client_id;
        $admissions->admission_number = $admission_number;
        $admissions->status = "pending";
        $admissions->cohort_id = $request->cohort_id;
        $admissions->save();
        
        $validated['admission_number'] = $admission_number;

        $payments = Payments::create($validated);
   
        // Commit the transaction
        DB::commit();

        // Return a response, typically JSON
        return response()->json([
            'message' => 'Payment created successfully',
            'payments' => $payments,
        ], 201); // HTTP status code 201: Created

    } catch (\Exception $e) {
        // Roll back the transaction if something goes wrong
        DB::rollBack();

        // Return an error response
        return response()->json([
            'message' => 'Failed to create payment',
            'error' => $e->getMessage(),
        ], 500); // HTTP status code 500: Internal Server Error
    }
}





public function storeManualPayment(Request $request)
{
    $admission_number = mt_rand(1000000, 9999999);
  
    $admissions = new Admissions();
    $admissions->client_id = $request->client_id;
    $admissions->admission_number = $admission_number;
    $admissions->status = "pending";
    $admissions->save();

    $check_course_amount = CourseList::select('cost')->where('course_id', $request->course_id)->first();
    $payments = new Payments();
    $payments->client_id = $request->client_id;
    $payments->amount = $check_course_amount->cost;
    $payments->transaction_reference = $request->transaction_reference;
    $payments->payment_method = $request->payment_method;
    $payments->course_id = $request->course_id;
    $payments->status = 1;
    $payments->created_by = auth()->id();
    $payments->admission_number = $admission_number;
    $payments->save();
   
    
   

        return response()->json([
            'message' => $payments
        ], 200); // HTTP success code 200: Internal Server Error
    }

    public function uploadProofOfPayment(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file|mimes:png,jpg,jpeg,JPG,pdf,doc,docx|max:2048', // Make 'id' required for the update
            'client_id' => 'required|string', // Assuming 'client_id' is required
           
        ]);
    $admission_number = mt_rand(1000000, 9999999);
    $admissions = new Admissions();
    $admissions->client_id = auth()->user()->client_id;
    $admissions->admission_number = $admission_number;
    $admissions->status = "pending";
    $admissions->cohort_id = $request->cohort_id;
    $admissions->save();

    
    $other_reference = mt_rand(1000000, 9999999);
    $payment_gateway = "SELF";
    $payment_method = "Mobile Transefer";
    $check_course_amount = CourseList::select('cost')->where('course_id', $request->course_id)->first();
    $payments = new Payments();
    $payments->client_id = $request->client_id;
    $payments->amount = $check_course_amount->cost;
    // $payments->transaction_reference = $request->client_id;
    $payments->payment_method = $payment_method;
    $payments->payment_gateway = $payment_gateway;
    $payments->course_id = $request->course_id;
    $payments->other_reference = $other_reference;
    $payments->status = 0; //change to 0 later
    $payments->created_by = auth()->id();
    $payments->admission_number = $admission_number;
    $payments->cohort_id = $request->cohort_id;
    $payments->part_payment = $request->part_payment;
    $payments->save();

    $payment_id = $payments->id;

    $part_payments = new PartPayments();
    $part_payments->client_id = $request->client_id;
    $part_payments->payment_id = $payment_id;
    $part_payments->amount = $request->part_payment;
    $part_payments->save();
   
    if ($request->file('file')) {
        $file = $request->file('file');
        $path = $file->store('receipts', 'public'); // Store in the 'public/documents' directory
    
        $validated['file_path'] = $path;
        $validated['client_id'] = auth()->user()->client_id;
    
        // Save the file path or other related information to the database if needed
        $save = ProofOfPayment::create($validated);
    
    }

    return response()->json([
        'message' => 'Payment uploaded successfully',
        // 'path' => $path
    ], 200);
  
}




public function topUpPayment(Request $request)
{
    $validated = $request->validate([
        'id' => 'required|integer',  // Make 'id' required for the update
        'client_id' => 'required|string', // Assuming 'client_id' is required
        'part_payment' => 'required|numeric', // Assuming part_payment should be numeric
        'file' => 'required|file|mimes:png,jpg,jpeg,JPG,pdf,doc,docx|max:2048', // Make 'id' required for the update
            'client_id' => 'required|string', // Assuming 'client_id' is required
    ]);
    
    $retrive_payment_detail = Payments::where('id', $validated['id'])->first();
    
    if (!$retrive_payment_detail) {
        return response()->json(['error' => 'Payment not found'], 404);
    }
    
    $previous_balance = $retrive_payment_detail->part_payment;
    
    // Ensure part_payment is numeric before adding
    $validated['part_payment'] += $previous_balance;
    
    $updated = Payments::where('id', $validated['id'])
                ->update(['part_payment' => $validated['part_payment'], 'client_id' => $validated['client_id']]);
    


                $payment_id = $retrive_payment_detail->id;

$part_payments = new PartPayments();
$part_payments->client_id = $request->client_id;
$part_payments->payment_id = $payment_id;
$part_payments->amount = $request->part_payment;
$part_payments->save();

if ($request->file('file')) {
    $file = $request->file('file');
    $path = $file->store('receipts', 'public'); // Store in the 'public/documents' directory

    $validated['file_path'] = $path;
    $validated['client_id'] = auth()->user()->client_id;

    // Save the file path or other related information to the database if needed
    $save = ProofOfPayment::create($validated);

}

    if ($updated) {
        return response()->json(['message' => 'Payment updated successfully']);
    } else {
        return response()->json(['error' => 'Update failed'], 500);
    }
    





}


    public function registeredCourses()
    {
        $my_courses = Payments::with(['admissions', 'courses.centers', 'cohorts'])->where('client_id', '=', auth()->user()->client_id)->get();
        return response()->json(['my_courses' => $my_courses]);
    }

    public function confirmPayment(Request $request)
    {
        $validated = $request->validate([
            'transaction_reference' => 'string',
            'client_id' => 'string',
            'other_reference' => 'string',
            'status' => 'string',
            'updated_by'
        ]);
    
      
        // Use the where clause first to get the query builder instance
        $status = "1";
        $other_reference = $validated['other_reference'];
        $validated['status'] = $status;
        $validated['updated_by'] = auth()->id();
        $validated['transaction_reference'] = $other_reference;
        $confirm_payment = Payments::where('other_reference', $validated['other_reference'])
            ->update($validated);
    
            $my_data = Payments::with('users', 'courses', 'clients')->where('transaction_reference', $request->transaction_reference)
            ->orWhere('other_reference', $request->other_reference)
            ->first();
            // $email = $user_data->user->email;
            // $amount = $user_data->amount;

            $user_data = ([
            'client_id' => $my_data->client_id,
            'amount' => $my_data->amount,
            'created_at' => $my_data->created_at->format('Y-m-d'),
            'firstname' => $my_data->clients->firstname,
            'surname' => $my_data->clients->surname,
            'othernames' => $my_data->clients->othernames,
            'phone_number' => $my_data->users->phone_number,
            'email' => $my_data->users->email,
            'payment_method' => $my_data->payment_method,
            'transaction_reference' => $my_data->transaction_reference,
            'course_name' => $my_data->courses->course_name,
            'course_id' => $my_data->courses->course_id,
            'transaction_date' => $my_data->created_at,
            ]);

    //         $numberToWords = new NumberToWords();
    //         $numberTransformer = $numberToWords->getNumberTransformer('en');
    //         $word_amount = $numberTransformer->toWords($user_data['amount'], 'NGN');
        
    //     // Optionally, add the word amount back into the $data array
    //         $user_data['amount_in_words'] = $word_amount;  
        
    //         $verificationUrl = route('verify-payment', ['reference' => $my_data->transaction_reference]);
    //         $qrCode = Builder::create()
    //         ->writer(new PngWriter())
    //         ->data(route('pdf.verify', ['reference' => $my_data->transaction_reference]))
    //         ->size(200)
    //         ->build();

    // // Save the QR code to a file or directly use it in the PDF
    //     $user_data['qr_code'] = $qrCode->getDataUri();

    //         $pdf = Pdf::loadView('pdf.receipt', $user_data);
    //         Mail::to($my_data->users->email)->send(new EmailReceipt($user_data));
            
            // return $user_data;

            return response()->json([
                'message' => 'Payment successfully confirmed'
            ], 201);
    }




    public function rejectPayment(Request $request)
    {
        $status = 2; // You can use integer if the column type is integer.
        $confirm_payment = Payments::where('transaction_reference', $request->transaction_reference)
            ->orWhere('other_reference', $request->other_reference)
            ->update(['status' => $status]); 
    }
    
    
    public function viewReceipt(Request $request){
        $view_receipt = Documents::where('transaction_reference', $request->transaction_reference)
        ->orWhere('other_reference', $request->other_reference)
        ->get();

        
        return response()->json([
        'message' => 'Receipt retrieved succesfully',
        'receipt' => $view_receipt,
        ]);
    }

    public function fetchProof(Request $request, $other_reference){
        $view_proof = ProofOfPayment::where('other_reference', $other_reference)
        ->get();

        
        return response()->json([
        'message' => 'Proof retrieved succesfully',
        'proof' => $view_proof,
        ]);
    }




    public function updateAmount(Request $request){
        $validated = $request->validate([
            'edited_payment' => 'required|integer',
            'client_id' => 'string',
            'transaction_id' => 'string',
            'id' => 'required|integer',
        ]);
        
        $retrive_payment_detail = Payments::where('id', $validated['id'])->first();
        
        if (!$retrive_payment_detail) {
            return response()->json(['error' => 'Payment not found'], 404);
        }
        
        
        $updated = Payments::where('id', $validated['id'])
                    ->update(['part_payment' => $validated['edited_payment']]);
        
                    PartPayments::where('client_id', $validated['client_id'])
                    ->where('payment_id', $validated['id'])
                    ->update(['amount' => $validated['edited_payment']]);

                    return response()->json([
                        'message' => 'Payment successfully verified and created',
                        'updated' => $updated,
                    ], 201);
    }






    public function initializePayment(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'email' => 'required|email',
            'tx_ref' => 'required|string',
            'redirect_url' => 'required|url',
        ]);

        $flutterwaveUrl = env('FLUTTERWAVE_URL', 'https://api.flutterwave.com/v3/payments');
        $secretKey = env('FLUTTERWAVE_SECRET_KEY');

        $payload = [
            'tx_ref' => $validated['tx_ref'],
            'amount' => $validated['amount'],
            'currency' => 'NGN',
            'redirect_url' => $validated['redirect_url'],
            'customer' => [
                'email' => $validated['email'],
            ],
        ];

        $response = Http::withToken($secretKey)
            ->post($flutterwaveUrl, $payload);

        if ($response->successful()) {
            return response()->json($response->json(), 200);
        }

        return response()->json([
            'error' => 'Payment initialization failed',
            'details' => $response->json(),
        ], 400);
    }



    public function verifyAndStorePayment(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'tx_ref' => 'required|string',
            'amount' => 'required|numeric',
            'course_id' => 'required|string',
            'cohort_id' => 'required|string',
            'payment_gateway' => 'required|string',
            'payment_method' => 'required|string',
        ]);
    
        // Get the transaction reference from the request
        // $transactionReference = $request->input('tx_ref');
        $transactionReference = $validated['tx_ref'];
        $amount = $validated['amount'];
        $courseId = $validated['course_id'];
        $cohortId = $validated['cohort_id'];
    
        // Flutterwave API credentials (get from your .env file)
        $flutterwaveSecretKey = env('FLUTTERWAVE_SECRET_KEY');
    
        try {
            // Step 1: Verify the payment with Flutterwave
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $flutterwaveSecretKey,
            ])->get("https://api.flutterwave.com/v3/charges?tx_ref={$transactionReference}");
    
            $paymentData = $response->json();
    
            if ($response->successful() && isset($paymentData['status']) && $paymentData['status'] === 'success') {
                // Step 2: Check if the payment already exists in the database
                $existingPayment = Payments::where('transaction_reference', $transactionReference)->first();
    
                if ($existingPayment) {
                    // If the payment already exists, proceed as successful
                    return response()->json([
                        'message' => 'Payment already exists, proceeding as successful.',
                        'payments' => $existingPayment,
                    ], 200); // HTTP status code 200: OK
                }
    
                // Step 3: Proceed to store the payment if it's verified
                // Generate a 7-digit random number for payment_id
                $validated['payment_id'] = mt_rand(1000000, 9999999);
                $validated['created_by'] = auth()->id();
                $validated['client_id'] = auth()->user()->client_id;
                $validated['amount'] = $amount;
                $validated['status'] = 1; // Payment verified, set status to 'success'
    
                // Create a new admission record
                $admission_number = mt_rand(1000000, 9999999);
                $admissions = new Admissions();
                $admissions->client_id = auth()->user()->client_id;
                $admissions->admission_number = $admission_number;
                $admissions->status = "pending";
                $admissions->cohort_id = $cohortId;
                $admissions->save();
    
                // Save the payment record
                $validated['admission_number'] = $admission_number;
                $payments = Payments::create($validated);
    
                // Step 4: Return a response, indicating success
                return response()->json([
                    'message' => 'Payment successfully verified and created',
                    'payments' => $payments,
                ], 201); // HTTP status code 201: Created
            } else {
                // Step 5: Handle payment verification failure
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Payment verification failed',
                ], 400); // HTTP status code 400: Bad Request
            }
        } catch (\Exception $e) {
            // Handle any exceptions that might occur during the verification process
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during payment verification',
                'error' => $e->getMessage(),
            ], 500); // HTTP status code 500: Internal Server Error
        }
    }
    
}


