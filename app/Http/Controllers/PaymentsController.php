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
use Flutterwave\Flutterwave;
use Flutterwave\Rave;

class PaymentsController extends Controller
{
    public function show()
    {
       
        // $payments = Payments::with(['clients'])->orderBy('updated_at', 'desc')->get();
        $payments = Payments::with(['clients', 'proof', 'part_payments', 'courses'])->where('status', '1')->orderBy('created_at', 'desc')->get();

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
       
        $payments = Payments::with(['clients', 'proof', 'courses'])->where('status', '0')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Payments retrieved successfully',
            'payments' => $payments,
        ]);
    }

    public function pendingPartPayments()
{
    $payments = PartPayments::with([
        'clients',
        'proof',
        // 'part_payments.clients',
        'payment.courses',
        
    ])
    ->where('status', 'pending')
    // ->whereHas('part_payments', function ($query) {
    //     $query->where('status', 'pending');
    // })
    ->orderBy('created_at', 'desc')
    ->get();

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

    // Assuming a client is making a manual payment, use this endpoint to store that payment
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
    $payment_method = "Mobile Transfer";
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
    $part_payments->status = 'pending';
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
$part_payments->status = 'pending';
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
        
        //  $my_courses = Payments::rightjoin('admissions', 'admissions.client_id', '=', 'payments.client_id')
        // ->rightjoin('course_list', 'course_list.course_id', '=', 'payments.course_id')
        // ->rightjoin('centers', 'centers.center_id', '=', 'course_list.center_id')
        // ->rightjoin('cohorts', 'cohorts.cohort_id', '=', 'payments.cohort_id')
        // ->where('payments.client_id', '=', auth()->user()->client_id)
        // ->select('course_list.course_id', 'course_list.course_name', 'cohorts.cohort_name', 'centers.center_name', 'payments.status')
        // ->get();
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

    // Fetch the payment details
    $payment = Payments::where('other_reference', $validated['other_reference'])->first();

    if (!$payment) {
        return response()->json([
            // $payment->course_id,
            'message' => 'Payment not found'
        ], 404);
    }

    $course = DB::table('course_list')->where('course_id', $payment->course_id)->first();

    // Retrieve the course price from CourseList model
    // $course = CourseList::where('course_id', '=', $payment->course_id)->get();

    if (!$course) {
        return response()->json([
            'message' => 'Course not found'
        ], 404);
    }

    // Compare payment amount with course price
    if ($payment->amount < $course->cost) {
        $part_payment_status = 'incomplete';
    } else {
        $part_payment_status = 'complete';
    }

    // Update the payment status
    $validated['status'] = "1";  // Assuming '1' means confirmed
    $validated['updated_by'] = auth()->id();
    $validated['transaction_reference'] = $validated['other_reference'];
    $validated['part_payment_status'] = $part_payment_status;

    $payment->update($validated);

    // Fetch updated payment data with relationships
    $my_data = Payments::with('users', 'courses', 'clients')
        ->where('transaction_reference', $request->transaction_reference)
        ->orWhere('other_reference', $request->other_reference)
        ->first();

        // Update Part payments table
        PartPayments::where('payment_id', $payment->id)->update(['status' => 'paid']);

    $user_data = [
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
        'course_name' => $my_data->course->course_name ?? '',
        'course_id' => $my_data->course->course_id ?? '',
        'transaction_date' => $my_data->created_at,
        'part_payment_status' => $part_payment_status
    ];

    return response()->json([
        'message' => 'Payment successfully confirmed',
        'data' => $user_data
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
        // Retrieve transaction reference from the query string
        $txRef = $request->query('transaction_id');
        
        if (!$txRef) {
            return response()->json([
                'error' => 'Transaction reference missing',
            ], 400);
        }
        
        // Flutterwave verification endpoint
        $flutterwaveUrl = env('FLUTTERWAVE_VERIFY_TRANSACTION') . "/{$txRef}/verify";
        $secretKey = env('FLUTTERWAVE_SECRET_KEY');
        
        // Make a request to verify the payment
        $response = Http::withToken($secretKey)->get($flutterwaveUrl);
        
        if ($response->successful()) {
            $responseData = $response->json();
            
            // Check if the payment was successful
            if ($responseData['data']['status'] === 'successful') {
                // Handle successful payment, e.g., update the database
                $paymentData = $responseData['data'];
              
                // Generate a 7-digit random number for payment_id
                $validated['payment_id'] = mt_rand(1000000, 9999999);
                $validated['created_by'] = auth()->id();
                // $validated['client_id'] = auth()->user()->client_id;
                $validated['client_id'] = $request->clientId;
                $validated['amount'] = $paymentData['amount'];
                $validated['part_payment'] = $paymentData['amount'];
                $validated['payment_method'] = "Online";
                $validated['payment_gateway'] = "FLUTTERWAVE";
                $validated['transaction_reference'] = $paymentData['id'];
                $validated['transaction_id'] = $txRef;
                $validated['other_reference'] = $paymentData['id'];
                $validated['payment_gateway'] = "FLUTTERWAVE";
                $validated['status'] = 1; 
                $validated['cohort_id'] = $request->cohort_id;
                $validated['course_id'] = $request->course_id;
    
                // Create a new admission record
                $admission_number = mt_rand(1000000, 9999999);
                $admissions = new Admissions();
                $admissions->client_id = $request->clientId;
                $admissions->admission_number = $admission_number;
                $admissions->status = "pending";
                $admissions->cohort_id = $request->cohort_id;
                $admissions->save();
    
                // Save the payment record
                $validated['admission_number'] = $admission_number;
                $payments = Payments::create($validated);
    
    
                $redirectUrl = env('FRONTEND_PAYMENT_SUCCESS_URL') . "?tx_ref={$paymentData['tx_ref']}&status=success";
                return redirect()->away($redirectUrl);
                return response()->json([
                    'message' => 'Payment verified successfully',
                    'data' => $paymentData,
                    'redirect_url' => $redirectUrl,
                ], 200);
            }
    
            // Handle unsuccessful payment
            return response()->json([
                'error' => 'Payment was not successful',
                'details' => $responseData,
            ], 400);
        }
    
        // Handle verification failure
        return response()->json([
            'error' => 'Payment verification failed',
            'details' => $response->json(),
        ], 400);
    }
    


    public function verifyAndStorePayment2(Request $request)
    {
        // return $trxRef;
        // Validate the incoming request data
        // $validated = $request->validate([
        //     // 'tx_ref' => 'required|string',
        //     // 'amount' => 'required|numeric',
        //     'course_id' => 'required|string',
        //     'cohort_id' => 'required|string',
        //     // 'payment_gateway' => 'required|string',
        //     // 'payment_method' => 'required|string',
        // ]);
    
        
        $transactionReference = $request->tx_ref;
        $transaction_id = $request->transaction_id;
        // $amount = $request['amount'];
        $courseId = $request->course_id;
        $cohortId = $request->cohort_id;    
        
        $flutterwaveSecretKey = env('FLUTTERWAVE_SECRET_KEY');
        // $flutterwaveSecretKey = "FLWPUBK_TEST-b6b497fa28aa6cfa4988650d59c87096-X";
    
        try {
            // Step 1: Verify the payment with Flutterwave
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $flutterwaveSecretKey,
            ])->get(env('FLUTTERWAVE_VERIFY_TRANSACTION') . "/{$transaction_id}/verify");
            
            $paymentData = $response->json();
            
            return $paymentData;
            if ($response->successful() && isset($paymentData['status']) && $paymentData['status'] === 'successful') {
                // Step 2: Check if the payment already exists in the database
                $existingPayment = Payments::where('transaction_reference', $transaction_id)->first();
    
              

                if ($existingPayment) {
                    // If the payment already exists, proceed as successful
                    return response()->json([
                        'message' => 'Payment already exists, proceeding as successful.',
                        'payments' => $existingPayment,
                    ], 200); // HTTP status code 200: OK
                }
    
                // Step 3: Proceed to store the payment if it's verified
                // Generate a 7-digit random number for payment_id
                // $validated['payment_id'] = mt_rand(1000000, 9999999);
                // $validated['created_by'] = auth()->id();
                // $validated['client_id'] = auth()->user()->client_id;
                // $validated['amount'] = $amount;
                // $validated['status'] = 1; // Payment verified, set status to 'success'
    
                // // Create a new admission record
                // $admission_number = mt_rand(1000000, 9999999);
                // $admissions = new Admissions();
                // $admissions->client_id = auth()->user()->client_id;
                // $admissions->admission_number = $admission_number;
                // $admissions->status = "pending";
                // $admissions->cohort_id = $cohortId;
                // $admissions->save();
    
                // // Save the payment record
                // $validated['admission_number'] = $admission_number;
                // $payments = Payments::create($validated);
    
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


