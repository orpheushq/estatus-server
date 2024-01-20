<?php

namespace App\Classes;

use App\Models\Property;
use App\Models\Land;
use App\Models\Region;
use App\Models\RegionStatistic;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

function median($arr) {
    sort($arr);
    $count = count($arr);
    $middle = floor(($count - 1) / 2);

    if ($count % 2 == 0) {
        $low = $arr[$middle];
        $high = $arr[$middle + 1];
        return ($low + $high) / 2;
    } else {
        return $arr[$middle];
    }
}
class ProcLand
{
    public static function processUpload ($filePath, $isTest = TRUE): void
    {
        set_time_limit(0);

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path("app/${filePath}"));

        $noOfSheets = $spreadsheet->getSheetCount();

        for ($sheetIndex = 0; $sheetIndex < $noOfSheets; $sheetIndex++) {
            Log::channel("upload")->info("Processing sheet " . ($sheetIndex + 1) . " of ${noOfSheets}");
            $spreadsheet->setActiveSheetIndex($sheetIndex);

            $doContinue = true;
            $i = 2; // start at this row

            $areaPrices = [];

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
                if (!empty($area) && $size > 0 && $price >= 0) {
                  
                    $pricePerSize = $price;
                    $areaPrices[$area][] = $pricePerSize;
                } else {
                    // Handle validation errors (e.g., log or skip invalid data)
                    Log::channel("upload")->warning("Skipping invalid data at line ${i}");
                }

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

            foreach ($areaPrices as $area => $pricePerSize) {
                if (!is_null($area)) {
                    // Check if $area is not null before inserting into the regions table
                    if (!$isTest) {
                        Region::updateOrCreate(
                            ['region' => $area],
                            ['created_at' => now(), 'updated_at' => now()]
                        );
                    }
                }
            }

            foreach ($areaPrices as $area => $pricePerSize) {
                // Calculate the median value for the prices
                $medianPrice = median($pricePerSize);

                // Find the corresponding region ID based on the area
                $region = Region::where('region', $area)->first();

                if ($region) {
                    try {
                        // Insert statistics
                        if (!$isTest) {
                            $region->statistics()->create([
                                'price' => $medianPrice
                            ]);
                        }

                        // Log a success message
                        Log::channel("upload")->info("Successfully calculated median for area: ${area}, median price: ${medianPrice}");
                    } catch (QueryException $e) {
                        // Log an error message for database errors
                        Log::channel("upload")->error("Error median for area: ${area}, error: " . $e->getMessage());
                    }
                } else {
                    // Log an error message if the region is not found
                    Log::channel("upload")->error("Region not found for area: ${area}");
                }
            }
        }

        Log::channel("upload")->notice("Finished processing");
    }
}
