<?php

namespace Database\Factories;

use App\Models\Machine;
use App\Models\MaintenanceType;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceSheetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $maintenance_type = MaintenanceType::inRandomOrder()->limit(1)->first();
        $supplier = Supplier::inRandomOrder()->limit(1)->first();
        $machine = Machine::inRandomOrder()->limit(1)->first();

        return [
            'date' => $this->faker->dateTimeBetween('-20 days',  '-1 days'),
            'responsible' => $this->faker->name()." ".$this->faker->lastName(),
            'technical' => $this->faker->name()." ".$this->faker->lastName(),
            'description' => $this->faker->text(),
            'maintenance_type_id'=>$maintenance_type,
            'supplier_id'=>$supplier,
            'machine_id'=>$machine,
        ];
    }
}
