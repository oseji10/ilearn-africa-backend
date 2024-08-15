<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admissions extends Model
{
    use HasFactory;
    public $table = 'admissions';
    protected $fillable = [
        'admission_number',
        'client_id',
        'status',
        'admitted_by'
    ];


    public function clients()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    public function courses()
    {
        return $this->belongsTo(CourseList::class, 'client_id', 'client_id');
    }

    public function payments()
    {
        return $this->belongsTo(Payments::class, 'admission_number', 'admission_number');
    }
}
