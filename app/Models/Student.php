<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = [
        'roll_number',
        'name',
        'email',
        'phone_number',
        'date_of_birth',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];   

    protected $guard = 'student';
}
