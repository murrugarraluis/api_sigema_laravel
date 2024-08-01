<?php

namespace Tests\Unit;

use App\Models\AttendanceSheet;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Database\Seeders\ConfigurationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttendanceSheetControllerTest extends TestCase
{
    use RefreshDatabase;

    private $resource = 'attendance-sheets';

    public function seedData()
    {
        $role = Role::create(['name' => 'Admin']);

				$this->seed(ConfigurationSeeder::class);

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
        Position::factory()->create(['name' => 'System Engineer']);
        DocumentType::factory()->create(['name' => 'DNI']);
        $employees = Employee::factory(2)->create();
        AttendanceSheet::factory(5)
            ->hasAttached($employees, [
                "check_in" => '10:00:00',
                "check_out" => '15:00:00',
                "attendance" => false,
            ])
            ->create();
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
        Employee::factory()->create([
            'user_id' => $user
        ]);

//        $attendance_sheet = AttendanceSheet::limit(1)->first();
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->getJson("api/v1/$this->resource");

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                '*' => [
                    'id',
                    'date',
                    'responsible',
                    'is_open',
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
        Employee::factory()->create([
            'user_id' => $user
        ]);

        $attendance_sheet = AttendanceSheet::limit(1)->first();
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->getJson("api/v1/$this->resource/$attendance_sheet->id");

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'id',
                'date',
                'responsible',
                'employees' => [
                    '*' => [
                        'id',
                        'check_in',
                        'check_out',
                        'document_number',
                        'name',
//                        'attendance_number',
//                        'absences_number',
                        'attendance'
                    ]
                ],
                'is_open',
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
            ->assertExactJson(['message' => "Unable to locate the attendance sheet you requested."]);
    }

    function test_store()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);
        $this->seedData();
        $user->assignRole('Admin');
        $employee = Employee::factory()->create([
            'user_id' => $user
        ]);

				$payload = [
					'employees'=> [
						$employee
					],
					'turn' => 'day'
				];

        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->postJson("api/v1/$this->resource",$payload);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => [
                'id',
                'date',
                'responsible',
                'employees' => [
                    '*' => [
                        'id',
                        'document_number',
                        'name',
                        'lastname',
                        'check_in',
                        'check_out',
                        'attendance',
                        'status_working'
                    ]
                ],
                'is_open',
            ]]);

    }

    function test_update_employees()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);
        $this->seedData();
        $user->assignRole('Admin');
        Employee::factory()->create([
            'user_id' => $user
        ]);
        $employees = Employee::all();
        $attendance_sheet = AttendanceSheet::factory(5)
            ->hasAttached($employees, [
                "check_in" => '10:00:00',
                "check_out" => '15:00:00',
                "attendance" => false,
            ])
            ->create([
                'date' => date('Y-m-d'),
                'responsible' => '',
                'is_open' => true,
            ]);
        $payload = [
            "employees" => [
                [
                    'id' => Employee::inRandomOrder()->limit(1)->first()->id,
                    'check_in' => '07:04:30',
										'attendance' => false
                ],
                [
                    'id' => Employee::inRandomOrder()->limit(1)->first()->id,
                    'check_in' => '07:07:10',
									  'attendance' => false
								]
            ]
        ];
//        dd($attendance_sheet->first()->id);
        $attendance_sheet_id = $attendance_sheet->first()->id;
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->putJson("api/v1/$this->resource/$attendance_sheet_id", $payload);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'id',
                'date',
                'responsible',
                'employees' => [
                    '*' => [
                        'id',
                        'document_number',
                        'name',
                        'lastname',
                        'check_in',
                        'check_out',
                        'attendance',
                        'status_working'
                    ]
                ],
                'is_open',
            ]])
            ->assertJson([
                'message' => 'Attendance Sheet updated.',
                'data' => []
            ]);;

    }

//    function test_update_employees_sheet_without_range_date()
//    {
////        $this->withoutExceptionHandling();
//        $user = User::factory()->create([
//            'email' => 'admin@jextecnologies.com',
//            'password' => bcrypt('123456')
//        ]);
//        $this->seedData();
//        $user->assignRole('Admin');
//        Employee::factory()->create([
//            'user_id' => $user
//        ]);
//        $employees = Employee::all();
//        $attendance_sheet = AttendanceSheet::factory(5)
//            ->hasAttached($employees, [
//                "check_in" => '10:00:00',
//                "check_out" => '15:00:00',
//                "attendance" => false,
//            ])
//            ->create([
//                'date' => '2022-05-05',
//                'responsible' => '',
//                'is_open' => true,
//            ]);
//        $payload = [
//            "employees" => [
//                [
//                    'id' => Employee::inRandomOrder()->limit(1)->first()->id,
//                    'check_in' => '07:04:30',
//										'attendance' => false
//                ],
//                [
//                    'id' => Employee::inRandomOrder()->limit(1)->first()->id,
//                    'check_in' => '07:07:10',
//										'attendance' => false
//
//                ]
//            ]
//        ];
//        $attendance_sheet_id = $attendance_sheet->first()->id;
//        $response = $this->actingAs($user)->withSession(['banned' => false])
//            ->putJson("api/v1/$this->resource/$attendance_sheet_id", $payload);
//
//        $response->assertStatus(400)
//            ->assertExactJson(['message' => 'cannot update a past attendance sheet.']);
//
//    }

//    function test_update_employees_sheet_closed()
//    {
//        $this->withoutExceptionHandling();
//        $user = User::factory()->create([
//            'email' => 'admin@jextecnologies.com',
//            'password' => bcrypt('123456')
//        ]);
//        $this->seedData();
//        $user->assignRole('Admin');
//        Employee::factory()->create([
//            'user_id' => $user
//        ]);
//        $employees = Employee::all();
//        $attendance_sheet = AttendanceSheet::factory(5)
//            ->hasAttached($employees, [
//                "check_in" => '10:00:00',
//                "check_out" => '15:00:00',
//                "attendance" => false,
//            ])
//            ->create([
//                'date' => date('Y-m-d'),
//                'responsible' => '',
//                'is_open' => false,
//            ]);
//        $payload = [
//            "employees" => [
//                [
//                    'id' => Employee::inRandomOrder()->limit(1)->first()->id,
//                    'check_in' => '07:04:30',
//										'attendance' => false
//                ],
//                [
//                    'id' => Employee::inRandomOrder()->limit(1)->first()->id,
//                    'check_in' => '07:07:10',
//										'attendance' => false
//                ]
//            ]
//        ];
//        $attendance_sheet_id = $attendance_sheet->first()->id;
//
//        $response = $this->actingAs($user)->withSession(['banned' => false])
//            ->putJson("api/v1/$this->resource/$attendance_sheet_id", $payload);
//
//        $response->assertStatus(400)
//            ->assertExactJson(['message' => 'cannot update a closed attendance sheet.']);
//
//    }

    function test_update_employees_status()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);
        $this->seedData();
        $user->assignRole('Admin');
        Employee::factory()->create([
            'user_id' => $user
        ]);
        $employees = Employee::all();
        $attendance_sheet = AttendanceSheet::factory(5)
            ->hasAttached($employees, [
                "check_in" => '10:00:00',
                "check_out" => '15:00:00',
                "attendance" => false,
            ])
            ->create([
                'date' => date('Y-m-d'),
                'responsible' => '',
                'is_open' => true,
            ]);
        $payload = [
            "is_open" => false
        ];
        $attendance_sheet_id = $attendance_sheet->first()->id;

        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->putJson("api/v1/$this->resource/$attendance_sheet_id", $payload);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'id',
                'date',
                'responsible',
                'employees' => [
                    '*' => [
                        'id',
                        'document_number',
                        'name',
                        'lastname',
                        'check_in',
                        'check_out',
                        'attendance',
                        'status_working'
                    ]
                ],
                'is_open',
            ]])
            ->assertJson([
                'message' => 'Attendance Sheet updated.',
                'data' => []
            ]);;

    }

}
