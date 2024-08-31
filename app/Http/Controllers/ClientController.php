<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Educationaldetails;
use App\Models\Workdetails;
use App\Models\Grades;
use App\Models\Payments;
use App\Models\Admissions;
use App\Models\ProfileImage;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ClientController extends Controller
{
    public function show()
    {
        // $clients = Client::all();
    //     $clients = Client::select('clients.*', 'users.email', 'users.phone_number', 'country.country_name as country', 'nationality.nationality as nationality', 'qualifications.qualification_name as qualification', 'educational_details.*')
    //         ->join('users', 'users.client_id', '=', 'clients.client_id')
    //         ->join('nationality', 'nationality.id', '=', 'clients.nationality')
    //         ->join('country', 'country.id', '=', 'clients.country')
    //         ->join('qualifications', 'qualifications.id', '=', 'clients.qualification')
    //         ->leftJoin('educational_details', 'educational_details.client_id', '=', 'clients.client_id')
    // ->groupBy('clients.client_id', 'users.email', 'users.phone_number', 'country.country_name', 'nationality.nationality', 'qualifications.qualification_name')
    // ->get();

    $clients = Client::with(['user', 'nationality', 'country', 'workDetails', 'educationalDetails.grade', 'educationalDetails.qualification', 'documents'])->get();

    
        return response()->json(['clients' => $clients]);
    }


 


    public function getClient(Request $request)
    {
        $client = Client::with(['user', 'nationality', 'country', 'workDetails', 'educationalDetails.grade', 'educationalDetails.qualification'])->where('client_id', $request->client_id)->get();
        return response()->json(['client' => $client]);
    }
    

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'othernames' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:50',
            'marital_status' => 'nullable|string|max:50',
            'nationality' => 'nullable|string',
            'country' => 'nullable|string',
            'qualification' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'title' => 'nullable|string',
        ]);

    

        // Create a new client with the validated data
        $client = Client::create($validated);
        

        // Return a response, typically JSON
        return response()->json([
            'message' => 'Client created successfully',
            'client' => $client,
        ], 201); // HTTP status code 201: Created
    }

    public function update(Request $request, $client_id)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'firstname' => 'sometimes|nullable|string|max:255',
            'surname' => 'sometimes|nullable|string|max:255',
            'othernames' => 'sometimes|nullable|string|max:255',
            'gender' => 'sometimes|nullable|string|max:50',
            'marital_status' => 'sometimes|nullable|string|max:50',
            'nationality' => 'sometimes|nullable|string',
            'country' => 'sometimes|nullable|string',
            'qualification' => 'sometimes|nullable|string|max:255',
            'status' => 'sometimes|boolean',
            'title' => 'sometimes|nullable',
            'date_of_birth' => 'sometimes|nullable',
            'address' => 'sometimes|nullable',
            'status' => 'sometimes|nullable',
        ]);
        $validated['status'] = 'registered';
        $client = Client::where('client_id', '=', $client_id)->firstOrFail();
        $client->update($validated);

        // $education = $request->validate([
        //     // 'client_id' => 'required|string',
        //     'qualification_id' => 'required|string',
        //     'grade' => 'required|string',
        //     'date_acquired' => 'required|string',
        //     ]);

        $educationDetails = $request->validate([
            'client_id' => 'required|string',
            'educationalDetails' => 'required|array',
            'educationalDetails.*.qualification_id' => 'required|string',
            'educationalDetails.*.grade' => 'required|string',
            'educationalDetails.*.course_studied' => 'required|string',
            'educationalDetails.*.date_acquired' => 'required|date_format:Y-m-d',
        ]);
        

        $educationDetails = $request->all();
        $client_id = $educationDetails['client_id'];
        $educationalDetailsList = $educationDetails['educationalDetails'];
    
        if (!empty($educationalDetailsList)) {
            // Extract and process the first detail
            $firstDetail = array_shift($educationalDetailsList);
    
            \Log::info('First Detail:', $firstDetail);
            \Log::info('Remaining Details:', $educationalDetailsList);
    
            // Update or insert the first detail
            $existingEducationalDetail = Educationaldetails::where('client_id', $client_id)->first();
    
            if ($existingEducationalDetail) {
                $existingEducationalDetail->update($firstDetail);
            } else {
                Educationaldetails::create(array_merge(['client_id' => $client_id], $firstDetail));
            }
    
            // Insert remaining details
            foreach ($educationalDetailsList as $detail) {
                Educationaldetails::create(array_merge(['client_id' => $client_id], $detail));
            }
        }
    
        $workDetails = $request->validate([
        'workDetails' => 'required|array',
        'workDetails.*.start_date' => 'required|date_format:Y-m-d',
        'workDetails.*.end_date' => 'required|date_format:Y-m-d',
        'workDetails.*.organization' => 'required|string',
        'workDetails.*.job_title' => 'required|string',
        ]);

        $client_id = $client_id;
        $workDetailsList = $workDetails['workDetails'];
         // Handle work details
    if (!empty($workDetailsList)) {
        // Extract and process the first detail
        $firstWorkDetail = array_shift($workDetailsList);

        // \Log::info('First Work Detail:', $firstWorkDetail);
        // \Log::info('Remaining Work Details:', $workDetailsList);

        // Update or insert the first work detail
        $existingWorkDetail = Workdetails::where('client_id', $client_id)->first();

        if ($existingWorkDetail) {
            $existingWorkDetail->update($firstWorkDetail);
        } else {
            Workdetails::create(array_merge(['client_id' => $client_id], $firstWorkDetail));
        }

        // Insert remaining work details
        foreach ($workDetailsList as $detail) {
            Workdetails::create(array_merge(['client_id' => $client_id], $detail));
        }


    }
        // Return a response, typically JSON
        return response()->json([
            'message' => 'Client updated successfully',
            'client' => $client,
        ]);
    }


    public function statistics(){
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek(); // Defaults to Monday
        $endOfWeek = Carbon::now()->endOfWeek(); 
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        $incomplete_applications = Client::where('status', 'profile_created')->count();
        $registered_clients = Client::where('status', 'registered')->count();
        $pending_admissions = Admissions::where('status', 'pending')->count();
        $currently_admitted_clients = Admissions::where('status', 'ADMITTED')->count();
        $all_graduated_clients = Admissions::where('status', 'COMPLETED')->count();

        $payments_today = Payments::where('status', 1)->whereDate('created_at', $today)->sum('amount');
        $payments_this_week = Payments::where('status', 1)->whereBetween('created_at', [$startOfWeek, $endOfWeek])->sum('amount');
        $payments_this_month = Payments::where('status', 1)->whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('amount');
        $all_payments = Payments::where('status', 1)->sum('amount');

        return response()->json([
            // 'message' => 'Client updated successfully',
            'incomplete_applications' => $incomplete_applications,
            'registered_clients' => $registered_clients,
            'pending_admissions' => $pending_admissions,
            'currently_admitted_clients' => $currently_admitted_clients,
            'all_graduated_clients' => $all_graduated_clients,

            'payments_today' => $payments_today,
            'payments_this_week' => $payments_this_week,
            'payments_this_month' => $payments_this_month,
            'all_payments' => $all_payments,
        ]);
       
    }




    public function profileImage(Request $request)
{
    $validated = $request->validate([
        'image' => 'required|file|mimes:png,jpg,jpeg|max:5048', // Adjust validation rules as needed
        'client_id' => 'required|string', // Ensure client_id is passed and validated
    ]);

    // Retrieve existing profile image for the client
    $existingImage = ProfileImage::where('client_id', $request->client_id)->first();

    if ($existingImage) {
        // Delete the existing image from the storage
        Storage::disk('public')->delete($existingImage->image_url);

        // Delete the existing image record from the database
        $existingImage->delete();
    }

    // Store the new image
    if ($request->file('image')) {
        $file = $request->file('image');
        $path = $file->store('profile_images', 'public'); // Store in the 'public/profile_images' directory

        // Save the new image data to the database
        $validated['image_url'] = $path;

        // Create a new profile image record
        $save = ProfileImage::create($validated);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'path' => $path
        ], 200);
    }

    return response()->json(['message' => 'File not uploaded'], 400);
}
    
}
