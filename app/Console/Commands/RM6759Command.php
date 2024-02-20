<?php

namespace App\Console\Commands;

use App\Donor;
use App\Project;
use App\Contribution;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;

class RM6759Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rm:6759';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Forcefully set balance owing to 0 for all paid projects';

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
        Project::where('is_fully_paid', 1)->get()->each(function ($project) {
            if ($project->getBalanceOwing()->value() >= 1) {
                $this->info('Updating project ID: ' . $project->id);

                $donor = Donor::create([
                    'local_conference_id' => env('SYSTEM_DONOR_ID'),
                    'project_id'          => $project->id,
                ]);

                $paid_at      = Carbon::now();
                $contribution = Contribution::create([
                    'donor_id' => $donor->id,
                    'paid_at'  => $paid_at,
                    'quarter'  => $paid_at->quarter,
                    'year'     => $paid_at->year,
                    'amount'   => $project->getBalanceOwing()->value(),
                ]);

                $project->save();
                $project->updatePaymentStatus($paid_at)->save();
                $project->updateSortFields();
            }
        });
    }
}
