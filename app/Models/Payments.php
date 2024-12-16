<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory, softDeletes;
    public $table = 'payments';
    protected $fillable = ['client_id', 'payment_for', 'course_id', 'payment_gateway', 'payment_method', 'amount', 'transaction_reference', 'other_reference','status', 'created_by', 'admission_number', 'cohort_id', 'part_payment'];

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


    public function admissions()
    {
        return $this->belongsTo(Admissions::class, 'client_id', 'client_id');
    }

    public function part_payments()
    {
        return $this->hasMany(PartPayments::class, 'payment_id', 'id');
    }

    public function cohorts()
    {
        return $this->belongsTo(Cohorts::class, 'cohort_id', 'cohort_id');
    }

}
