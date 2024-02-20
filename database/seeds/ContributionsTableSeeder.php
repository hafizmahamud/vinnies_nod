<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class ContributionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('import:contribution', [
            'path' => storage_path('import/contributions.csv'),
        ]);
    }
}
