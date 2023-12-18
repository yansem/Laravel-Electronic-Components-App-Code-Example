<?php

namespace Database\Seeders;

use App\Models\Element;
use App\Models\PcbCode;
use App\Models\Version;
use Illuminate\Database\Seeder;

class PcbCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $chars = array_merge(range(0, 9), range('A', 'Z'));
        $codes = [];

        foreach ($chars as $char) {
            foreach (range(0, 9) as $number) {
                $codes[] = $char . $number;
            }
            foreach (range('A', 'Z') as $alpha) {
                $codes[] = $char . $alpha;
            }
        }

        foreach ($codes as $code) {
            if ($code === 'A5') break;
            $pcbCode = PcbCode::create(
                [
                    'code' => $code,
                    'description' => 'Описание ' . $code,
                    'url_wiki' => 'https://wiki.orlan.in/doku.php/брэо:пп:коды_плат:' . mb_strtolower($code),
                    'url_svn' => 'https://svn.orlan.in/svn/main/pcb_projects/' . $code
                ]
            );

            $randVersion = rand(2, 6);
            for ($i = 1; $i <= $randVersion; $i++) {
                Version::create(
                    [
                        'pcb_code_id' => $pcbCode->id,
                        'version' => $i,
                        'count' => rand(1, 100),
                        'description' => 'Описание ' . $i . ' версии'
                    ]
                );
            }
        }
    }
}
