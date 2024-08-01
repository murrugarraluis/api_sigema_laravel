<?php

namespace Database\Seeders;

use App\Models\ArticleType;
use Illuminate\Database\Seeder;

class ArticleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ArticleType::factory()->create(['name'=>'Office']);
        ArticleType::factory()->create(['name'=>'Spare Part']);
        ArticleType::factory()->create(['name'=>'EPP']);
    }
}
