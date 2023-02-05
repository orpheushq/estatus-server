<?php

namespace App\Classes;
use App\Models\Property;
use App\Models\Rental;
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
            Log::channel("upload")->info("Processing sheet ".($sheetIndex + 1)." of ${noOfSheets}");
            $spreadsheet->setActiveSheetIndex($sheetIndex);

            $doContinue = true;
            $i = 6; // start at this row

            while ($doContinue)
            {
                $sheet = $spreadsheet->getActiveSheet();

                $sn = $sheet->getCell("A${i}")->getValue(); // user set ID
                $area = $sheet->getCell("B${i}")->getValue();
                $rooms = intval($sheet->getCell("C${i}")->getValue());
                $bathrooms = intval($sheet->getCell("D${i}")->getValue());
                $title = "${rooms} bedroom house";
                $description = "Rental house with ${rooms} rooms and ${bathrooms} bathrooms";
                $price = intval($sheet->getCell("E${i}")->getValue());
                $url = $sheet->getCell("F${i}")->getValue();
                $location = null;
                $propertyId = $sheet->getCell("K${i}")->getValue(); // if the property had been already added, this would contain a value
                $thisProperty = null;
                if (!is_null($propertyId)) {
                    $thisProperty = Property::where('id', '=', intval($propertyId))->first();
                }

                $greyHighlightStyle = [
                    'font' => [ 'bold' => true ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [ 'argb' => 'FF999999' ]
                    ]
                ];

                $sheet->getCell("K5")
                    ->setValue("Property ID")
                    ->getStyle()
                    ->applyFromArray($greyHighlightStyle);

                if (!is_null($sn)) {
                    if (!is_null($thisProperty)) {
                        // existing property
                        if (!$isTest) {
                            $thisRental = $thisProperty->propertyable()->first();

                            $thisProperty['title'] = $title;
                            $thisProperty['area'] = $area;
                            $thisProperty['description'] = $description;
                            $thisProperty['url'] = $url;
                            $thisProperty['location'] = $location;
                            $thisProperty->save();

                            $thisRental['rooms'] = $rooms;
                            $thisRental['bathrooms'] = $bathrooms;
                            $thisRental->save();
                        }
                        Log::channel("upload")->info(($isTest ? "Update": "Updated")." rental with ID ".$thisProperty->id);
                    } else {
                        // new property
                        if (!$isTest) {
                            $newRental = Rental::create([
                                'rooms' => $rooms,
                                'bathrooms' => $bathrooms
                            ]);
                            $newProperty = $newRental->property()->create([
                                'title' => $title,
                                'area' => $area,
                                'description' => $description,
                                'url' => $url,
                                'location' => $location
                            ]);
                            $sheet->getCell("K${i}")
                                ->setValue($newProperty->id)
                                ->getStyle()
                                ->applyFromArray($greyHighlightStyle);
                        }
                        Log::channel("upload")->info(($isTest ? "Add": "Added")." new rental in ${area} with ${rooms} rooms and ${bathrooms} bathrooms");
                    }
                } else {
                    $doContinue = false;
                }

                $i++;
            }
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save(storage_path("app/public/rental-upload.xlsx"));
        Log::channel("upload")->notice("Finished processing");
    }
}
