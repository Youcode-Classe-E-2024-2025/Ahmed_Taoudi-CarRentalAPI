<?php

namespace Database\Factories;

use App\Models\Car;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\=Car>
 */
class CarFactory extends Factory
{
    // The model that the factory is for
    protected $model = Car::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'make' => $this->faker->company,
            'model' => $this->faker->word,
            'matricul' => $this->faker->word,
            'year' => $this->faker->year,
            'price_per_day' => $this->faker->randomFloat(2, 10, 100),
            'status' => $this->faker->randomElement(['available', 'rented', 'maintenance']),
            'color' => $this->faker->safeColorName,
        ];
    }
}
