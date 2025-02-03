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

        $studentMessFee = Student_mess_fee::findOrFail($data['id']);


        $order = $this->razorpay->order->create([
            'amount' => $studentMessFee->total_fee * 100, // Amount in paise
            'currency' => 'INR',
            'receipt' => 'order_' . time(),
        ]);

        Student_mess_fee::where('id', $data['id'])->update([
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

        $order = Student_mess_fee::where('razorpay_order_id', $request->razorpay_order_id)->firstOrFail();

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

    public function createAndHandlePayment(Request $request)
    {
        $data = $request->validate([
            'id' => 'required',
        ]);

        $studentMessFee = Student_mess_fee::findOrFail($data['id']);

        // Create Razorpay order
        $order = $this->razorpay->order->create([
            'amount' => $studentMessFee->total_fee * 100, // Amount in paise
            'currency' => 'INR',
            'receipt' => 'order_' . time(),
        ]);

        // Save Razorpay order ID to the database
        Student_mess_fee::where('id', $data['id'])->update([
            'razorpay_order_id' => $order->id,
        ]);

        // Prepare order response
        $response = [
            'order_id' => $order->id,
            'amount' => $studentMessFee->total_fee,
            'currency' => 'INR',
            'key' => env('RAZORPAY_KEY'),
        ];

        // Now, proceed to handle the payment verification
        if ($request->has('razorpay_payment_id') && $request->has('razorpay_order_id') && $request->has('razorpay_signature')) {
            // Verify payment signature
            $attributes = [
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_signature' => $request->razorpay_signature,
            ];

            try {
                $this->razorpay->utility->verifyPaymentSignature($attributes);

                // Update payment status in the database
                $order = Student_mess_fee::where('razorpay_order_id', $request->razorpay_order_id)->firstOrFail();
                $order->update([
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature' => $request->razorpay_signature,
                    'status' => 'paid',
                ]);

                return response()->json(['message' => 'Payment successful', 'order' => $response]);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Payment verification failed', 'message' => $e->getMessage()], 400);
            }
        }

        // If Razorpay payment data is not provided, return the order data
        return response()->json($response);
    }

}
