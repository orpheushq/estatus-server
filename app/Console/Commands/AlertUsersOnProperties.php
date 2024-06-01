<?php

namespace App\Console\Commands;

use App\Classes\SmsClient;
use App\Models\Property;
use App\Models\Statistic;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;

class AlertUsersOnProperties extends Command
{
    private $smsClient = null;

    public function __construct()
    {
        parent::__construct();
        $this->smsClient = new SmsClient();
    }

    private $logPrefix = 'AlertUsersOnProperties -';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alert:newProperties
        {minDate? : The minimum date by which properties should have been updated by to qualify }
        {--D|dry : Dry run where SMS are not actually sent instead logged to console}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send alerts to users for properties updated on or after the provided date';

    private function getSmsAlertString($alerts): string
    {
        if (count($alerts) === 1) {
            return "New " . ($alerts[0]['count'] === 1 ? 'property' : 'properties') . " of interest in " . ucfirst($alerts[0]['region']);
        } else {
            $regionList = array_map(fn($alert) => ucfirst($alert['region']), $alerts);
            $regionList[array_key_last($regionList)] = "and " . $regionList[array_key_last($regionList)];
            return "New properties of interest in " . join(", ", $regionList);
        }
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $minDate = $this->argument('minDate');
        $dryRun = $this->option('dry');
        $type = 'land';
        try {
            $date = is_null($minDate) ? new \DateTime() : new \DateTime($minDate);
            Log::channel("cli")->info("{$this->logPrefix} START for date {$date->format('Y-m-d')}");

            $usersWithAlerts = User::whereNotNull('alert_regions')->lazy();
            foreach ($usersWithAlerts as $user) {
                Log::channel("cli")->info("{$this->logPrefix} BEGIN alert processing for {$user->email}");

                $alertRegions = json_decode($user->alert_regions);
                $priceRange = [1, 800000]; // TODO: remove hardcoded test
                $alerts = array();
                foreach ($alertRegions as $region) {
                    $qualifyingPropertiesCount = Property
                        ::where("propertyable_type", "like", "%${type}")
                        ->where('area', '=', $region)
                        ->whereHas('statistics', function (Builder $query) use ($priceRange, $date) {
                            $query
                                ->whereBetween('price', $priceRange)
                                ->where('created_at', '>', $date->format('Y-m-d'));
                        })
                        ->count();

                    if ($qualifyingPropertiesCount > 0) {
                        $alerts[] = array("region" => $region, "count" => $qualifyingPropertiesCount);
                    }
                }
                Log::channel("cli")->info("{$this->logPrefix} END found " . count($alerts) . " alert(s) for {$user->email}");
                if (!is_null($user->phone_number) && count($alerts) > 0) {
                    if ($dryRun) {
                        $this->info($this->getSmsAlertString($alerts));
                    } else {
                        $this->smsClient->sendSms($user->phone_number, $this->getSmsAlertString($alerts));
                    }
                }
            }
            Log::channel("cli")->info("{$this->logPrefix} END");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->info("ERROR: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
