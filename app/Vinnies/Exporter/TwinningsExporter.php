<?php
namespace App\Vinnies\Exporter;

//use Excel;
use Carbon\Carbon;
use App\Beneficiary;
use App\Vinnies\Helper;
use App\LocalConference;
use App\OverseasConference;
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

class TwinningsExporter implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents, WithProperties, WithColumnWidths
{
    public function __construct(Collection $data)
    {
        $this->filename = sprintf('Twinning - %s', date('Y.m.d'));
        $this->data = $data;
        $this->twinning_types = Helper::getTwinningTypes();
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        $value = [
          'TWINNING TYPE',
          'TWINNING STATUS',
          'DATE BECAME ACTIVE',
          'DATE STATUS CHECKED INITIATED',
          'DATE SURRENDERED',
          'QUARTER',
          'YEAR',
          'TWINNING ID',
          'OS CONF SRN',
          'OS CONF RECEIVING REMITTANCES?',
          'OS CONF NAME',
          'OS CONF PARISH',
          'CONTACT NAME',
          'CONTACT EMAIL',
          'ADDRESS',
          'SUBURB',
          'STATE',
          'POSTCODE',
          'COUNTRY',
          'NATIONAL/SUPERIOR COUNCIL',
          'CENTRAL COUNCIL',
          'OS CONF PARTICULAR COUNCIL',
          'AUS CONF SRN',
          'AUS CONF STATUS',
          'AUS CONF NAME',
          'AUS CONF PARISH',
          'AUS CONF CONTACT NAME',
        //   'AUS CONF CONTACT PHONE', //Z
          'AUS CONF CONTACT EMAIL',
          'AUS CONF ADDRESS',
          'AUS CONF STATE/TERRITORY COUNCIL',
          'AUS CONF DIOCESAN/CENTRAL COUNCIL',
          'AUS CONF REGIONAL COUNCIL',
          'PAYMENT AMOUNT AUD',
          'S/T COST CODE',
        ];
        
        if(Auth::user()->hasRole('Full Admin')){
            array_splice( $value, 14, 0, ['CONTACT PHONE']);
            array_splice( $value, 29, 0, ['AUS CONF CONTACT PHONE']);
        }

        return $value;
    }

    public function columnWidths(): array
    {
        if(Auth::user()->hasRole('Full Admin')){
            return [
                'A'     =>  30,
                'B'     =>  15,
                'C'     =>  20,
                'D'     =>  20,
                'E'     =>  20,
                'F'     =>  15,
                'G'     =>  15,
                'H'     =>  18,
                'I'     =>  18,
                'J'     =>  20,
                'K'     =>  45,
                'L'     =>  30,
                'M'     =>  25,
                'N'     =>  50,
                'O'     =>  25, //phone
                'P'     =>  30,
                'Q'     =>  15,
                'R'     =>  15,
                'S'     =>  15,
                'T'     =>  15,
                'U'     =>  25,
                'V'     =>  15,
                'W'     =>  15,
                'X'     =>  35,
                'Y'     =>  25,
                'Z'     =>  25,
                'AA'    =>  25,
                'AB'    =>  25,
                'AC'    =>  50,
                'AD'    =>  25, //phone
                'AE'    =>  15,
                'AF'    =>  25,
                'AG'    =>  25,
                'AH'    =>  20,
                'AI'    =>  20,
                'AJ'    =>  15,
            ];
        }else{
            return [
                'A'     =>  30,
                'B'     =>  15,
                'C'     =>  20,
                'D'     =>  20,
                'E'     =>  20,
                'F'     =>  15,
                'G'     =>  15,
                'H'     =>  18,
                'I'     =>  18,
                'J'     =>  20,
                'K'     =>  45,
                'L'     =>  30,
                'M'     =>  25,
                'N'     =>  50,
                'O'     =>  30,
                'P'     =>  15,
                'Q'     =>  15,
                'R'     =>  15,
                'S'     =>  15,
                'T'     =>  25,
                'U'     =>  15,
                'V'     =>  15,
                'W'     =>  35,
                'X'     =>  25,
                'Y'     =>  25,
                'Z'     =>  25,
                'AA'    =>  25,
                'AB'    =>  50,
                'AC'    =>  15,
                'AD'    =>  25,
                'AE'    =>  25,
                'AF'    =>  20,
                'AG'    =>  20,
                'AH'    =>  15,
              ];
        }
    }

    /**
    */
    public function map($twinning): array
    {
        $overseas_conference = OverseasConference::find($twinning->overseas_conference_id);
        $local_conference    = LocalConference::withTrashed()->find($twinning->local_conference_id);

        $address_full = $overseas_conference->address_line_1;
        if (!empty($overseas_conference->address_line_2)) $address_full .= ', ' . $overseas_conference->address_line_2;
        if (!empty($overseas_conference->address_line_3)) $address_full .= ', ' . $overseas_conference->address_line_3;

        if (empty($local_conference->contact_phone) && is_numeric($local_conference->address_line_3)) { // set the contact phone from address_line_3
            $local_conf_contact_phone = $local_conference->address_line_3;
        } elseif (!empty($local_conference->contact_phone)) {
            $local_conf_contact_phone = $local_conference->contact_phone;
        } else {
            $local_conf_contact_phone = 'N/A';
        }

        if (!empty($overseas_conference->contact_phone)) {
            $overseas_conf_contact_phone = $overseas_conference->contact_phone;
        } else {
            $overseas_conf_contact_phone = 'N/A';
        }

        $local_conf_address  = array();
        (empty($local_conference->address_line_1) ? '' : $local_conf_address[] = $local_conference->address_line_1);
        (empty($local_conference->address_line_2) ? '' : $local_conf_address[] = $local_conference->address_line_2);
        //(empty($local_conference->address_line_3) ? '' : $local_conf_address[] = $local_conference->address_line_3); // remove as rarely use and used as phone contact instead
        (empty($local_conference->suburb) ? '' : $local_conf_address[] = $local_conference->suburb);
        strtoupper(empty($local_conference->state) ? '' : $local_conf_address[] = strtoupper($local_conference->state));
        (empty($local_conference->postcode) ? '' : $local_conf_address[] = $local_conference->postcode);
        $loc_conf_address = implode(', ', $local_conf_address);

        $value = [
            $this->twinning_types[$twinning->type],
            ($twinning->is_active ? 'Active' : 'Surrendered'),
            ($twinning->is_active_at ? (new Carbon($twinning->is_active_at))->format(config('vinnies.date_format')) : 'N/A'),
            ($overseas_conference->status_check_initiated_at ? (new Carbon($overseas_conference->status_check_initiated_at))->format(config('vinnies.date_format')) : 'N/A'),
            ($twinning->is_surrendered_at ? (new Carbon($twinning->is_surrendered_at))->format(config('vinnies.date_format')) : 'N/A'),
            '',
            '',
            $twinning->id,
            $overseas_conference->id,
            ($overseas_conference->is_active ? 'Remittances' : 'No Remittances'),
            $overseas_conference->name,
            (empty($overseas_conference->parish) ? 'N/A' : $overseas_conference->parish),
            (empty($overseas_conference->contact_name) ? 'N/A' : $overseas_conference->contact_name),
            (empty($overseas_conference->contact_email) ? 'N/A' : $overseas_conference->contact_email),
            (empty($address_full) ? 'N/A' : $address_full),
            (empty($overseas_conference->suburb) ? 'N/A' : $overseas_conference->suburb),
            (empty($overseas_conference->state) ? 'N/A' : $overseas_conference->state),
            (empty($overseas_conference->postcode) ? 'N/A' : $overseas_conference->postcode),
            $overseas_conference->country->name,
            $overseas_conference->beneficiary?->name ? $overseas_conference->beneficiary?->name : 'N/A',
            (empty($overseas_conference->central_council) ? 'N/A' : $overseas_conference->central_council),
            (empty($overseas_conference->particular_council) ? 'N/A' : $overseas_conference->particular_council),
            $local_conference->id,
            ($local_conference->trashed() ? 'Abeyant' : 'Active'),
            $local_conference->name,
            (empty($local_conference->parish) ? 'N/A' : $local_conference->parish),
            (empty($local_conference->contact_name) ? 'N/A' : $local_conference->contact_name),
            // $local_conf_contact_phone,
            (empty($local_conference->contact_email) ? 'N/A' : $local_conference->contact_email),
            (empty($loc_conf_address) ? 'N/A' : $loc_conf_address),
            (empty($local_conference->state_council)) ? 'N/A' : Helper::getStateNameByKey($local_conference->state_council),
            (empty($local_conference->diocesanCouncil) ? 'N/A' : $local_conference->diocesanCouncil->name),
            (empty($local_conference->regional_council) ? 'N/A' : $local_conference->regional_council),
            '',
            (empty($local_conference->cost_code) ? 'N/A' : $local_conference->cost_code),
        ];        
        
        if(Auth::user()->hasRole('Full Admin')){
            array_splice( $value, 14, 0, [$overseas_conf_contact_phone]);
            array_splice( $value, 29, 0, [$local_conf_contact_phone]);
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


               $event->sheet->getStyle('A')->setConditionalStyles($conditionalStyles);
               $event->sheet->getStyle('B')->setConditionalStyles($conditionalStyles);
               $event->sheet->getStyle('J')->setConditionalStyles($conditionalStyles);

               if(Auth::user()->hasRole('Full Admin')){
                    $event->sheet->getStyle('W')->setConditionalStyles($conditionalStyles);

                    $event->sheet->getDelegate()->getStyle('A1:AJ1')->applyFromArray($styleArray)->getAlignment()->setVertical('center')->setWrapText(true);
                    $event->sheet->getStyle('A:AJ')->getAlignment()->setHorizontal('center')->setVertical('center');

                    $rows = ['A', 'B', 'F', 'G', 'H', 'I', 'K', 'T', 'X', 'AF', 'AI'];
               }else{
                    $event->sheet->getStyle('V')->setConditionalStyles($conditionalStyles);
    
                    $event->sheet->getDelegate()->getStyle('A1:AH1')->applyFromArray($styleArray)->getAlignment()->setVertical('center')->setWrapText(true);
                    $event->sheet->getStyle('A:AH')->getAlignment()->setHorizontal('center')->setVertical('center');
    
                    $rows = ['A', 'B', 'F', 'G', 'H', 'I', 'K', 'S', 'W', 'AD', 'AG'];
               }
               
               foreach ($rows as $row) {
                     $event->sheet->getDelegate()->getStyle($row . '1:' . $row . (count($this->data) + 1))->getFill()
                                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                    ->getStartColor()->setRGB('92d050');
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
