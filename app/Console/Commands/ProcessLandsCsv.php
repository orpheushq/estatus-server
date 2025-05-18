<?php

namespace App\Console\Commands;

use App\Classes\ProcLand;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessLandsCsv extends Command
{
    private $logPrefix = 'ProcessLandsCsv -';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:lands
        {filePath : The path of the CSV file to process }
        {source : Source of the CSV }
        {--D|dry : Dry run where Lands are not actually added to the DB}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process the lands from the CSV provided';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $filePath = $this->argument('filePath');
            $source = $this->argument('source');
            $dryRun = $this->option('dry');

            Log::channel("upload")->info("{$this->logPrefix} Land from source ${source} processing started for file ${filePath}" . ($dryRun ? ' (Dry run)' : ''));

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path("app/${filePath}"));
            $spreadsheet->setActiveSheetIndex(0); // INFO: process only first worksheet (which is fine as in CSV there is only 1 sheet)
            $sheet = $spreadsheet->getActiveSheet();
            $rowIterator = $sheet->getRowIterator();

            $rowIterator = $rowIterator->resetStart(2); // INFO: skip headings row

            $colMap = array(
                'A' => 'area',
                'C' => 'url',
                'D' => 'address',
                'E' => 'title',
                'F' => 'description',
                'I' => 'size',
                'J' => 'price',
                'K' => 'mapLink',
                'L' => 'raw_maplink',
                'N' => 'interest'
            );

            foreach ($rowIterator as $row) {
                if ($row->isEmpty()) { // Ignore empty rows
                    break;
                }

                $landRow = array();

                $columnIterator = $row->getCellIterator();
                foreach ($columnIterator as $cell) {
                    $colName = $cell->getColumn();

                    if (isset($colMap[$colName])) {
                        $landRow[$colMap[$colName]] = $cell->getValue();
                    }
                }

                try {
                    ProcLand::processSingleLand($landRow, $source, $dryRun);
                } catch (\Exception $e) {
                    Log::channel("upload")->error("{$this->logPrefix} Could not process entry with URL ".($landRow['url'] ?? '[no url]')." Error: {$e->getMessage()}");
                }
            }


            if (!$dryRun) {
                // INFO: we can't get dry run results anyway because DB is not updated for the processor to calculate medians!
                Log::channel("upload")->notice("{$this->logPrefix} Invoking Median processor");
                $this->call('region:calculateMedians');
                Log::channel("upload")->notice("{$this->logPrefix} Invoking Interest processor");
                $this->call('region:calculateInterest');
            }


            Log::channel("upload")->notice("{$this->logPrefix} Finished processing");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->info("ERROR: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
