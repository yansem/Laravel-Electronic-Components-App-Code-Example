<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\ConsoleOutput;

class FootprintsReferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $db_param = ["name" => base_path() . '/tmp/bla_ad_lib_main.accdb'];
        if(!file_exists($db_param["name"])) {
            die('Error finding access database');
        }

        $sql = 'select "Footprint Ref 1" from components';
        $allFootprints = collect(access_result_array_all($db_param,$sql))->unique();
        $sql = 'select "Footprint Ref 2" from components';
        $footprints = collect(access_result_array_all($db_param,$sql))->unique();
        $allFootprints = $footprints->merge($allFootprints);
        $sql = 'select "Footprint Ref 3" from components';
        $footprints = collect(access_result_array_all($db_param,$sql))->unique();
        $allFootprints = $footprints->merge($allFootprints);

        $allFootprints->each(function ($item, $key){
            if (isset($item['Footprint Ref 1']) && $item['Footprint Ref 1']) {
                DB::table('footprints_reference')->insert([
                    'title' => $item['Footprint Ref 1']
                ]);
            }
            if (isset($item['Footprint Ref 2']) && $item['Footprint Ref 2']) {
                DB::table('footprints_reference')->insert([
                    'title' => $item['Footprint Ref 2']
                ]);
            }
            if (isset($item['Footprint Ref 3']) && $item['Footprint Ref 3']) {
                DB::table('footprints_reference')->insert([
                    'title' => $item['Footprint Ref 3']
                ]);
            }
        });
    }
}
