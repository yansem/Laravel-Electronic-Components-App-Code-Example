<?php

namespace App\Console\Commands;

use App\Imports\BoardImport;
use App\Models\Element;
use App\Models\PcbCode;
use App\Models\Version;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;

class ImportBoardCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'board_codes:import {--clear=}';

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
        if ($this->option('clear') === 'true') {
            Schema::disableForeignKeyConstraints();
            Version::query()->truncate();
            PcbCode::query()->truncate();
            Schema::enableForeignKeyConstraints();
        }

        $import = new BoardImport();

        $array = Excel::toArray($import, 'db_board2.xlsx');

        foreach ($array['codeVersions'] as &$codeVersion) {
            $pcb_code = PcbCode::create([
                'code' => $codeVersion['code'],
                'url_svn' => PcbCode::URL_SVN . $codeVersion['code'],
                'url_wiki' => PcbCode::URL_WIKI . mb_strtolower($codeVersion['code']),
            ]);

            if ($codeVersion['version'] !== null) {
                $codeVersion['version'] = str_replace('V', '', $codeVersion['version']);
                $codeVersion['version'] = str_replace(' ', '', $codeVersion['version']);
                $codeVersion['version'] = explode('/', $codeVersion['version']);


                foreach ($codeVersion['version'] as $version) {
                    $element_id = null;
                    $element = Element::where(
                        'stock_title',
                        'LIKE',
                        '%(' . $codeVersion['code'] . '.' . $version . ')%')->get();
                    if ($element->count() === 1) {
                        $element_id = $element->first()->id;
                    }

                    Version::create([
                        'pcb_code_id' => $pcb_code->id,
                        'element_id' => $element_id,
                        'version' => $version
                    ]);
                }
            }
        }

        foreach ($array['codes'] as $code) {
            if ($code['url_wiki'] === null) {
                $code['url_wiki'] = PcbCode::URL_WIKI . mb_strtolower($code['code']);
            }
            if ($code['url_svn'] === null) {
                $code['url_svn'] = PcbCode::URL_SVN . $code['code'];
            }

            $pcbCode = PcbCode::where('code', $code)->get()->first();
            $pcbCode->update([
                'description' => $code['description'],
                'url_wiki' => $code['url_wiki'],
                'url_svn' => $code['url_svn']
            ]);
        }
    }
}
