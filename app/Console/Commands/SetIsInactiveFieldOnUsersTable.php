<?php

namespace App\Console\Commands;

use App\Activity;
use App\LocalConference;
use App\User;
use DateTime;
use Illuminate\Console\Command;

class SetIsInactiveFieldOnUsersTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:set_is_inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set is_active field in users table based on deleted_at field';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::withTrashed()->get();
        foreach($users as $user){
            if($user->deleted_at){
                $user->is_active = false;
                $this->warn('inactive');
            }else{
                $user->is_active = true;
                $this->info('active');
            }
            // $user->deleted_at = null;
            $user->save();
        }
        return Command::SUCCESS;
    }
}
