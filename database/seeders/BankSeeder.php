<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bank::factory()->create(["name"=>"Banco de la Nación"]);
        Bank::factory()->create(["name"=>"Banco Central del Perú"]);
    }
}
