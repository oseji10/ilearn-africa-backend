<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\CBT;
use App\Models\Questions;
use App\Models\QuestionOptions;
use App\Models\Client;
use App\Models\Admissions;
use App\Models\ExamQuestions;
use App\Models\Payments;
use App\Models\ExamRetake;
use DB;
use App\Models\ExamResult;
use App\Models\ExamResultMaster;
use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\QuestionsImport;

class CBTController extends Controller
{
    public function RetrieveAll()
    {
        $cbt = CBT::with('course', 'cohort')
        ->orderBy('created_at', 'desc')
        ->get();
        return response()->json($cbt);
    }


    public function retakeExam(Request $request){
        {
            // $request->validate([
            //     'examId' => 'required|integer|exists:cbts,id',
            //     'clientId' => 'required|integer|exists:clients,id',
            // ]);
    
            $examId = $request->examId;
            $clientId = $request->clientId;
    
            // Check if the client has already retaken this exam
            $examRetake = ExamRetake::where('examId', $examId)
                                    ->where('clientId', $clientId)
                                    ->first();
    
            if ($examRetake) {
                // Increment retake_count if the record exists
                $examRetake->increment('retake_count');
            } else {
                // Create a new record with retake_count = 1
                ExamRetake::create([
                    'examId' => $examId,
                    'clientId' => $clientId,
                    'retake_count' => 1,
                    'permittedBy' => Auth::id(),
                ]);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Exam retake request processed successfully',
            ]);
        }
    }
    // public function RetrieveClientWithCohort($client_id)
    // {
    //     $now = Carbon::now(); // Get the current timestamp
    //     $midnight = Carbon::today()->endOfDay();
    //     $cbts = CBT::with('course', 'cohort', 'clientCohort')
    //         ->where('status', 'active')
    //         ->whereDate('examDate', Carbon::today()->toDateString()) // Ensure exam is today
    //         ->whereTime('examTime', '>=', $now->toTimeString()) // Ensure exam time is now or later
    //         ->whereTime('examTime', '<=', $midnight->toTimeString())
    //         ->whereHas('clientCohort', function ($query) use ($client_id) {
    //             $query->where('status', '=', 'ADMITTED')
    //                   ->where('client_id', $client_id);
    //         })
    //         ->where(function ($query) use ($client_id) {
    //             $query->whereDoesntHave('cbt_exam_result', function ($subQuery) use ($client_id) {
    //                 $subQuery->where('clientId', $client_id);
    //             })
    //             ->orWhere('canRetake', 1); // Allow if retakes are allowed
    //         })
    //         ->get();
    
    //     return response()->json($cbts);
    // }


 public function RetrieveClientWithCohort($client_id)
{
    $now = Carbon::now(); // Get the current timestamp
    $midnight = Carbon::today()->endOfDay();
    
    // Fetch all exams for the client
   return $cbts = CBT::with('course', 'cohort', 'admission_status', 'retakes')
        ->where('status', 'active')
        ->whereDate('examDate', Carbon::today()->toDateString()) // Ensure exam is today
        ->whereTime('examTime', '<=', $midnight->toTimeString())
        ->whereHas('admission_status', function ($query) use ($client_id) {
            $query->where('status', '=', 'ADMITTED')
                  ->where('client_id', $client_id);
        })
        ->orderBy('created_at', 'desc')
        ->get();

    $client_status = 'not eligible'; // Default status
    $eligible_for_exam = false; // Flag to check if any exam is available now

    // Process each exam
    $cbts = $cbts->map(function ($exam) use ($client_id, $now, &$eligible_for_exam, &$client_status) {
        $course_id = $exam->course->course_id;

        // Retrieve payment record for the course
        $payment = Payments::where('client_id', $client_id)
            ->where('course_id', $course_id)
            ->first();

        // Count how many times the client has taken this exam
        $examAttempts = ExamResultMaster::where('clientId', $client_id)
            ->where('examId', $exam->examId)
            ->count();

        // Check if the client has a retake record and retrieve retake count
        $examRetake = ExamRetake::where('clientId', $client_id)
            ->where('examId', $exam->examId)
            ->first();

        $retakeCount = $examRetake ? $examRetake->retake_count : 0;

        // Determine status
        if (!$payment || $payment->part_payment < $payment->amount) {
            $exam->status2 = 'incomplete payment';
        } elseif ($now->lt(Carbon::parse($exam->examTime))) {
            $exam->status2 = 'not yet available';
        } elseif ($examAttempts > 0 && (!$examRetake || $examAttempts > $retakeCount)) {
            // Client has taken the exam but has no more allowed retakes
            $exam->status2 = 'cannot retake exam';
        } else {
            $exam->status2 = 'eligible for exam';
            $eligible_for_exam = true;
        }

        return $exam;
    });

    // If at least one exam is available now, update client status
    if ($eligible_for_exam) {
        $client_status = 'eligible for exam';
    }

    return response()->json([
        'client_status' => $client_status,
        'exams' => $cbts
    ]);
}


// public function RetrieveClientWithCohort($client_id)
// {
//     $now = Carbon::now();
//     $midnight = Carbon::today()->endOfDay();
    
//     // Fetch all exams for the client
//     $cbts = CBT::with('course', 'cohort', 'clientCohort')
//         ->where('status', 'active')
//         ->whereDate('examDate', Carbon::today()->toDateString()) // Ensure exam is today
//         ->whereTime('examTime', '<=', $midnight->toTimeString())
//         ->whereHas('clientCohort', function ($query) use ($client_id) {
//             $query->where('status', '=', 'ADMITTED')
//                   ->where('client_id', $client_id);
//         })
//         ->orderBy('created_at', 'desc')
//         ->get();

//     $client_status = 'not eligible'; // Default status
//     $eligible_for_exam = false; // Flag to check if any exam is available now

//     // Process each exam
//     $cbts = $cbts->map(function ($exam) use ($client_id, $now, &$eligible_for_exam, &$client_status) {
//         $course_id = $exam->course->course_id;

//         // Retrieve payment record for the course
//         $payment = Payments::where('client_id', $client_id)
//             ->where('course_id', $course_id)
//             ->first();

//         // Retrieve all past attempts of the exam
//         $examAttempts = ExamResultMaster::where('clientId', $client_id)
//             ->where('examId', $exam->id)
//             ->get();

//         // Retrieve total score from all exam questions
//         $totalScore = ExamQuestions::where('examId', $exam->id)
//             ->sum('score');

//         $canRetake = true; // Default retake status

//         // If the student has taken the exam before
//         if (!$examAttempts->isEmpty() && $totalScore > 0) {
//             foreach ($examAttempts as $attempt) {
//                 // Calculate percentage score for each attempt
//                 $percentageScore = ($attempt->score / $totalScore) * 100;

//                 // If any attempt has a score of 50% or higher, do not allow retake
//                 if ($percentageScore >= 50) {
//                     $canRetake = false;
//                     break;
//                 }
//             }
//         }

//         // Determine eligibility status
//         if (!$payment || $payment->part_payment < $payment->amount) {
//             $exam->status2 = 'incomplete payment';
//         } elseif ($now->lt(Carbon::parse($exam->examTime))) {
//             $exam->status2 = 'not yet available';
//         } elseif (!$canRetake) {
//             $exam->status2 = 'cannot retake exam (score >= 50%)';
//         } else {
//             $exam->status2 = 'eligible for exam';
//             $eligible_for_exam = true;
//         }

//         return $exam;
//     });

//     // If at least one exam is available now, update client status
//     if ($eligible_for_exam) {
//         $client_status = 'eligible for exam';
//     }

//     return response()->json([
//         'client_status' => $client_status,
//         'exams' => $cbts
//     ]);
// }


    
    

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
        $cbt = CBT::find($examId);
    
        if (!$cbt) {
            return response()->json([
                'error' => 'Exam not found',
            ], 404); // HTTP status code 404: Not Found
        }
    
        // Get the data from the request
        $data = $request->all();
        $cbt->update($data);
        return response()->json([
            'message' => 'Exam updated successfully',
            'data' => $cbt,
        ], 200); // HTTP status code 200: OK
    }


    
    
    public function cloneCBT($examId)
    {
        // Find the original exam
        $originalExam = CBT::find($examId);
        if (!$originalExam) {
            return response()->json(['message' => 'Exam not found'], 404);
        }
    
        // Clone the exam
        $newExam = CBT::create([
            'examName' => $originalExam->examName . ' (Copy)',
            'isShuffle' => $originalExam->isShuffle,
            'isRandom' => $originalExam->isRandom,
            'canRetake' => $originalExam->canRetake,
            'canSeeResult' => $originalExam->canSeeResult,
            'status' => 'inactive',
            'timeAllowed' => $originalExam->timeAllowed,
            'addedBy' => Auth::id(),
        ]);
    
        // Clone the questions
        $originalQuestions = ExamQuestions::where('examId', $examId)->get();
        foreach ($originalQuestions as $question) {
            ExamQuestions::create([
                'examId' => $newExam->examId,
                'questionId' => $question->questionId,
                'score' => $question->score
            ]);
        }

        
    
        return response()->json(['message' => 'Exam cloned successfully', 'new_exam_id' => $newExam->examId], 201);
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
        // Fetch the CBT exam details
        $cbtExam = CBT::find($examId);
    
        if (!$cbtExam) {
            return response()->json(['error' => 'Exam not found'], 404);
        }
    
        // Retrieve questions based on the examId
        $questions = ExamQuestions::with(['exams', 'questions.options'])
            ->where('examId', $examId)
            ->get();
    
        // Shuffle the questions if isShuffle is set to 1
        if ($cbtExam->isShuffle == 1) {
            $questions = $questions->shuffle();
        }
    
        // Check if isRandom is set to 1 and shuffle options inside each question
        if ($cbtExam->isRandom == 1) {
            $questions->transform(function ($examQuestion) {
                $examQuestion->questions->transform(function ($question) {
                    if ($question->relationLoaded('options')) {
                        $question->setRelation('options', $question->options->shuffle());
                    }
                    return $question;
                });
    
                return $examQuestion;
            });
        }
    
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
        'answers' => 'array',
        'answers.*.questionId' => 'integer',
        'answers.*.optionSelected' => 'integer',
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
            'masterId' => $masterResult->masterId, // Store reference to ExamResultMaster
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
public function ExamResults($examId)
{
    
    // $results = ExamResultMaster::join('cbt_exams', 'cbt_exams.examId', '=', 'cbt_master_results.examId')
    // ->join('clients', 'clients.client_id', '=', 'cbt_master_results.clientId')
    // ->join('cbt_exams_retake', 'cbt_exams_retake.examId', '=', 'cbt_master_results.examId')
    // ->where('cbt_master_results.examId', $examId)
    // ->select('cbt_master_results.*', 'cbt_exams.*', 'clients.*', 'cbt_exams_retake.*') // Explicitly select fields
    // ->get();

     $results = ExamResultMaster::with('exam', 'client')
    ->where('examId', $examId)
    ->get();
    return response()->json($results);
}


public function MyExamResult(Request $request)
{
    // Fetch the most recent exam result for the client in the specified exam
    // $result = ExamResult::join('clients', 'clients.client_id', '=', 'cbt_exams_results.clientId')
    //     ->join('cbt_exams', 'cbt_exams.examId', '=', 'cbt_exams_results.examId')
    //     ->where('cbt_exams_results.clientId', $request->client_id)
    //     ->where('cbt_exams_results.examId', $request->examId)
    //     ->orderBy('cbt_exams_results.created_at', 'desc')  // Sort by exam_date (assuming you have this column)
    //     ->select('cbt_exams.examName', 'clients.firstname', 'clients.surname', DB::raw('SUM(cbt_exams_results.score) as total_score'))
    //     ->groupBy('cbt_exams.examName', 'clients.client_id', 'clients.firstname', 'clients.surname', 'cbt_exams_results.created_at')
    //     ->first();
$result = ExamResultMaster::
        // join('cbt_exams', 'cbt_exams.examId', '=', 'cbt_master_results.examId')
        join('cbt_exams', 'cbt_exams.examId', '=', 'cbt_master_results.examId')
        ->join('clients', 'clients.client_id', '=', 'cbt_master_results.clientId')
        ->where('clientId', $request->client_id)
        ->where('cbt_master_results.examId', $request->examId)
        ->latest('cbt_master_results.created_at')->first();
    // Return the most recent result
    return response()->json($result);
}

public function MyCBTExamResult($client_id) {
    $results = ExamResultMaster::where('clientId', $client_id)
        ->with(['exam' => function ($query) {
            $query->select('examId', 'examName', 'examDate', 'canSeeResult'); // Include canSeeResult for validation
        }])
        ->select('masterId', 'clientId', 'total_score', 'examId') // Ensure examId is selected for relationship
        ->get();

    // Modify results based on canSeeResult
    $results->transform(function ($result) {
        if ($result->exam && $result->exam->canSeeResult == 0) {
            unset($result->total_score); // Remove total_score if canSeeResult is 0
        }
        return $result;
    });

    return response()->json($results);
}


// See all results for an exam
public function CBTExamResults() {
    // $results = ExamResultMaster::with(['exam' => function ($query) {
    //         $query->select('examId', 'examName', 'examDate', 'canSeeResult'); // Include canSeeResult for validation
    //     }])
    //     ->select('masterId', 'clientId', 'total_score', 'examId') // Ensure examId is selected for relationship
    //     ->get();

    // // Modify results based on canSeeResult
    // $results->transform(function ($result) {
    //     if ($result->exam && $result->exam->canSeeResult == 0) {
    //         unset($result->total_score); // Remove total_score if canSeeResult is 0
    //     }
    //     return $result;
    // });
    $results = CBT::with('cohort')->orderBy('examDate', 'desc')->get();
    return response()->json($results);
    
}


// Delete Exam
public function deleteStudentExamResult($masterId){
    $examResultMaster = ExamResultMaster::find($masterId);
if ($examResultMaster) {
    $examResultMaster->examResults()->delete(); // Delete child records
    $examResultMaster->delete(); // Delete parent record
}

}


public function getUserExamResults($masterId)
{
    $results = ExamResultMaster::where('masterId', $masterId)
        ->with(
            'client',
            'exam', 
            'exam_questions.questions.options',
            'cbt_results'
        )
        ->first();

    // Check if results exist
    if (!$results) {
        return response()->json(['message' => 'No exam results found'], 404);
    }

    // Calculate total score from exam_questions
    $totalScore = $results->exam_questions->sum('score');

    // Append total score to response
    $results->total_score2 = $totalScore;

    return response()->json($results);
}


// Download Exam Results in PDF format
public function downloadExamResults($masterId)
{
    $results = ExamResultMaster::where('masterId', $masterId)
        ->with(
            'client.admissions',
            'exam.course', 
            'exam_questions',
            'cbt_results',
            'client.passport'
        )
        ->first();

    // Check if results exist
    if (!$results) {
        return response()->json(['message' => 'No exam results found'], 404);
    }

    // Calculate total score from exam_questions
    $totalScore = $results->exam_questions->sum('score');

    // Append total score to response
    $results->total_score2 = $totalScore;
// return $results;
    // Generate and return PDF
    // return view ('pdf.test_report', compact('results'));
     $pdf = \PDF::loadView('pdf.test_report', compact('results'));
    return $pdf->setPaper('undefined')->stream('result-slip.pdf');
    // return $pdf->setPaper('undefined')->download('invoice.pdf');

    // return $pdf->download('exam-results.pdf');

}


// Bulk import questions
public function importQuestions(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,csv',
        // 'examId' => 'required|exists:exams,id', // Ensure examId is valid
    ]);

    Excel::import(new QuestionsImport($request->examId), $request->file('file'));

    return response()->json(['message' => 'Questions uploaded successfully!']);
}



}
