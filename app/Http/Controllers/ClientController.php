<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Educationaldetails;
use App\Models\Workdetails;
use App\Models\Grades;

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

    $clients = Client::with(['user', 'nationality', 'country', 'workDetails', 'educationalDetails.grade', 'educationalDetails.qualification'])->get();

    
        return response()->json(['clients' => $clients]);
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

        \Log::info('First Work Detail:', $firstWorkDetail);
        \Log::info('Remaining Work Details:', $workDetailsList);

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
    
}
