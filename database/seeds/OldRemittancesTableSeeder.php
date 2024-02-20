<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class OldRemittancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('import:old-remittance', [
            'path' => storage_path('import/old-remittances.csv'),
        ]);
    }
}
