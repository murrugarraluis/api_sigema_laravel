<?php

namespace Tests\Unit;

use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PositionControllerTest extends TestCase
{
    use RefreshDatabase;
    private $resource = 'positions';
    public function seedData()
    {
        Position::factory()->create(['name'=>'position']);
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
//        $position = Position::limit(1)->first();
//        $response = $this->actingAs($user)->withSession(['banned' => false])
//            ->getJson("api/v1/$this->resource/$position->id");
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
//            ->assertExactJson(['message' => "Unable to locate the position you requested."]);
//    }
//    public function test_destroy()
//    {
//        $this->withoutExceptionHandling();
//        $user = User::factory()->create([
//            'email' => 'admin@jextecnologies.com',
//            'password' => bcrypt('123456')
//        ]);
//        $this->seedData();
//        $position = Position::limit(1)->first();
//        $response = $this->actingAs($user)->withSession(['banned' => false])
//            ->deleteJson("api/v1/$this->resource/$position->id");
//
//        $response->assertStatus(200)
//            ->assertExactJson(['message' => 'Position removed.']);
//
//    }

}
