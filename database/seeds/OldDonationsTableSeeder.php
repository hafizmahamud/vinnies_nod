<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class OldDonationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('import:old-donation', [
            'path' => storage_path('import/old-donations.csv'),
        ]);
    }
}
