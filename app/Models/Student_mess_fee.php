<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student_mess_fee extends Model
{
    use HasFactory;

    protected $table = 'student_fees';


    protected $fillable = [
        'mess_fee_id',
        'student_roll_number',
        'status',
        'total_fee',

        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',

        'payment_date',
    ];
}
