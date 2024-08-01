<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DocumentType::factory()->create(['name' => 'DNI']);
        DocumentType::factory()->create(['name' => 'RUC']);
        DocumentType::factory()->create(['name' => 'Passport']);
    }
}
