<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Client extends Model
{
    use HasFactory, SoftDeletes;
    public $table = 'clients';
    protected $fillable = [
        'firstname',
        'surname',
        'othernames',
        'gender',
        'marital_status',
        'date_of_birth',
        'qualification',
        'client_id',
        'status',
        'date_of_birth',
        'country',
        'nationality',
        'address',
        'qualification',
        'title',
        'created_by'
    ]; 


    public function user()
    {
        return $this->belongsTo(User::class, 'client_id', 'client_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country');
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class, 'nationality');
    }

    public function passport()
    {
        return $this->hasOne(ProfileImage::class, 'client_id', 'client_id');
    }
    

    public function educationalDetails()
    {
        return $this->hasMany(Educationaldetails::class, 'client_id', 'client_id');
    }

    public function workDetails()
    {
        return $this->hasMany(Workdetails::class, 'client_id', 'client_id');
    }

    public function payments()
    {
        return $this->hasMany(Payments::class, 'client_id', 'client_id');
    }

    public function documents()
    {
        return $this->belongsTo(Documents::class, 'client_id', 'client_id');
    }

    public function admissions()
    {
        return $this->belongsTo(Admissions::class, 'client_id', 'client_id');
    }
  
}
