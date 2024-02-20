<?php

namespace App\Console\Commands;

use Artisan;
use Illuminate\Console\Command;

class PermissionReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reload permission table';

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
        $exitCode = Artisan::call('db:seed', [
            '--class' => 'RolesAndPermissionsSeeder'
        ]);
    }
}
