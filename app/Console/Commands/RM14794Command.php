<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Document;

class RM14794Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rm:14794';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generic changes after deployment for Redmine issue 14794';

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
        $this->info('Updating Project type...');
        Document::where('type', 'Project Application')->update(['type' => 'project_application']);
        Document::where('type', 'Signed Cover Sheet')->update(['type' => 'signed_cover_sheet']);
        Document::where('type', 'Project Completion Report')->update(['type' => 'project_completion_report']);
        Document::where('type', 'Completion Report')->update(['type' => 'project_completion_report']);
        Document::where('type', 'Status Check Request')->update(['type' => 'status_check_request']);
        Document::where('type', 'Surrender Notification')->update(['type' => 'surrender_notification']);
        Document::where('type', 'Aggregation Certificate')->update(['type' => 'aggregation_certificate']);
        Document::where('type', 'Other')->update(['type' => 'other']);
        Document::where('type', 'Twinning Payments')->update(['type' => 'twinning_payments']);
        Document::where('type', 'Grants Payments')->update(['type' => 'grants_payments']);
        Document::where('type', 'Council to Council Payments')->update(['type' => 'council_to_council_payments']);
        Document::where('type', 'Project Payments')->update(['type' => 'project_payments']);
        Document::where('type', 'Correspondence')->update(['type' => 'correspondence']);

        $this->info('Done updates');
        return 0;
    }
}
