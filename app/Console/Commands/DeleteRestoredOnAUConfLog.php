<?php

namespace App\Console\Commands;

use App\Activity;
use App\LocalConference;
use DateTime;
use Illuminate\Console\Command;

class DeleteRestoredOnAUConfLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:delete_restored_AU_conf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete unexpected restored log activity on Australian Conf';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $localConfs = LocalConference::withTrashed()->pluck('id');
        foreach($localConfs as $localConf){
            
            $this->error($localConf);
            $localConfActivities = Activity::where('subject_type', 'App\LocalConference')->where('subject_id', $localConf)->get();

            $event = 'restored';

            foreach($localConfActivities as $localConfActivity){

                $this->info($localConfActivity->event);

                if($localConfActivity->event == $event){
                    $localConfActivity->delete();
                    $this->info('Data deleted');
                }else if($localConfActivity->event == 'restored' || $localConfActivity->event == 'deleted'){
                    $event = $localConfActivity->event;
                    $this->warn('Data not deleted');
                }
            }
        }
        return Command::SUCCESS;
    }
}
