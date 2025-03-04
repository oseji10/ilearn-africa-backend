<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionOptions extends Model
{
    use HasFactory;
    public $table = 'cbt_question_options';
    protected $primaryKey = 'optionId';
    protected $fillable = ['questionId', 'optionName', 'optionDetail', 'isCorrect']; 

 
}
