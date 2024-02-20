<?php

namespace App\Console\Commands;

use App\Activity;
use DateTime;
use Illuminate\Console\Command;

class DeleteUnchangedLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:delete_unchanged';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete unchanged log activity';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $activities = Activity::all();

        foreach($activities as $activity){            
            if(isset($activity->properties['old']) && isset($activity->properties['attributes'])){
                if($activity->properties['attributes'] == $activity->properties['old']){
                    $activity->delete();
                    $this->info('Log id ' . $activity->id . ' is unchanged, sucessfully deleted.');
                }else{
                    $length = count($activity->properties['attributes']);

                    $array_key = array_keys($activity->properties['attributes']);
                    $array_attributes = array_values($activity->properties['attributes']);
                    $array_old = array_values($activity->properties['old']);

                    $new_attributes = [];
                    $new_old = [];

                    for ($x = 0; $x < $length; $x++) {
                        if($array_attributes[$x] != $array_old[$x]){
                            if($array_key[$x] == 'password'){
                                continue;
                            }
                            if(in_array($array_key[$x], array('received_at', 'completed_at', 'estimated_completed_at', 'project_completion_date', 'is_abeyant_at', 'last_confirmed_at', 'is_active_at', 'is_active_at', 'is_abeyant_at', 'twinned_at', 'untwinned_at', 'surrendering_initiated_at', 'surrendering_deadline_at', 'status_check_initiated_at', 'confirmed_date_at', 'is_active_at', 'is_surrendered_at', 'date', 'conditions_accepted_at'))){
                                $diff = date_diff(new DateTime($array_attributes[$x]), new DateTime($array_old[$x]));
                                if ($diff->format('%a') === '0') {
                                    continue;
                                }
                            }
                            $new_attributes[$array_key[$x]] = $array_attributes[$x];
                            $new_old[$array_key[$x]] = $array_old[$x];
                        }
                    }

                    $new_properties = [];
                    $new_properties['attributes'] = $new_attributes;
                    $new_properties['old'] = $new_old;
                    $json_new_properties = json_encode($new_properties);

                    if($activity->properties['attributes'] == $activity->properties['old']){
                        $activity->delete();
                        $this->info('Log id ' . $activity->id . ' is unchanged, sucessfully deleted.');
                    }else if($activity->properties != $json_new_properties){
                        $activity->update([
                            'properties'     => $new_properties,
                        ]);
                        $this->info('Log id ' . $activity->id . ' is contains unchanged field, the properties sucessfully updated.');
                    }
                }
            }
        }

        return Command::SUCCESS;
    }
}
