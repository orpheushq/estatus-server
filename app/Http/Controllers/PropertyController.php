<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\RegionStatistic;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    public function getUniqueAddresses(Request $request)
    {
        $region = $request->get('region');
        $properties = Property::where('area', '=', $region)->select('address')->distinct()->get();
        return response(
            array_values(
                array_filter(
                    array_map(fn($property): string | null => $property['address'], $properties->toArray()),
                    fn($address): string => !is_null($address)
                )
            )
        );
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

//    private function getRegionAverage($region, $type)
//    {
//        $properties = Property::where('area', '=', $region)
//            ->where("propertyable_type", "like", "%{$type}");
//        $properties->join('statistics', function ($join) {
//            $join
//                ->on('statistics.id', '=', DB::raw("
//                        (SELECT id from `statistics`
//                        WHERE property_id=properties.id
//                        ORDER BY id DESC LIMIT 1)
//                    "));
//        });
//        return $properties->avg('price');
//    }

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
                case "onlyLatestResults":
                    break;
                default:
                    if (!is_null($v)) {
                        $properties->where($k, '=', $v);
                    }
                    break;
            }
        }

        if (isset($values['onlyLatestResults'])) {
//            TODO: current approach works only for the present (latest) week. Modify to work with any date provided to the API
            /**
             * Things to check:
             * [x] When the region has been updated during the current week, only the up-to-date properties should be fetched
             * [x] When the region has not been updated during the current week, the properties of that region that were last updated (i.e. property has same `updated_at` date as the region) should be fetched
             */

            $currentDate = (new \DateTime())->format('Y-m-d');
            // Get the start of the week (Sunday)
            $startOfWeekDay = date_create_from_format('Y-m-d', date('Y-m-d', strtotime('last Sunday', strtotime($currentDate))));
            // Get the end of the week (Saturday)
            $endOfWeekDay = date_create_from_format('Y-m-d', date('Y-m-d', strtotime('next Sunday', $startOfWeekDay->getTimestamp())));

            $properties->where(function ($query) use ($startOfWeekDay, $endOfWeekDay, $values) {
                $query->whereBetween('updated_at', [$startOfWeekDay, $endOfWeekDay]);

                if (isset($values['area'])) {
                    $region = Region::where('region', '=', $values['area'])->first();
                    if (!is_null($region)) {
                        $lastUpdatedDate = date_create_from_format('Y-m-d H:i:s', $region['updated_at']);

                        if ($startOfWeekDay > $lastUpdatedDate) {
                            // INFO: Region was last updated before the week period
                            $query->orWhereBetween('updated_at', ["{$lastUpdatedDate->format('Y-m-d')} 00:00:00", "{$lastUpdatedDate->format('Y-m-d')} 23:59:59"]);
                        }
                    }
                }

            });
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
