<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DonorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('import:donor', [
            'path' => storage_path('import/donors.csv'),
        ]);
    }
}
