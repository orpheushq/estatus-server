<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller
{
    private $propertyTypes = [];
    private $propertyAreas = [];

    public function __construct()
    {
        $this->propertyTypes = (new Property())->getTypes();
        $this->propertyAreas = (new Property())->getAreas();
    }

    private function updateEntity ($newValues, $entity)
    {
        foreach ($newValues as $k => $v) {
            if (!is_null($v)) {
                switch ($k) {
                    case "type": {
                        $entity[$k] = strtolower($v);
                        break;
                    }
                    case "latitude": {
                        $entity['location'] = new Point($newValues['latitude'], $newValues['longitude']);
                        break;
                    }
                    case "longitude": {
                        // handled in a different case
                        break;
                    }
                    default: {
                        $entity[$k] = $v;
                    }
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
            'types' => array_map(fn($v) => [strtolower(str_getcsv($v, "\\\\")[2]) => ucfirst(str_getcsv($v, "\\\\")[2])], $this->propertyTypes),
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

    /**
     * Returns the average price per region using the latest statistic
     */
    public function getRegion(Request $request, string $region, $type = 'land')
    {
//        DB::enableQueryLog();
        /* Solution 4 https://learnsql.com/blog/sql-join-only-first-row/*/
        $sanitizedRegion = strip_tags($region);
        $sanitizedType = strip_tags($type);


//        dd(DB::getQueryLog());
        return response([
            'area' => $sanitizedRegion,
            'avgPrice' => $this->getRegionAverage($sanitizedRegion, $sanitizedType)
        ], 200);
    }

    public function getAllRegions(Request $request, $type = 'land')
    {
        $sanitizedType = strip_tags($type);

        $averages = [];
        $properties = Property::distinct(['area']);

        $areas = $properties->get(['area']);
        foreach ($areas as $a) {
            $thisAverage = $this->getRegionAverage($a->area, $sanitizedType);
            if (!is_null($thisAverage)) {
                $averages[$a->area] = $thisAverage;
            }
        }
        return response($averages, 200);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
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
                case "type": {
                    $properties->where("propertyable_type", "like", "%${v}");
                    break;
                }
                default: {
                    if (!is_null($v)) {
                        $properties->where($k, '=', $v);
                    }
                    break;
                }
            }
        }

        // Add validation rules
        $isValid = TRUE;

        if ($request->wantsJson()) {
            if (!$isValid) {
                return response([ "errors" => $validator->errors() ], 422);
            } else {
                $properties->with('propertyable');
                $properties->with([ 'statistics' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                } ]);

//                $processedProperties = $properties->get()->map(function ($p) {
//                    return $p->statistics()->first();
//                });

                return response($properties->get(), 200);
            }
        } else {
            if (!$isValid) {
                return redirect()->back()->withErrors($validator);
            } else {
                // TODO: render view
                return view('properties.list', [ "entities" => $properties->get()]);
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
        //
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
        //
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
        //
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
        //
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
