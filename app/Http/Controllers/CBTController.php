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
use App\Models\ExamResult;
use App\Models\ExamResultMaster;

class CBTController extends Controller
{
    public function RetrieveAll()
    {
        $cbt = CBT::with('course', 'cohort')->get();
        return response()->json($cbt);
    }

    public function RetrieveClientWithCohort($client_id)
    {
        
$cbts = CBT::with('course', 'cohort', 'clientCohort')
    ->where('status', 'active')
    ->whereHas('clientCohort', function ($query) use ($client_id) {
        $query->where('status', '=', 'ADMITTED')
              ->where('client_id', $client_id);
    })
    ->get()
    ->filter(function ($cbt) use ($client_id) {
        // Check if the client has taken this exam
        $hasTakenExam = ExamResultMaster::where('clientId', $client_id)
            ->where('examId', $cbt->id)
            ->exists();

        // Allow the exam only if it hasn't been taken OR if retakes are allowed
        return !$hasTakenExam || $cbt->canRetake == 1;
    });

// Return the filtered exams
return response()->json($cbts->values()); // Reset array keys


    
    
        // return response()->json($cbts);
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

    public function RetrieveExamQuestions($examId)
    {
        $questions = Questions::
        with('options')
        ->whereHas('questions', function ($query) use ($examId) {
            $query->where('examId', $examId);
        })
        ->get();
        return response()->json($questions);

        // $doctors = User::where('role', 2)
        //     ->whereHas('hospital_admins', function ($query) use ($hospitalId) { // Pass hospitalId into closure
        //         $query->where('hospitalId', $hospitalId);
        //     })
        //     ->get();
    }

    

    public function loadQuestions($examId)
    {
        $questions = ExamQuestions::with('exams', 'questions', 'questions.options')->where('examId', $examId)->get();
        return response()->json($questions);
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


public function submitExam(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'clientId' => 'required|string',
        'examId' => 'required|integer',
        'answers' => 'required|array',
        'answers.*.questionId' => 'required|integer',
        'answers.*.optionSelected' => 'required|integer',
    ]);

    $examId = $request->input('examId');
    $answers = $request->input('answers');
    $clientId = $request->input('clientId');
    $totalScore = 0;
    $results = [];

    // Create a master record for the exam submission
    $masterResult = ExamResultMaster::create([
        'examId' => $examId,
        'clientId' => $clientId, // Assuming clientId should also be stored in ExamResultMaster
        'total_score' => 0, // Initialize score, will update later
    ]);

    foreach ($answers as $answer) {
        $questionId = $answer['questionId'];
        $optionSelected = $answer['optionSelected'];

        // Fetch the question
        $question = Questions::find($questionId);

        if (!$question) {
            return response()->json([
                'message' => "Question with ID $questionId not found."
            ], 404);
        }

        // Fetch the correct option for the question
        $correctOption = QuestionOptions::where('questionId', $questionId)
                                ->where('isCorrect', 1)
                                ->first();

        if (!$correctOption) {
            return response()->json([
                'message' => "No correct option set for question with ID $questionId."
            ], 500);
        }

        // Determine if the selected option is correct
        $isCorrect = $correctOption->optionId === $optionSelected;
        $score = $isCorrect ? $question->score : 0;

        // Save the result to ExamResult table with masterId reference
        $result = ExamResult::create([
            'masterId' => $masterResult->id, // Store reference to ExamResultMaster
            'clientId' => $clientId,
            'examId' => $examId,
            'questionId' => $questionId,
            'optionSelected' => $optionSelected,
            'isCorrect' => $isCorrect,
            'score' => $score,
        ]);

        $results[] = $result;
        $totalScore += $score;
    }

    // Update the total score in the ExamResultMaster record
    $masterResult->update(['total_score' => $totalScore]);

    // Return the response
    return response()->json([
        'message' => 'Exam submitted successfully.',
        'totalScore' => $totalScore,
        'masterId' => $masterResult->masterId,
        'results' => $results,
    ]);
}

// // Exam Results
// public function ExamResults()
// {
//     $results = ExamResult::with('clients')->get();
//     return response()->json($results);
// }
// public function ExamResults()
// {
//     $results = ExamResult::with('clients')->get();

//     // Add total score to each result
//     $results = $results->map(function ($result) use ($results) {
//         $result->total_score = $results->where('client_id', $result->client_id)->sum('score');
//         return $result;
//     });

//     return response()->json($results);
// }
public function ExamResults()
{
    // Fetch all results with client relationships
    $results = ExamResult::with('clients')->get();

    // Make the results distinct by client_id
    $distinctResults = $results->unique('client_id')->map(function ($result) use ($results) {
        // Calculate the total score for each client
        $result->total_score = $results->where('client_id', $result->client_id)->sum('score');
        return $result;
    });

    return response()->json($distinctResults->values()); // Reset array keys and return as JSON
}


public function MyExamResult(Request $request)
{
    // Fetch the most recent exam result for the client in the specified exam
    $result = ExamResult::join('clients', 'clients.client_id', '=', 'cbt_exams_results.clientId')
        ->join('cbt_exams', 'cbt_exams.examId', '=', 'cbt_exams_results.examId')
        ->where('cbt_exams_results.clientId', $request->client_id)
        ->where('cbt_exams_results.examId', $request->examId)
        ->orderBy('cbt_exams_results.created_at', 'desc')  // Sort by exam_date (assuming you have this column)
        ->select('cbt_exams.examName', 'clients.firstname', 'clients.surname', DB::raw('SUM(cbt_exams_results.score) as total_score'))
        ->groupBy('cbt_exams.examName', 'clients.client_id', 'clients.firstname', 'clients.surname', 'cbt_exams_results.created_at')
        ->first();

    // Return the most recent result
    return response()->json($result);
}

public function MyCBTExamResult($client_id) {
    
    $results = ExamResultMaster::where('clientId', $client_id)
    ->with(['exam' => function ($query) {
        $query->select('examId', 'examName', 'examDate'); // Include 'id' to maintain relationship
    }])
    ->select('masterId', 'clientId', 'total_score', 'examId') // Ensure 'examId' is selected for relationship
    ->get();

return response()->json($results);

}

}
