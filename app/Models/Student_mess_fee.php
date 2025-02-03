<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student_mess_fee extends Model
{
    use HasFactory;

    protected $primaryKey = 'mess_fee_id';

    protected $fillabele = [
        'student_roll_number',
        'status',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'payment_date'
    ];
}
