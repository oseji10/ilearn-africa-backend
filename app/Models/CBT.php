<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CBT extends Model
{
    use HasFactory;
    public $table = 'cbt_exams';
    protected $primaryKey = 'examId';
    protected $fillable = ['examName', 'details', 'examDate', 'examTime', 'isShuffle', 'isRandom', 'canRetake', 'canSeeResult', 'status', 'courseId', 'cohortId', 'addedBy', 'timeAllowed'];
    
   

    public function cohort()
    {
        return $this->hasOne(Cohorts::class, 'cohort_id', 'cohortId');
    }

    public function retakes()
    {
        return $this->hasOne(ExamRetake::class, 'examId', 'examId');
    }

    public function course()
    {
        return $this->hasOne(CourseList::class, 'course_id', 'courseId');
    }

    public function clientCohort()
    {
        return $this->belongsTo(Admissions::class, 'cohortId', 'cohort_id');
    }

    public function questions()
    {
        return $this->hasMany(Questions::class, 'questionId', 'questionId');
    }

    public function cbt_exam_result()
    {
        return $this->hasMany(ExamResult::class, 'examId', 'examId');
    }
    
    public function cbt_exam_questions()
    {
        return $this->hasMany(ExamQuestions::class, 'examId', 'examId');
    }
    
}
