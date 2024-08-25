<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseList;
use App\Models\Payments;

class CourseListController extends Controller
{
    public function show()
    {
        // $courses = CourseList::orderBy('id', 'desc')->get();
        $courses = CourseList::with('centers')
        ->orderBy('id', 'desc')
        ->get();
        return response()->json([
            'message' => 'Courses retrieved successfully',
            'courses' => $courses,
        ]);
       
    }

    public function showMyRegisterableCourses()
{
    // Get the logged-in user's ID
    $userId = auth()->user()->client_id;

    // Fetch all courses
    $courses = CourseList::all();

    // Iterate through each course and check if the user has paid for it
    $coursesWithStatus = $courses->map(function ($course) use ($userId) {
        // Check if a payment exists for this course by this user
        $paymentExists = Payments::where('course_id', $course->course_id)
                                ->where('client_id', $userId)
                                ->exists();

        // Add a status field based on whether the payment exists
        $course->status = $paymentExists ? 'Paid' : 'Not Paid';

        return $course;
    });

    // Return the modified course list with the payment status
    return response()->json([
        'message' => 'Courses retrieved successfully',
        'courses' => $coursesWithStatus,
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
