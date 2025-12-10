<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Centers extends Model
{
    use HasFactory;
    public $table = 'centers';
    protected $fillable = ['center_id', 'center_name', 'status', 'created_by'];
}
