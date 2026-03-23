<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Courses;
use App\Models\CourseList;
use App\Models\CohortsCourses;
use App\Models\Cohorts;

    use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
// use App\Models\CohortsCourses;
use App\Models\Payments; // or your actual enrollment model


class CoursesController extends Controller
{
    public function show()
    {
        $courses = Courses::all();
        return response()->json(['courses' => $courses]);
    }

    public function activeCourses()
    {
        $cohorts = CohortsCourses::with('course_list', 'cohorts')
            ->whereHas('cohorts', function ($query) {
                $query->where('status', 'active');
            })
         
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json([
            'message' => 'Cohorts retrieved successfully',
            'cohorts' => $cohorts,
        ]);
    }



public function activeClientCourses(): JsonResponse
{
    $user = auth()->user();

    if (!$user) {
        return response()->json([
            'message' => 'Unauthorized',
        ], 401);
    }

    // Adjust this depending on where client_id lives in your app
    $clientId = $user->client_id ?? null;

    if (!$clientId) {
        return response()->json([
            'message' => 'Client ID not found for this user',
        ], 400);
    }

    /*
    |--------------------------------------------------------------------------
    | Get all enrolled course/cohort combinations for this client
    |--------------------------------------------------------------------------
    | Change this query to match your real enrollment source.
    | Example below assumes a payment means the user has already registered
    | for that course/cohort.
    */
    $enrolledKeys = Payments::where('client_id', $clientId)
        ->whereNotNull('course_id')
        ->whereNotNull('cohort_id')
        ->get(['course_id', 'cohort_id'])
        ->map(fn ($item) => $item->course_id . '_' . $item->cohort_id)
        ->toArray();

    $cohorts = CohortsCourses::with('course_list', 'cohorts')
        ->whereHas('cohorts', function ($query) {
            $query->where('status', 'active');
        })
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($item) use ($enrolledKeys) {
            $item->isEnrolled = in_array(
                $item->course_id . '_' . $item->cohort_id,
                $enrolledKeys
            );

            return $item;
        });

    return response()->json([
        'message' => 'Cohorts retrieved successfully',
        'cohorts' => $cohorts,
    ]);
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
