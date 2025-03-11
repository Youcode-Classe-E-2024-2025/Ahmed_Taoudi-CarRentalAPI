<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\=Rental>
 */
class RentalFactory extends Factory
{
    // The model that the factory is for
    protected $model = Rental::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'car_id' => Car::inRandomOrder()->first()->id,
            'rental_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'return_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => $this->faker->randomElement(['pending', 'completed', 'canceled']),
        ];
    }
}
