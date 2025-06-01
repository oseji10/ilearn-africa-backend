<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientExtra extends Model
{
    use HasFactory;
    public $table = 'clients_extra';
    protected $fillable = ['client_id', 'preferred_mode_of_communication', 'employment_status', 'job_title', 'name_of_organization', 'years_of_experience'];
   

   
}
