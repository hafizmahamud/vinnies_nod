<?php

namespace App\Vinnies\Exporter;

use App\Project;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

use Illuminate\Support\Collection;


class ProjectsExporter implements FromView, WithColumnWidths, WithProperties, ShouldAutoSize, WithEvents
{
    private $projects;

    public function __construct(Collection $projects)
    {
        $this->projects = $projects;
    }

    public function view(): View
    {

        return view('exports.projects', [
            'projects' => $this->projects,
            'header'   => $this->getHeader()
        ]);
    }

    public function columnWidths(): array
    {
        return [
          'A'     =>  15,
          'B'     =>  15,
          'C'     =>  15,
          'D'     =>  20,
          'E'     =>  20,
          'F'     =>  20,
          'G'     =>  20,
          'H'     =>  20,
          'I'     =>  15,
          // 'J'     =>  35,
          'K'     =>  15,
          'L'     =>  15,
          'M'     =>  15,
          'N'     =>  15,
          // 'O'     =>  45,
          'P'     =>  15,
          'Q'     =>  15,
          'R'     =>  15,
          // 'S'     =>  40,
          'T'     =>  15,
          'U'     =>  20,
          'V'     =>  20,
          'W'     =>  15,
          'X'     =>  15,
          //'Y'     =>  35,
          'Z'     =>  15,
          'AA'     =>  20,
          'AB'     =>  20,
          'AC'     =>  15,
          'AD'    =>  15,
          'AE'    =>  15,
          'AF'    =>  15,
          'AG'    =>  20,
          'AH'    => 15,
        ];
    }

    // /**
    // * @return array
    // */
    public function registerEvents(): array
    {
       return [
           AfterSheet::class    => function(AfterSheet $event) {
               $event->sheet->getStyle('A1:AH1')->getAlignment()->setVertical('center')->setWrapText(true);
               $event->sheet->getStyle('A:AH')->getAlignment()->setHorizontal('center')->setWrapText(true);

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


    private function getHeader()
    {
        return [
          'QUARTER',
          'YEAR',
          'Project ID',
          'Overseas Project ID',
          'Project Status',
          'Project Type',
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
          'DONOR STATE/TERRITORY COUNCIL',
          'DONOR REGIONAL COUNCIL',
          'DONOR DIOCESAN/CENTRAL COUNCIL',
          'DONOR PAID TO DATE AMOUNT AUD',
          'Project Completion Report Received',
          'Project Completion Report Received Date',
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
