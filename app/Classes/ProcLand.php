<?php

namespace App\Classes;

use App\Models\Property;
use App\Models\Land;
use App\Models\Region;
use App\Models\RegionStatistic;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

function median($arr)
{
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
    public static function processSingleLand(array $landRow, string $source, bool $dryRun): void
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
        $address = is_null($address) || $address === '' || $address === 'N/A' ? null : $address;
        $currentDate = date('Y-m-d');

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
            Log::channel("upload")->info(($dryRun ? "Add" : "Added") . " new land in ${landRow['area']} of size ${landRow['size']}");
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

                $hasStatistics = $thisProperty->statistics()->whereDate('updated_at', $currentDate)->first();

                if ($hasStatistics) {
                    $statistics = $thisProperty->statistics()->whereDate('updated_at', $currentDate)->first();
                    $statistics->update(['price' => $price]);
                } else {
                    $thisProperty->statistics()->create([
                        'price' => $price
                    ]);
                };
            }
            Log::channel("upload")->info(($dryRun ? "Update" : "Updated") . " land in ${landRow['area']} of size ${landRow['size']}");
        }
    }
}
