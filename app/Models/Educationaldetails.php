<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Educationaldetails extends Model
{
    use HasFactory;
    public $table = 'educational_details';
    protected $fillable = ['client_id', 'qualification_id', 'date_acquired', 'grade'];
}
