<?php

namespace App\Http\Controllers;

use App\Models\Student_mess_fee;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class PaymentController extends Controller
{
    //
    private $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
    }

    public function createOrder(Request $request)
    {
        $data = $request->validate([
            'id' => 'required',
        ]);

        // Lock the row for update to prevent concurrent modifications
        $studentMessFee = Student_mess_fee::where('id', $data['id'])->lockForUpdate()->firstOrFail();

        $order = $this->razorpay->order->create([
            'amount' => $studentMessFee->total_fee * 100, // Amount in paise
            'currency' => 'INR',
            'receipt' => 'order_' . time(),
        ]);

        $studentMessFee->update([
            'razorpay_order_id' => $order->id,
        ]);

        return response()->json([
            'order_id' => $order->id,
            'amount' => $studentMessFee->total_fee,
            'currency' => 'INR',
            'key' => env('RAZORPAY_KEY'),
        ]);
    }

    public function handlePayment(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required',
            'razorpay_order_id' => 'required',
            'razorpay_signature' => 'required',
        ]);

        // Lock the row to prevent concurrent updates
        $order = Student_mess_fee::where('razorpay_order_id', $request->razorpay_order_id)
            ->lockForUpdate()
            ->firstOrFail();

        $attributes = [
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_signature' => $request->razorpay_signature,
        ];

        try {
            $this->razorpay->utility->verifyPaymentSignature($attributes);

            $order->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
                'status' => 'paid',
            ]);

            return response()->json(['message' => 'Payment successful']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment verification failed'], 400);
        }
    }
}
