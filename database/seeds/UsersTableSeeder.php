<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'first_name'     => env('ADMIN_FIRST_NAME'),
            'last_name'      => env('ADMIN_LAST_NAME'),
            'email'          => env('ADMIN_EMAIL'),
            'password'       => bcrypt(env('ADMIN_PASSWORD')),
            'states'         => 'national',
            'branch_display' => 'National Office'
        ]);
    }
}
