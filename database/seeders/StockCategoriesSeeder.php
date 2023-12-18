<?php

namespace Database\Seeders;

use App\Models\ComponentReference;
use App\Models\GroupReference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stock_categories')->insert($this->getData());
    }

    public function getData(): array
    {
        $data = [];

        $data[] = [
            'id_in_stock'       => 15,
            'title_in_stock'    => 'Электрика - Аккумуляторы',
            'use'               => 0
        ];

        $data[] = [
            'id_in_stock'       => 98,
            'title_in_stock'    => 'Электрика - Готовые изделия',
            'use'               => 0
        ];

        $data[] = [
            'id_in_stock'       => 13,
            'title_in_stock'    => 'Электрика - Зарядные устройства',
            'use'               => 0
        ];

        $data[] = [
            'id_in_stock'       => 16,
            'title_in_stock'    => 'Электрика - Комплектующие',
            'use'               => 1
        ];

        $data[] = [
            'id_in_stock'       => 1,
            'title_in_stock'    => 'Электрика - Сервоприводы',
            'use'               => 0
        ];

        $data[] = [
            'id_in_stock'       => 14,
            'title_in_stock'    => 'Электрика - Стартеры',
            'use'               => 0
        ];

        return $data;
    }
}
