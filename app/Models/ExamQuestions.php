<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamQuestions extends Model
{
    use HasFactory;
    public $table = 'cbt_exams_questions';
    protected $primaryKey = 'examQuestionId';
    protected $fillable = ['questionId', 'examId', 'score']; 

    public function questions()
    {
        return $this->hasMany(Questions::class, 'questionId', 'questionId');
    }

    public function exams()
    {
        return $this->belongsTo(CBT::class, 'examId', 'examId');
    }

    public function exam_results()
{
    return $this->hasMany(ExamResult::class, 'examId', 'examId'); // Exam can have many results
}

    
}
