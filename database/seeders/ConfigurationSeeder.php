<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Configuration::factory()->create(['name' => 'start_time_turn_day', "value" => '07:00:00']);
		Configuration::factory()->create(['name' => 'end_time_turn_day', "value" => '19:00:00']);

		Configuration::factory()->create(['name' => 'start_time_turn_night', "value" => '19:00:00']);
		Configuration::factory()->create(['name' => 'end_time_turn_night', "value" => '07:00:00']);

	}
}
