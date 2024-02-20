<?php

namespace App\Console\Commands;

use App\LocalConference;
use Illuminate\Console\Command;

class LocalConferencesPopulateSortField extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'local-conf:populate-sort-field';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate related sort field for all Australian Conferences';

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
        LocalConference::withTrashed()->get()->each(function ($conference) {
            $this->info('Processing Australian Conference ID: ' . $conference->id);
            $conference->updateSortField();
        });
    }
}
