<?php

namespace Database\Seeders;

use App\Models\MaintenanceType;
use Illuminate\Database\Seeder;

class MaintenanceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MaintenanceType::factory()->create(['name'=>'Preventive']);
        MaintenanceType::factory()->create(['name'=>'Corrective']);
        MaintenanceType::factory()->create(['name'=>'Predictive']);
    }
}
