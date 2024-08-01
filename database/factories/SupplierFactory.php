<?php

namespace Database\Factories;

use App\Models\DocumentType;
use App\Models\Position;
use App\Models\SupplierType;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $supplier_type = SupplierType::inRandomOrder()->limit(1)->first();
        $document_type = DocumentType::where('name', 'RUC')->first();
        return [
            'name' => $this->faker->company(),
            'document_number' => $this->faker->randomNumber(9),
            'phone' => $this->faker->randomNumber(9),
            'email' => $this->faker->email(),
            'address' => $this->faker->address(),
            'supplier_type_id' => $supplier_type,
            'document_type_id' => $document_type,

        ];
    }
}
