<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Notification;
use App\Models\Position;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Employee::factory(50)->create();
        Employee::factory()->create([
            'user_id' => null
        ]);
        Employee::factory()->create([
            'user_id' => null
        ]);
    }
}
