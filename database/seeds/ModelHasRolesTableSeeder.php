<?php

use App\User;
use Illuminate\Database\Seeder;

class ModelHasRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::whereEmail(env('ADMIN_EMAIL'))->first();
        $user->assignRole('Full Admin');
    }
}
