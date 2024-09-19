<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes; 

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseList extends Model
{
    use HasFactory, softDeletes;
    public $table = 'course_list';
    protected $fillable = ['course_id', 'course_name', 'cost', 'course_image', 'status', 'created_by', 'center_id', 'certification_name', 'professional_certification_name'];


    public function centers()
    {
        return $this->belongsTo(Centers::class, 'center_id', 'center_id');
    }

    public function cohort_courses()
    {
        return $this->belongsTo(CohortsCourses::class, 'course_id', 'course_id');
    }
}
