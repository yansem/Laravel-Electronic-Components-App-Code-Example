<?php

namespace Database\Seeders;

use Database\Seeders\iseed\CategoriesReferenceTableSeeder;
use Database\Seeders\iseed\ComponentsReferenceTableSeeder;
use Database\Seeders\iseed\ElementsTableSeeder;
use Database\Seeders\iseed\FootprintsReferenceTableSeeder;
use Database\Seeders\iseed\GroupsReferenceTableSeeder;
use Database\Seeders\iseed\LibraryRefReferenceTableSeeder;
use Database\Seeders\iseed\LogCodesTableSeeder;
use Database\Seeders\iseed\LogsTableSeeder;
use Database\Seeders\iseed\ManufacturersReferenceTableSeeder;
use Database\Seeders\iseed\PartStatusesReferenceTableSeeder;
use Database\Seeders\iseed\PcbCodesTableSeeder;
use Database\Seeders\iseed\StockCategoriesTableSeeder;
use Database\Seeders\iseed\TempRangesReferenceTableSeeder;
use Database\Seeders\iseed\VersionsTableSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ComponentsReferenceTableSeeder::class);
        $this->call(GroupsReferenceTableSeeder::class);
        $this->call(CategoriesReferenceTableSeeder::class);
        $this->call(ManufacturersReferenceTableSeeder::class);
        $this->call(LibraryRefReferenceTableSeeder::class);
        $this->call(FootprintsReferenceTableSeeder::class);
        $this->call(PartStatusesReferenceTableSeeder::class);
        $this->call(TempRangesReferenceTableSeeder::class);
        $this->call(ElementsTableSeeder::class);
        $this->call(LogCodesTableSeeder::class);
//        $this->call(LogsTableSeeder::class);
        $this->call(StockCategoriesTableSeeder::class);
        $this->call(OperationSeeder::class);
//        $this->call(HistorySeeder::class);
        $this->call(PcbCodesTableSeeder::class);
        $this->call(VersionsTableSeeder::class);
    }
}
