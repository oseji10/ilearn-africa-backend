<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;
    public $table = 'payments';
    protected $fillable = ['client_id', 'payment_for', 'course_id', 'payment_gateway', 'payment_method', 'amount', 'transaction_reference', 'other_reference','status', 'created_by'];

    public function clients()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }
}
