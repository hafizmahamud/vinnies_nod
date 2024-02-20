<?php
namespace App\Vinnies\Exporter;

use Maatwebsite\Excel\Excel;
use App\NewRemittance;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\WithProperties;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithEvents;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class NewRemittanceExporter implements WithProperties, Responsable, FromView, WithEvents, WithStyles, WithDrawings, WithColumnWidths
{
    use Exportable;

    private $fileName;
    private $writerType = Excel::XLSX;

    protected $remittance;
    protected $rowNum = 13;

    public function __construct(NewRemittance $remittance)
    {
        $this->remittance = $remittance;
        $this->title   = sprintf(
            'Remittance Cover Sheet - %s',
            $remittance->id
        );

        $this->fileName = $this->title  . '.xlsx';
    }

    public function view(): View
    {
        return view('exports.remittance', [
            'remittance'     => $this->remittance,
            'header'   => $this->getHeader(),
            //'total'    => $this->total,
        ]);
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath(storage_path('img/report-logo.png'));
        //$drawing->setHeight(90);
        $drawing->setCoordinates('A1');

        return $drawing;
    }

    public function columnWidths(): array
    {
        return [
          'A'     =>  50,
          'B'     =>  30,
          'C'     =>  20,
          'D'     =>  20,
        ];
    }

    /**
    * @return array
    */
    public function registerEvents(): array
    {
       return [
           AfterSheet::class    => function(AfterSheet $event) {
               $generalStyle= [
                   'font' => [
                       'size' => 11,
                       'name' => 'Arial',
                   ],
                   'alignment' => [
                       'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                   ],
               ];

               $borderStyle= [
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                            'color' => ['argb' => '00000000'],
                        ],
                    ],
               ];

               $event->sheet->getStyle('C1:C6')->getAlignment()->setHorizontal('right');
               $event->sheet->getStyle('D1:D6')->getAlignment()->setHorizontal('center');
               $event->sheet->getDelegate()->getStyle('A1:D500')->applyFromArray($generalStyle);

               $event->sheet->getDelegate()->getStyle('A6')->getFont()->setSize(16)->setBold(true);
               $event->sheet->getDefaultRowDimension()->setRowHeight(18);
               $event->sheet->getRowDimension('6')->setRowHeight(25);

               $event->sheet->getStyle('C3:D6')->applyFromArray($borderStyle);
               $event->sheet->getStyle('A6:B6')->applyFromArray($borderStyle);
               $event->sheet->getStyle('A8:B8')->applyFromArray($borderStyle);
               $event->sheet->getStyle('C8:D8')->applyFromArray($borderStyle);
               $event->sheet->getStyle('A9:B9')->applyFromArray($borderStyle);
               $event->sheet->getStyle('A11:D11')->applyFromArray($borderStyle);

               $event->sheet->getDelegate()->getStyle('C8:D8')->getAlignment()->setHorizontal('right');

               $this->setCustomCellAtrribute($event, 'twinnings');
               $this->setCustomCellAtrribute($event, 'grants');
               $this->setCustomCellAtrribute($event, 'councils');
               $this->setCustomCellAtrribute($event, 'projects');

               $this->rowNum = $this->rowNum + 1;

               $event->sheet->getStyle('A' . $this->rowNum . ':C' . $this->rowNum)->applyFromArray($borderStyle);
               $event->sheet->getStyle('A' . $this->rowNum . ':C' . $this->rowNum)->getAlignment()->setHorizontal('right');
               $event->sheet->getStyle('D' . $this->rowNum)->applyFromArray($borderStyle);
               $event->sheet->getStyle('D' . $this->rowNum)->getNumberFormat()->setFormatCode('$#,##0');


               //print setup
               $event->sheet->getPageSetup()->setFitToPage(true);
               $event->sheet->getPageSetup()->setFitToWidth(1);
               $event->sheet->getPageSetup()->setFitToHeight(1);
               // $event->sheet->getPageMargins()->setTop(0.1);
               // $event->sheet->getPageMargins()->setRight(0.1);
               // $event->sheet->getPageMargins()->setLeft(0.1);
               // $event->sheet->getPageMargins()->setBottom(0.1);
               $event->sheet->getPageMargins()->setHeader(1);
               $event->sheet->getPageMargins()->setFooter(1);
           },
       ];
    }

    private function setCustomCellAtrribute($event, $type)
    {
        switch ($type) {
            case 'twinnings':
                $heading   = 'TWINNINGS';
                $footer    = 'Total Twinnings';
                $donations = $this->remittance->twinningDonations;
                break;

            case 'grants':
                $heading   = 'GRANTS';
                $footer    = 'Total Grants';
                $donations = $this->remittance->grantDonations;
                break;

            case 'councils':
                $heading   = 'COUNCIL TO COUNCIL';
                $footer    = 'Total Council to Council';
                $donations = $this->remittance->councilDonations;
                break;

            case 'projects':
                $heading   = 'PROJECTS';
                $footer    = 'Total Projects';
                $donations = $this->remittance->projectDonations;
                break;
        }

        if ($donations->isEmpty()) {
            $this->rowNum++;
            $event->sheet->getStyle('B' . $this->rowNum . ':D' . $this->rowNum)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
            $event->sheet->getStyle('C' . $this->rowNum . ':C' . $this->rowNum)->getAlignment()->setHorizontal('center');
            $event->sheet->getStyle('D' . $this->rowNum . ':D' . $this->rowNum)->getNumberFormat()->setFormatCode('$#,##0');
        } else {
            $this->rowNum++;

            $event->sheet->getStyle('B' . $this->rowNum . ':D' . $this->rowNum)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);


            $groupedDonations = $donations->sortBy(function ($donation, $key) use ($type) {
                if ($type == 'projects') {
                    return optional($donation->project->beneficiary)->country->name;
                }

                return optional($donation->twinning->overseasConference->country)->name;
            })->groupBy(function ($donation, $key) use ($type) {
                if ($type =='projects') {
                    return optional($donation->project->beneficiary)->country->name;
                }

                return optional($donation->twinning->overseasConference->country)->name;
            });

            $rowStart     = $this->rowNum;
            $this->rowNum = $this->rowNum + count($groupedDonations);

            $event->sheet->getStyle('B' . $this->rowNum . ':D' . $this->rowNum)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
            $event->sheet->getStyle('C' . $rowStart . ':C' . $this->rowNum)->getAlignment()->setHorizontal('center');
            $event->sheet->getStyle('D' . $rowStart . ':D' . $this->rowNum)->getNumberFormat()->setFormatCode('$#,##0');

        }

        $this->rowNum = $this->rowNum + 2;

        return $event;
    }

    private function getHeader()
    {
        return [
          'YEAR',
          'Project ID',
          'Project Name',
          'Beneficiary Country',
          'OS CONF SRN',
          'OS CONF NAME',
          'CENTRAL COUNCIL',
          'OS CONF PARTICULAR COUNCIL',
          'OS CONF Parish',
          'DONOR SRN',
          'DONOR NAME',
          'DONOR STATE',
          'GROUP TOTAL',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $style = array();

        $style['C7'] = ['font' => [
                            'underline' => true,
                            'color' => ['rgb' => '0000ff']
                          ]
                       ];

        return $style;
    }

    public function properties(): array
    {
        return [
            'creator'        => 'St Vincent de Paul Society',
            'lastModifiedBy' => 'St Vincent de Paul Society',
            'title'          =>  $this->title,
            'manager'        => 'St Vincent de Paul Society',
            'company'        => 'St Vincent de Paul Society',
        ];
    }
}
