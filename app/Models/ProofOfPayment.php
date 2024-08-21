<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProofOfPayment extends Model
{
    use HasFactory;
    public $table = 'proof_of_payment';
    protected $fillable = ['client_id', 'file_path'];

    public function clients()
    {
        return $this->belongsTo(Clients::class, 'client_id');
    }

}
