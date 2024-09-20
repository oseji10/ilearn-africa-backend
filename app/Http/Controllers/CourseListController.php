<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseList;
use App\Models\Payments;
use App\Models\CourseMaterial;
use App\Models\Cohorts;
use App\Models\CohortsClients;
use App\Models\CohortsCourses;
use App\Models\Client;

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

    public function showMyRegisterableCourses(Request $request, $cohort_id)
{
    // Get the logged-in user's ID
    $userId = auth()->user()->client_id;

    // Fetch all courses
    $courses = CohortsCourses::with('course_list')->where('cohort_id', $cohort_id)->get();

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
        // 'courses' => $courses,
    ]);
}




// public function showMyRegisterableCourses()
// {
//     // Get the logged-in user's ID
//     $userId = auth()->user()->client_id;

//     // Fetch all courses
//     $courses = CourseList::all();

//     // Iterate through each course and check if the user has paid for it
//     $coursesWithStatus = $courses->map(function ($course) use ($userId) {
//         // Check if a payment exists for this course by this user
//         $paymentExists = Payments::where('course_id', $course->course_id)
//                                 ->where('client_id', $userId)
//                                 ->exists();

//         // Add a status field based on whether the payment exists
//         $course->status = $paymentExists ? 'Paid' : 'Not Paid';

//         return $course;
//     });

//     // Return the modified course list with the payment status
//     return response()->json([
//         'message' => 'Courses retrieved successfully',
//         'courses' => $coursesWithStatus,
//     ]);
// }


public function store(Request $request)
{
    // Validate the incoming request data
    $validated = $request->validate([
        'course_id' => 'required|string',
        'course_name' => 'required|string',
        'course_image' => 'nullable|string',
        'cost' => 'nullable|string',
        'center_id' => 'nullable|string',
        'certification_name' => 'nullable|string',
        'professional_certification_name' => 'nullable|string',
    ]);

    // Check if the course_id already exists
    if (CourseList::where('course_id', $validated['course_id'])->exists()) {
        return response()->json([
            'message' => 'A course with this ID already exists',
        ], 409); // HTTP status code 409: Conflict
    }

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



    public function deleteCourse($course_id)
    {
        // First, find and delete the related course materials
        $courseMaterials = CourseMaterial::where('course_id', $course_id)->get();
    
        if ($courseMaterials->isNotEmpty()) {
            foreach ($courseMaterials as $material) {
                $material->delete(); // Delete each course material
            }
        }
    
        // Now, find the course in the course list
        $course = CourseList::where('course_id', $course_id)->first();
    
        // Check if the course exists
        if ($course) {
            $course->delete(); // Delete the course from the course list
            return response()->json(['message' => 'Course and related materials deleted successfully.']);
        } else {
            return response()->json(['message' => 'Course not found.'], 404);
        }
    }





    public function updateCourse(Request $request, $course_id)
{
    // Validate the incoming request data
    $validatedData = $request->validate([
        // 'course_id' => 'required|string',
        'course_name' => 'required|string',
        'course_image' => 'nullable|string',
        'cost' => 'nullable|string',
        'certification_name' => 'nullable|string',
        'center_id' => 'nullable|string',
        'professional_certification_name' => 'nullable|string',
    ]);

    // Find the course by its course_id
    $course = CourseList::where('course_id', $course_id)->first();

    // Check if the course exists
    if (!$course) {
        return response()->json(['message' => 'Course not found.'], 404);
    }

    // Update the course with the validated data
    $course->update($validatedData);

    return response()->json(['message' => 'Course updated successfully.']);
}

    
    
}
