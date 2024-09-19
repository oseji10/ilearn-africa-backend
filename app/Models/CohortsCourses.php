<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CohortsCourses extends Model
{
    use HasFactory;
    public $table = 'cohorts_courses';
    protected $fillable = ['cohort_id', 'course_id', 'status', 'created_by'];
    
    public function course_list()
    {
        return $this->belongsTo(CourseList::class, 'course_id', 'course_id');
    }
}
