<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Centers;

class CentersController extends Controller
{
    public function show()
    {
        $centers = Centers::all();
        return response()->json([
            'message' => 'Centers retrieved successfully',
            'centers' => $centers,
        ]);
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'center_name' => 'required|string',
            'center_contact_person' => 'nullable|string',
        ]);
        
        // Generate a 7-digit random number for center_id
        $validated['center_id'] = mt_rand(1000000, 9999999);
        
        // Add the created_by field with the authenticated user's ID
        $validated['created_by'] = auth()->id();
        
        // Create a new center with the validated data
        $center = Centers::create($validated);
        
        // Return a response, typically JSON
        return response()->json([
            'message' => 'Center created successfully',
            'center' => $center,
        ], 201); // HTTP status code 201: Created
    }


    public function updateCenter(Request $request, $center_id)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            // 'center_id' => 'required|string',
            'center_name' => 'required|string',
            'center_contact_person' => 'nullable|string',
        ]);
    
        // Find the center by its course_id
        $center = Centers::where('center_id', $center_id)->first();
    
        // Check if the center exists
        if (!$center) {
            return response()->json(['message' => 'Center not found.'], 404);
        }
    
        // Update the course with the validated data
        $center->update($validatedData);
    
        return response()->json(['message' => 'Center updated successfully.']);
    }
    
}
