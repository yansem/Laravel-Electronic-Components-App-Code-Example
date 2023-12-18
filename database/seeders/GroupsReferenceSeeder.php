<?php

namespace Database\Seeders;

use App\Models\ComponentReference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupsReferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $db_param["name"] = base_path() . '/tmp/bla_ad_lib_main.accdb';
        if(!file_exists($db_param["name"])) {
            die('Error finding access database');
        }
        $sql = 'select components, groups from components';

        $groups = collect(access_result_array_all($db_param,$sql))->unique();

        $components_ref = ComponentReference::all()->map->only(['id', 'title']);

        $groups->each(function ($item, $key) use ($components_ref)
        {
            if($item['groups'] != "") {
                $component_ref_id = $components_ref->firstWhere('title', $item['components'])['id'];
                DB::table('groups_reference')->insert([
                    'title' => $item['groups'],
                    'component_ref_id' => $component_ref_id,
                ]);               
            }
        });
    }
}
