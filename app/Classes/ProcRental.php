<?php

namespace App\Classes;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ProcRental
{
    public static function processUpload ($filePath)
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path("app/${filePath}"));

        $spreadsheet->getActiveSheet()->getCell('B1')->getValue();
        dd($spreadsheet->getActiveSheet()->getCell('B1')->getValue());
    }
}
