<?php

namespace App\Classes;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ProcRental
{
    public static function processUpload ($filePath, $isTest = TRUE)
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path("app/${filePath}"));

        $noOfSheets = $spreadsheet->getSheetCount();

        for ($sheetIndex = 0; $sheetIndex < $noOfSheets; $sheetIndex++)
        {
            Log::channel("upload")->info("Processing sheet ${sheetIndex} of ${noOfSheets}");
            $spreadsheet->setActiveSheetIndex($sheetIndex);

            $doContinue = true;
            $i = 6; // start at this row

            while ($doContinue)
            {
                $sheet = $spreadsheet->getActiveSheet();

                $sn = $sheet->getCell("A${i}")->getValue(); // user set ID
                $area = $sheet->getCell("B${i}")->getValue();
                $description = "Rental house";
                $rooms = intval($sheet->getCell("C${i}")->getValue());
                $bathrooms = intval($sheet->getCell("D${i}")->getValue());
                $price = intval($sheet->getCell("E${i}")->getValue());
                $url = $sheet->getCell("F${i}")->getValue();
                $location = null;

                if (!is_null($sn)) {
                    Log::channel("upload")->info("Added new rental in ${area} with ${rooms} rooms and ${bathrooms} bathrooms");
                } else {
                    $doContinue = false;
                }

                $i++;
            }
        }

        Log::channel("upload")->notice("Finished processing");
    }
}
