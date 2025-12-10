<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartPayments extends Model
{
    use HasFactory;
    public $table = 'part_payments';
    protected $fillable = ['client_id', 'payment_id', 'client_id', 'status'];
    
   
    public function clients()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }
   
    public function proof()
    {
        return $this->belongsTo(ProofOfPayment::class, 'client_id', 'client_id');
    }

    public function payment()
    {
        return $this->hasMany(Payments::class, 'id', 'payment_id');
    }

    public function courses()
    {
        return $this->belongsTo(CourseList::class, 'course_id', 'course_id');
    }
}
