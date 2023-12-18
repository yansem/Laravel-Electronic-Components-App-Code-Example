<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PartStatusesReferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $resultset = collect(['Не проверено', 'Активный', 'Устарел', 'Не рекомендуется']);

        $resultset->each(function ($item, $key) {
            DB::table('part_statuses_reference')->insert([
                'title' => $item,
            ]);
        });
    }
}