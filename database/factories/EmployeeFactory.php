<?php

namespace Database\Factories;

use App\Models\DocumentType;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $position = Position::inRandomOrder()->limit(1)->first();
        $document_type = DocumentType::inRandomOrder()->limit(1)->first();
        return [
            'document_number' => $this->faker->randomNumber('8'),
            'name' => $this->faker->name(),
            'lastname' => $this->faker->lastName() . " " . $this->faker->lastName(),
            'personal_email' => $this->faker->email(),
            'phone' => $this->faker->randomNumber(9),
            'address' => $this->faker->address(),
            'position_id' => $position,
            'document_type_id' => $document_type,
            'native_language' => 'spanish',
            'type' => $this->faker->randomElement(array('permanent', 'relay')),
            'turn' => $this->faker->randomElement(array('day', 'night')),
            'user_id' => User::factory()->create()
        ];
    }
}
