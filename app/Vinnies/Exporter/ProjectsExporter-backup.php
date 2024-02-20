<?php

namespace App\Vinnies\Exporter;

use Excel;
use App\Project;
use App\Vinnies\Helper;
use App\LocalConference;
use App\OverseasConference;
use App\Beneficiary;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ProjectsExporter implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize, WithMapping, WithProperties, WithColumnWidths
{
    // private $projects;

    // public function __construct(Collection $projects)
    // {
    //     $this->projects = $projects;
    // }

    // public function view(): View
    // {

    //     return view('exports.projects', [
    //         'projects' => $this->projects,
    //         'header'   => $this->getHeader()
    //     ]);
    // }

    // public function columnWidths(): array
    // {
    //     return [
    //       'A'     =>  15,
    //       'B'     =>  15,
    //       'C'     =>  15,
    //       'D'     =>  20,
    //       'E'     =>  20,
    //       'F'     =>  20,
    //       'G'     =>  20,
    //       'H'     =>  20,
    //       'I'     =>  15,
    //       // 'J'     =>  35,
    //       'K'     =>  15,
    //       'L'     =>  15,
    //       'M'     =>  15,
    //       'N'     =>  15,
    //       // 'O'     =>  45,
    //       'P'     =>  15,
    //       'Q'     =>  15,
    //       'R'     =>  15,
    //       // 'S'     =>  40,
    //       'T'     =>  15,
    //       'U'     =>  20,
    //       'V'     =>  20,
    //       'W'     =>  15,
    //       'X'     =>  15,
    //       //'Y'     =>  35,
    //       'Z'     =>  15,
    //       'AA'     =>  20,
    //       'AB'     =>  20,
    //       'AC'     =>  15,
    //       'AD'    =>  15,
    //       'AE'    =>  15,
    //       'AF'    =>  15,
    //       'AG'    =>  15,
    //       'AH'    => 20,
    //     ];
    // }

    // /**
    // * @return array
    // */
    public function registerEvents(): array
    {
       return [
           AfterSheet::class    => function(AfterSheet $event) {
               $event->sheet->getStyle('A1:AG1')->getAlignment()->setVertical('center')->setWrapText(true);
               $event->sheet->getStyle('A1:AG800')->getAlignment()->setHorizontal('center')->setWrapText(true);

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


    // private function getHeader()
    // {
    //     return [
    //       'QUARTER',
    //       'YEAR',
    //       'Project ID',
    //       'Overseas Project ID',
    //       'Project Status',
    //       'DFAT Consolidated List Approved?',
    //       'Date Application Received',
    //       'Project Estimated Completion Date',
    //       'Project Awaiting Support',
    //       'Project Name',
    //       'Total Project Amount AUD',
    //       'Total Project Balance Owing',
    //       'Project Fully Paid?',
    //       'Date when Fully Paid',
    //       'Beneficiary Name',
    //       'Beneficiary Country',
    //       'OS CONF SRN',
    //       'OS CONF Active Recipient',
    //       'OS CONF NAME',
    //       'OS CONF COUNTRY',
    //       'CENTRAL COUNCIL',
    //       'OS CONF PARTICULAR COUNCIL',
    //       'DONOR SRN',
    //       'DONOR STATUS',
    //       'DONOR NAME',
    //       'DONOR STATE COUNCIL',
    //       'DONOR REGIONAL COUNCIL',
    //       'DONOR DIOCESAN/CENTRAL COUNCIL',
    //       'DONOR PAID TO DATE AMOUNT AUD',
    //       'Project Completion Report Received',
    //       'Project Completion Report Date',
    //       'Project Completion Date',
    //       'DONOR CONTRIBUTION AMOUNT AUD',
    //     ];
    // }


    // public function properties(): array
    // {
    //     return [
    //         'creator'        => 'St Vincent de Paul Society',
    //         'lastModifiedBy' => 'St Vincent de Paul Society',
    //         'title'          =>  sprintf('Projects - %s', date('Y.m.d')),
    //         'manager'        => 'St Vincent de Paul Society',
    //         'company'        => 'St Vincent de Paul Society',
    //     ];
    // }


    public function __construct(Collection $data)
    {
        $this->filename = sprintf('Projects - %s', date('Y.m.d'));
        $this->data = $data;
        // $this->projects = Project::all();
        // $this->statuses = Helper::getOSConferencesTwinningStatuses();
    }
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
          'QUARTER',
          'YEAR',
          'Project ID',
          'Overseas Project ID',
          'Project Status',
          'DFAT Consolidated List Approved?',
          'Date Application Received',
          'Project Estimated Completion Date',
          'Project Awaiting Support',
          'Project Name',
          'Total Project Amount AUD',
          'Total Project Balance Owing',
          'Project Fully Paid?',
          'Date when Fully Paid',
          'Beneficiary Name',
          'Beneficiary Country',
          'OS CONF SRN',
          'OS CONF Active Recipient',
          'OS CONF NAME',
          'OS CONF COUNTRY',
          'CENTRAL COUNCIL',
          'OS CONF PARTICULAR COUNCIL',
          'DONOR SRN',
          'DONOR STATUS',
          'DONOR NAME',
          'DONOR STATE COUNCIL',
          'DONOR REGIONAL COUNCIL',
          'DONOR DIOCESAN/CENTRAL COUNCIL',
          'DONOR PAID TO DATE AMOUNT AUD',
          'Project Completion Report Received',
          'Project Completion Report Date',
          'Project Completion Date',
          'DONOR CONTRIBUTION AMOUNT AUD',
        ];
    }

    public function columnWidths(): array
    {
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

    public function map($projects): array
    {
        return [
            "",
            "",
            $projects->id,
            $projects->overseasConference?->id ? $projects->overseasConference?->id : 'N/A',
            $projects->status ? $projects->status : 'N/A',
            $projects->consolidated_status ? $projects->consolidated_status : 'N/A',
            $projects->received_at ? $projects->received_at->format(config('vinnies.date_format')) : 'N/A',
            $projects->estimated_completed_at ? $projects->estimated_completed_at->format(config('vinnies.date_format')) : 'N/A',
            $projects->is_awaiting_support,
            $projects->name,
            (empty($projects->au_value) ? 'N/A' : $projects->au_value),
            // $projects->_balance_owing ? $projects->_balance_owing : 'N/A',

          'Project Fully Paid?',
          'Date when Fully Paid',
          'Beneficiary Name',
          'Beneficiary Country',
          'OS CONF SRN',
          'OS CONF Active Recipient',
          'OS CONF NAME',
          'OS CONF COUNTRY',
          'CENTRAL COUNCIL',
          'OS CONF PARTICULAR COUNCIL',
          'DONOR SRN',
          'DONOR STATUS',
          'DONOR NAME',
          'DONOR STATE COUNCIL',
          'DONOR REGIONAL COUNCIL',
          'DONOR DIOCESAN/CENTRAL COUNCIL',
          'DONOR PAID TO DATE AMOUNT AUD',
          'Project Completion Report Received',
          'Project Completion Report Date',
          'Project Completion Date',
          'DONOR CONTRIBUTION AMOUNT AUD',
        ];
    }

    public function properties(): array
    {
        return [
            'creator'        => 'St Vincent de Paul Society',
            'lastModifiedBy' => 'St Vincent de Paul Society',
            'title'          =>  sprintf('Projects - %s', date('Y.m.d')),
            'manager'        => 'St Vincent de Paul Society',
            'company'        => 'St Vincent de Paul Society',
        ];
    }
}
