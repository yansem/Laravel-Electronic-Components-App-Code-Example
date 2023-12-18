<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class BoardImport implements ToCollection, WithMultipleSheets, WithTitle
{
    public function collection(Collection $rows)
    {
        return $rows;
    }

    public function sheets(): array
    {
        return [
            'codeVersions' => new SheetImport(),
            'codes' => new SheetImport()
        ];
    }

    public function title(): string
    {

    }
}