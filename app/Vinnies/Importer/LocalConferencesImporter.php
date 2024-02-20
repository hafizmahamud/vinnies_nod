<?php

namespace App\Vinnies\Importer;

use App\Vinnies\Helper;
use App\DiocesanCouncil;
use App\LocalConference;

class LocalConferencesImporter extends BaseImporter
{
    public function import()
    {
        $this->log->info('START');
        $this->log->info('Metadata', [
            'headers' => $this->getHeaders(),
            'records' => count($this->getData()),
        ]);

        $known_diocesan_councils = collect(Helper::getDiocesanCouncils())->flatten()->all();

        $this->getData()->each(function ($row) use ($known_diocesan_councils) {
            $conf  = LocalConference::firstOrNew(['id' => $row['pk_localconferences_ID']]);
            $state = $this->determineState($row);

            if (!empty($row['localconferences_DioscesanCouncil']) && $state) {
                $diocesan_council_name = Helper::utf8_encode(
                    $this->fixDiocesanCouncilName($row['localconferences_DioscesanCouncil'])
                );

                $diocesan_council = DiocesanCouncil::firstOrCreate([
                    'name'     => $diocesan_council_name,
                    'state'    => $this->fixDiocesanCouncilState($diocesan_council_name, $state),
                    'is_valid' => in_array($diocesan_council_name, $known_diocesan_councils)
                ]);
            } else {
                $diocesan_council = null;
            }

            $conf->id                  = $row['pk_localconferences_ID'];
            $conf->name                = Helper::utf8_encode($row['localconferences_ConferenceName']);
            $conf->regional_council    = Helper::utf8_encode($row['localconferences_RegionalCouncil']);
            $conf->diocesan_council_id = optional($diocesan_council)->id;
            $conf->parish              = Helper::utf8_encode($row['ozconferences_Parish']);
            $conf->is_flagged          = (bool) $row['localconferences_MarkRecordFlag'];
            $conf->contact_name        = Helper::utf8_encode($row['localconferences_ContactName']);
            $conf->contact_email       = '';
            $conf->address_line_1      = Helper::utf8_encode($row['localconferences_Address_1']);
            $conf->address_line_2      = Helper::utf8_encode($row['localconferences_Address_2']);
            $conf->address_line_3      = Helper::utf8_encode($row['localconferences_Address_3']);
            $conf->suburb              = $row['localconferences_Suburb'];
            $conf->postcode            = $row['localconferences_PostCode'];
            $conf->state               = $state;
            $conf->comments            = Helper::utf8_encode($row['localconferences_Comments']);

            $this->save($conf, $row);
        });

        $this->log->info('END');

        return $this->result;
    }

    private function determineState($row)
    {
        if (!empty($row['localconferences_State_ID'])) {
            $state = $row['localconferences_State_ID'];
        } elseif (!empty($row['localconferences_State'])) {
            $state = $row['localconferences_State'];
        } else {
            $state = false;
        }

        if ($state) {
            $state = strtolower($state);

            if (in_array($state, array_keys(Helper::getStates()))) {
                return $state;
            }
        }

        return null;
    }

    private function fixDiocesanCouncilName($name)
    {
        $this->log->info($name);
        $replacements = [
            'ADELAIDE'              => 'Adelaide',
            'ARMIDALE'              => 'Armidale',
            'BALLARAT'              => 'Ballarat',
            'BATHURST'              => 'Bathurst',
            'BRISBANE NORTH'        => 'Brisbane',
            'BRISBANE NORTHERN'     => 'Brisbane',
            'BRISBANE SOUTH/CITY'   => 'Brisbane',
            'BRISBANE SOUTHERN'     => 'Brisbane',
            'BROKEN  BAY'           => 'Broken Bay',
            'BROKEN BAY'            => 'Broken Bay',
            'CAIRNS'                => 'Cairns',
            'Canberra and Goulburn' => 'Canberra and Goulburn',
            'CANB GOULBURN'         => 'Canberra and Goulburn',
            'CANBERRA GOULBURN'     => 'Canberra and Goulburn',
            'CANBERRA/GOULBURN'     => 'Canberra and Goulburn',
            'DARWIN'                => 'Darwin',
            'GOLD COAST'            => 'South Coast',
            'GOLD COAST CNTY'       => 'South Coast',
            'GOLD COAST COUNTRY'    => 'South Coast',
            'GOLD COAST COUNTY'     => 'South Coast',
            'LISMORE'               => 'Lismore',
            'Maitland-Newcastle'    => 'Maitland-Newcastle',
            'MAITLAND/NEWCASTLE'    => 'Maitland-Newcastle',
            'NORTH WESTERN'         => 'North Western',
            'NORTHER'               => 'Northern',
            'NORTHERN'              => 'Northern',
            'PARRAMATTA'            => 'Parramatta',
            'PORT PIRIE'            => 'Port Pirie',
            'ROCKHAMPTON'           => 'Rockhampton',
            'SALE'                  => 'Sale',
            'SANDHURST'             => 'Sandhurst',
            'SOUTHERN'              => 'Southern',
            'SYDNEY'                => 'Sydney',
            'SYDNEY ARCH'           => 'Sydney',
            'TOOWOOMBA'             => 'Toowooomba',
            'TOWNSVILLE'            => 'Townsville',
            'WAGGA WAGGA'           => 'Wagga Wagga',
            'WESTERN'               => 'Western',
            'WESTERN BRISBANE'      => 'Western Brisbane',
            'Wilcannia-Forbes'      => 'Wilcannia-Forbes',
            'WILCANNIA FORBE'       => 'Wilcannia-Forbes',
            'WILCANNIA FORBES'      => 'Wilcannia-Forbes',
            'WILCANNIA/FORBES'      => 'Wilcannia-Forbes',
            'WOLLONGONG'            => 'Wollongong'
        ];

        if (array_key_exists($name, $replacements)) {
            return $replacements[$name];
        }

        return ucwords(strtolower($name));
    }

    private function fixDiocesanCouncilState($name, $state)
    {
        $states = [
            'Canberra and Goulburn' => 'act',
            'Townsville'            => 'qld',
        ];

        if (array_key_exists($name, $states)) {
            return $states[$name];
        }

        return $state;
    }
}
