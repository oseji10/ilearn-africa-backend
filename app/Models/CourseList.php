<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseList extends Model
{
    use HasFactory;
    public $table = 'course_list';
    protected $fillable = ['course_id', 'course_name', 'cost', 'course_image', 'status', 'created_by', 'center_id'];
}
