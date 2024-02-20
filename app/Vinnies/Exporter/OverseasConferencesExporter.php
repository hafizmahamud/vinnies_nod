<?php

namespace App\Vinnies\Exporter;

use Excel;
use App\Project;
use App\Vinnies\Helper;
use App\LocalConference;
use App\OverseasConference;
use App\Beneficiary;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class OverseasConferencesExporter implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents, WithProperties, WithColumnWidths
{
    public function __construct(Collection $data)
    {
        $this->filename = sprintf('Overseas Conferences - %s', date('Y.m.d'));
        $this->data = $data;
        $this->projects = Project::all();
        $this->statuses = Helper::getOSConferencesTwinningStatuses();
    }
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        $value = [
            'OS Conf. SRN',
            'OS Conf. Name',
            'OS Conf. Parish',
            'OS Conf. Status',
            'Aggregation No.',
            'Aggregation Date',
            'OS Conf. Twinning Status',
            'Receiving Remittances?',
            'Currently In Status Check?',
            'Date when current/last Status check initiated',
            'Currently Surrendering?',
            'Date when Surrendering initiated',
            'Surrendering Deadline',
            'Date Twinned',
            'Date Untwinned',
            'Contact Name',
            'Contact Email',
            'Country',
            'National/Superior Council',
            'Central Council',
            'Particular Council',
            'Address Line 1',
            'Address Line 2',
            'Address Line 3',
            'Suburb',
            'State',
            'Postcode',
            'Active Twinnings Count',
            'AUS Conf SRN',
            'AUS Conf Name',
            'AUS Conf Parish',
            'AUS Conf Contact name',
            'AUS Conf Contact email',
            'State/Territory Council',
            'Diocesan/Central Council',
            'Regional Council',
            'Surrendered Twinnings Count',
            'Linked Projects Count',
            'Documents Count',
            'Comments',
        ];

        if(Auth::user()->hasRole('Full Admin')){
            array_splice( $value, 17, 0, ['Contact Phone']);
        }

        return $value;
    }

    public function columnWidths(): array
    {
        if(Auth::user()->hasRole('Full Admin')){
            return [
                'A'     =>  15,
                'B'     =>  35,
                'C'     =>  15,
                'D'     =>  15,
                'E'     =>  15,
                'F'     =>  15,
                'G'     =>  15,
                'H'     =>  15,
                'I'     =>  15,
                'J'     =>  15,
                'K'     =>  15,
                'L'     =>  15,
                'M'     =>  35,
                'N'     =>  15,
                'O'     =>  15,
                'P'     =>  18,
                'Q'     =>  15,
                'R'     =>  15,
                'S'     =>  15,
                'T'     =>  20,
                'U'     =>  20,
                'V'     =>  20,
                'W'     =>  15,
                'X'     =>  15,
                'Y'     =>  15,
                'Z'     =>  15,
                'AA'     =>  15,
                'AB'    =>  35,
                'AC'    =>  20,
                'AD'    =>  20,
                'AE'    =>  35,
                'AF'    =>  20,
                'AG'    =>  20,
                'AH'    =>  15,
                'AI'    =>  15,
                'AJ'    =>  15,
                'AK'    =>  15,
                'AL'    =>  15,
                'AM'    =>  15,
                'AN'    =>  15,
                'AO'    =>  15,
            ];
        }else{
            return [
                'A'     =>  15,
                'B'     =>  35,
                'C'     =>  15,
                'D'     =>  15,
                'E'     =>  15,
                'F'     =>  15,
                'G'     =>  15,
                'H'     =>  15,
                'I'     =>  15,
                'J'     =>  15,
                'K'     =>  15,
                'L'     =>  15,
                'M'     =>  35,
                'N'     =>  15,
                'O'     =>  15,
                'P'     =>  18,
                'Q'     =>  15,
                'R'     =>  15,
                'S'     =>  20,
                'T'     =>  20,
                'U'     =>  20,
                'V'     =>  15,
                'W'     =>  15,
                'X'     =>  15,
                'Y'     =>  15,
                'Z'     =>  15,
                'AA'    =>  35,
                'AB'    =>  20,
                'AC'    =>  20,
                'AD'    =>  35,
                'AE'    =>  20,
                'AF'    =>  20,
                'AG'    =>  15,
                'AH'    =>  15,
                'AI'    =>  15,
                'AJ'    =>  15,
                'AK'    =>  15,
                'AL'    =>  15,
                'AM'    =>  15,
                'AN'    =>  15,
            ];
        }
    }

    /**
    */
    public function map($conference): array
    {
        $twinnings = $conference->twinnings;
        $twinnings_not_empty = $twinnings->isNotEmpty();

        $active_twinning_states = $twinnings->filter(function ($twinning) {
            return $twinning->is_active;
        })->map(function ($twinning) {
            return optional($twinning->localConference)->state;
        })
        ->filter()
        ->sort()
        ->map(function ($state) {
            if ($state == 'national') {
                return ucwords($state);
            }

            return strtoupper($state);
        });

        if ($active_twinning_states->isEmpty()) {
            $active_twinning_states = 'N/A';
        } else {
            $active_twinning_states = $active_twinning_states->implode('/');
        }

        if ($twinnings_not_empty) {
            $active_twinnings = $twinnings->filter(function ($twinning) {
                return $twinning->is_active;
            })->first();
        }

        if (!empty($conference->contact_phone)) {
            $contact_phone = $conference->contact_phone;
        } else {
            $contact_phone = 'N/A';
        }

        $value = [
            $conference->id,
            $conference->name,
            $conference->parish ? $conference->parish : 'N/A',
            strtoupper($conference->status ? $conference->status : 'N/A'),
            $conference->aggregation_number ? $conference->aggregation_number : 'N/A',
            ($conference->is_active_at ? $conference->is_active_at->format(config('vinnies.date_format')) : 'N/A'),
            $this->statuses[$conference->twinning_status],
            $conference->is_active ? 'Remittances' : 'No Remittances',
            $conference->is_in_status_check ? 'Yes' : 'No',
            ($conference->status_check_initiated_at ? $conference->status_check_initiated_at->format(config('vinnies.date_format')) : 'N/A'),
            $conference->is_in_surrendering ? 'Yes' : 'No',
            ($conference->surrendering_initiated_at ? $conference->surrendering_initiated_at->format(config('vinnies.date_format')) : 'N/A'),
            ($conference->surrendering_deadline_at ? $conference->surrendering_deadline_at->format(config('vinnies.date_format')) : 'N/A'),
            ($conference->twinned_at ? $conference->twinned_at->format(config('vinnies.date_format')) : 'N/A'),
            ($conference->untwinned_at ? $conference->untwinned_at->format(config('vinnies.date_format')) : 'N/A'),
            $conference->contact_name ? $conference->contact_name : 'N/A',
            $conference->contact_email ? $conference->contact_email : 'N/A',
            $conference->country()->get()->isNotEmpty() ? $conference->country->name : 'N/A',
            $conference->beneficiary?->name ? $conference->beneficiary?->name : 'N/A',
            $conference->central_council ? $conference->central_council : 'N/A',
            $conference->particular_council ? $conference->particular_council : 'N/A',
            $conference->address_line_1 ? $conference->address_line_1 : 'N/A',
            $conference->address_line_2 ? $conference->address_line_2 : 'N/A',
            $conference->address_line_3 ? $conference->address_line_3 : 'N/A',
            $conference->suburb ? $conference->suburb : 'N/A',
            $conference->state ? $conference->state : 'N/A',
            $conference->postcode ? $conference->postcode : 'N/A',
            $twinnings->filter(function ($twinning) {
                return $twinning->is_active;
            })->count(),
  
            $twinnings_not_empty ? ((empty($active_twinnings->localConference->id) ? 'N/A' : $active_twinnings->localConference->id)) : 'N/A',
            $twinnings_not_empty ? ((empty($active_twinnings->localConference->name) ? 'N/A' : $active_twinnings->localConference->name)) : 'N/A',
            $twinnings_not_empty ? ((empty($active_twinnings->localConference->parish) ? 'N/A' : $active_twinnings->localConference->parish)) : 'N/A',
            $twinnings_not_empty ? ((empty($active_twinnings->localConference->contact_name) ? 'N/A' : $active_twinnings->localConference->contact_name)) : 'N/A',
            $twinnings_not_empty ? ((empty($active_twinnings->localConference->contact_email) ? 'N/A' : $active_twinnings->localConference->contact_email)) : 'N/A',
            $twinnings_not_empty ? ((empty($active_twinnings->localConference->state_council) ? 'N/A' : Helper::getStateNameByKey($active_twinnings->localConference->state_council))) : 'N/A',
            $twinnings_not_empty ? ((empty($active_twinnings->localConference->diocesanCouncil) ? 'N/A' : $active_twinnings->localConference->diocesanCouncil->name)) : 'N/A',
            $twinnings_not_empty ? ((empty($active_twinnings->localConference->regional_council) ? 'N/A' : $active_twinnings->localConference->regional_council)) : 'N/A',
  
          //   $active_twinning_states, //Removed
            $twinnings->reject(function ($twinning) {
                return $twinning->is_active;
            })->count(),
            $this->projects->filter(function ($project) use ($conference) {
                return $project->overseas_conference_id == $conference->id;
            })->count(),
            $conference->documents->count(),
            $conference->comments ? $conference->comments : 'N/A',
        ];

        if(Auth::user()->hasRole('Full Admin')){
            array_splice( $value, 17, 0, $contact_phone);
        }

        return $value;
    }

    /**
    * @return array
    */
    public function registerEvents(): array
    {
       return [
           AfterSheet::class    => function(AfterSheet $event) {
              $styleArray = [
                 'font' => [
                     'bold' => true,
                 ],
                 'alignment' => [
                     'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                 ],
                 'fill' => [
                     'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                     'rotation' => 90,
                     'startColor' => [
                         'argb' => 'FFA0A0A0',
                     ],
                     'endColor' => [
                         'argb' => 'FFFFFFFF',
                     ],
                 ],
              ];

              $conditional1 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
              $conditional1->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
              $conditional1->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT);
              $conditional1->setText("Council to Council Twinning");
              $conditional1->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE);

              $conditional2 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
              $conditional2->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
              $conditional2->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT);
              $conditional2->setText("Surrendered");
              $conditional2->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);

              $conditional3 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
              $conditional3->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
              $conditional3->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT);
              $conditional3->setText("Inactive");
              $conditional3->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);

              $conditional4 = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
              $conditional4->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CONTAINSTEXT);
              $conditional4->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_CONTAINSTEXT);
              $conditional4->setText("Abeyant");
              $conditional4->getStyle()->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);

              $conditionalStyles = $event->sheet->getStyle('A2:A100')->getConditionalStyles();
              $conditionalStyles[] = $conditional1;
              $conditionalStyles[] = $conditional2;
              $conditionalStyles[] = $conditional3;
              $conditionalStyles[] = $conditional4;

              $dataArray = $event->sheet->rangeToArray(
                                'D2:D' . (count($this->data) + 1),     // The worksheet range that we want to retrieve
                                NULL,        // Value that should be returned for empty cells
                                TRUE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
                                TRUE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
                                TRUE         // Should the array be indexed by cell row and cell column
                            );

              foreach ($dataArray as $key => $value) {
                    if ($value['D'] == 'Yes') {
                        $event->sheet->getStyle('D' . $key)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                        $event->sheet->getStyle('E' . $key)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                    }
              }

              $dataArray = $event->sheet->rangeToArray(
                                'F2:F' . (count($this->data) + 1),     // The worksheet range that we want to retrieve
                                NULL,        // Value that should be returned for empty cells
                                TRUE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
                                TRUE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
                                TRUE         // Should the array be indexed by cell row and cell column
                            );

              foreach ($dataArray as $key => $value) {
                    if ($value['F'] == 'Yes') {
                        $event->sheet->getStyle('F' . $key)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                        $event->sheet->getStyle('H' . $key)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
                    }
              }

              $event->sheet->getStyle('A')->setConditionalStyles($conditionalStyles);
              $event->sheet->getStyle('B')->setConditionalStyles($conditionalStyles);
              $event->sheet->getStyle('J')->setConditionalStyles($conditionalStyles);

              if(Auth::user()->hasRole('Full Admin')){
                $event->sheet->getStyle('W')->setConditionalStyles($conditionalStyles);

                $event->sheet->getDelegate()->getStyle('A1:AO1')->applyFromArray($styleArray)->getAlignment()->setVertical('center')->setWrapText(true);
                $event->sheet->getStyle('A:AO')->getAlignment()->setHorizontal('center')->setVertical('center')->setWrapText(true);
              }else{
                $event->sheet->getStyle('V')->setConditionalStyles($conditionalStyles);

                $event->sheet->getDelegate()->getStyle('A1:AN1')->applyFromArray($styleArray)->getAlignment()->setVertical('center')->setWrapText(true);
                $event->sheet->getStyle('A:AN')->getAlignment()->setHorizontal('center')->setVertical('center')->setWrapText(true);
              }
              
              //print setup
              //$event->sheet->getPageSetup()->setFitToPage(true);
              $event->sheet->getPageSetup()->setFitToWidth(true);
              $event->sheet->getPageSetup()->setFitToHeight(false);
              $event->sheet->getPageMargins()->setRight(0);
              $event->sheet->getPageMargins()->setLeft(0);
              $event->sheet->getPageMargins()->setTop(0);
              $event->sheet->getPageMargins()->setBottom(0);
           },
       ];
    }

    public function properties(): array
    {
        return [
            'creator'        => 'St Vincent de Paul Society',
            'lastModifiedBy' => 'St Vincent de Paul Society',
            'title'          => $this->filename,
            'manager'        => 'St Vincent de Paul Society',
            'company'        => 'St Vincent de Paul Society',
        ];
    }

}
