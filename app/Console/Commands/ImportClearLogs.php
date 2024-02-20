<?php

namespace App\Console\Commands;

use File;
use Illuminate\Console\Command;

class ImportClearLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:clear-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all import logs from storage';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $logs = File::glob(storage_path('logs/import-*.log'));

        File::delete($logs);

        $this->info('Deleted ' . count($logs) . ' logs.');
    }
}
