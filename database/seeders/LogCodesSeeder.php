<?php

namespace Database\Seeders;

use App\Models\ComponentReference;
use App\Models\GroupReference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LogCodesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('log_codes')->insert($this->getData());
    }

    public function getData(): array
    {
        return [
            ['title' => 'Elements'],
            ['title' => 'Components'],
            ['title' => 'Groups'],
            ['title' => 'Categories'],
            ['title' => 'Manufacturers'],
            ['title' => 'Temp ranges'],
            ['title' => 'Part statuses'],
            ['title' => 'Library Ref'],
            ['title' => 'Footprints'],
        ];
    }
}
