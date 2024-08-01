<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
		$this->call(NotificationSeeder::class);
		$this->call(PositionSeeder::class);
		$this->call(DocumentTypeSeeder::class);
		$this->call(RoleSeeder::class);
		$this->call(UserSeeder::class);
		$this->call(EmployeeSeeder::class);
		$this->call(AttendanceSheetSeeder::class);
		$this->call(BankSeeder::class);
		$this->call(SupplierTypeSeeder::class);
		$this->call(SupplierSeeder::class);
		$this->call(ArticleTypeSeeder::class);
		$this->call(ArticleSeeder::class);
		$this->call(MachineSeeder::class);
		$this->call(WorkingSheetSeeder::class);
		$this->call(MaintenanceTypeSeeder::class);
		$this->call(MaintenanceSheetSeeder::class);
		$this->call(ConfigurationSeeder::class);
		$this->call(ImageSeeder::class);
    }
}
