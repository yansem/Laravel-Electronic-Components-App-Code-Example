<?php

namespace Database\Seeders;

use App\Models\Operation;
use Illuminate\Database\Seeder;

class OperationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $operations = [
            'Добавление',
            'Добавление (склад)',
            'Обновление',
            'Обновление (склад)',
            'Скрытие',
            'Восстановление',
            'Объединение'
        ];
        foreach ($operations as $operation) {
            Operation::create(['title' => $operation]);
        }
    }
}
