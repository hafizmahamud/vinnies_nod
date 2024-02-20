<?php

namespace App\Vinnies\Importer;

use Auth;
use App\Document;
use App\NewRemittance;
use App\Vinnies\Helper;

abstract class RemittanceImporter extends BaseImporter
{
    protected $document;
    protected $remittance;
    protected $requiredHeaders;
    protected $stateColumnName;
    protected $amountColumnName;

    // For validation purposes
    public $invalidMsg = 'The remittance file you are trying to upload has one of the following issues:<br>- It is not in CSV format or the CSV format used is not standard comma delimited. Please open it with MS Excel and save it in CSV comma delimited format.<br>- Contains extra empty columns. Please open it with MS Excel and delete the extra columns.<br>- Contains extra empty rows. Please open it with MS Excel and delete the extra rows at the end.';

    public $invalidHeadersMsg;
    public $invalidYearMsg;
    public $invalidQuarterMsg;
    public $invalidStateMsg;
    public $invalidAmountMsg;
    public $invalidTwinningMsg;

    public $invalidRows = [
        'projects'  => [],
        'donors'    => [],
        'twinnings' => [],
    ];

    public function setRemittance(NewRemittance $remittance)
    {
        $this->remittance = $remittance;

        return $this;
    }

    public function setDocument(Document $document)
    {
        $this->document = $document;

        return $this;
    }

    public function setRequiredHeaders($headers = [])
    {
        $this->requiredHeaders = $headers;

        return $this;
    }

    public function setStateColumnName($name = '')
    {
        $this->stateColumnName = $name;

        return $this;
    }

    public function setAmountColumnName($name = '')
    {
        $this->amountColumnName = $name;

        return $this;
    }

    public function isValidHeaders()
    {
        $headers = array_diff($this->requiredHeaders, $this->getHeaders());

        $this->invalidHeadersMsg = sprintf(
            'The heading %s appear%s to be missing. Please check if %s or correct it to proper heading name as per the remittance template',
            '"' . implode('", "', $headers) . '"',
            count($headers) > 1 ? '' : 's',
            count($headers) > 1 ? 'they are present' : 'it is present'
        );

        return empty($headers);
    }

    public function isValidQuarter()
    {
        $valid = true;

        $all_quarters = collect(
            $this->csv->fetchColumn(
                $this->index('QUARTER')
            )
        )->map(function ($quarter) {
            return (int) $quarter;
        });

        $quarters = $all_quarters->filter()->unique();

        if ($quarters->count() > 1) {
            $valid = false;
        }

        if ($quarters->first() !== $this->remittance->quarter) {
            $valid = false;
        }

        if (!$valid) {
            if ($quarters->count() > 1) {
                $quarter_to_search = $quarters->reject(function ($quarter) {
                    return $quarter === $this->remittance->quarter;
                })->first();
            } else {
                $quarter_to_search = $quarters->first();
            }

            $row = $all_quarters->search($quarter_to_search);

            $this->invalidQuarterMsg = sprintf(
                'At row %s the value of the field %s does not match the existing quarter',
                $row + 1,
                'QUARTER'
            );
        }

        return $valid;
    }

    public function isValidYear()
    {
        $valid = true;

        $all_years = collect(
            $this->csv->fetchColumn(
                $this->index('YEAR')
            )
        )->map(function ($year) {
            return (int) $year;
        });

        $years = $all_years->filter()->unique();

        if ($years->count() > 1) {
            $valid = false;
        }

        if ($years->first() !== $this->remittance->year) {
            $valid = false;
        }

        if (!$valid) {
            if ($years->count() > 1) {
                $year_to_search = $years->reject(function ($year) {
                    return $year === $this->remittance->year;
                })->first();
            } else {
                $year_to_search = $years->first();
            }

            $row = $all_years->search($year_to_search);

            $this->invalidYearMsg = sprintf(
                'At row %s the value of the field %s does not match the existing year',
                $row + 1,
                'YEAR'
            );
        }

        return $valid;
    }

    public function isValidState()
    {
        $user  = Auth::user();
        $valid = true;

        $all_states = collect(
            $this->csv->fetchColumn(
                $this->index($this->stateColumnName)
            )
        )->map(function ($state) {
            return strtolower($state);
        });

        $states = $all_states->filter()->unique();

        // Must contain single state only
        if ($states->count() > 1) {
            $valid = false;
        }

        if (Helper::getStateKeyByName($states->first()) !== $this->remittance->state) {
            $valid = false;
        }

        if (!$user->hasRole('Full Admin')) {
            $valid = in_array(Helper::getStateKeyByName($states->first()), $user->states);
        }

        if (!$valid) {
            if ($states->count() > 1) {
                $state_to_search = $states->reject(function ($state) {
                    return $state === $this->remittance->state;
                })->first();
            } else {
                $state_to_search = Helper::getStateKeyByName($states->first());
            }

            $row = $all_states->search($state_to_search);

            $this->invalidStateMsg = sprintf(
                'At row %s the value of the field %s does not match the existing state',
                $row + 1,
                $this->stateColumnName
            );
        }

        return $valid;
    }

    public function isValidAmount()
    {
        $valid = true;

        $all_amounts = collect(
            $this->csv->fetchColumn(
                $this->index($this->amountColumnName)
            )
        )->map(function ($amount) {
            return floatval($amount);
        });

        $amounts = $all_amounts->unique()->filter(function ($amount) {
            return empty($amount);
        });

        $valid = $amounts->isEmpty();

        if (!$valid) {
            $empty_ammounts = $all_amounts->filter(function (int $item, int $key) {
                if ($item == 0) {
                    return true;
                }
            })->map(function (int $item, int $key) {
                return $key + 1;
            });

            $i = 1;

            foreach ($empty_ammounts as $row_num) {
                if ($i == 1) {
                    $row = $row_num;
                } elseif ($i == sizeof($empty_ammounts)) {
                    $row = $row . ' & ' . $row_num;
                } else {
                    $row = $row . ', ' . $row_num;
                }

                $i++;
            }

            $this->invalidAmountMsg = sprintf(
                //'Missing/Zero payment amount or remove unneeded <b>$</b> sign at row %s',
                'File will not upload if contains any <b>$</b> signs or zero payment amounts.<br>Remove all <b>$</b> signs from payment amounts and remove all record lines with zero amounts at row %s.',
                $row
            );
        }

        return $valid;
    }
}
