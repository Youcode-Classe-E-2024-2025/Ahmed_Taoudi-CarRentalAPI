<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/cars",
     *     summary="Get all cars",
     *     tags={"Cars"},
     *     @OA\Response(
     *         response=200,
     *         description="List of cars",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Car")
     *         )
     *     )
     * )
     */
    public function index()
    {
        // i need pagination 
        // dont use Car::paginate
        $cars = Car::all();
        return response()->json($cars);
    }
    /**
     * @OA\Get(
     *     path="/api/cars/page/{page}",
     *     summary="Paginate cars",
     *     description="Fetch a paginated list of cars.",
     *     operationId="getPaginatedCars",
     *     tags={"Cars"},
     *     @OA\Parameter(
     *         name="page",
     *         in="path",
     *         description="Page number",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully fetched paginated cars.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     ref="#/components/schemas/Car"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="current_page",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="total_pages",
     *                 type="integer",
     *                 example=10
     *             ),
     *             @OA\Property(
     *                 property="total_items",
     *                 type="integer",
     *                 example=30
     *             ),
     *             @OA\Property(
     *                 property="per_page",
     *                 type="integer",
     *                 example=3
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid page number.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Invalid page number"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Page not found.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="No cars found on this page"
     *             )
     *         )
     *     )
     * )
     */
    public function paginate($page)
    {

        $perPage = 3;
        $offset = ($page - 1) * $perPage;
        $cars = Car::skip($offset)->take($perPage)->get();
        $totalCars = Car::count();
        $totalPages = ceil($totalCars / $perPage);
        return response()->json([
            'data' => $cars,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_items' => $totalCars,
            'per_page' => $perPage,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/cars",
     *     summary="Create a new car",
     *     tags={"Cars"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"make", "model", "matricul", "year", "price", "status"},
     *             @OA\Property(property="make", type="string", description="Car make", example="Toyota"),
     *             @OA\Property(property="model", type="string", description="Car model", example="Corolla"),
     *             @OA\Property(property="matricul", type="string", description="Car matriculation number", example="ABC123"),
     *             @OA\Property(property="year", type="integer", description="Car manufacturing year", example=2020),
     *             @OA\Property(property="price", type="number", format="float", description="Price per day", example=50.0),
     *             @OA\Property(property="status", type="string", description="Car status", enum={"available", "rented", "maintenance"}, example="available"),
     *             @OA\Property(property="image", type="string", description="Car image URL", example="https://example.com/car.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Car created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Car")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'matricul' => 'required|string|max:255',
            'year' => 'required|integer|digits:4',
            'price' => 'required|numeric',
            'status' => 'required|in:available,rented,maintenance',
            'image' => 'nullable|string',
        ]);

        $car = Car::create($request->all());

        return response()->json($car, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/cars/{id}",
     *     summary="Get a car by ID",
     *     tags={"Cars"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the car to fetch",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Car details",
     *         @OA\JsonContent(ref="#/components/schemas/Car")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Car not found"
     *     )
     * )
     */
    public function show(string $id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        return response()->json($car);
    }

    /**
     * @OA\Put(
     *     path="/api/cars/{id}",
     *     summary="Update a car by ID",
     *     tags={"Cars"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the car to update",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="make", type="string", example="Toyota"),
     *             @OA\Property(property="model", type="string", example="Corolla"),
     *             @OA\Property(property="matricul", type="string", example="ABC123"),
     *             @OA\Property(property="year", type="integer", example=2020),
     *             @OA\Property(property="price", type="number", format="float", example=50.0),
     *             @OA\Property(property="status", type="string", enum={"available", "rented", "maintenance"}, example="available"),
     *             @OA\Property(property="image", type="string", example="https://example.com/car.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Car updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Car")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Car not found"
     *     )
     * )
     */
    public function update(Request $request, string $id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        $request->validate([
            'make' => 'string|max:255',
            'model' => 'string|max:255',
            'year' => 'integer|digits:4',
            'price' => 'numeric',
            'status' => 'in:available,rented,maintenance',
            'image' => 'nullable|url',
        ]);

        $car->update($request->all());

        return response()->json($car);
    }

    /**
     * @OA\Delete(
     *     path="/api/cars/{id}",
     *     summary="Delete a car by ID",
     *     tags={"Cars"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the car to delete",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Car deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Car not found"
     *     )
     * )
     */
    public function destroy(string $id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        $car->delete();

        return response()->json(['message' => 'Car deleted successfully']);
    }
}
