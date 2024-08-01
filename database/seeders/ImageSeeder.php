<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Image;
use App\Models\Machine;
use App\Models\User;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $employee = Employee::limit(1)->first();
        $machine = Machine::limit(1)->first();
        Image::factory()->create([
            'imageable_type' => Employee::class,
            'imageable_id' => $employee,
        ]);
        Image::factory()->create([
            'imageable_type' => Machine::class,
            'imageable_id' => $machine,
        ]);
    }
}
