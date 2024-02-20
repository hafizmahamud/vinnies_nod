<?php

use App\Country;
use Illuminate\Database\Seeder;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $countries = [
            'Bangladesh',
            'Cambodia',
            'ECI',
            'East Timor',
            'Fiji',
            'Sri Lanka',
            'India',
            'Indonesia',
            'Jerusalem',
            'Kiribati',
            'Myanmar',
            'Pakistan',
            'Philippines',
            'PNG',
            'Solomon Islands',
            'Thailand',
            'Vanuatu',
        ];

        foreach ($countries as $country) {
            Country::create(['name' => $country]);
        }
    }
}
