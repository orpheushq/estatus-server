<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AlertUsersOnProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alert:newProperties {minDate : The minimum date by which properties should have been updated by to qualify }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send alerts to users for properties updated on or after the provided date';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $minDate = $this->argument('minDate');
        try {
            $date = new \DateTime($minDate);
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->info($e->getMessage());
            return Command::FAILURE;
        }
    }
}
