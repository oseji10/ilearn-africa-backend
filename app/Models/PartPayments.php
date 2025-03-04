<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartPayments extends Model
{
    use HasFactory;
    public $table = 'part_payments';
    protected $fillable = ['client_id', 'payment_id', 'client_id', 'status'];
    
   

   
}
