<?php

namespace App\Classes;
/*
 * INFO: https://dev.to/faisalshaikh8433/run-artisan-command-instantly-in-the-background-using-symfony-process-component-33b5
 * */

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class CliProcess
{
    /**
     * @param string $command - artisan command without the artisan keyword
     * @param array $params - a string array of options that should be appended
     * @return void
     */
    public static function startBgProcess(string $command, array $params): void
    {
        $logPrefix = 'CliProcess -';

        $phpBinaryFinder = new PhpExecutableFinder();

        $phpBinaryPath = $phpBinaryFinder->find();
        $process = new Process ([$phpBinaryPath, base_path('artisan'), $command, ...$params]);

        Log::channel("cli")->info("{$logPrefix} BEGIN background process {$command}");
        $process->setoptions(['create_new_console' => true]); //Run process in background
        $process->start();
    }
}
