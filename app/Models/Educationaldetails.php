<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Educationaldetails extends Model
{
    use HasFactory, softDeletes;
    public $table = 'educational_details';
    protected $fillable = ['client_id', 'qualification_id', 'course_studied', 'date_acquired', 'grade'];

    public function grade()
    {
        return $this->belongsTo(Grades::class, 'grade');
    }

    public function qualification()
    {
        return $this->belongsTo(Qualifications::class, 'qualification_id');
    }
}
