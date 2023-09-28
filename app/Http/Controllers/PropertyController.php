<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Support\Facades\DB;
use App\Models\Region;
use Illuminate\Support\Facades\Log;

class PropertyController extends Controller
{
    private $propertyTypes = [];
    private $propertyAreas = [];

    public function __construct()
    {
        $this->propertyTypes = (new Property())->getTypes();
        $this->propertyAreas = (new Property())->getAreas();
    }

    private function updateEntity($newValues, $entity)
    {
        foreach ($newValues as $k => $v) {
            if (!is_null($v)) {
                switch ($k) {
                    case "type":
                        $entity[$k] = strtolower($v);
                        break;
                    case "latitude":
                        $entity['location'] = new Point($newValues['latitude'], $newValues['longitude']);
                        break;
                    case "longitude":
                        // handled in a different case
                        break;
                    default:
                        $entity[$k] = $v;
                }
            }
        }
    }

    /**
     * Display lists with available options (i.e. property type, property area)
     */
    public function getLists(Request $request)
    {
        return response([
            'types' => array_map(fn ($v) => [strtolower(str_getcsv($v, "\\\\")[2]) => ucfirst(str_getcsv($v, "\\\\")[2])], $this->propertyTypes),
            'areas' => $this->propertyAreas
        ], 200);
    }

    private function getRegionAverage($region, $type)
    {
        $properties = Property::where('area', '=', $region)
            ->where("propertyable_type", "like", "%{$type}");
        $properties->join('statistics', function ($join) {
            $join
                ->on('statistics.id', '=', DB::raw("
                        (SELECT id from `statistics`
                        WHERE property_id=properties.id
                        ORDER BY id DESC LIMIT 1)
                    "));
        });
        return $properties->avg('price');
    }

    private function getRegionMedian($region, $type)
    {
        
        {
            // Find the relevant region in the 'regions' table based on the provided 'region'
            $regionData = Region::where('region', '=', $region)->first();
        
            if (!$regionData) {
                return null; // Handle the case where the region is not found
            }
        
            // Use the retrieved 'id' from 'regions' to locate the matching 'region_id' in 'region_statistics'
            $statistics = DB::table('region_statistics')
                ->where('region_id', '=', $regionData->id)
                ->orderBy('created_at', 'desc')
                ->first();
        
            if (!$statistics) {
                return null; // Handle the case where statistics for the region are not found
            }
        
            return $statistics->price;
        }
        
    }

    /**
     * Returns the median price per region using the latest statistic
     */
    public function getRegionMed(Request $request, string $region, $type = 'land')
    {
        $sanitizedRegion = strip_tags($region);
        $sanitizedType = strip_tags($type);
    
        $medianPrice = $this->getRegionMedian($sanitizedRegion, $sanitizedType);
    
        return response([
            'region' => $sanitizedRegion,
            'medianPrice' => $medianPrice
        ], 200);
    }

    /**
     * Returns the average price per region using the latest statistic
     */
    public function getRegion(Request $request, string $region, $type = 'land')
    {
        $sanitizedRegion = strip_tags($region);
        $sanitizedType = strip_tags($type);

        return response([
            'area' => $sanitizedRegion,
            'avgPrice' => $this->getRegionAverage($sanitizedRegion, $sanitizedType)
        ], 200);
    }

    public function getAllRegions(Request $request, $type = 'land')
    {
        $sanitizedType = strip_tags($type);

        try {
            // Retrieve all regions from the 'regions' table
            $regions = Region::all();

            $prices = [];

            foreach ($regions as $region) {
                // Retrieve the relevant price for each region from the 'region_statistics' table
                $statistics = $region->statistics()
                    ->where('region_id', $region->id) // Use the 'id' of the region as 'region_id'
                    ->first(); // Use 'first' to retrieve a single result

                if ($statistics) {
                    $prices[$region->region] = $statistics->price;
                }
            }

            // You can add a default value for 'Kurunegala' here if needed.

            return response($prices, 200);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Error in getAllRegions: ' . $e->getMessage());
            return response(['error' => 'An error occurred while fetching data.'], 500);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $values = [];
        $validations = [];
        $permissions = [];
        $validator = null;
        $isValid = FALSE;
        $properties = Property::where('id', '>', 0);

        $values = $request->all();
        unset($values['_token']);

        foreach ($values as $k => $v) {
            switch ($k) {
                case "type":
                    $properties->where("propertyable_type", "like", "%${v}");
                    break;
                default:
                    if (!is_null($v)) {
                        $properties->where($k, '=', $v);
                    }
                    break;
            }
        }

        // Add validation rules
        $isValid = TRUE;

        if ($request->wantsJson()) {
            if (!$isValid) {
                return response(["errors" => $validator->errors()], 422);
            } else {
                $properties->with('propertyable');
                $properties->with(['statistics' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                }]);

                return response($properties->get(), 200);
            }
        } else {
            if (!$isValid) {
                return redirect()->back()->withErrors($validator);
            } else {
                // TODO: render view
                return view('properties.list', ["entities" => $properties->get()]);
            }
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $property = new Property();
        return view('properties.view', [
            'entity' => $property,
            'types' => $this->propertyTypes,
            'areas' => $this->propertyAreas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $property = new Property();

        $newValues = $request->all();
        unset($newValues['_token']);

        $this->updateEntity($newValues, $property);

        $property->save();

        return redirect()->route('properties.show', $property->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $property = Property::where('id', '=', $id)->first();

        return view('properties.view', [
            'entity' => $property,
            'types' => $this->propertyTypes,
            'areas' => $this->propertyAreas
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $property = Property::where('id', '=', $id)->first();

        $newValues = $request->all();
        unset($newValues['_token']);

        $this->updateEntity($newValues, $property);

        $property->save();

        return back()->withInput();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
