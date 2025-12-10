<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProofOfPayment extends Model
{
    use HasFactory, softDeletes;
    public $table = 'proof_of_payment';
    protected $fillable = ['client_id', 'file_path', 'other_reference', 'transaction_reference'];

    public function clients()
    {
        return $this->belongsTo(Clients::class, 'client_id');
    }

}
