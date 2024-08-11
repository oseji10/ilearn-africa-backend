<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admissions extends Model
{
    use HasFactory;
    public $table = 'clients';
    protected $fillable = [
        'admission_number',
        'client_id',
        'status',
        'admitted_by'
    ];
}
