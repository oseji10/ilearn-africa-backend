<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;
    public $table = 'payments';
    protected $fillable = ['client_id', 'payment_for', 'course_id', 'payment_gateway', 'payment_method', 'amount', 'transaction_reference', 'other_reference','status', 'created_by', 'admission_number'];

    public function clients()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    public function courses()
    {
        return $this->belongsTo(CourseList::class, 'course_id', 'course_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'client_id', 'client_id');
    }

    public function proof()
    {
        return $this->belongsTo(ProofOfPayment::class, 'client_id', 'client_id');
    }
}
