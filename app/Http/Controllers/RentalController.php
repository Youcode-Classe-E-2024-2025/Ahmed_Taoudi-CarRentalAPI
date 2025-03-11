<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rentals = Rental::all(); 
        return response()->json($rentals);
    }

    /**
     * Store a newly created resource in storage.
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

        return response()->json($rental, 201);  
    }

    /**
     * Display the specified resource.
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
