<?php

namespace Database\Seeders;

use App\Models\ComponentReference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComponentTypesReferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // collect "Altium" data
        // db settings
        $db_param["row_limit"] = 1000;
        $db_param["name"] = base_path() . '/tmp/bla_ad_lib_main.accdb';
        if(!file_exists($db_param["name"])) {
            die('Error finding access database');
        }
        // collect data
        $sql = 'select components, ct from components';
        $component_types = collect(access_result_array_all($db_param,$sql))->unique();
        // parent references
        $components_ref = ComponentReference::all()->map->only(['id', 'title']);    
        // insert data        
        $component_types->each(function ($item, $key) use ($components_ref)
        {
            $component_ref_id = $components_ref->firstWhere('title', $item['components'])['id'];
            DB::table('component_types_reference')->insert([
                'title' => $item['ct'],
                'component_ref_id' => $component_ref_id,
            ]);
        });
    }
}
