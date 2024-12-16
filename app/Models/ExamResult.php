<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    use HasFactory;
    public $table = 'cbt_exams_results';
    protected $primaryKey = 'resultId';
    protected $fillable = ['examId', 'questionId', 'optionSelected', 'score', 'clientId']; 

    public function exams()
    {
        return $this->belongsTo(CBT::class, 'examId', 'examId');
    }

    public function clients()
    {
        return $this->belongsTo(Client::class, 'clientId', 'client_id');
    }
}
