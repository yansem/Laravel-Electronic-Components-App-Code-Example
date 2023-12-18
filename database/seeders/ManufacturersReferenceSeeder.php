<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\ConsoleOutput;

class ManufacturersReferenceSeeder extends Seeder
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

        $sql = 'select * from "~TMPCLP425271"';
        $manufacturers = collect(access_result_array_all($db_param,$sql));

        // insert data
        $manufacturers->each(function ($item, $key) {
            DB::table('manufacturers_reference')->insert([
                'title' => $item['Name'],
            ]);
        });

    }
}
