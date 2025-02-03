<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mess_fee extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $fillable = [
        'month',
        'year',
        'fee_per_day',
        'days_in_month',
        'total_fee'
    ];
}
