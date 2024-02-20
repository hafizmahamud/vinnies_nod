<?php

namespace App\Vinnies\Importer;

use App\Donor;
use App\Project;
use App\Vinnies\Helper;
use App\LocalConference;

class DonorsImporter extends BaseImporter
{
    public function import()
    {
        $this->log->info('START');
        $this->log->info('Metadata', [
            'headers' => $this->getHeaders(),
            'records' => count($this->getData()),
        ]);

        $this->getData()->each(function ($row) {
            $donor = Donor::firstOrNew(['id' => $row['Project_Donors::pk_projectdonors_ID']]);

            $donor->id                  = $row['Project_Donors::pk_projectdonors_ID'];
            $donor->project_id          = $row['Project_Donors::fk_project_ID'];
            $donor->local_conference_id = $row['Project_Donors::fk_localconference_ID'];

            if (!Project::find($donor->project_id)) {
                $donor->project_id = null;

                $this->log->warning('Missing Project', ['row' => $row]);
            }

            if (!LocalConference::find($donor->local_conference_id)) {
                $donor->local_conference_id = null;

                $this->log->warning('Missing Australian Conference', ['row' => $row]);
            }

            $this->save($donor, $row);
        });

        $this->log->info('END');

        return $this->result;
    }
}
