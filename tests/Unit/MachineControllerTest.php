<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Models\ArticleType;
use App\Models\Machine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MachineControllerTest extends TestCase
{
    use RefreshDatabase;

    private $resource = 'machines';

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

        ArticleType::factory()->create(['name' => 'Repuesto']);
        Article::factory(2)->create();
        Machine::factory(5)->create();
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
                    'serie_number',
                    'name',
                    'brand',
                    'model',
                    'image',
                    'maximum_working_time',
                    'status',
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

        $machine = Machine::limit(1)->first();
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->getJson("api/v1/$this->resource/$machine->id");
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'id',
                'serie_number',
                'name',
                'brand',
                'model',
                'image',
                'maximum_working_time',
                'articles' => [
                    '*' => [
                        'id',
                        'name',
                    ]
                ],
                'status',
                'date_last_use',
                'total_time_used',
                'date_last_maintenance',
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
            ->assertExactJson(['message' => "Unable to locate the machine you requested."]);
    }

    public
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
            'serie_number' => '123456789',
            'name' => 'Machine',
            'brand' => 'brand',
            'model' => 'model',
            'image' => 'www.image.com',
            'maximum_working_time' => 300,
						'maximum_working_time_per_day' => 10,
            'articles' => [
                [
                    'id' => Article::limit(1)->first()->id,
                ]
            ],
            'status' => '',
        ];
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->postJson("api/v1/$this->resource", $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'serie_number',
                    'name',
                    'brand',
                    'model',
                    'image',
                    'technical_sheet',
                    'maximum_working_time',
                    'articles' => [
                        '*' => [
                            'id',
                            'name',
                        ]
                    ],
                    'status',
                ]
            ])->assertJson([
                'message' => 'Machine created.',
                'data' => []
            ]);

    }

    public
    function test_update()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);
        $this->seedData();
        $user->assignRole('Admin');

        $machine = Machine::limit(1)->first();
        $payload = [
            'serie_number' => '123456789',
            'name' => 'Machine',
            'brand' => 'brand',
            'model' => 'model',
            'image' => '',
            'maximum_working_time' => 300,
						'maximum_working_time_per_day' => 10,
            'articles' => [
                [
                    'id' => Article::limit(1)->first()->id,
                ]
            ],
            'status' => '',
        ];
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->putJson("api/v1/$this->resource/$machine->id", $payload);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'serie_number',
                    'name',
                    'brand',
                    'model',
                    'image',
                    'maximum_working_time',
                    'articles' => [
                        '*' => [
                            'id',
                            'name',
                        ]
                    ],
                    'status',
                ]
            ])->assertJson([
                'message' => 'Machine updated.',
                'data' => []
            ]);

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

        $machine = Machine::limit(1)->first();
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->deleteJson("api/v1/$this->resource/$machine->id");
        $response->assertStatus(200)
            ->assertExactJson(['message' => 'Machine removed.']);

    }

}
