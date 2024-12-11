<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CBT;
use App\Models\Questions;

class CBTController extends Controller
{
    public function RetrieveAll()
    {
        $cbt = CBT::with('course', 'cohort')->get();
        return response()->json($cbt);
    }

      public function store(Request $request)
    {
        $data = $request->all();
        $cbt = CBT::create($data);
        return response()->json($cbt, 201); 
    }   
    
    
    public function updateCBT(Request $request, $examId)
    {
        // Find the patient by ID
        $cbt = CBT::find($examId);
    
        // If the patient doesn't exist, return an error response
        if (!$cbt) {
            return response()->json([
                'error' => 'Exam not found',
            ], 404); // HTTP status code 404: Not Found
        }
    
        // Get the data from the request
        $data = $request->all();
    
        // Update the patient record
        $cbt->update($data);
    
        // Return the updated patient record as a response
        return response()->json([
            'message' => 'Exam updated successfully',
            'data' => $cbt,
        ], 200); // HTTP status code 200: OK
    }


    public function RetrieveAllQuestions()
    {
        $questions = Questions::with('options')->get();
        return response()->json($questions);
    }

    public function storeQuestion(Request $request)
    {
        $data = $request->all();
        $cbt = Questions::create($data);
        return response()->json($cbt, 201); 
    } 
}
