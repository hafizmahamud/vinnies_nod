<?php

namespace App\Vinnies\Importer;

use App\Donor;
use App\Contribution;
use App\Vinnies\Money;
use App\Vinnies\Helper;

class ContributionsImporter extends BaseImporter
{
    public function import()
    {
        $this->log->info('START');
        $this->log->info('Metadata', [
            'headers' => $this->getHeaders(),
            'records' => count($this->getData()),
        ]);

        $this->getData()->each(function ($row) {
            if (empty($row['Donor_Contributions.fromProjectDonors::pk_donorcontributions_ID'])) {
                return;
            }

            $contribution = Contribution::firstOrNew(['id' => $row['Donor_Contributions.fromProjectDonors::pk_donorcontributions_ID']]);
            $quarter      = $this->parseQuarter($row['Donor_Contributions.fromProjectDonors::c_donorcontributions_Quarter']);
            $donor = Donor::where('local_conference_id', $row['Donor_Contributions.fromProjectDonors::fk_donor_ID'])->where('project_id', $row['Donor_Contributions.fromProjectDonors::fk_project_ID'])->first();

            $contribution->id       = $row['Donor_Contributions.fromProjectDonors::pk_donorcontributions_ID'];
            $contribution->donor_id = optional($donor)->id;
            $contribution->paid_at  = $this->parseDate($row['Donor_Contributions.fromProjectDonors::donorcontributions_DatePaid']);
            $contribution->quarter  = $quarter['quarter'];
            $contribution->year     = $quarter['year'];
            $contribution->amount   = (new Money(Helper::formatDecimal($row['Donor_Contributions.fromProjectDonors::donorcontributions_AmountPaid'])))->value();

            if (!$donor) {
                $contribution->donor_id = null;

                $this->log->warning('Missing Donor', ['row' => $row]);
            }

            $this->save($contribution, $row);
        });

        $this->log->info('END');

        return $this->result;
    }
}
