<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseMaterial extends Model
{
    use HasFactory;
    public $table = 'course_material';
    protected $fillable = ['course_id', 'material_link', 'material_type'];

    public function courses()
    {
        return $this->belongsTo(CourseList::class, 'course_id', 'course_id');
    }

}
