<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\MaintenanceSheet;
use App\Models\MaintenanceSheetDetail;
use Illuminate\Database\Seeder;

class MaintenanceSheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $articles = Article::inRandomOrder()->limit(5)->get();

//        MaintenanceSheet::factory(10)
//            ->hasAttached($articles, [
//                'quantity' => 6,
//                'price' => 40.5
//            ], 'maintenance_sheet_details')
//            ->create();
        MaintenanceSheet::factory(50)
            ->has(MaintenanceSheetDetail::factory()
                ->count(3)
                ->state(function (array $attributes, MaintenanceSheet $maintenanceSheet) {
                    return ['maintenance_sheet_id' => $maintenanceSheet->id];
                }),'maintenance_sheet_details')
            ->create();
    }
}
