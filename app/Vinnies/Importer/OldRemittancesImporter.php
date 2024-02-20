<?php

namespace App\Vinnies\Importer;

use App\OldRemittance;
use App\Vinnies\Money;
use App\Vinnies\Helper;

class OldRemittancesImporter extends BaseImporter
{
    public function import()
    {
        $this->log->info('START');
        $this->log->info('Metadata', [
            'headers' => $this->getHeaders(),
            'records' => count($this->getData()),
        ]);

        $this->getData()->each(function ($row) {
            if (empty($row['remittances_in_DateReceived'])) {
                return;
            }

            $remittance = OldRemittance::firstOrNew(['id' => $row['pk_remittances_in_ID']]);
            $quarter    = $this->parseQuarter($row['c_remittances_in_ConcatenatedQuarterYear']);

            $remittance->id             = $row['pk_remittances_in_ID'];
            $remittance->state          = strtolower($row['oz_state_council_State_LU']);
            $remittance->received_at    = $this->parseDate($row['remittances_in_DateReceived']);
            $remittance->quarter        = $quarter['quarter'];
            $remittance->year           = $quarter['year'];
            $remittance->payment_method = Helper::utf8_encode($row['remittances_in_PaymentMethod']);
            $remittance->cheque_number  = Helper::utf8_encode($row['remittances_in_ChequeNumber']);
            $remittance->comments       = Helper::utf8_encode($row['remittances_in_Comments']);
            $remittance->allocated      = (new Money(Helper::formatDecimal($row['c_remittances_in_RemittanceTotalAllocated'])))->value();

            $this->save($remittance, $row);
        });

        $this->log->info('END');

        return $this->result;
    }
}
