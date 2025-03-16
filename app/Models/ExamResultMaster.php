<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResultMaster extends Model
{
    use HasFactory;
    public $table = 'cbt_master_results';
    protected $primaryKey = 'masterId';
    protected $fillable = ['examId', 'total_score', 'clientId']; 

    public function cbt_results()
    {
        return $this->hasMany(ExamResult::class, 'masterId', 'masterId');
    }

    public function clients()
    {
        return $this->hasMany(Client::class, 'client_id', 'clientId');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'client_id', 'clientId');
    }

    public function admission()
    {
        return $this->belongsTo(Admissions::class, 'client_id', 'client_id');
    }

    public function exam_retake_with_exam($examId)
{
    return $this->hasOne(ExamRetake::class, 'client_id', 'clientId')
                ->where('examId', $examId);
}


    public function exam_questions()
{
    return $this->hasMany(ExamQuestions::class, 'examId', 'examId'); // ExamResult is linked to multiple questions
}


public function exam()
{
    return $this->belongsTo(CBT::class, 'examId', 'examId'); // ExamResult is linked to multiple questions
}

public function examResults()
    {
        return $this->hasMany(ExamResult::class, 'masterId', 'masterId');
    }
}
