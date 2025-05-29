<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Courses;
use App\Models\CourseList;
use App\Models\CohortsCourses;
use App\Models\Cohorts;
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
            ->get();
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
