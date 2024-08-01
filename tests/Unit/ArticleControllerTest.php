<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Models\ArticleType;
use App\Models\DocumentType;
use App\Models\Machine;
use App\Models\Supplier;
use App\Models\SupplierType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    private $resource = 'articles';

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
        SupplierType::factory()->create(['name' => 'Proveedor de Articulos']);
        Supplier::factory(5)->create();
        ArticleType::factory()->create(['name' => 'Oficina']);
        Article::factory(5)->create();
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
                    'quantity',
                    'article_type' => [
                        'id',
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

        $article = Article::limit(1)->first();
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->getJson("api/v1/$this->resource/$article->id");

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'id',
                'serie_number',
                'name',
                'brand',
                'model',
                'quantity',
                'article_type' => [
                    'id',
                    'name'
                ],
                'suppliers' => [
                    '*' => [
                        'id',
                        'name',
                        'price'
                    ]
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
            ->assertExactJson(['message' => "Unable to locate the article you requested."]);
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
            'serie_number' => 'AAA',
            'name' => 'Article',
            'brand' => 'Brand',
            'model' => 'Model',
            'quantity' => 2,
            'article_type' => [
                'id' => ArticleType::limit(1)->first()->id,
            ],
            'suppliers' => [
                ['id' => Supplier::orderBy('id', 'desc')->limit(1)->first()->id, 'price' => 12.5],
                ['id' => Supplier::orderBy('id', 'asc')->limit(1)->first()->id, 'price' => 12.5],
            ],
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
                    'quantity',
                    'image',
                    'technical_sheet',
                    'article_type' => [
                        'id',
                        'name'
                    ],
                    'suppliers' => [
                        '*' => [
                            'id',
                            'name',
                            'price'
                        ]
                    ]
                ],
            ])->assertJson([
                'message' => 'Article created.',
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

        $article = Article::limit(1)->first();
        $payload = [
            'serie_number' => 'AAA',
            'name' => 'Article',
            'brand' => 'Brand',
            'model' => 'Model',
            'quantity' => 2,
            'article_type' => [
                'id' => ArticleType::limit(1)->first()->id,
            ],
            'suppliers' => [
                ['id' => Supplier::orderBy('id', 'desc')->limit(1)->first()->id, 'price' => 12.5],
                ['id' => Supplier::orderBy('id', 'asc')->limit(1)->first()->id, 'price' => 12.5],
            ],
        ];
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->putJson("api/v1/$this->resource/$article->id", $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'serie_number',
                    'name',
                    'brand',
                    'model',
                    'quantity',
                    'article_type' => [
                        'id',
                        'name'
                    ],
                    'suppliers' => [
                        '*' => [
                            'id',
                            'name',
                            'price'
                        ]
                    ]
                ],
            ])->assertJson([
                'message' => 'Article updated.',
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

        $article = Article::limit(1)->first();
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->deleteJson("api/v1/$this->resource/$article->id");

        $response->assertStatus(200)
            ->assertExactJson(['message' => 'Article removed.']);

    }
}
