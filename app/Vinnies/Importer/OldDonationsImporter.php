<?php

namespace App\Vinnies\Importer;

use App\Beneficiary;
use App\OldDonation;
use App\OldRemittance;
use App\Vinnies\Money;
use App\Vinnies\Helper;

class OldDonationsImporter extends BaseImporter
{
    public function import()
    {
        $this->log->info('START');
        $this->log->info('Metadata', [
            'headers' => $this->getHeaders(),
            'records' => count($this->getData()),
        ]);

        $this->getData()->each(function ($row) {
            if (empty($row['Remittances_In_LineItems::pk_remittances_in_lineitems_ID'])) {
                return;
            }

            $donation = OldDonation::firstOrNew(['id' => $row['Remittances_In_LineItems::pk_remittances_in_lineitems_ID']]);

            $donation->id                = $row['Remittances_In_LineItems::pk_remittances_in_lineitems_ID'];
            $donation->old_remittance_id = $row['Remittances_In_LineItems::fk_remittances_in_ID'];
            $donation->beneficiary_id    = $row['Remittances_In_LineItems::remittances_in_lineitems_Beneficiary_ID'];
            $donation->purpose           = Helper::utf8_encode($row['Remittances_In_LineItems::remittances_in_lineitems_Purpose']);
            $donation->myob_code         = Helper::utf8_encode($row['Remittances_In_LineItems::remittances_in_lineitems_MYOB_Code_TryThis_LU']);
            $donation->state             = strtolower($row['Remittances_In_LineItems::remittances_in_lineitems_State_LU']);
            $donation->twins             = (int) $row['Remittances_In_LineItems::remittances_in_lineitems_TwinningAmount'];
            $donation->comments          = Helper::utf8_encode($row['Remittances_In_LineItems::remittances_in_lineitems_Comments']);
            $donation->amount            = (new Money(Helper::formatDecimal($row['Remittances_In_LineItems::remittances_in_lineitems_Amount'])))->value();

            if (!OldRemittance::find($donation->old_remittance_id)) {
                $donation->old_remittance_id = null;

                $this->log->warning('Missing Old Remittance', ['row' => $row]);
            }

            if (!Beneficiary::find($donation->beneficiary_id)) {
                $donation->beneficiary_id = null;

                $this->log->warning('Missing Beneficiary', ['row' => $row]);
            }

            $this->save($donation, $row);
        });

        $this->log->info('END');

        return $this->result;
    }
}
