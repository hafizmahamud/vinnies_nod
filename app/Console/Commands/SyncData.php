<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class SyncData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync all data from predefined CSV files in storage folder';

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
        $this->info('Syncing Australian conferences...');
        DB::table('local_conferences')->truncate();
        Artisan::call('import:local-conf', ['path' => storage_path('import/local-conferences.csv')]);

        $this->info('Syncing overseas conferences...');
        DB::table('overseas_conferences')->truncate();
        Artisan::call('import:os-conf', ['path' => storage_path('import/overseas-conferences.csv')]);

        $this->info('Syncing beneficiaries...');
        DB::table('beneficiaries')->truncate();
        Artisan::call('import:beneficiary', ['path' => storage_path('import/beneficiaries.csv')]);

        $this->info('Syncing projects...');
        DB::table('projects')->truncate();
        Artisan::call('import:project', ['path' => storage_path('import/projects.csv')]);

        $this->info('Syncing twinnings...');
        DB::table('twinnings')->truncate();
        Artisan::call('import:twinning', ['path' => storage_path('import/twinnings.csv')]);

        $this->info('Syncing donors...');
        DB::table('donors')->truncate();
        Artisan::call('import:donor', ['path' => storage_path('import/donors.csv')]);

        $this->info('Syncing contributions...');
        DB::table('contributions')->truncate();
        Artisan::call('import:contribution', ['path' => storage_path('import/contributions.csv')]);

        $this->info('Syncing old remittances...');
        DB::table('old_remittances')->truncate();
        Artisan::call('import:old-remittance', ['path' => storage_path('import/old-remittances.csv')]);

        $this->info('Syncing old donations...');
        DB::table('old_donations')->truncate();
        Artisan::call('import:old-donation', ['path' => storage_path('import/old-donations.csv')]);

        $this->info('Removing new remittances...');
        DB::table('new_remittances')->truncate();
        DB::table('project_donations')->truncate();
        DB::table('grant_donations')->truncate();
        DB::table('twinning_donations')->truncate();
        DB::table('council_donations')->truncate();

        $this->info('Removing documents...');
        DB::table('documents')->truncate();
    }
}
