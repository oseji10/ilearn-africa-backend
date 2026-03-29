<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamQuestions extends Model
{
    use HasFactory;

    protected $table = 'cbt_exams_questions';
    protected $primaryKey = 'examQuestionId';

    protected $fillable = [
        'questionId',
        'examId',
        'score',
    ];

    public function question()
    {
        return $this->belongsTo(Questions::class, 'questionId', 'questionId');
    }

    public function questions()
    {
        return $this->hasMany(Questions::class, 'questionId', 'questionId');
    }

    public function exam()
    {
        return $this->belongsTo(CBT::class, 'examId', 'examId');
    }
}