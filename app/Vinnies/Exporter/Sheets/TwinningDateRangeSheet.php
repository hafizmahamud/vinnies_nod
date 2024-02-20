<?php
namespace App\Vinnies\Exporter\Sheets;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TwinningDateRangeSheet implements FromView, ShouldAutoSize, WithEvents, WithTitle
{
    public function __construct($title, $donations, $total)
    {
        $this->title     = $title;
        $this->donations = $donations;
        $this->total     = $total;
    }

    public function view(): View
    {
        return view('exports.reports.twinningdaterange', [
            'data'     => $this->donations,
            'header'   => $this->getHeader(),
            'total'    => $this->total,
        ]);
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
          'GROUP TOTAL',
        ];
    }

    /**
     * @return string
     * sheet name
     */
    public function title(): string
    {
        return $this->title;
    }
}
