<?php

namespace App\Classes;
use App\Models\Property;
use App\Models\Land;
use Illuminate\Support\Facades\Log;

class ProcLand
{
    public static function processUpload ($filePath, $isTest = TRUE): void
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path("app/${filePath}"));

        $noOfSheets = $spreadsheet->getSheetCount();

        for ($sheetIndex = 0; $sheetIndex < $noOfSheets; $sheetIndex++) {
            Log::channel("upload")->info("Processing sheet " . ($sheetIndex + 1) . " of ${noOfSheets}");
            $spreadsheet->setActiveSheetIndex($sheetIndex);

            $doContinue = true;
            $i = 2; // start at this row

            while ($doContinue)
            {
                $sheet = $spreadsheet->getActiveSheet();

                $area = $sheet->getCell("A${i}")->getValue();
                $url = $sheet->getCell("C${i}")->getValue();
                $title = $sheet->getCell("E${i}")->getValue();
                $description = $sheet->getCell("F${i}")->getValue();
                $location = null;
                $size = floatval($sheet->getCell("I${i}")->getValue());
                $price = floatval($sheet->getCell("J${i}")->getValue());

                $thisProperty = null;
                $thisLand = null;
                $thisProperty = Property::where('url', '=', $url)->first();

                if (!is_null($area)) {
                    if (is_null($title)) {
                        // no title so skip this entry
                        Log::channel("upload")->info("Skipping the land without a title at line ${i}");
                    } else if (!is_null($thisProperty)) {
                        // existing property
                        if (!$isTest) {
                            $thisLand = $thisProperty->propertyable()->first();

                            $thisProperty['title'] = $title;
                            $thisProperty['area'] = $area;
                            $thisProperty['description'] = $description;
                            $thisProperty['url'] = $url;
                            $thisProperty['location'] = $location;
                            $thisProperty->save();

                            $thisLand['size'] = $size;
                            $thisLand->save();

                            $thisProperty->statistics()->create([
                                'price' => $price
                            ]);
                        }
                        Log::channel("upload")->info(($isTest ? "Update": "Updated")." land with property ID ".$thisProperty->id);
                    } else {
                        // new property
                        if (!$isTest) {
                            $thisLand = Land::create([
                                'size' => $size
                            ]);
                            $thisProperty = $thisLand->property()->create([
                                'title' => $title,
                                'area' => $area,
                                'description' => $description,
                                'url' => $url,
                                'location' => $location
                            ]);
                            $thisProperty->statistics()->create([
                                'price' => $price
                            ]);
                        }
                        Log::channel("upload")->info(($isTest ? "Add": "Added")." new land in ${area} of size {$size}");
                    }
                } else {
                    $doContinue = false;
                }

                $i++;
            }
        }

        Log::channel("upload")->notice("Finished processing");
    }
}
