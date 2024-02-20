<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class OverseasConferencesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('import:os-conf', [
            'path' => storage_path('import/overseas-conferences.csv'),
        ]);
    }
}
