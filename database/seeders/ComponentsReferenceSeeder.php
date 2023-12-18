<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ComponentsReferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $db_param = ['name' => base_path() . '/tmp/bla_ad_lib_main.accdb'];
        if(!file_exists($db_param["name"])) {
            die('Error finding access database');
        }

        $sql = 'select components from components';
        $components_main = collect(access_result_array_all($db_param,$sql))->pluck('components')->unique();

        $db_param["name"] = base_path() . '/tmp/bla_ad_lib_no_bom.accdb';
        if(!file_exists($db_param["name"])) {
            die('Error finding access database');
        }

        $sql = 'select components from components';
        $components_no_bom = collect(access_result_array_all($db_param,$sql))->pluck('components')->unique();

        DB::table('components_reference')->insert([
            'title' => "Не распределенные",
        ]);

        $components = $components_main->merge($components_no_bom);
        $components->each(function ($item, $key)
        {
            DB::table('components_reference')->insert([
                'title' => $item,
            ]);
        });
    }
}
