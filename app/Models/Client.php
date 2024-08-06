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
        'lastname',
        'othernames',
        'gender',
        'marital_status',
        'date_of_birth',
        'state_of_origin',
        'state_of_residence',
        'qualification',
        'client_id',
    ]; 
}
