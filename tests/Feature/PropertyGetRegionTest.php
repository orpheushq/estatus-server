<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Property;

class PropertyGetRegionTest extends TestCase
{
    private function getRegionAverage(string $region)
    {
        $properties = Property::select('id', 'area')
            ->where('area', '=', $region)
            ->with([ 'statistics' => function ($query) {
                $query
                    ->orderBy('created_at', 'desc')
                    ->limit(1);
            }])
            ->get();
        $sum = 0;
        $count = count($properties);
        foreach ($properties as $p)
        {
            $latestStat = $p->statistics()->latest()->first();
            $sum += $latestStat['price'];
        }
        return intval($sum/ $count);
    }
    /**
     * Read data from database and check if the explicit calculation result and
     * SQL result matches
     *
     * @return void
     */
    public function test_nawala()
    {
        $region = "nawala";

        $expectedAverage = $this->getRegionAverage($region);

        $response = $this->get("/api/region/{$region}");

        $response->assertStatus(200);
        $this->assertTrue($response['area'] === $region);
        $this->assertEquals($expectedAverage, intval($response['avgPrice']));
    }

    public function test_multiple()
    {
        $regions = ['piliyandala', 'ratmalana'];
        foreach ($regions as $r)
        {
            $region = $r;

            $expectedAverage = $this->getRegionAverage($region);

            $response = $this->get("/api/region/{$region}");

            $response->assertStatus(200);
            $this->assertTrue($response['area'] === $region);
            $this->assertEquals($expectedAverage, intval($response['avgPrice']));
        }
    }
}
