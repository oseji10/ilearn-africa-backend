<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cohorts;
use App\Models\CohortsCourses;

class CohortsController extends Controller
{
    public function showCohorts()
    {
        // $cohorts = Cohorts::all();
        $cohorts = Cohorts::with('cohort_courses')->withCount('cohort_courses')->get();
        return response()->json([
            'message' => 'Cohorts retrieved successfully',
            'cohorts' => $cohorts,
            // 'cohort_courses' => $cohort_courses
        ]);
    }


    public function changeCohortStatus(Request $request)
{
    // Update the cohort's status based on what the frontend sends
    $cohort_status = Cohorts::where('cohort_id', $request->cohort_id)
        ->update(['status' => $request->status]);

    // Check if the update was successful
    if ($cohort_status) {
        // Fetch the updated cohort to confirm the new status
        $updatedCohort = Cohorts::where('cohort_id', $request->cohort_id)->first();

        return response()->json([
            'message' => 'Cohort status updated successfully',
            'new_status' => $updatedCohort->status,  // Return the confirmed updated status
        ], 200);
    } else {
        return response()->json([
            'message' => 'Failed to update cohort status',
        ], 500);
    }
}

    


    public function activeCohorts()
    {
        $cohorts = Cohorts::with('cohort_courses')->where('status', 'active')->get();
        return response()->json([
            'message' => 'Cohorts retrieved successfully',
            'cohorts' => $cohorts,
        ]);
    }

    public function showCohortsCourses(Request $request, $cohort_id)
    {
        // $cohort_courses = CohortsCourses::with('course_list')->where('cohort_id', $cohort_id)->get();
        $cohort_courses = CohortsCourses::join('course_list', 'course_list.course_id', '=', 'cohorts_courses.course_id')
    ->where('cohort_id', $cohort_id)
    ->select('course_list.*')
    ->get();
        return response()->json([
            'message' => 'Courses for this Cohort retrieved successfully',
            'courses' => $cohort_courses,
        ]);
    }


    public function showAllCohortscourses()
    {
        $cohort_courses = CohortsCourses::all();
        return response()->json([
            'message' => 'Cohorts Courses retrieved successfully',
            'centers' => $cohort_courses,
        ]);
    }

    public function addCohort(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            // 'cohort_id' => 'required|string',
            'cohort_name' => 'nullable|string',
            'start_date' => 'nullable|string',
            'capacity_per_class' => 'nullable|string',
            'status' => 'nullable|string',
        ]);
        
        // Generate a 7-digit random number for cohort_id
        $validated['cohort_id'] = mt_rand(1000000, 9999999);
        
        // Add the created_by field with the authenticated user's ID
        $validated['created_by'] = auth()->id();
        
        // Create a new center with the validated data
        $cohort = Cohorts::create($validated);
        
        // Return a response, typically JSON
        return response()->json([
            'message' => 'Cohort created successfully',
            'cohort' => $cohort,
        ], 201); // HTTP status code 201: Created
    }


    public function addCohortCourses(Request $request)
    {
        // Validate the incoming request data to accept an array of course IDs
        $validated = $request->validate([
            'cohort_id' => 'required|exists:cohorts,cohort_id', // Ensure the cohort exists
            'course_ids' => 'required|array', // Expecting an array of course IDs
            'course_ids.*' => 'string|exists:course_list,course_id', // Ensure each course ID exists
        ]);
    
        $created_by = auth()->id(); // Get the authenticated user's ID
        $cohort_id = $validated['cohort_id'];
        $course_ids = $validated['course_ids']; // Array of course IDs
    
        $cohort_courses = [];
        $duplicates = [];
        
        // Loop through the course IDs and create a CohortsCourses record for each
        foreach ($course_ids as $course_id) {
            // Check if this combination of cohort_id and course_id already exists
            $exists = CohortsCourses::where('cohort_id', $cohort_id)
                                    ->where('course_id', $course_id)
                                    ->exists();
            
            if (!$exists) {
                // Create the CohortsCourses entry if it does not exist
                $cohort_courses[] = CohortsCourses::create([
                    'cohort_id' => $cohort_id,
                    'course_id' => $course_id,
                    'created_by' => $created_by,
                ]);
            } else {
                // Track duplicate course entries
                $duplicates[] = $course_id;
            }
        }
    
        // Return a response with created cohort courses and duplicates
        return response()->json([
            'message' => 'Cohort Courses processed successfully',
            'cohort_courses' => $cohort_courses,
            'duplicates' => $duplicates, // Inform which courses were duplicates
        ], 201); // HTTP status code 201: Created
    }
    
    
    


    public function updateCohort(Request $request, $cohort_id)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            // 'center_id' => 'required|string',
            'cohort_name' => 'required|string',
            'start_date' => 'nullable|string',
            'capacity_per_class' => 'nullable|string',
        ]);
    
        // Find the center by its course_id
        $cohort = Cohorts::where('cohort_id', $cohort_id)->first();
    
        // Check if the center exists
        if (!$cohort) {
            return response()->json(['message' => 'Cohort not found.'], 404);
        }
    
        // Update the course with the validated data
        $cohort->update($validatedData);
    
        return response()->json(['message' => 'Cohort updated successfully.']);
    }


    public function deleteCohort($cohort_id)
{
    // Find the client using the unique client_id field
    $model = Cohorts::where('cohort_id', $cohort_id)->first();
    // return $model->created_at;
    // Check if the model was found
    if ($model) {
        $model->delete();
        return response()->json(['message' => 'Cohort successfully deleted.']);
    } else {
        return response()->json(['message' => 'Cohort not found.'], 404);
    }
}

public function deleteCohortCourse(Request $request)
{
    // Validate the incoming request
    // $request->validate([
    //     'cohort_id' => 'required|exists:cohorts,id', // Adjust table name and column as necessary
    //     'course_id' => 'required|exists:courses,id', // Adjust table name and column as necessary
    // ]);

    // Find the course in the cohort
    $model = CohortsCourses::where('course_id', "=", $request->cohort_id)
        ->where('cohort_id', "=", $request->course_id)
        ->first();
        
      
        
        // Check if the model was found
        if ($model) {
            $model->delete();
        return response()->json(['message' => 'Course successfully deleted from cohort.']);
    } else {
        return response()->json(['message' => 'Course not found.'], 404);
    }
}

    
}
