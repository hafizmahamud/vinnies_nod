<?php
namespace App\Vinnies\Exporter\Sheets;

use App\Country;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class TwinningSheet implements FromView, WithColumnWidths, ShouldAutoSize, WithEvents, WithTitle
{
    protected $remittances;

    public function __construct($remittances, $year, $quarter, Country $country, $title ,$type)
    {
        $this->remittances = $remittances;

        $this->year    = $year;
        $this->quarter = $quarter;
        $this->title   = $title;
        $this->type    = $type;
    }

    public function view(): View
    {
        return view('exports.reports.twinning', [
            'data'     => $this->remittances['donations'][$this->type],
            'header'   => $this->getHeader(),
            'year'     => $this->year,
            'quarter'  => $this->quarter,
            'total'    => $this->remittances[$this->type],
        ]);
    }

    public function columnWidths(): array
    {
        return [
          'A'     =>  15,
          'B'     =>  15,
          'C'     =>  15,
          'E'     =>  20,
          'I'     =>  15,
          'K'     =>  15,
          'M'     =>  15,
          'N'     =>  15,
          'O'     =>  15,
          'Q'     =>  15,
          'R'     =>  20,
          'S'     =>  20,
          'T'     =>  15,
          'U'     =>  15,
          'W'     =>  15,
          'X'     =>  20,
          'Y'     =>  20,
          'Z'     =>  15,
          'AA'    =>  15,
          'AB'    =>  20,
          'AC'    =>  20,
          'AD'    =>  20,
          'AE'    =>  20
        ];
    }

    /**
    * @return array
    */
    public function registerEvents(): array
    {
       return [
           AfterSheet::class    => function(AfterSheet $event) {
               $event->sheet->getStyle('A1:AB2000')->getAlignment()->setHorizontal('center')->setWrapText(true);

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
          'OS CONF SRN',
          'OS CONF NAME',
          'COUNTRY',
          'CENTRAL COUNCIL',
          'OS CONF PARTICULAR COUNCIL',
          'OS CONF Parish',
          'AUS CONF SRN',
          'AUS CONF NAME',
          'AUS CONF STATE',
          'DATE RECEIVED',
          'DATE UPLOAD',
          'DATE APPROVED',
          'AMOUNT AUD',
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
