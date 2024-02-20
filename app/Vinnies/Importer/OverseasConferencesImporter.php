<?php

namespace App\Vinnies\Importer;

use App\Country;
use App\Vinnies\Helper;
use App\OverseasConference;

class OverseasConferencesImporter extends BaseImporter
{
    public function import()
    {
        $this->log->info('START');
        $this->log->info('Metadata', [
            'headers' => $this->getHeaders(),
            'records' => count($this->getData()),
        ]);

        $this->getData()->each(function ($row) {
            if (empty($row['overseasconference_ConferenceName'])) {
                return;
            }

            $conf = OverseasConference::firstOrNew(['id' => $row['pk_overseasconference_ID']]);

            if (!empty($row['overseasconference_Conference_Country'])) {
                $country_name = Helper::utf8_encode($row['overseasconference_Conference_Country']);
                $country_name = ucwords(strtolower($country_name));

                $country = Country::firstOrCreate(['name' => $country_name]);
            }

            $conf->id                 = $row['pk_overseasconference_ID'];
            $conf->name               = Helper::utf8_encode($row['overseasconference_ConferenceName']);
            $conf->central_council    = Helper::utf8_encode($row['overseasconference_Conference_CentralCouncil']);
            $conf->particular_council = Helper::utf8_encode($row['overseasconference_Conference_ParticularCouncil']);
            $conf->parish             = Helper::utf8_encode($row['overseasconference_ConferenceParish']);
            $conf->is_active          = (strtolower($row['overseasconference_Conference_Active_Flag']) === 'active');
            $conf->contact_name       = Helper::utf8_encode($row['overseasconference_ConferenceContactName']);
            $conf->contact_email      = '';
            $conf->address_line_1     = Helper::utf8_encode($row['overseasconference_Conference_Address_1']);
            $conf->address_line_2     = Helper::utf8_encode($row['overseasconference_Conference_Address_2']);
            $conf->address_line_3     = Helper::utf8_encode($row['overseasconference_Conference_Address_3']);
            $conf->suburb             = Helper::utf8_encode($row['overseasconference_Conference_Suburb']);
            $conf->postcode           = $row['overseasconference_Conference_PostCode'];
            $conf->state              = Helper::utf8_encode($row['overseasconference_Conference_State']);
            $conf->country_id         = !empty($country) ? $country->id : null;
            $conf->comments           = Helper::utf8_encode($row['overseasconference_Comments']);
            $conf->twinned_at         = $this->parseDate($row['overseasconference_DateTwinned']);
            $conf->untwinned_at       = $this->parseDate($row['overseasconference_DateUnTwinned']);

            $this->save($conf, $row);
        });

        $this->log->info('END');

        return $this->result;
    }
}
