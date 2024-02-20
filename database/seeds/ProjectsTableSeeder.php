<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class ProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('import:project', [
            'path' => storage_path('import/projects.csv'),
        ]);
    }
}
