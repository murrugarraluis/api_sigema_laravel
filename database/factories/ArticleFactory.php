<?php

namespace Database\Factories;

use App\Models\ArticleType;
use App\Models\SupplierType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $article_type = ArticleType::inRandomOrder()->limit(1)->first();

        return [
            'serie_number' => "Serie - " . $this->faker->randomLetter() . $this->faker->randomNumber(5),
            'name'=>"Article ".$this->faker->randomLetter().$this->faker->randomNumber(3),
            'brand'=>"Brand ".$this->faker->randomLetter().$this->faker->randomNumber(3),
            'model'=>$this->faker->randomLetter()."-".$this->faker->randomNumber(3),
            'quantity'=>$this->faker->randomNumber(2),
            'article_type_id'=>$article_type,
        ];
    }
}
