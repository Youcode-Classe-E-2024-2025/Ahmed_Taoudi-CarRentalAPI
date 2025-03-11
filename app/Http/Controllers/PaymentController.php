<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Rental;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/payments",
     *     summary="Get all payments",
     *     tags={"Payments"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of payments",
     *         
     *     )
     * )
     */
    public function index()
    {
        $payments = Payment::all();
        return response()->json($payments);
    }

    /**
     * @OA\Post(
     *     path="/api/payments",
     *     summary="Create a new payment",
     *     tags={"Payments"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"rental_id", "amount", "payment_method", "status"},
     *             @OA\Property(property="rental_id", type="integer", example=1),
     *             @OA\Property(property="amount", type="number", format="float", example=150.75),
     *             @OA\Property(property="payment_method", type="string", example="credit_card"),
     *             @OA\Property(property="status", type="string", example="completed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payment created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'rental_id' => 'required|exists:rentals,id',
            'amount' => 'required|numeric',
            'payment_method' => 'required|in:credit_card,stripe,cash',
            'status' => 'required|in:pending,completed,failed,canceled',
        ]);

        $payment = Payment::create($validatedData);

        return response()->json($payment, 201);
    }


    /**
     * @OA\Get(
     *     path="/api/payments/{id}",
     *     summary="Get a specific payment by ID",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment details"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment not found")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        return response()->json($payment);
    }

    /**
     * @OA\Put(
     *     path="/api/payments/{id}",
     *     summary="Update payment status",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", example="completed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment updated successfully"
     *         
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment not found")
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $validatedData = $request->validate([
            'status' => 'required|in:pending,completed,failed,canceled',
        ]);

        $payment->update($validatedData);

        return response()->json($payment);
    }

     /**
     * @OA\Delete(
     *     path="/api/payments/{id}",
     *     summary="Delete a specific payment",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Payment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Payment not found")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $payment->delete();

        return response()->json(['message' => 'Payment deleted successfully']);
    }

     /**
     * @OA\Get(
     *     path="/api/rentals/{rentalId}/payments",
     *     summary="Get payments by rental ID",
     *     tags={"Payments"},
     *     @OA\Parameter(
     *         name="rentalId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of payments for the rental"
     *         
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rental not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Rental not found")
     *         )
     *     )
     * )
     */
    public function paymentsByRental($rentalId)
    {
        $rental = Rental::find($rentalId);

        if (!$rental) {
            return response()->json(['message' => 'Rental not found'], 404);
        }

        $payments = $rental->payment;
        return response()->json($payments);
    }
}
