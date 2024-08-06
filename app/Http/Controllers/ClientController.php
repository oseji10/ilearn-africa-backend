<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
class ClientController extends Controller
{
    public function show()
    {
        // $clients = Client::all();
        $clients = Client::select('clients.*', 'users.*')
            ->join('users', 'users.client_id', '=', 'clients.client_id')
            ->get();
    
        return response()->json(['clients' => $clients]);
    }
    

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'othernames' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:50',
            'marital_status' => 'nullable|string|max:50',
            'state_of_origin' => 'nullable|string|max:255',
            'state_of_residence' => 'nullable|string|max:255',
            'qualification' => 'nullable|string|max:255',
        ]);

        // Create a new client with the validated data
        $client = Client::create($validated);

        // Return a response, typically JSON
        return response()->json([
            'message' => 'Client created successfully',
            'client' => $client,
        ], 201); // HTTP status code 201: Created
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'firstname' => 'sometimes|nullable|string|max:255',
            'lastname' => 'sometimes|nullable|string|max:255',
            'othernames' => 'sometimes|nullable|string|max:255',
            'gender' => 'sometimes|nullable|string|max:50',
            'marital_status' => 'sometimes|nullable|string|max:50',
            'state_of_origin' => 'sometimes|nullable|string|max:255',
            'state_of_residence' => 'sometimes|nullable|string|max:255',
            'qualification' => 'sometimes|nullable|string|max:255',
            'status' => 'sometimes|boolean',
        ]);

        // Find the client by ID
        $client = Client::findOrFail($id);

        // Update the client's data
        $client->update($validated);

        // Return a response, typically JSON
        return response()->json([
            'message' => 'Client updated successfully',
            'client' => $client,
        ]);
    }
    
}
