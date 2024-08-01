<?php

namespace Database\Seeders;

use App\Models\SupplierType;
use Illuminate\Database\Seeder;

class SupplierTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SupplierType::factory()->create(['name'=>'Proveedor de Servicios']);
        SupplierType::factory()->create(['name'=>'Proveedor de Articulos']);
    }
}
