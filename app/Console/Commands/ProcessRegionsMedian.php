<?php

namespace App\Console\Commands;

use App\Models\Property;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessRegionsMedian extends Command
{
    private $logPrefix = 'ProcessRegionsMedian -';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'region:calculateMedians
        {minDate? : The minimum date by which properties should have been updated by to qualify }
        {--D|dry : Dry run where DB is not actually updated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Calculate medians for regions and create new regions if required.\n  " .
    "IMPORTANT: User should ensure data integrity. For the given date, there can be only one statistic ".
    "If a date is provided, there should be no dates more recent than the date because multiple properties might show";


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $minDate = $this->argument('minDate');
        $dryRun = $this->option('dry');
        $type = 'Land';


        try {
            $date = is_null($minDate) ? new \DateTime() : new \DateTime($minDate);
            Log::channel("cli")->info("{$this->logPrefix} START for date {$date->format('Y-m-d')}");

            $regionsToUpdate = Property
                ::select("area", DB::raw("COUNT(id) as regionCount"))
                ->where("propertyable_type", "like", "%${type}")
                ->whereBetween('updated_at', [
                    $date->format('Y-m-d') . ' 00:00:00',
                    $date->format('Y-m-d') . ' 23:59:59']
                )
                ->groupBy("area")
                ->orderBy("area")
                ->lazy();

            foreach ($regionsToUpdate as $regionItem) {
                $regionCount = intval($regionItem['regionCount']);

                $median = 0;

                if ($regionCount % 2 === 0) {
                    // INFO: even number, median is the average of two properties
                    Log::channel("cli")->info("{$this->logPrefix} CALCULATED for ${regionItem["area"]}, median is the average ${median}");
                } else {
                    // INFO: odd number so median is the property at the number
//                    $thisProperty = Property
//                        ::with([
//                            'statistics' => function (HasMany $query) {
//                                $query->latest()->first();
//                            }
//                        ])->first()
//                        ->where("propertyable_type", "like", "%${type}")
//                        ->where('updated_at', '>', $date->format('Y-m-d'))
//                        ->orderBy('statistics.price', 'asc')
//                        ->get();
                    $medianResult = Property
                        ::select('statistics.price', 'properties.id', 'properties.title')
                        ->where("propertyable_type", "like", "%${type}")
                        ->where('properties.updated_at', '>', $date->format('Y-m-d'))
                        ->where('area', '=', $regionItem["area"])
                        ->join('statistics', function ($join) use ($date) {
                            /**
                             * INFO: for a given date, there can be only one statistic otherwise the join creates
                             * multiple entries per property because there are multiple statistics
                             *
                             * INFO: also make sure there can be only one statistic per day, even if admin uploaded
                             * multiple CSV for the same property on the same day
                             *
                             */
                            $join->on('properties.id', '=', 'statistics.property_id');
                            $join->on('statistics.created_at', '>', DB::raw("'" . $date->format('Y-m-d') . " 00:00:00'"));
                            $join->on('statistics.created_at', '<', DB::raw("'" . $date->format('Y-m-d') . " 23:59:59'"));
                        })
                        ->orderBy('statistics.price', 'asc')
                        ->offset(($regionCount - 1) / 2) // INFO: get the 'middle' index
                        ->limit(1)
                        ->first();
                    Log::channel("cli")->info("{$this->logPrefix} CALCULATED for ${regionItem["area"]}, median is the exact ${median}");
                }
            }


            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->info("ERROR: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
