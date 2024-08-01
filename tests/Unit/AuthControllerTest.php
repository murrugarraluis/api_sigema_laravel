<?php

namespace Tests\Unit;

use App\Http\Controllers\AuthController;
use App\Http\Requests\LoginRequest;
use App\Models\Article;
use App\Models\ArticleType;
use App\Models\DocumentType;
use App\Models\Supplier;
use App\Models\SupplierType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

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
    }

    public function test_login()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);
        $payload = [
            'email' => 'admin@jextecnologies.com',
            'password' => '123456'
        ];
        $this->seedData();
        $user->assignRole('Admin');
        $response = $this->postJson('api/v1/login', $payload);
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'email',
                    'employee',
                    'permissions' => [
                        '*' => [
                            'id',
                            'name'
                        ]
                    ]
                ],
                'token',
            ]);
    }

    public function test_login_invalid_credentials()
    {
        $this->withoutExceptionHandling();
        User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);
        $payload = [
            'email' => 'admin1@jextecnologies.com',
            'password' => '123456'
        ];
        $response = $this->postJson('api/v1/login', $payload);
        $response
            ->assertStatus(401)
            ->assertExactJson([
                'message' => 'Invalid Credentials.',
            ]);
    }

    public function test_logout()
    {
//        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);
        $this->seedData();
        $user->assignRole('Admin');
        $payload = new LoginRequest();
        $payload->merge([
            'email' => 'admin@jextecnologies.com',
            'password' => '123456'
        ]);
        $response = app(AuthController::class)->login($payload);
        $token = $response->additional["token"];

        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $token,
        ])->postJson('api/v1/logout');
        $response
            ->assertStatus(200)
            ->assertExactJson([
                'message' => 'Token removed.',
            ]);
    }

    public function test_logout_unauthenticated()
    {
//        $this->withoutExceptionHandling();
        User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);
        $payload = new LoginRequest();
        $payload->merge([
            'email' => 'admin1@jextecnologies.com',
            'password' => '123456'
        ]);
        $response = app(AuthController::class)->login($payload);
        $token = '';

        $response = $this->withHeaders([
            'Authorization' => "Bearer " . $token,
        ])->postJson('api/v1/logout');
        $response
            ->assertStatus(401)
            ->assertExactJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
