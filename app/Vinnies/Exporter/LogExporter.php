<?php
namespace App\Vinnies\Exporter;

//use Excel;
use Carbon\Carbon;
use App\Beneficiary;
use App\Vinnies\Helper;
use App\LocalConference;
use App\OverseasConference;
use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithProperties;

class LogExporter implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents, WithProperties, WithColumnWidths
{
    public function __construct(Collection $activity, $isIndividual, $type)
    {
        $this->activity = $activity;
        $this->isIndividual = $isIndividual;
        $this->type = $type;
    }

    public function collection()
    {
        return $this->activity;
    }

    public function headings(): array
    {
        $value = [
          'Log ID',
          'Event Type',
          'Subject ID',
          'Old',
          'Changes',
          'Updated At',
          'Updated By',
        ];

        return $value;
    }

    public function columnWidths(): array
    {
        return [
            'A'     =>  15,
            'B'     =>  20,
            'C'     =>  15,
            'D'     =>  50,
            'E'     =>  50,
            'F'     =>  20,
            'G'     =>  30,
        ];
    }

    /**
    */
    public function map($activity): array
    {
        $user = User::where('id', $activity->causer_id)->withTrashed()->first();

        $old = '';
        if(isset($activity->properties['old'])){
            foreach($activity->properties['old'] as $field => $value){
                if(!is_array($activity->properties['old'][$field])){
                    $old .= $field . ' : ' . $activity->properties['old'][$field] . PHP_EOL;
                }else{
                    $old .= $field . PHP_EOL;
                    foreach($activity->properties['old'][$field] as $key => $secondValue){
                        $old .= $activity->properties['old'][$field][$key] . PHP_EOL;
                    }
                }
            }
        }

        $changes = '';
        if(isset($activity->properties['attributes'])){
            foreach($activity->properties['attributes'] as $field => $value){
                if(!is_array($activity->properties['attributes'][$field])){
                    $changes .= $field . ' : ' . $activity->properties['attributes'][$field] . PHP_EOL;
                }else{
                    $changes .= $field . PHP_EOL;
                    foreach($activity->properties['attributes'][$field] as $key => $secondValue){
                        $changes .= $activity->properties['attributes'][$field][$key] . PHP_EOL;
                    }
                }
            }
        }

        $event = '';
        if($this->isIndividual){
            $events = explode('\\',$activity->subject_type);
            if($events[1] != $this->type){
                $event .= ($events[1] . ' ');
            }
            $event .= $activity->event;
        }else{
            $event = $activity->event;
        }

        $value = [
            $activity->id,
            $event,
            $activity->subject_id,
            $old,
            $changes,
            date('d-m-Y H:i:s', strtotime($activity->updated_at)),
            $user ? $user->first_name . ' ' . $user->last_name : $activity->causer_id
        ];

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

                $event->sheet->getDelegate()->getStyle('A1:G1')->applyFromArray($styleArray)->getAlignment()->setVertical('center')->setWrapText(true);
                $event->sheet->getStyle('A:G')->getAlignment()->setHorizontal('center')->setVertical('center');
                $event->sheet->getStyle('D:E')->getAlignment()->setWrapText(true);

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
            'title'          => 'Log Activity',
            'manager'        => 'St Vincent de Paul Society',
            'company'        => 'St Vincent de Paul Society',
        ];
    }
}
