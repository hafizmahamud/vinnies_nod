<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class BeneficiariesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('import:beneficiary', [
            'path' => storage_path('import/beneficiaries.csv'),
        ]);
    }
}
