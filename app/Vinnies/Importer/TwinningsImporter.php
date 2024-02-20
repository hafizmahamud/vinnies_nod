<?php

namespace App\Vinnies\Importer;

use App\Twinning;
use App\Vinnies\Helper;
use App\LocalConference;
use App\OverseasConference;

class TwinningsImporter extends BaseImporter
{
    public function import()
    {
        $this->log->info('START');
        $this->log->info('Metadata', [
            'headers' => $this->getHeaders(),
            'records' => count($this->getData()),
        ]);

        $this->getData()->each(function ($row) {
            if (empty($row['Local_To_Overseas_Twinnings::pk_local_to_overseas_twinnings_ID'])) {
                return;
            }

            $twinning = Twinning::firstOrNew(['id' => $row['Local_To_Overseas_Twinnings::pk_local_to_overseas_twinnings_ID']]);

            $twinning->id                     = $row['Local_To_Overseas_Twinnings::pk_local_to_overseas_twinnings_ID'];
            $twinning->local_conference_id    = $row['Local_To_Overseas_Twinnings::fk_localconference_ID'];
            $twinning->overseas_conference_id = $row['Local_To_Overseas_Twinnings::fk_overseasconference_ID'];
            $twinning->is_active              = true;
            $twinning->comments               = '';

            if (!LocalConference::find($twinning->local_conference_id)) {
                $twinning->local_conference_id = null;

                $this->log->warning('Missing Australian Conference', ['row' => $row]);
            }

            if (!OverseasConference::find($twinning->overseas_conference_id)) {
                $twinning->overseas_conference_id = null;

                $this->log->warning('Missing Overseas Conference', ['row' => $row]);
            }

            $this->save($twinning, $row);
        });

        $this->log->info('END');

        return $this->result;
    }
}
