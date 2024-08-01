<?php

namespace Database\Seeders;

use App\Models\WorkingHour;
use App\Models\WorkingSheet;
use Illuminate\Database\Seeder;

class WorkingSheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WorkingSheet::factory(20)
            ->has(WorkingHour::factory()->count(3),'working_hours')
            ->create();
    }
}
