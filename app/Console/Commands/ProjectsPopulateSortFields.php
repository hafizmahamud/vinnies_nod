<?php

namespace App\Console\Commands;

use App\Project;
use Illuminate\Console\Command;

class ProjectsPopulateSortFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'projects:populate-sort-fields';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate related sort fields for all projects';

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
        Project::all()->each(function ($project) {
            $this->info('Processing Project ID: ' . $project->id);
            $project->updateSortFields();
        });
    }
}
