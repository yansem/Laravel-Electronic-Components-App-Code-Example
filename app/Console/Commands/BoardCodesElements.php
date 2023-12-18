<?php

namespace App\Console\Commands;

use App\Models\Element;
use App\Models\PcbCode;
use Illuminate\Console\Command;

class BoardCodesElements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'board_codes:elements';

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
    public function handle()
    {
        $fewElements = [];
        $emptyElements = [];

        $pcbCodes = PcbCode::with('versions')->get();

        foreach ($pcbCodes as $pcbCode) {
            if ($pcbCode->versions) {
                foreach ($pcbCode->versions as $version) {
                    $element = Element::where(
                        'stock_title',
                        'LIKE',
                        '%(' . $pcbCode->code . '.' . $version->version . ')%')->get();
                    if ($element->isEmpty()) {
                        if (!isset($emptyElements[$pcbCode->code]))
                            $emptyElements[$pcbCode->code] = '';
                        $emptyElements[$pcbCode->code] .= $version->version . ',';
                    } else if ($element->count() > 1) {
                        if (!isset($fewElements[$pcbCode->code]))
                            $fewElements[$pcbCode->code] = '';
                        $fewElements[$pcbCode->code] .= $version->version . ',';
                    }
                }
            }
        }
        dump($emptyElements);
        dump($fewElements);
    }
}
