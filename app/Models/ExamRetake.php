<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamRetake extends Model
{
    use HasFactory;
    public $table = 'cbt_exams_retake';
    protected $primaryKey = 'retakeId';
    protected $fillable = ['examId', 'clientId', 'retake_count', 'permittedBy']; 

    public function cbt()
    {
        return $this->belongsTo(CBT::class, 'examId', 'examId');
    }

    public function clients()
    {
        return $this->belongsTo(Client::class, 'clientId', 'client_id');
    }

    public function exam_questions()
{
    return $this->hasMany(ExamQuestions::class, 'examId', 'examId'); // ExamResult is linked to multiple questions
}

}
