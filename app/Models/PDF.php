<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PDF extends Model
{
    use HasFactory;

    public function workDetails()
    {
        return $this->hasMany(Workdetails::class, 'client_id', 'client_id');
    }
}
