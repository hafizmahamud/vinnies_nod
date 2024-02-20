<?php

namespace App\Vinnies\Importer;

use App\Twinning;
use App\Vinnies\Money;
use App\Vinnies\Helper;
use App\TwinningDonation;

class TwinningDonationsImporter extends RemittanceImporter
{
    public $invalidTwinningMsg = 'The Twinning type does not match the remittance file type.';

    public function import()
    {
        $this->log->info('START');
        $this->log->info('Metadata', [
            'headers' => $this->getHeaders(),
            'records' => count($this->getData()),
        ]);

        $this->remittance = $this->remittance->fresh();

        $this->getData()->each(function ($row, $key) {
            $donation = new TwinningDonation;

            $donation->new_remittance_id = $this->remittance->id;
            $donation->twinning_id       = $row['TWINNING ID'];
            $donation->document_id       = $this->document->id;
            $donation->amount            = (new Money(Helper::formatDecimal($row['PAYMENT AMOUNT AUD'])))->value();

            $twinning = Twinning::where('id', $donation->twinning_id)
                ->where('type', 'standard')
                ->where('local_conference_id', $row['AUS CONF SRN'])
                ->where('overseas_conference_id', $row['OS CONF SRN'])
                ->get();

            if ($twinning->isEmpty()) {
                $donation->twinning_id = null;
                $this->invalidRows['twinnings'][$key] = $row;

                $this->log->warning('Missing Twinning', ['row' => $row]);
            }

            $this->save($donation, $row);
        });

        $this->log->info('END');

        return $this->result;
    }

    public function isValidType()
    {
        $types = collect(
            $this->csv->fetchColumn(
                $this->index('TWINNING TYPE')
            )
        )->filter()->unique();

        // Must contain single type only
        if ($types->count() > 1) {
            return false;
        }

        return strtolower($types->first()) === 'standard twinning';
    }
}
