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
    public static function processSingleLand (array $landRow, string $source, bool $dryRun): void
    {
        [
            "area" => $area,
            "url" => $url,
            "address" => $address,
            "title" => $title,
            "description" => $description,
            "size" => $size,
            "price" => $price,
            "mapLink" => $maplink,
            "raw_maplink" => $rawMapLink
        ] = $landRow;
        $location = null; // TODO: implement later
        $rawAddress = null; // TODO: implement later

        // INFO: validation
        if (is_null($area)) {
            throw new \Exception("No area");
        }
        if (is_null($title)) {
            throw new \Exception("No title");
        }

        // INFO: transforms
        $area = strtolower(trim($area));
        $title = mb_convert_encoding(substr($title, 0, 250), "UTF-8");
        $size = floatval($size);
        $price = floatval($price);
        $address = is_null($address) || $address === '' || $address === 'N/A' ? null || $address;

        $thisProperty = Property::where('url', '=', $url)->first();

        if (is_null($url) || is_null($thisProperty)) { // INFO: consider as new property always if URL is null
            // INFO: new property
            if (!$dryRun) {
                $thisLand = Land::create([
                    'size' => $size
                ]);
                $thisProperty = $thisLand->property()->create([
                    'title' => $title,
                    'source' => $source,
                    'area' => $area,
                    'description' => $description,
                    'url' => $url,
                    'location' => $location,
                    'maplink' => $maplink,
                    'raw_maplink' => $rawMapLink,
                    'address' => $address,
                    'raw_address' => $rawAddress,
                ]);
                $thisProperty->statistics()->create([
                    'price' => $price
                ]);
            }
            Log::channel("upload")->info(($dryRun ? "Add": "Added")." new land in ${landRow['area']} of size ${landRow['size']}");
        } else {
            if (!$dryRun) {
                $thisLand = $thisProperty->propertyable()->first();
                $thisProperty['title'] = $title;
                $thisProperty['area'] = $area;
                $thisProperty['description'] = $description;
                $thisProperty['url'] = $url;
                $thisProperty['location'] = $location;
                $thisProperty['maplink'] = $maplink;
                $thisProperty['raw_maplink'] = $rawMapLink;
                $thisProperty['address'] = $address;
                $thisProperty['raw_address'] = $rawAddress;
                $thisProperty->save();

                $thisLand['size'] = $size;
                $thisLand->save();

                $thisProperty->statistics()->create([
                    'price' => $price
                ]);
            }
            Log::channel("upload")->info(($dryRun ? "Update": "Updated")." land in ${landRow['area']} of size ${landRow['size']}");
        }
    }
    public static function processUpload ($filePath, $isTest = TRUE, $dataSource): void
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
                // INFO: https://stackoverflow.com/questions/7979567/php-convert-any-string-to-utf-8-without-knowing-the-original-character-set-or
                $title = mb_convert_encoding(substr($sheet->getCell("E${i}")->getValue(), 0, 250), "UTF-8");
                $description = $sheet->getCell("F${i}")->getValue();
                $location = null;
                $size = floatval($sheet->getCell("I${i}")->getValue());
                $price = floatval($sheet->getCell("J${i}")->getValue());
                $maplink = $sheet->getCell("K${i}")->getValue();
                $raw_maplink = $sheet->getCell("L${i}")->getValue();
                $address = $sheet->getCell("D${i}")->getValue();
                $raw_address = $sheet->getCell("D${i}")->getValue();

                $thisProperty = null;
                $thisLand = null;
                $thisProperty = Property::where('url', '=', $url)->first();
                if (!empty($area) && $size > 0 && $price >= 0) {
                    $area = strtolower(trim($area));
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
                            $thisProperty['source'] = $dataSource;
                            $thisProperty['area'] = $area;
                            $thisProperty['description'] = $description;
                            $thisProperty['url'] = $url;
                            $thisProperty['location'] = $location;
                            $thisProperty['maplink'] = $maplink;
                            $thisProperty['raw_maplink'] = $raw_maplink;
                            $thisProperty['address'] = $address;
                            $thisProperty['raw_address'] = $raw_address;
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
                            try {
                                $thisProperty = $thisLand->property()->create([
                                    'title' => $title,
                                    'source' => $dataSource,
                                    'area' => $area,
                                    'description' => $description,
                                    'url' => $url,
                                    'location' => $location,
                                    'maplink' => $maplink,
                                    'raw_maplink' => $raw_maplink,
                                    'address' => $address,
                                    'raw_address' => $raw_address,
                                ]);
                            } catch (\Exception $err){
                                dd(array(
                                    'error' => $err,
                                    'entry' => [
                                        'title' => $title,
                                        'source' => $dataSource,
                                        'area' => $area,
                                        'description' => $description,
                                        'url' => $url,
                                        'location' => $location,
                                        'maplink' => $maplink,
                                        'raw_maplink' => $raw_maplink,
                                        'address' => $address,
                                        'raw_address' => $raw_address,
                                    ]
                                ));
                            }
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
