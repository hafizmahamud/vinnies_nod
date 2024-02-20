<?php

namespace App\Console\Commands;

use App\OverseasConference;
use Illuminate\Console\Command;

class OverseasConferencesFixTwinningStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'os-conf:fix-twinning-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically populate twinning status for os conf based on predefined conditions';

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
     * @return mixed
     */
    public function handle()
    {
        OverseasConference::with('twinnings')->where('twinning_status', 'n/a')->get()->each(function ($conference) {
            $this->info('Checking overseas conference ID: ' . $conference->id);

            $active_twinnings = $conference->twinnings->filter(function ($twinning) {
                return $twinning->is_active;
            });

            $surrendered_twinnings = $conference->twinnings->filter(function ($twinning) {
                return !$twinning->is_active;
            });

            if ($active_twinnings->count() > 0) {
                $conference->twinning_status = 'twinned';
                $conference->save();
                $this->info('Updated twinning status to: twinned');
            }

            if ($conference->twinnings->count() > 0 && $conference->twinnings->count() == $surrendered_twinnings->count()) {
                $conference->twinning_status = 'untwinned';
                $conference->save();
                $this->info('Updated twinning status to: untwinned');
            }
        });
    }
}
