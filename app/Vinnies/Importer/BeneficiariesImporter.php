<?php

namespace App\Vinnies\Importer;

use App\Country;
use App\Beneficiary;
use App\Vinnies\Helper;

class BeneficiariesImporter extends BaseImporter
{
    public function import()
    {
        $this->log->info('START');
        $this->log->info('Metadata', [
            'headers' => $this->getHeaders(),
            'records' => count($this->getData()),
        ]);

        $this->getData()->each(function ($row) {
            $beneficiary = Beneficiary::firstOrNew(['id' => $row['pk_beneficiary_ID']]);

            if (!empty($row['beneficiary_OrganisationCountry'])) {
                $country_name = Helper::utf8_encode($row['beneficiary_OrganisationCountry']);
                $country_name = ucwords(strtolower($country_name));

                $country = Country::firstOrCreate(['name' => $country_name]);
            }

            $beneficiary->id                     = $row['pk_beneficiary_ID'];
            $beneficiary->name                   = Helper::utf8_encode($row['beneficiary_OrganisationName']);
            $beneficiary->contact_title          = Helper::utf8_encode($row['beneficiary_ContactTitle']);
            $beneficiary->contact_first_name     = Helper::utf8_encode($row['beneficiary_ContactNameFirst']);
            $beneficiary->contact_last_name      = Helper::utf8_encode($row['beneficiary_ContactNameLast']);
            $beneficiary->contact_preferred_name = Helper::utf8_encode($row['beneficiary_ContactNamePreferred']);
            $beneficiary->comments               = Helper::utf8_encode($row['beneficiary_Comments']);
            $beneficiary->address_line_1         = Helper::utf8_encode($row['beneficiary_OrganisationAddress_1']);
            $beneficiary->address_line_2         = Helper::utf8_encode($row['beneficiary_OrganisationAddress_2']);
            $beneficiary->address_line_3         = Helper::utf8_encode($row['beneficiary_OrganisationAddress_3']);
            $beneficiary->suburb                 = Helper::utf8_encode($row['beneficiary_OrganisationSuburb']);
            $beneficiary->postcode               = $row['beneficiary_OrganisationPostCode'];
            $beneficiary->state                  = $row['beneficiary_OrganisationState'];
            $beneficiary->country_id             = !empty($country) ? $country->id : null;
            $beneficiary->phone                  = $row['beneficiary_Phone'];
            $beneficiary->fax                    = $row['beneficiary_Fax'];
            $beneficiary->email                  = $row['beneficiary_Email'];

            $this->save($beneficiary, $row);
        });

        $this->log->info('END');

        return $this->result;
    }
}
