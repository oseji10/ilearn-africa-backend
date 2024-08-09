<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;
    public $table = 'clients';
    protected $fillable = [
        'firstname',
        'surname',
        'othernames',
        'gender',
        'marital_status',
        'date_of_birth',
        'qualification',
        'client_id',
        'status',
        'date_of_birth',
        'country',
        'nationality',
        'address',
        'qualification',
        'title',
    ]; 
}
