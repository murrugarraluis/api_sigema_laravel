<?php

namespace Tests\Unit;

use App\Models\Supplier;
use App\Models\SupplierType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierTypeControllerTest extends TestCase
{
    use RefreshDatabase;
    private $resource = 'supplier-types';
    public function seedData()
    {
       SupplierType::factory()->create(['name'=>'Supplier Type']);

    }
    public function test_index()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'admin@jextecnologies.com',
            'password' => bcrypt('123456')
        ]);
        $response = $this->actingAs($user)->withSession(['banned' => false])
            ->getJson("api/v1/$this->resource");

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => []]);

    }
//    public function test_show()
//    {
//        $this->withoutExceptionHandling();
//        $user = User::factory()->create([
//            'email' => 'admin@jextecnologies.com',
//            'password' => bcrypt('123456')
//        ]);
//        $this->seedData();
//        $supplier_type = SupplierType::limit(1)->first();
//        $response = $this->actingAs($user)->withSession(['banned' => false])
//            ->getJson("api/v1/$this->resource/$supplier_type->id");
//
//        $response->assertStatus(200)
//            ->assertJsonStructure(['data' => []]);
//
//    }
//    public function test_show_not_found()
//    {
//        $user = User::factory()->create([
//            'email' => 'admin@jextecnologies.com',
//            'password' => bcrypt('123456')
//        ]);
//        $response = $this->actingAs($user)->withSession(['banned' => false])
//            ->getJson("api/v1/$this->resource/1");
//
//        $response->assertStatus(404)
//            ->assertExactJson(['message' => "Unable to locate the supplier type you requested."]);
//    }
//    public function test_destroy()
//    {
//        $this->withoutExceptionHandling();
//        $user = User::factory()->create([
//            'email' => 'admin@jextecnologies.com',
//            'password' => bcrypt('123456')
//        ]);
//        $this->seedData();
//        $supplier_type = SupplierType::limit(1)->first();
//        $response = $this->actingAs($user)->withSession(['banned' => false])
//            ->deleteJson("api/v1/$this->resource/$supplier_type->id");
//
//        $response->assertStatus(200)
//            ->assertExactJson(['message' => 'Supplier Type removed.']);
//
//    }
}
