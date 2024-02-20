<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class LocalConferencesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('import:local-conf', [
            'path' => storage_path('import/local-conferences.csv'),
        ]);
    }
}
