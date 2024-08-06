<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Courses;
class CoursesController extends Controller
{
    public function show()
    {
        $courses = Courses::all();
        return response()->json(['courses' => $courses]);
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'course_name' => 'required|string|max:255',
            'instructor' => 'nullable|string|max:255',
            
        ]);

        // Create a new client with the validated data
        $course = Courses::create($validated);

        // Return a response, typically JSON
        return response()->json([
            'message' => 'Course created successfully',
            'course' => $course,
        ], 201); // HTTP status code 201: Created
    }
}
