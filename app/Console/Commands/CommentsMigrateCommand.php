<?php

namespace App\Console\Commands;

use App\User;
use App\Project;
use App\Twinning;
use App\Beneficiary;
use App\NewRemittance;
use App\LocalConference;
use App\OverseasConference;
use Illuminate\Console\Command;

class CommentsMigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'comments:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate all comments to the dedicated table';

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
        $user = User::find(1);

        Beneficiary::withTrashed()->each(function ($beneficiary) use ($user) {
            if (!empty($beneficiary->comments)) {
                $this->info('Processing beneficiary ID: ' . $beneficiary->id);

                $beneficiary->commentAsUser($user, $beneficiary->comments);
                $beneficiary->update(['comments' => null]);
            }
        });

        LocalConference::withTrashed()->each(function ($local_conference) use ($user) {
            if (!empty($local_conference->comments)) {
                $this->info('Processing Australian conference ID: ' . $local_conference->id);

                $local_conference->commentAsUser($user, $local_conference->comments);
                $local_conference->update(['comments' => null]);
            }
        });

        OverseasConference::all()->each(function ($overseas_conference) use ($user) {
            if (!empty($overseas_conference->comments)) {
                $this->info('Processing overseas conference ID: ' . $overseas_conference->id);

                $overseas_conference->commentAsUser($user, $overseas_conference->comments);
                $overseas_conference->update(['comments' => null]);
            }
        });

        Project::all()->each(function ($project) use ($user) {
            if (!empty($project->comments)) {
                $this->info('Processing project ID: ' . $project->id);

                $project->commentAsUser($user, $project->comments);
                $project->update(['comments' => null]);
            }
        });

        Twinning::all()->each(function ($twinning) use ($user) {
            if (!empty($twinning->comments)) {
                $this->info('Processing twinning ID: ' . $twinning->id);

                $twinning->commentAsUser($user, $twinning->comments);
                $twinning->update(['comments' => null]);
            }
        });

        NewRemittance::all()->each(function ($new_remittance) use ($user) {
            if (!empty($new_remittance->comments)) {
                $this->info('Processing new remittance ID: ' . $new_remittance->id);

                $new_remittance->commentAsUser($user, $new_remittance->comments);
                $new_remittance->update(['comments' => null]);
            }
        });

        return 0;
    }
}
