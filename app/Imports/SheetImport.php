<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SheetImport implements ToCollection, WithStartRow, ToArray, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        return $rows;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function array(array $array)
    {
        return $array;
    }
}