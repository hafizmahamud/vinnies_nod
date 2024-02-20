<?php

namespace App\Console\Commands;

use App\Country;
use App\Project;
use App\Twinning;
use App\Beneficiary;
use App\OverseasConference;
use Illuminate\Console\Command;

class PurgeCountry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purge:country {country}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove specified country from database and all its associated data.';

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
        $country = Country::where('name', $this->argument('country'))->first();

        if (!$country) {
            $this->error('Invalid country specified');
            return;
        }

        // Remove associated twinning
        $twinnings = Twinning::whereHas('overseasConference', function ($query) use ($country) {
            $query->where('country_id', $country->id);
        })->get();

        $twinnings->each(function($twinning) {
            $this->info('Deleting Twinning ID: ' . $twinning->id);
            $twinning->delete();
        });

        // Remove associated projects
        $projects = Project::all();

        $projects->each(function($project) use ($country) {
            if ($project->hasOverseasConference()) {
                if ($project->overseasConference->country->id == $country->id) {
                    $this->info('Deleting Project ID: ' . $project->id);
                    $project->delete();
                }
            }
        });

        // Remove associated overseas conferences
        $conferences = OverseasConference::where('country_id', $country->id)->get();

        $conferences->each(function($conference) {
            $this->info('Deleting Overseas Conference ID: ' . $conference->id);
            $conference->delete();
        });

        // Remove associated beneficiary
        $beneficiaries = Beneficiary::where('country_id', $country->id)->get();

        $beneficiaries->each(function($beneficiary) {
            $this->info('Deleting Beneficiary ID: ' . $beneficiary->id);
            $beneficiary->forceDelete();
        });

        // Delete from countries table
        $this->info('Deleting Country ID: ' . $country->id);
        $country->delete();
    }
}
