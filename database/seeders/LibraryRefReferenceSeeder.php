<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Output\ConsoleOutput;

class LibraryRefReferenceSeeder extends Seeder
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

        $sql = 'select "Library Ref" from components';
        $libraryRefs = collect(access_result_array_all($db_param,$sql))->unique();

        $libraryRefs->each(function ($item, $key){
            DB::table('library_ref_reference')->insert([
                'title' => $item['Library Ref']
            ]);
        });
    }
}
