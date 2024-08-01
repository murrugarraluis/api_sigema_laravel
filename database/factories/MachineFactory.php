<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MachineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'serie_number' => "Serie - " . $this->faker->randomLetter() . $this->faker->randomNumber(5),
            'name' => "Machine " . $this->faker->randomLetter() . $this->faker->randomNumber(3),
            'brand' => "Brand " . $this->faker->randomLetter() . $this->faker->randomNumber(3),
            'model' => $this->faker->randomLetter() . "-" . $this->faker->randomNumber(3),
            'maximum_working_time' => $this->faker->numberBetween($min = 300, $max = 370),
            'maximum_working_time_per_day' => $this->faker->numberBetween($min = 10, $max = 15),

        ];
    }
}
