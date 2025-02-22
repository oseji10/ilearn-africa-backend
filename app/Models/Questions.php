<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questions extends Model
{
    use HasFactory;
    public $table = 'cbt_questions';
    protected $primaryKey = 'questionId';
    protected $fillable = ['question', 'questionCategoryId', 'score']; 

    public function options()
    {
        return $this->hasMany(QuestionOptions::class, 'questionId', 'questionId');
    }

    public function questions()
    {
        return $this->belongsTo(ExamQuestions::class, 'questionId', 'questionId');
    }
}
