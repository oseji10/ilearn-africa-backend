<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payments;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\CourseList;
use App\Models\Admissions;
use App\Models\User;
class PaymentsController extends Controller
{
    public function show()
    {
       
        $payments = Payments::with(['clients'])->orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Payments retrieved successfully',
            'payments' => $payments,
        ]);
    }

    public function myPayments()
    {
       
        $payments = Payments::with(['clients'])->where('client_id', auth()->user()->client_id)->orderBy('created_at', 'desc')->get();

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


    public function store(Request $request)
{
    // Validate the incoming request data
    $validated = $request->validate([
        'payment_for' => 'nullable|string',
        'course_id' => 'nullable|string',
        'payment_gateway' => 'nullable|string',
        'amount' => 'nullable|string',
        'transaction_reference' => 'nullable|string',
        'other_reference' => 'nullable|string',
        'status' => 'nullable|integer',
        'payment_method' => 'nullable|string',
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

        // Create a new payment with the validated data
        $admission_number = mt_rand(1000000, 9999999);
        
        $admissions = new Admissions();
        $admissions->client_id = auth()->user()->client_id;
        $admissions->admission_number = $admission_number;
        $admissions->status = "pending";
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


    public function registeredCourses()
    {
        $my_courses = Payments::with(['courses'])->where('client_id', '=', auth()->user()->client_id)->get();
        return response()->json(['my_courses' => $my_courses]);
    }

}


