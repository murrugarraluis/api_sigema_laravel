<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WorkingHourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date_time_start' => $this->faker->dateTimeBetween('-5 hours','now'),
            'date_time_end' => $this->faker->dateTimeBetween('-3 hours','now'),
        ];
    }
}
