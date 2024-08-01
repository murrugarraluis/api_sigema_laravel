<?php

namespace Tests\Unit;

use App\Models\Bank;
use App\Models\DocumentType;
use App\Models\Supplier;
use App\Models\SupplierType;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SupplierControllerTest extends TestCase
{
    use RefreshDatabase;

    private $resource = 'suppliers';

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

        DocumentType::factory()->create(['name' => 'RUC']);
        SupplierType::factory()->create(['name' => 'Proveedor de Servicios']);
        Supplier::factory(10)->create();
    }

    public function test_index()
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
                    'document_number',
                    'name',
                    'phone',
                    'email',
                    'address',
                    'supplier_type' => [
                        'id',
                        'name'
                    ],
                    'document_type' => [
                        'id',
                        'name'
                    ],
                ]
            ]]);

    }

    public function test_show()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);
        $this->seedData();
        $user->assignRole('Admin');
        $supplier = Supplier::limit(1)->first();
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->getJson("api/v1/$this->resource/$supplier->id");

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'id',
                'document_number',
                'name',
                'phone',
                'email',
                'address',
                'supplier_type' => [
                    'id',
                    'name'
                ],
                'document_type' => [
                    'id',
                    'name'
                ],
                'banks' => [
                    '*' => [
                        'id',
                        'name',
                        'account_number',
                        'interbank_account_number',
                    ]
                ]
            ]]);

    }

    public function test_show_not_found()
    {
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->getJson("api/v1/$this->resource/1");

        $response->assertStatus(404)
            ->assertExactJson(['message' => "Unable to locate the supplier you requested."]);
    }

    public function test_store()
    {

//        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);
        $this->seedData();
        $user->assignRole('Admin');

        $payload = [
            'document_number' => '12345678',
            'name' => 'Supplier',
            'phone' => '123456788',
            'email' => 'example@email.com',
            'address' => 'Av.Larco',
            'supplier_type' => [
                'id' => SupplierType::limit(1)->first()->id,
            ],
            'document_type' => [
                'id' => DocumentType::limit(1)->first()->id,
            ],
            'banks' => [
                [
                    'id' => Bank::factory()->create(['name' => 'BCP'])->id,
                    'account_number' => '12345678912312',
                    'interbank_account_number' => '1234566788642134',
                ]
            ]
        ];
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->postJson("api/v1/$this->resource", $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'document_number',
                    'name',
                    'phone',
                    'email',
                    'address',
                    'supplier_type' => [
                        'id',
                        'name'
                    ],
                    'document_type' => [
                        'id',
                        'name'
                    ],
                    'banks' => [
                        '*' => [
                            'id',
                            'name',
                            'account_number',
                            'interbank_account_number',
                        ]
                    ]
                ],
            ])->assertJson([
                'message' => 'Supplier created.',
                'data' => []
            ]);

    }

    public function test_update()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);
        $this->seedData();
        $user->assignRole('Admin');

        $supplier = Supplier::limit(1)->first();
        $payload = [
            'document_number' => '12345678',
            'name' => 'Supplier',
            'phone' => '123456788',
            'email' => 'example@email.com',
            'address' => 'Av.Larco',
            'supplier_type' => [
                'id' => SupplierType::limit(1)->first()->id,
            ],
            'document_type' => [
                'id' => DocumentType::limit(1)->first()->id,
            ],
            'banks' => [
                [
                    'id' => Bank::factory()->create(['name' => 'BCP'])->id,
                    'account_number' => '12345678912312',
                    'interbank_account_number' => '1234566788642134',
                ]
            ]
        ];
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->putJson("api/v1/$this->resource/$supplier->id", $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'document_number',
                    'name',
                    'phone',
                    'email',
                    'address',
                    'supplier_type' => [
                        'id',
                        'name'
                    ],
                    'document_type' => [
                        'id',
                        'name'
                    ],
                    'banks' => [
                        '*' => [
                            'id',
                            'name',
                            'account_number',
                            'interbank_account_number',
                        ]
                    ]
                ],
            ])->assertJson([
                'message' => 'Supplier updated.',
                'data' => []
            ]);

    }

    public function test_destroy()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);
        $this->seedData();
        $user->assignRole('Admin');

        $supplier = Supplier::limit(1)->first();
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->deleteJson("api/v1/$this->resource/$supplier->id");

        $response->assertStatus(200)
            ->assertExactJson(['message' => 'Supplier removed.']);

    }

}
