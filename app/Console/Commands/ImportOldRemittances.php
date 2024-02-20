<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Vinnies\Importer\OldRemittancesImporter;

class ImportOldRemittances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:old-remittance {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import old remittances from provided csv file path';

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
        $path  = $this->argument('path');
        $start = microtime(true);

        if (!file_exists($path)) {
            $this->error('File not exists');
            return;
        }

        $this->info('Importing old remittances from file: ' . basename($path));

        $result = (new OldRemittancesImporter($path))
            ->setLogger('import-old-remittances')
            ->import();

        $this->info(
            sprintf(
                '%d record(s) imported in %d second(s) (Failed: %d)',
                $result['success'],
                microtime(true) - $start,
                $result['failed']
            )
        );
    }
}
