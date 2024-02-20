<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(ModelHasRolesTableSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(LocalConferencesTableSeeder::class);
        $this->call(OverseasConferencesTableSeeder::class);
        $this->call(BeneficiariesTableSeeder::class);
        $this->call(ProjectsTableSeeder::class);
        $this->call(TwinningsTableSeeder::class);
        $this->call(DonorsTableSeeder::class);
        $this->call(ContributionsTableSeeder::class);
        $this->call(OldRemittancesTableSeeder::class);
        $this->call(OldDonationsTableSeeder::class);
    }
}
