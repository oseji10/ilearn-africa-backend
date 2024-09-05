<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grades extends Model
{
    use HasFactory, softDeletes;
    public $table = 'grades';
    protected $fillable = ['grade'];

  

}
