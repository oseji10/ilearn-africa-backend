<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseModule extends Model
{
    use HasFactory;
    public $table = 'course_list_modules';
    protected $fillable = ['course_id', 'modules'];
   

   
}
