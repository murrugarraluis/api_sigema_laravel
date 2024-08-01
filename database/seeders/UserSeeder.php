<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $notifications = Notification::all();
        $user = User::factory()->hasAttached($notifications)->create([
            'email' => 'admin@jextechnologies.com',
            'password' => bcrypt('123456')
        ]);
        $user->assignRole('Admin');
        Employee::factory()->create([
            'user_id' => $user
        ]);

        $user = User::factory()->hasAttached($notifications)->create([
            'email' => 'employee@jextechnologies.com',
            'password' => bcrypt('123456')
        ]);
        $user->assignRole('Employee');
        Employee::factory()->create([
            'user_id' => $user
        ]);
    }
}
