<?php

namespace Database\Seeders;

use App\Models\ComponentReference;
use App\Models\GroupReference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesReferenceSeeder extends Seeder
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

        $sql = 'select components, groups, categories from components';
        $categories = collect(access_result_array_all($db_param,$sql))->unique();

        $components_ref = ComponentReference::all();
        $groups_ref = GroupReference::all();

        $categories->each(function ($item, $key) use ($components_ref, $groups_ref)
        {
            if($item['categories'] != "") {
                $component_ref_id = $components_ref->firstWhere('title', $item['components'])->id;
                $group_ref_id = $groups_ref->where('title', $item['groups'])->where('component_ref_id', $component_ref_id)->first()->id;
                DB::table('categories_reference')->insert([
                    'title' => $item['categories'],
                    'group_ref_id' => $group_ref_id,
                    'component_ref_id' => $component_ref_id
                ]);
            }
        });
    }
}
