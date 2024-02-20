<?php

namespace App\Vinnies\Importer;

use App\Project;
use App\Beneficiary;
use App\Vinnies\Money;
use App\Vinnies\Helper;
use App\OverseasConference;

class ProjectsImporter extends BaseImporter
{
    public function import()
    {
        $this->log->info('START');
        $this->log->info('Metadata', [
            'headers' => $this->getHeaders(),
            'records' => count($this->getData()),
        ]);

        $this->getData()->each(function ($row) {
            $project = Project::firstOrNew(['id' => $row['pk_project_ID']]);

            $project->id                     = $row['pk_project_ID'];
            $project->name                   = Helper::utf8_encode($row['projects_ProjectName']);
            $project->beneficiary_id         = $row['fk_beneficiary_ID'];
            $project->overseas_conference_id = $row['fk_overseasconference_ID'];
            $project->overseas_project_id    = Helper::utf8_encode($row['project_OverseasProject_ID']);
            $project->currency               = $this->fixCurrency($row['project_LocalCurrency']);
            $project->exchange_rate          = $row['project_ExchangeRate'];
            $project->is_fully_paid          = !empty($row['c_projects_Paid_Flag']);
            $project->is_awaiting_support    = false;
            $project->comments               = Helper::utf8_encode($row['projects_ProjectComments']);
            $project->received_at            = $this->parseDate($row['projects_DateProjectReceived']);
            $project->fully_paid_at          = $this->parseDate($row['projects_Paid_Date']);
            $project->completed_at           = $this->parseDate($row['projects_CompletionReportDate']);

            if (!empty($row['projects_ProjectValueLocal'])) {
                $project->local_value = (new Money(Helper::formatDecimal($row['projects_ProjectValueLocal'])))->value();
            }

            if (!empty($row['c_projects_ProjectValue_AUD'])) {
                $project->au_value = (new Money(Helper::formatDecimal($row['c_projects_ProjectValue_AUD'])))->value();
            }

            if (!Beneficiary::find($project->beneficiary_id)) {
                $project->beneficiary_id = null;

                $this->log->warning('Missing Beneficiary', ['row' => $row]);
            }

            if (!OverseasConference::find($project->overseas_conference_id)) {
                $project->overseas_conference_id = null;

                $this->log->warning('Missing Overseas Conference', ['row' => $row]);
            }

            $this->save($project, $row);
        });

        $this->log->info('END');

        return $this->result;
    }

    public function fixCurrency($currencyCode)
    {
        $replacements = [
            'BAHT' => 'THB',
            'PNK'  => 'PGK',
        ];

        if (array_key_exists($currencyCode, $replacements)) {
            return $replacements[$currencyCode];
        }

        return $currencyCode;
    }
}
