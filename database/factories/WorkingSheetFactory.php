<?php

namespace Database\Factories;

use App\Models\Machine;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkingSheetFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{
		$machine = Machine::inRandomOrder()->limit(1)->first();
		return [
			'date' => $this->faker->dateTimeBetween('-20 days', '-1 days'),
//            'date_end' => $this->faker->dateTimeBetween('-20 days', '-1 days'),
			'description' => $this->faker->text(),
			'machine_id' => $machine,
			'is_open' => false
		];
	}
}
