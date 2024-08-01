<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $suppliers = Supplier::all();
        Article::factory(20)
            ->hasAttached($suppliers,[
                "price"=>rand(0, 10) / 5,
            ])
            ->create();
    }
}
