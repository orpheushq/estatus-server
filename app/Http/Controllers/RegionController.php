<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\RegionStatistic;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /**
         * TODO: currently region table has no type column. After adding this, add type handling as well
         */
        $type = $request->input('type');

        try {
            // Method 4 of https://laraveldaily.com/post/eloquent-hasmany-get-parent-latest-row-of-relationship
            $regions = Region
                ::addSelect([
                    'price' =>
                        RegionStatistic
                            ::select('price')
                            ->whereColumn('region_id', 'regions.id')
                            ->latest()
                            ->take(1)
                ])->get();

            $prices = [];

            foreach ($regions as $region) {
                $prices[$region->region] = $region->price;
            }

            return response($prices, 200);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Error in getAllRegions: ' . $e->getMessage());
            return response(['error' => 'An error occurred while fetching data.'], 500);
        }
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
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $region
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, string $region)
    {
        /**
         * TODO: currently region table has no type column. After adding this, add type handling as well
         */
        $type = $request->input('type');
        $regionData = Region
            ::where('region', '=', $region)
            ->with([
                'statistics' => function (HasMany $query) {
                    $query->latest()->first();
                }
            ])->first();
        return response($regionData, 200);
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
