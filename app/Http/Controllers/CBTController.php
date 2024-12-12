<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CBT;
use App\Models\Questions;
use App\Models\QuestionOptions;
use App\Models\Client;
use App\Models\Admissions;
use App\Models\ExamQuestions;
use DB;
class CBTController extends Controller
{
    public function RetrieveAll()
    {
        $cbt = CBT::with('course', 'cohort')->get();
        return response()->json($cbt);
    }

    public function RetrieveClientWithCohort($client_id)
    {
        // Retrieve the client with their admissions relationship
        // $client = Client::with('admissions')->where('client_id', $client_id)->first();
    
        // // Handle the case where the client is not found
        // if (!$client) {
        //     return response()->json(['message' => 'Client not found'], 404);
        // }
    
        // // Assuming admissions is a single relationship (not a collection)
        // $admission = $client->admissions->first(); // Use ->first() if admissions is a collection
    
        // // Handle the case where no admission is associated
        // if (!$admission) {
        //     return response()->json(['message' => 'No admissions found for this client'], 404);
        // }
    
     // Retrieve CBT records matching the cohort_id
$cbts = CBT::with('course', 'cohort', 'clientCohort')
// ->where('cohortId', $admission->cohort_id)
->whereHas('clientCohort', function ($query) use ($client_id) {
    $query->where('status', '=', 'pending')
          ->where('client_id', $client_id); // Assuming $admission->client_id is correct.
})
->get();

    
    
        return response()->json($cbts);
    }
    

    public function RetrieveCBT(Request $request)
    {
        $cbt = CBT::with('course', 'cohort', 'clientCohort')
        ->where('cohortId', '=', $request->cohort_id)
        ->get();
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

    public function loadQuestions($examId)
    {
        $questions = ExamQuestions::with('questions', 'questions.options')->where('examId', $examId)->get();
        return response()->json($questions);

//         $cbts = CBT::with('questions', 'cohort', 'clientCohort')
// // ->where('cohortId', $admission->cohort_id)
// ->whereHas('clientCohort', function ($query) use ($client_id) {
//     $query->where('status', '=', 'pending')
//           ->where('client_id', $client_id); // Assuming $admission->client_id is correct.
// })
// ->get();
    }


    public function storeQuestion(Request $request)
    {
        DB::beginTransaction(); // Start the transaction
    
        try {
            $data = $request->all();
    
            // Create the question and save it to the questions table
            $question = Questions::create($data);
    
            // Create the exam_question record
            $exam_question = ExamQuestions::create([
                'examId' => $data['examId'], // Pass the correct examId
                'questionId' => $question->questionId,
                'score' => $data['score'], // Ensure 'score' is passed
            ]);
    
            // Add options to the question
            foreach ($data['options'] as $index => $optionText) {
                QuestionOptions::create([
                    'questionId' => $question->questionId, // Ensure this matches your table schema
                    'optionDetail' => $optionText,
                    'isCorrect' => ($index === (int)$data['correctOptionIndex']) ? 1 : 0,
                ]);
            }
    
            // If everything is successful, commit the transaction
            DB::commit();
            
            return response()->json($question, 201); // Return success response
        } catch (\Exception $e) {
            // If there is any exception, rollback the transaction
            DB::rollBack();
    
            // Log the error for debugging purposes (optional)
            \Log::error('Error storing question: ' . $e->getMessage());
    
            // Return error response
            return response()->json(['error' => 'Failed to store question.'], 500);
        }
    }


    public function updateQuestion(Request $request, $questionId)
{
    $data = $request->all();

    // Find the question by ID
    $question = Questions::findOrFail($questionId);

    // Update the question details
    $question->update([
        'question' => $data['question'],
        'score' => $data['score']
    ]);

    // Collect all existing option IDs from the database for this question
    $existingOptionIds = QuestionOptions::where('questionId', $questionId)->pluck('optionId')->toArray();

    // Iterate over provided options to update or create as needed
    $receivedOptionIds = [];
    foreach ($data['options'] as $index => $option) {
        if (isset($option['optionId']) && in_array($option['optionId'], $existingOptionIds)) {
            // Update existing option
            $existingOption = QuestionOptions::findOrFail($option['optionId']);
            $existingOption->update([
                'optionDetail' => $option['optionDetail'],
                'isCorrect' => ($index === (int)$data['correctOptionIndex']) ? 1 : 0,
            ]);
            $receivedOptionIds[] = $option['optionId']; // Track updated options
        } else {
            // Create new option
            $newOption = QuestionOptions::create([
                'questionId' => $question->questionId,
                'optionDetail' => $option['optionDetail'],
                'isCorrect' => ($index === (int)$data['correctOptionIndex']) ? 1 : 0,
            ]);
            $receivedOptionIds[] = $newOption->optionId; // Track newly created option
        }
    }

    // Delete options that are not in the received options list
    QuestionOptions::where('questionId', $questionId)
        ->whereNotIn('optionId', $receivedOptionIds)
        ->delete();

    // Return the updated question with its options
    return response()->json($question->load('options'), 200);
}


public function deleteQuestion($questionId)
{
    try {
        
        DB::transaction(function () use ($questionId) {
            
            QuestionOptions::where('questionId', $questionId)->delete();

            
            Questions::where('questionId', $questionId)->delete();
        });

        return response()->json(['message' => 'Question and its options deleted successfully.'], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to delete question.'], 500);
    }
}


}
