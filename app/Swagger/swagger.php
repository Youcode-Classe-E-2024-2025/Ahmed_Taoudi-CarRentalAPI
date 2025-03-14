<?php 

namespace App\Swagger;

/**
 * @OA\Info(
 *     title="Car Rental API",
 *     version="1.0",
 *     description="This is the API documentation for the car rental service."
 * )

 * @OA\Schema(
 *     schema="Rental",
 *     type="object",
 *     required={"user_id", "car_id", "start_date", "end_date", "total_price", "status"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the rental",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="ID of the user renting the car",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="car_id",
 *         type="integer",
 *         description="ID of the rented car",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="start_date",
 *         type="string",
 *         format="date",
 *         description="Rental start date",
 *         example="2025-03-10"
 *     ),
 *     @OA\Property(
 *         property="end_date",
 *         type="string",
 *         format="date",
 *         description="Rental end date",
 *         example="2025-03-20"
 *     ),
 *     @OA\Property(
 *         property="total_price",
 *         type="number",
 *         format="float",
 *         description="Total rental price",
 *         example=250.00
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         description="Status of the rental",
 *         enum={"pending", "active", "completed", "canceled"},
 *         example="active"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the rental was created",
 *         example="2025-03-10T12:00:00Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the rental was last updated",
 *         example="2025-03-10T12:00:00Z"
 *     )
 * )
 * @OA\Schema(
 *     schema="Car",
 *     type="object",
 *     required={"make", "model", "matricul", "year", "price", "status"},
 *     @OA\Property(property="id", type="integer", description="Car ID", example=1),
 *     @OA\Property(property="make", type="string", description="Car make", example="Toyota"),
 *     @OA\Property(property="model", type="string", description="Car model", example="Corolla"),
 *     @OA\Property(property="matricul", type="string", description="Car matriculation number", example="ABC123"),
 *     @OA\Property(property="year", type="integer", description="Car year", example=2020),
 *     @OA\Property(property="price", type="number", format="float", description="Price per day", example=50.0),
 *     @OA\Property(property="status", type="string", description="Car status", enum={"available", "rented", "maintenance"}, example="available"),
 *     @OA\Property(property="image", type="string", description="Image URL", example="https://example.com/car.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the car was created", example="2025-03-10T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp when the car was last updated", example="2025-03-10T12:00:00Z")
 * )
 */

class swagger{}