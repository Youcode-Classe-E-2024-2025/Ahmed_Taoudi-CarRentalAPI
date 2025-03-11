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
        // Get a random car and user
        $car = Car::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();

        $start_date = $this->faker->dateTimeBetween('-1 month', 'now');
        $end_date = $this->faker->dateTimeBetween('now', '+1 month');

        $interval = $start_date->diff($end_date); 
        $total_price = $car->price * $interval->days; 

        return [
            'user_id' => $user->id, 
            'car_id' => $car->id,  
            'start_date' => $start_date,  
            'end_date' => $end_date, 
            'total_price' =>$total_price, 
            'status' => $this->faker->randomElement(['pending', 'active', 'completed', 'canceled']), 
        ];
    }
}
