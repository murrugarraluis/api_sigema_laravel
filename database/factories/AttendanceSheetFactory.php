<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceSheetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
//            'registration_number' => $this->faker->randomNumber(8),
            'date' => $this->faker->dateTimeBetween('-20 days',  '-1 days'),
//            'time_start' => '09:00:00',
//            'time_end' => '18:00:00',
            'responsible' => $this->faker->name()." ".$this->faker->lastName(),
						'turn'=>'day',
            'is_open' => false,
        ];
    }
}
