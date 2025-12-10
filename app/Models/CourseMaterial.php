<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes; 

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseMaterial extends Model
{
    use HasFactory, softDeletes;
    public $table = 'course_material';
    protected $fillable = ['course_id', 'material_link', 'material_type', 'material_name'];

    public function courses()
    {
        return $this->belongsTo(CourseList::class, 'course_id', 'course_id');
    }

}
