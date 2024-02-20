<?php
namespace App\Vinnies\Exporter\Sheets;

use App\Country;
use App\Vinnies\RemittanceFactory;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;

use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class CoverLetterSheet implements FromView, WithEvents, WithTitle, WithStyles, WithDrawings, WithColumnWidths
{
    protected $remittances;

    public function __construct($remittances, $year, $quarter, Country $country, $title ,$type)
    {
        // $factory = new RemittanceFactory($year, $quarter);
        // $factory->populate();
        //
        // $remittances       = $factory->get();
        $this->remittances = $remittances;

        $this->year    = $year;
        $this->quarter = $quarter;
        $this->title   = $title;
        $this->type    = $type;
    }

    public function view(): View
    {
        return view('exports.reports.coverletter', [
            'remittances'     => $this->remittances,
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
        $drawing->setCoordinates('A2');

        return $drawing;
    }

    public function columnWidths(): array
    {
        return [
          'A'     =>  50,
          'B'     =>  15,
          'C'     =>  35,
        ];
    }

    /**
    * @return array
    */
    public function registerEvents(): array
    {
       return [
           AfterSheet::class    => function(AfterSheet $event) {
               $event->sheet->getStyle('C1:C8')->getAlignment()->setHorizontal('right');

               $this->setCustomCellAtrribute($event);
               /////////$event->sheet->getRowDimension('4')->setRowHeight(100, 'pt');
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

    private function setCustomCellAtrribute($event)
    {
        $rowNum = 12;

        if ($this->remittances['beneficiary']['contact_position']) {
            $rowNum++;
        }

        if ($this->remittances['beneficiary']['address_line_1']) {
            $rowNum++;
        }

        if ($this->remittances['beneficiary']['address_line_2'] || $this->remittances['beneficiary']['address_line_3']) {
            $rowNum++;
        }

        if ($this->remittances['beneficiary']['suburb'] || $this->remittances['beneficiary']['state'] || $this->remittances['beneficiary']['postcode']) {
            $rowNum++;
        }

        $rowNum = $rowNum + 2;
        $event->sheet->getCell('A' . $rowNum)->getHyperlink()->setUrl('mailto:' .$this->remittances['beneficiary']['email']);;

        $rowNum = $rowNum + 4;
        $event->sheet->getStyle('A' . $rowNum . ':C' . $rowNum)->getAlignment()->setHorizontal('center');

        $rowNum = $rowNum + 2;
        $event->sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal('center');

        $rowStart = $rowNum + 1;
        $rowNum = $rowNum + 6;
        $event->sheet->getStyle('C' . $rowStart . ':C' . $rowNum)->getNumberFormat()->setFormatCode('$#,##0');
        $event->sheet->getStyle('B' . $rowStart . ':B' . $rowNum)->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
        $event->sheet->getStyle('B' . $rowStart . ':B' . $rowNum)->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);

        $rowNum = $rowNum + 5;
        $event->sheet->getRowDimension($rowNum)->setRowHeight(40, 'pt');
        $event->sheet->getStyle('A' . $rowNum . ':B' . $rowNum)->getAlignment()->setVertical('center')->setWrapText(true);

        $rowNum++;
        $event->sheet->getRowDimension($rowNum)->setRowHeight(40, 'pt');
        $event->sheet->getStyle('A' . $rowNum . ':B' . $rowNum)->getAlignment()->setVertical('center')->setWrapText(true);

        $rowNum++;
        $event->sheet->getRowDimension($rowNum)->setRowHeight(40, 'pt');
        $event->sheet->getStyle('A' . $rowNum . ':B' . $rowNum)->getAlignment()->setVertical('center')->setWrapText(true);

        $rowNum = $rowNum + 17;
        $event->sheet->getStyle('A' . $rowNum)->getFont()->setItalic(true);;
        $event->sheet->getStyle('A' . ($rowNum + 1) . ':C' . ($rowNum + 5))->getAlignment()->setHorizontal('center');


        $event->sheet->getCell('C7')->getHyperlink()->setUrl('mailto:overseasadmin@svdp.org.au');

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

        return [
            // // Style the first row as bold text.
            // 1    => ['font' => ['bold' => true]],
            //
            // // Styling a specific cell by coordinate.
            // 'B2' => ['font' => [
            //             'italic' => true,
            //             'color' => ['rgb' => '7030a0']
            //         ],
            //         'borders' => [
            //             'outline' => [
            //                 'borderStyle' => Border::BORDER_THICK,
            //                 'color' => ['rgb' => '0070c0'],
            //             ],
            //         ],
            //     ],
            // // Styling an entire column.
            // 'A'  => [
            //   'font' => [
            //     'size' => 16,
            //     'color' => ['argb' => '0000000']
            //   ]
            // ],
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }
}
