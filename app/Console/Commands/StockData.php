<?php

namespace App\Console\Commands;

use App\Models\UserSpo;
use App\Services\ElementService;
use App\Services\Program\StockService;
use Illuminate\Console\Command;

class StockData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(ElementService $elementService, StockService $ss)
    {
        $responseArray = $elementService->updateStockData($ss, UserSpo::SCHEDULED_SYNCHRONIZATION_ID);
        \Log::info('Плановая синхронизация со складом', $responseArray);
        var_dump($responseArray);
    }
}
