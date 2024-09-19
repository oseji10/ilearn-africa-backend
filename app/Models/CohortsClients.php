<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CohortsClients extends Model
{
    use HasFactory;
    public $table = 'cohorts_clients';
    protected $fillable = ['cohort_id', 'client_id'];
    
    public function cohort_clients()
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }
}
