<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $banks = Bank::all();
        Supplier::factory(5)
            ->hasAttached($banks,[
                "account_number"=>'1234-5678-90123-11123',
                "interbank_account_number"=>'1234-1234-5678-90123-11123',
            ])
            ->create();
    }
}
