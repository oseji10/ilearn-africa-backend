<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cohorts extends Model
{
    use HasFactory;
    public $table = 'cohorts';
    protected $fillable = ['cohort_id', 'cohort_name', 'status', 'created_by', 'start_date', 'capacity_per_class'];
    
   

    public function cohort_courses()
    {
        return $this->hasMany(CohortsCourses::class, 'cohort_id', 'cohort_id');
    }
}
