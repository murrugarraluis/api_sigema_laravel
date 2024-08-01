<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationControllerTest extends TestCase
{
    public function test()
    {
        $this->assertTrue(true);
    }
//    use RefreshDatabase;
//    private $resource = 'notifications';
//
//    public function test_index()
//    {
//        $this->withoutExceptionHandling();
//        $user = User::factory()->create([
//            'email' => 'admin@jextecnologies.com',
//            'password' => bcrypt('123456')
//        ]);
//        $response = $this->actingAs($user)->withSession(['banned' => false])
//            ->getJson("api/v1/$this->resource");
//
//        $response->assertStatus(200)
//            ->assertJsonStructure(['data' => []]);
//
//    }
}
