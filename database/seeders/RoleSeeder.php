<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$role_admin = Role::create(['name' => 'Admin']);
		$role_employee = Role::create(['name' => 'Employee']);

		Permission::create(['name' => 'users']);
		Permission::create(['name' => 'employees']);
		Permission::create(['name' => 'attendance-sheets']);
		Permission::create(['name' => 'suppliers']);
		Permission::create(['name' => 'articles']);
		Permission::create(['name' => 'machines']);
		Permission::create(['name' => 'maintenance-sheets']);
		Permission::create(['name' => 'working-sheets']);
		Permission::create(['name' => 'article-types']);
		Permission::create(['name' => 'roles']);
		Permission::create(['name' => 'dashboard']);
		Permission::create(['name' => 'reports']);
		Permission::create(['name' => 'notifications']);

		$permissions = Permission::all();
		$role_admin->syncPermissions($permissions);
		$permissions = Permission::inRandomOrder()->limit(3)->get();
		$role_employee->syncPermissions($permissions);

	}
}
