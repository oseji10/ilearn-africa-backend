<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResultMaster extends Model
{
    use HasFactory;

    protected $table = 'cbt_master_results';
    protected $primaryKey = 'masterId';

    protected $fillable = [
        'examId',
        'total_score',
        'clientId',
    ];

    public function results()
    {
        return $this->hasMany(ExamResult::class, 'masterId', 'masterId');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'clientId', 'client_id');
    }

    public function exam()
    {
        return $this->belongsTo(CBT::class, 'examId', 'examId');
    }

    public function exam_questions()
    {
        return $this->hasMany(ExamQuestions::class, 'examId', 'examId');
    }

    public function examRetake()
    {
        return $this->hasOne(ExamRetake::class, 'client_id', 'clientId');
    }
}