<?php

namespace App\Console\Commands;

use App\Mail\Day60Mail;
use App\Mail\Day90Mail;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Day60OfInactiveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:60-days-of-inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sent email on 60 days of inactivity';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        User::all()->filter(function ($user) {
            if ($user->last_login !== null && Carbon::now()->diffInDays($user->last_login) >= 60 && $user->is_active == 1 && $user->is_60_inactivity_mail_sent == 0) {
                return true;
            }

            if ($user->last_login === null && Carbon::now()->diffInDays($user->created_at) >= 60 && $user->is_active == 1 && $user->is_60_inactivity_mail_sent == 0) {
                return true;
            }

            return false;
        })->each(function ($user) {            
            Mail::to($user->email)->cc('overseassupport@svdp.org.au')->send(new Day60Mail());

            $user->update([
                'is_60_inactivity_mail_sent'    => 1
            ]);

            $this->info('Email sent to user ID: ' . $user->id);
        });

        return 0;
    }
}
