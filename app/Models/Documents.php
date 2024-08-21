<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documents extends Model
{
    use HasFactory;
    public $table = 'documents';
    protected $fillable = ['client_id', 'file_path'];

    public function clients()
    {
        return $this->belongsTo(Clients::class, 'client_id');
    }

}
