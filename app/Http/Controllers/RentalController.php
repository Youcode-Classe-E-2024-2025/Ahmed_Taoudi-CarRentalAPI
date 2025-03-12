<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class RentalController extends Controller
{
    /**
     * Display a listing of the rentals.
     * 
     * @OA\Get(
     *     path="/api/rentals",
     *     summary="Get all rentals",
     *     tags={"Rentals"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of rentals",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Rental")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $rentals = Rental::all();
        return response()->json($rentals);
    }
    /**
     * Store a newly created rental.
     * 
     * @OA\Post(
     *     path="/api/rentals",
     *     summary="Create a new rental",
     *     tags={"Rentals"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"user_id", "car_id", "start_date", "end_date", "total_price", "status"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="car_id", type="integer", example=1),
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-03-10"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-03-20"),
     *             @OA\Property(property="total_price", type="number", format="float", example=250.00),
     *             @OA\Property(property="status", type="string", enum={"pending", "active", "completed", "canceled"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rental created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Rental")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Validation failed")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'car_id' => 'required|exists:cars,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'total_price' => 'required|numeric',
            'status' => 'required|in:pending,active,completed,canceled',
        ]);

        $rental = Rental::create($validatedData);
        $car = Car::findOrFail($validatedData['car_id']);
        $amount = $validatedData['total_price'];
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency'     => 'usd',
                        'product_data' => [
                            'name'        => 'Car Rental',
                            'description' => 'Rental for ' . $car->make . ' ' . $car->model,
                        ],
                        'unit_amount' => $amount * 100, // Stripe uses cents
                    ],
                    'quantity' => 1,
                ]],
                'mode'         => 'payment',
                'success_url'  => route('checkout.success', ['session_id' => '{CHECKOUT_SESSION_ID}']),
                'cancel_url'   => route('checkout.cancel'),
                'metadata'     => [
                    'rental_id' => $rental->id,
                ],
            ]);

            Payment::create([
                'rental_id' => $rental->id,
                'amount'    => $amount,
                'payment_method'=> 'stripe',
                'status'=> 'pending'
            ]);
            // return redirect($session->url);
            return response()->json([
                'checkout_url' => $session->url,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        // return response()->json($rental, 201);
    }

    /**
     * Display the specified rental.
     * 
     * @OA\Get(
     *     path="/api/rentals/{id}",
     *     summary="Get a rental by ID",
     *     tags={"Rentals"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Rental ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rental details",
     *         @OA\JsonContent(ref="#/components/schemas/Rental")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rental not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Rental not found")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        $rental = Rental::find($id);

        if (!$rental) {
            return response()->json(['message' => 'Rental not found'], 404);
        }

        return response()->json($rental);
    }

    /**
     * Update the specified rental.
     * 
     * @OA\Put(
     *     path="/api/rentals/{id}",
     *     summary="Update an existing rental",
     *     tags={"Rentals"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Rental ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="car_id", type="integer", example=1),
     *             @OA\Property(property="start_date", type="string", format="date", example="2025-03-10"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2025-03-20"),
     *             @OA\Property(property="total_price", type="number", format="float", example=250.00),
     *             @OA\Property(property="status", type="string", enum={"pending", "active", "completed", "canceled"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rental updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Rental")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rental not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Rental not found")
     *         )
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $rental = Rental::find($id);

        if (!$rental) {
            return response()->json(['message' => 'Rental not found'], 404);
        }

        $validatedData = $request->validate([
            'user_id' => 'exists:users,id',
            'car_id' => 'exists:cars,id',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'total_price' => 'numeric',
            'status' => 'in:pending,active,completed,canceled',
        ]);

        $rental->update($validatedData);

        return response()->json($rental);
    }
    /**
     * Remove the specified rental.
     * 
     * @OA\Delete(
     *     path="/api/rentals/{id}",
     *     summary="Delete a rental",
     *     tags={"Rentals"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Rental ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rental deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Rental deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Rental not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Rental not found")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $rental = Rental::find($id);

        if (!$rental) {
            return response()->json(['message' => 'Rental not found'], 404);
        }

        $rental->delete();

        return response()->json(['message' => 'Rental deleted successfully']);
    }

    /**
     * Get rentals by a specific user.
     * 
     * @OA\Get(
     *     path="/api/users/{userId}/rentals/",
     *     summary="Get rentals for a specific user",
     *     tags={"Rentals"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of rentals",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Rental")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function rentalsByUser($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $rentals = $user->rentals;
        return response()->json($rentals);
    }
    /**
     * Get rentals for a specific car.
     * 
     * @OA\Get(
     *     path="/api/cars/{carId}/rentals",
     *     summary="Get rentals for a specific car",
     *     tags={"Rentals"},
     *     @OA\Parameter(
     *         name="carId",
     *         in="path",
     *         description="Car ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of rentals",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Rental")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Car not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Car not found")
     *         )
     *     )
     * )
     */
    public function rentalsByCar($carId)
    {
        $car = Car::find($carId);

        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        $rentals = $car->rentals;
        return response()->json($rentals);
    }
}
