<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    private $resource = 'roles';

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
        Permission::create(['name' => 'roles']);


        $permissions = Permission::all();
        $role->syncPermissions($permissions);
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
                    'name',
                    'permissions' => []
                ]
            ]]);

    }

    function test_show()
    {

        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);
        $this->seedData();
        $user->assignRole('Admin');

        $role = Role::limit(1)->first();
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->getJson("api/v1/$this->resource/$role->id");

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'id',
                'name',
                'permissions' => [
                    '*' => [
                        'id',
                        'name',
                    ]
                ]
            ]]);

    }

    function test_store()
    {

//        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);
        $this->seedData();
        $user->assignRole('Admin');

        $payload = [
            'name' => 'Super Admin',
            'permissions' => [
                ['id' => Permission::orderBy('id', 'desc')->limit(1)->first()->id],
                ['id' => Permission::orderBy('id', 'asc')->limit(1)->first()->id],
            ],
        ];
//        dd($payload);
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->postJson("api/v1/$this->resource", $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'permissions' => [
                        '*' => [
                            'id',
                            'name',
                        ]
                    ]
                ],
            ])->assertJson([
                'message' => 'Role created.',
                'data' => []
            ]);

    }

    function test_update()
    {

        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);
        $this->seedData();
        $user->assignRole('Admin');

        $role = Role::limit(1)->first();
        $payload = [
            'name' => 'Super Admin',
            'permissions' => [
                ['id' => Permission::orderBy('id', 'desc')->limit(1)->first()->id],
                ['id' => Permission::orderBy('id', 'asc')->limit(1)->first()->id],
            ],
        ];
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->putJson("api/v1/$this->resource/$role->id", $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'permissions' => [
                        '*' => [
                            'id',
                            'name',
                        ]
                    ]
                ],
            ])->assertJson([
                'message' => 'Role updated.',
                'data' => []
            ]);

    }

    function test_destroy()
    {

        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);
        $this->seedData();
        $user->assignRole('Admin');

        $role = Role::limit(1)->first();
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->deleteJson("api/v1/$this->resource/$role->id");

        $response->assertStatus(200)
            ->assertExactJson(['message' => 'Role removed.']);

    }
}
