<?php

namespace App\Vinnies\Exporter;

//use Excel;
use App\Donor;
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

class LocalConferencesExporter implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents, WithProperties, WithColumnWidths
{
    public function __construct(Collection $data)
    {
        $this->filename = sprintf('Australian Conferences - %s', date('Y.m.d'));
        $this->data = $data;
        $this->donors = Donor::all();

    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        $value = [
            'AUS Conf SRN',
            'AUS Conf. Status',
            'Aggregation No.',
            'Aggregation Date',
            'Date became Abeyant',
            'AUS Conf Name',
            'AUS Conf Parish',
            'Contact Name',
            'Contact Email',
            //   'Contact Phone', //H
            'State/Territory Council',
            'Diocesan/Central Council',
            'Regional Council',
            'Address Line 1',
            'Address Line 2',
            //'Address Line 3',
            'Suburb',
            'State',
            'Postcode',
            'Comments',
            'Active Twinnings Count',
            'Surrendered Twinnings Count',
            'Linked Projects Count',
            'Documents Count',
        ];

        if(Auth::user()->hasRole('Full Admin')){
            array_splice( $value, 9, 0, ['Contact Phone']);
        }

        return $value;
    }

    public function columnWidths(): array
    {
        if(Auth::user()->hasRole('Full Admin')){
            return [
                'A'     =>  15,
                'B'     =>  15,
                'C'     =>  15,
                'D'     =>  20,
                //'E'     =>  20,
                //'F'     =>  15,
                // 'G'     =>  35,
                'H'     =>  15,
                'I'     =>  18,
                'J'     =>  15,
                'L'     =>  18,
                'M'     =>  15,
                //'N'     =>  15,
                //'O'     =>  15,
                'P'     =>  20,
                // 'Q'     =>  40,
                //'R'     =>  15,
                'S'     =>  20,
                'T'     =>  15,
                'U'     =>  15,
                //'V'     =>  15,
            ];
        }else{
            return [
                'A'     =>  15,
                'B'     =>  15,
                'C'     =>  15,
                'D'     =>  20,
                //'E'     =>  20,
                //'F'     =>  15,
                // 'G'     =>  35,
                'H'     =>  15,
                'I'     =>  18,
                //'J'     =>  15,
                'K'     =>  18,
                'L'     =>  15,
                //'M'     =>  15,
                //'N'     =>  15,
                'O'     =>  20,
                // 'P'     =>  40,
                //'Q'     =>  15,
                'R'     =>  20,
                'S'     =>  15,
                'T'     =>  15,
                //'U'     =>  15,
            ];
        }
    }

    /**
    */
    public function map($conference): array
    {
        if (empty($conference->contact_phone) && !empty($conference->address_line_3)) { // set the contact phone from address_line_3
            $contact_phone = $conference->address_line_3;
        } elseif (!empty($conference->contact_phone)) {
            $contact_phone = $conference->contact_phone;
        } else {
            $contact_phone = 'N/A';
        }

        //Checking State Council
        if($conference->state_council == 'act'){
            $state_council = "Canberra/Goulburn";
        } elseif($conference->state_council == 'nsw') {
            $state_council = "New South Wales";
        } elseif($conference->state_council == 'nt') {
            $state_council = "Northern Territory";
        } elseif($conference->state_council == 'qld') {
            $state_council = "Queensland";
        } elseif($conference->state_council == 'sa') {
            $state_council = "South Australia";
        } elseif($conference->state_council == 'tas') {
            $state_council = "Tasmania";
        } elseif($conference->state_council == 'vic') {
            $state_council = "Victoria";
        } elseif($conference->state_council == 'wa') {
            $state_council = "Western Australia";
        }else {
            $state_council = "National";
        }

        $value = [
            $conference->id,
            $conference->trashed() ? 'Abeyant' : 'Active',
            $conference->aggregation_number,
            ($conference->is_active_at ? $conference->is_active_at->format(config('vinnies.date_format')) : 'N/A'),
            ($conference->is_abeyant_at ? $conference->is_abeyant_at->format(config('vinnies.date_format')) : 'N/A'),
            $conference->name,
            ucwords($conference->parish ? $conference->parish : 'N/A'),
            $conference->contact_name ? $conference->contact_name : 'N/A',
            $conference->contact_email ? $conference->contact_email : 'N/A',
            // $contact_phone, //H
            ucwords($state_council ? $state_council : 'N/A'),
            ucwords($conference->diocesanCouncil()->get()->isNotEmpty() ? $conference->diocesanCouncil->name : 'N/A'),
            ucwords($conference->regional_council ? $conference->regional_council : 'N/A'),
            $conference->address_line_1 ? $conference->address_line_1 : 'N/A',
            $conference->address_line_2 ? $conference->address_line_2 : 'N/A',
            //$conference->address_line_3 ? $conference->address_line_3 : 'N/A',
            $conference->suburb ? $conference->suburb : 'N/A',
            $conference->state ? strtoupper($conference->state) : 'N/A',
            $conference->postcode ? $conference->postcode : 'N/A',
            $conference->comments ? $conference->comments : 'N/A',
            $conference->twinnings->filter(function ($twinning) {
                return $twinning->is_active;
            })->count(),
            $conference->twinnings->reject(function ($twinning) {
                return $twinning->is_active;
            })->count(),
            $this->donors->filter(function ($donor) use ($conference) {
                return $donor->local_conference_id == $conference->id;
            })->pluck('project_id')->unique()->count(),
            $conference->documents->count(),
        ];
        
        if(Auth::user()->hasRole('Full Admin')){
            array_splice( $value, 9, 0, $contact_phone);
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

               if(Auth::user()->hasRole('Full Admin')){
                    $event->sheet->getDelegate()->getStyle('A1:W1')->applyFromArray($styleArray)->getAlignment()->setVertical('center')->setWrapText(true);
                    $event->sheet->getStyle('A:W')->getAlignment()->setHorizontal('center')->setVertical('center');
                    $event->sheet->getStyle('Q')->getAlignment()->setWrapText(true);
               }else{
                    $event->sheet->getDelegate()->getStyle('A1:V1')->applyFromArray($styleArray)->getAlignment()->setVertical('center')->setWrapText(true);
                    $event->sheet->getStyle('A:V')->getAlignment()->setHorizontal('center')->setVertical('center');
                    $event->sheet->getStyle('R')->getAlignment()->setWrapText(true);
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
