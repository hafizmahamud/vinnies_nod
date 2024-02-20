<?php

namespace App\Vinnies\Importer;

use App\Project;
use App\Vinnies\Money;
use App\Vinnies\Helper;
use App\LocalConference;
use App\ProjectDonation;

class ProjectDonationsImporter extends RemittanceImporter
{
    public function import()
    {
        $this->log->info('START');
        $this->log->info('Metadata', [
            'headers' => $this->getHeaders(),
            'records' => count($this->getData()),
        ]);

        $this->invalidRows['project_donor_pair'] = [];
        $this->remittance = $this->remittance->fresh();

        $this->getData()->each(function ($row, $key) {
            $donation = new ProjectDonation;

            $donation->new_remittance_id   = $this->remittance->id;
            $donation->project_id          = $row['Project ID'];
            $donation->local_conference_id = $row['DONOR SRN'];
            $donation->document_id         = $this->document->id;
            $donation->amount              = (new Money(Helper::formatDecimal($row['DONOR CONTRIBUTION AMOUNT AUD'])))->value();

            if (!Project::find($donation->project_id)) {
                $donation->project_id = null;
                $this->invalidRows['projects'][$key] = $row;

                $this->log->warning('Missing Project', ['row' => $row]);
            }

            if (!LocalConference::find($donation->local_conference_id)) {
                $donation->local_conference_id = null;
                $this->invalidRows['donors'][$key] = $row;

                $this->log->warning('Missing Australian Conference', ['row' => $row]);
            }

            // Both are valid, now check for pairs
            if ($donation->project_id && $donation->local_conference_id) {
                $project = Project::find($donation->project_id);

                if (!$project->hasDonor($donation->local_conference_id)) {
                    $donation->project_id = null;
                    $donation->local_conference_id = null;

                    $this->invalidRows['project_donor_pair'][$key] = $row;

                    $this->log->warning('Invalid Project Donor Pair', ['row' => $row]);
                }
            }

            $this->save($donation, $row);
        });

        $this->log->info('END');

        return $this->result;
    }
}
