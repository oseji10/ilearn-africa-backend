<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Educationaldetails;
class ClientController extends Controller
{
    public function show()
    {
        // $clients = Client::all();
        $clients = Client::select('clients.*', 'users.email', 'users.phone_number', 'country.country_name as country', 'nationality.nationality as nationality', 'qualifications.qualification_name as qualification')
            ->join('users', 'users.client_id', '=', 'clients.client_id')
            ->join('nationality', 'nationality.id', '=', 'clients.nationality')
            ->join('country', 'country.id', '=', 'clients.country')
            ->join('qualifications', 'qualifications.id', '=', 'clients.qualification')
            ->get();
    
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

        $education = $request->validate([
        'client_id' => 'required|string',
        'client_id' => 'required|string',
        'client_id' => 'required|string',
        ]);
        // Create a new client with the validated data
        $client = Client::create($validated);
        $educational_details = Educationaldetails::create($education);

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
        ]);

        // Find the client by ID
        $client = Client::where('client_id', '=', $client_id)->firstOrFail();

        // Update the client's data
        $client->update($validated);

        // Return a response, typically JSON
        return response()->json([
            'message' => 'Client updated successfully',
            'client' => $client,
        ]);
    }
    
}
