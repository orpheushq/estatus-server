<?php

namespace App\Console\Commands;

use App\Models\Property;
use App\Models\Region;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessRegionsInterest extends Command
{
    private $logPrefix = 'ProcessRegionsInterest -';
    private $defaultLogChannel = 'cli'; // INFO: set to `stderr` for terminal output. Set to `cli` for production logging to cli.log

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'region:calculateInterest
        {minDate? : The minimum date by which properties should have been updated by to qualify }
        {--D|dry : Dry run where DB is not actually updated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Calculate interest for regions. Does NOT create new regions.\n  " .
        "IMPORTANT: User should ensure data integrity. For the given date, there can be only one statistic " .
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
            Log::channel($this->defaultLogChannel)->info("{$this->logPrefix} START for date {$date->format('Y-m-d')}");

            $regionsToUpdate = Property
                ::select("area", DB::raw("COUNT(id) as regionCount"))
                ->where("propertyable_type", "like", "%${type}")
                ->whereBetween(
                    'updated_at',
                    [
                        $date->format('Y-m-d') . ' 00:00:00',
                        $date->format('Y-m-d') . ' 23:59:59'
                    ]
                )
                ->groupBy("area")
                ->orderBy("area")
                ->lazy();

            foreach ($regionsToUpdate as $regionItem) {
                $interestResult = Property
                    ::select(
                        DB::raw('SUM(statistics.interest) as total_interest'),
                        DB::raw('COUNT(properties.id) as property_count')
                    )
                    ->where("propertyable_type", "like", "%${type}")
                    ->whereBetween(
                        'properties.updated_at',
                        [
                            $date->format('Y-m-d') . ' 00:00:00',
                            $date->format('Y-m-d') . ' 23:59:59'
                        ]
                    )
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
                    ->first();

                $normalizedInterest = $interestResult->property_count === 0 ? 0 : $interestResult->total_interest / $interestResult->property_count;
                
                Log::channel($this->defaultLogChannel)->info("{$this->logPrefix} {$regionItem["area"]} with {$interestResult->property_count} propert".($interestResult->property_count === 1 ? "y": "ies").", total interest {$interestResult->total_interest} and normalized interest {$normalizedInterest} (per property)");

                $thisRegion = Region::where('region', '=', $regionItem["area"])->first();

                // INFO: we don't create new regions here. This should not be a problem as regions will most likely be created during `region:calculateMedians`
                
                $currentDate = date('Y-m-d');
                $hasStatistics = $thisRegion->statistics()->whereDate('updated_at', $currentDate)->exists();
                if ($hasStatistics) {
                    $statistics = $thisRegion->statistics()->whereDate('updated_at', $currentDate)->first();
                    if (!$dryRun) {
                        $statistics->update(['interest' => $normalizedInterest]);
                        Log::channel($this->defaultLogChannel)->info("{$this->logPrefix} UPDATED interest statistic");
                    }
                } else {
                    Log::channel($this->defaultLogChannel)->warning("{$this->logPrefix} no existing statistic to update interest on for {$regionItem["area"]} on {$currentDate}");
                };
            }


            Log::channel($this->defaultLogChannel)->info("{$this->logPrefix} END for date {$date->format('Y-m-d')}");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->info("ERROR: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
