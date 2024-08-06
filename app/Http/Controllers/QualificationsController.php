<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Qualifications;
class QualificationsController extends Controller
{
    public function show()
    {
        $qualifications = Qualifications::all();
        return response()->json(['qualifications' => $qualifications]);
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'qualification_name' => 'required|string|max:255',
            
        ]);

        // Create a new client with the validated data
        $client = Qualifications::create($validated);

        // Return a response, typically JSON
        return response()->json([
            'message' => 'Qualification created successfully',
            'qualification' => $qualification,
        ], 201); // HTTP status code 201: Created
    }
}
