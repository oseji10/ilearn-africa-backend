<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseList;

class CourseListController extends Controller
{
    public function show()
    {
        $courses = CourseList::all();
        return response()->json([
            'message' => 'Courses retrieved successfully',
            'courses' => $courses,
        ]);
       
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'course_id' => 'required|string',
            'course_name' => 'required|string',
            'course_image' => 'nullable|string',
            'cost' => 'nullable|string',
            'center_id' => 'nullable|string',
        ]);
    
        // Add the created_by field with the authenticated user's ID
        $validated['created_by'] = auth()->id();
    
        // Create a new course with the validated data
        $course = CourseList::create($validated);
    
        // Return a response, typically JSON
        return response()->json([
            'message' => 'Course created successfully',
            'course' => $course,
        ], 201); // HTTP status code 201: Created
    }
    
}
