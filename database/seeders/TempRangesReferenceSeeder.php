<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TempRangesReferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $db_param["name"] = base_path() . '/tmp/bla_ad_lib_no_bom.accdb';
        if(!file_exists($db_param["name"])) {
            die('Error finding access database');
        }

        $sql = 'select * from "~TMPCLP306201"';
        $temp_ranges = collect(access_result_array_all($db_param,$sql));

        // insert data
        $temp_ranges->each(function ($item, $key) {
            DB::table('temp_ranges_reference')->insert([
                'title' => $item['RangeName'],
                'description' => $item['Txt'],
                'min' => $item['TermMin'],
                'max' => $item['TermMax'],
            ]);
        });
    }
}
