<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workdetails extends Model
{
    use HasFactory, softDeletes;
    public $table = 'work_details';
    protected $fillable = ['client_id', 'start_date', 'end_date', 'organization', 'job_title'];
}
