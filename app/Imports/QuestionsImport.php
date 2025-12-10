<?php
namespace App\Imports;

use App\Models\Questions;
use App\Models\QuestionOptions;
use App\Models\ExamQuestions;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class QuestionsImport implements ToCollection, WithHeadingRow
{
    private $currentQuestion = null;
    private $examId;

    public function __construct($examId)
    {
        $this->examId = $examId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (strtolower($row['type']) == 'question') {
                // Create a new question
                $this->currentQuestion = Questions::create([
                    'question'           => $row['question_text'],
                    'score'              => $row['score'],
                ]);

                // Link question to the exam
                ExamQuestions::create([
                    'questionId' => $this->currentQuestion->questionId,
                    'examId'     => $this->examId, // Pass the exam ID from constructor
                    'score'      => $row['score'],
                ]);
            } elseif (strtolower($row['type']) == 'option' && $this->currentQuestion) {
                // Create an option linked to the last question
                QuestionOptions::create([
                    'questionId'   => $this->currentQuestion->questionId,
                    'optionDetail' => $row['option_text'],
                    'isCorrect'    => $row['is_correct'] == 1 ? true : false,
                ]);
            }
        }
    }
}
