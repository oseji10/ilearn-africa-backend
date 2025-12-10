<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grades;
class GradesController extends Controller
{
    public function show()
    {
        $grades = Grades::all();
        return response()->json([
            'message' => 'Grades retrieved successfully',
            'grades' => $grades,
        ]);
       
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'grade' => 'required|string|max:255',
            
        ]);

        // Create a new client with the validated data
        $grade = Grades::create($validated);

        // Return a response, typically JSON
        return response()->json([
            'message' => 'Grade created successfully',
            'grade' => $grade,
        ], 201); // HTTP status code 201: Created
    }
}
