<?php

namespace Database\Factories;

use App\Models\CategoryReference;
use App\Models\ComponentReference;
use App\Models\Element;
use App\Models\FootprintReference;
use App\Models\GroupReference;
use App\Models\LibraryRefReference;
use App\Models\ManufacturerReference;
use App\Models\Operation;
use App\Models\PartStatusReference;
use App\Models\TempRangeReference;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\History;
use Illuminate\Support\Arr;

class HistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = History::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $userId = Arr::random([962, 440, 1030, 613, 754, 930, 393]);
        $entites = [
            1 => Element::class,
            2 => ComponentReference::class,
            3 => GroupReference::class,
            4 => CategoryReference::class,
            5 => ManufacturerReference::class,
            6 => TempRangeReference::class,
            7 => PartStatusReference::class,
            8 => LibraryRefReference::class,
            9 => FootprintReference::class
        ];
        $entityId = array_rand($entites);
        $operationId = Operation::query()->inRandomOrder()->first()->id;
        $historyableItem = $entites[$entityId]::query()->inRandomOrder()->first();

        return [
            'log_code_id' => $entityId,
            'user_id' => $userId,
            'operation_id' => $operationId,
            'historyable_type' => $entites[$entityId],
            'historyable_id' => $historyableItem->id,
            'before' => in_array($operationId, [
                Operation::UPDATE_ID,
                Operation::UPDATE_STOCK_ID,
                Operation::JOIN_ID
            ])
                ? json_encode($historyableItem)
                : null,
            'after' => in_array($operationId, [
                Operation::ADD_ID,
                Operation::ADD_STOCK_ID,
                Operation::UPDATE_ID,
                Operation::UPDATE_STOCK_ID,
                Operation::JOIN_ID
            ])
                ? json_encode($historyableItem)
                : null,
            'created_at' => $this->faker->dateTime(),
        ];
    }
}
