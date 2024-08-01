<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\MaintenanceSheet;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceSheetDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $article = Article::inRandomOrder()->limit(1)->first();
        $maintenance_sheet = MaintenanceSheet::inRandomOrder()->limit(1)->first();
        return [
            'maintenance_sheet_id' => $maintenance_sheet,
            'article_id' => $article,
            'quantity' => $this->faker->randomNumber(1),
            'price' => $this->faker->randomNumber(2),
            'description' => null,
        ];
    }
}
