<?php

namespace Tests\Unit;

use App\Models\DocumentType;
use App\Models\Machine;
use App\Models\MaintenanceSheet;
use App\Models\MaintenanceType;
use App\Models\Supplier;
use App\Models\SupplierType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MaintenanceSheetControllerTest extends TestCase
{
    use RefreshDatabase;

    private $resource = 'maintenance-sheets';

    public function seedData()
    {
        $role = Role::create(['name' => 'Admin']);

        Permission::create(['name' => 'users']);
        Permission::create(['name' => 'employees']);
        Permission::create(['name' => 'attendance-sheets']);
        Permission::create(['name' => 'suppliers']);
        Permission::create(['name' => 'articles']);
        Permission::create(['name' => 'machines']);
        Permission::create(['name' => 'maintenance-sheets']);
        Permission::create(['name' => 'working-sheets']);
        Permission::create(['name' => 'article-types']);

        $permissions = Permission::all();
        $role->syncPermissions($permissions);

        MaintenanceType::factory()->create(['name' => 'Preventivo']);
        SupplierType::factory()->create(['name' => 'Servicio']);
        DocumentType::factory()->create(['name' => 'RUC']);
        Supplier::factory()->create();
        Machine::factory()->create();

        MaintenanceSheet::factory()->create();
    }

    public
    function test_index()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);

        $this->seedData();
        $user->assignRole('Admin');


        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->getJson("api/v1/$this->resource");

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                '*' => [
                    'id',
                    'date',
                    'responsible',
                    'technical',
                    'description',
                    'maintenance_type' => [
                        'name'
                    ],
                    'supplier' => [
                        'name'
                    ],
                    'machine' => [
                        'name'
                    ],
                ]
            ]]);

    }

    public
    function test_show()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);

        $this->seedData();
        $user->assignRole('Admin');

        $maintenance_sheet = MaintenanceSheet::limit(1)->first();
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->getJson("api/v1/$this->resource/$maintenance_sheet->id");

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'id',
                'date',
                'responsible',
                'technical',
                'description',
                'maintenance_type' => [
                    'name'
                ],
                'supplier' => [
                    'name'
                ],
                'machine' => [
                    'name'
                ]
            ]]);

    }

    public
    function test_show_not_found()
    {
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->getJson("api/v1/$this->resource/1");

        $response->assertStatus(404)
            ->assertExactJson(['message' => "Unable to locate the maintenance sheet you requested."]);
    }

    public
    function test_destroy()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);

        $this->seedData();
        $user->assignRole('Admin');

        $maintenance_sheet = MaintenanceSheet::limit(1)->first();
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->deleteJson("api/v1/$this->resource/$maintenance_sheet->id");

        $response->assertStatus(200)
            ->assertExactJson(['message' => 'Maintenance Sheet removed.']);

    }

}
