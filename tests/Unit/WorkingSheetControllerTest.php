<?php

namespace Tests\Unit;

use App\Models\Machine;
use App\Models\User;
use App\Models\WorkingHour;
use App\Models\WorkingSheet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WorkingSheetControllerTest extends TestCase
{
    use RefreshDatabase;

    private $resource = 'working-sheets';

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

        Machine::factory()->create();
        WorkingSheet::factory()->has(WorkingHour::factory()->count(3), 'working_hours')->create();
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
                    'date',
//                    'date_end',
                    'description',
                    'machine' => [
                        'name'
                    ],
                ]
            ]]);

    }

    public function test_show()
    {
//        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);

        $this->seedData();
        $user->assignRole('Admin');

        $working_sheet = WorkingSheet::limit(1)->first();
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->getJson("api/v1/$this->resource/$working_sheet->id");

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'id',
                'date',
//                'date_end',
                'description',
                'machine' => [
                    'id',
                    'name',
                    'image',
                    'status',
                    'date_last_use',
                    'total_time_used',
                    'date_last_maintenance',
                ],
                'working_hours' => [
                    '*' => [
                        'date_time_start',
                        'date_time_end'
                    ]
                ]
            ]]);

    }

    public function test_start()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);

        $this->seedData();
        $user->assignRole('Admin');

        $working_sheet = WorkingSheet::limit(1)->first();
        $payload = [
            "machine" => Machine::limit(1)->first(),
            "description" => "My description",
            "date" => date('Y-m-d H:i:s')
        ];
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->postJson("api/v1/$this->resource/start", $payload);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => [
                'id',
                'date',
//                'date_end',
                'description',
                'machine' => [
                    'id',
                    'name',
                    'image',
                    'status',
                    'date_last_use',
                    'date_last_maintenance',
                    'total_time_used',
                ],
                'working_hours' => [
                    '*' => [
                        'date_time_start',
                        'date_time_end'
                    ]
                ]
            ]])
            ->assertJson([
                'message' => 'Work started.',
                'data' => []
            ]);;

    }

    public function test_pause()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);

        $this->seedData();
        $user->assignRole('Admin');

        $working_sheet = WorkingSheet::factory()
            ->has(WorkingHour::factory()->count(1)->state(function (array $attributes, WorkingSheet $ws) {
                return ['date_time_start' => '2022-02-02 12:00:00', 'date_time_end' => null];
            }), 'working_hours')
            ->create();
        $payload = [
            "date" => date('Y-m-d H:i:s')
        ];
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->putJson("api/v1/$this->resource/$working_sheet->id/pause", $payload);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'id',
                'date',
//                'date_end',
                'description',
                'machine' => [
                    'id',
                    'name',
                    'image',
                    'status',
                    'date_last_use',
                    'date_last_maintenance',
                    'total_time_used',
                ],
                'working_hours' => [
                    '*' => [
                        'date_time_start',
                        'date_time_end'
                    ]
                ]
            ]])
            ->assertJson([
                'message' => 'Work paused.',
                'data' => []
            ]);;

    }

    public function test_restart()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);

        $this->seedData();
        $user->assignRole('Admin');

        $working_sheet = WorkingSheet::factory()
            ->has(WorkingHour::factory()->count(1)->state(function (array $attributes, WorkingSheet $ws) {
                return ['date_time_start' => '2022-02-02 12:00:00', 'date_time_end' => '2022-02-13 13:00:00'];
            }), 'working_hours')
            ->create();
        $payload = [
            "date" => date('Y-m-d H:i:s')
        ];
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->putJson("api/v1/$this->resource/$working_sheet->id/restart", $payload);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'id',
                'date',
//                'date_end',
                'description',
                'machine' => [
                    'id',
                    'name',
                    'image',
                    'status',
                    'date_last_use',
                    'date_last_maintenance',
                    'total_time_used',
                ],
                'working_hours' => [
                    '*' => [
                        'date_time_start',
                        'date_time_end'
                    ]
                ]
            ]])
            ->assertJson([
                'message' => 'Work restarted.',
                'data' => []
            ]);;

    }

    public function test_stop()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);

        $this->seedData();
        $user->assignRole('Admin');

        $working_sheet = WorkingSheet::factory()
            ->has(WorkingHour::factory()->count(1)->state(function (array $attributes, WorkingSheet $ws) {
                return ['date_time_start' => '2022-02-02 12:00:00', 'date_time_end' => '2022-02-13 13:00:00'];
            }), 'working_hours')
            ->create();
        $payload = [
            "date" => date('Y-m-d H:i:s')
        ];
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->putJson("api/v1/$this->resource/$working_sheet->id/stop", $payload);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'id',
                'date',
//                'date_end',
                'description',
                'machine' => [
                    'id',
                    'name',
                    'image',
                    'status',
                    'date_last_use',
                    'date_last_maintenance',
                    'total_time_used',
                ],
                'working_hours' => [
                    '*' => [
                        'date_time_start',
                        'date_time_end'
                    ]
                ]
            ]])
            ->assertJson([
                'message' => 'Work stopped.',
                'data' => []
            ]);;

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
            ->assertExactJson(['message' => "Unable to locate the working sheet you requested."]);
    }

    public function test_destroy()
    {
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);

        $this->seedData();
        $user->assignRole('Admin');

        $working_sheet = WorkingSheet::limit(1)->first();
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->deleteJson("api/v1/$this->resource/$working_sheet->id");

        $response->assertStatus(200)
            ->assertExactJson(['message' => 'Working Sheet removed.']);

    }

}
