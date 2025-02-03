<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mess_fee extends Model
{
    use HasFactory;

    protected $table = 'mess_fees';

    protected $primaryKey = 'id';

    protected $fillable = [
        'month',
        'year',
        'fee_per_day',
        'fine_per_day',
        'days_in_month',
        'due_date',
        'total_fee'
    ];
}
