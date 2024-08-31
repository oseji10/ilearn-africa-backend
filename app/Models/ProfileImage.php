<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileImage extends Model
{
    
    use HasFactory;
    public $table = 'profile_image';
    protected $fillable = ['client_id', 'image_url'];

    public function clients()
    {
        return $this->belongsTo(Clients::class, 'client_id', 'client_id');
    }
}
