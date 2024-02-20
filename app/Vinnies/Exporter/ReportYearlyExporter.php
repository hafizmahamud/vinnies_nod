<?php
namespace App\Vinnies\Exporter;


use App\Country;
use Carbon\Carbon;

use App\Vinnies\RemittanceYearlyFactory;
use App\Vinnies\Exporter\Sheets\TwinningYearlySheet;
use App\Vinnies\Exporter\Sheets\ProjectsYearlySheet;

use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportYearlyExporter implements WithMultipleSheets, WithProperties, Responsable
{
    use Exportable;

    private $fileName;
    private $writerType = Excel::XLSX;

    protected $year;
    protected $quarter;
    protected $remittances;
    protected $total_amount = array();

    public function __construct($year)
    {
        $factory = new RemittanceYearlyFactory($year);
        $factory->populate();

        $remittances       = $factory->get();
        $this->remittances = $remittances;
        $this->year        = $year;

        $this->total_amount['twinning'] = 0;
        $this->total_amount['grants']   = 0;
        $this->total_amount['councils'] = 0;
        $this->total_amount['projects'] = 0;

        $this->title    = sprintf(
            'Remittance Summary Reports from %s to %s - %s',
            $year-1,
            $year,
            date('Y.m.d')
        );

        $this->fileName = $this->title  . '.xlsx';
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];
        $twinning = $this->getData('twinning');
        $grants   = $this->getData('grants');
        $councils = $this->getData('councils');
        $projects = $this->getProjectsData();

        $sheets[] = new TwinningYearlySheet($this->year, 'Twinning', $twinning, $this->total_amount['twinning']);
        $sheets[] = new TwinningYearlySheet($this->year, 'Grants', $grants, $this->total_amount['grants']);
        $sheets[] = new TwinningYearlySheet($this->year, 'Council to Council', $councils, $this->total_amount['councils']);
        $sheets[] = new ProjectsYearlySheet($this->year, 'Projects & Special Works', $projects, $this->total_amount['projects']);

        return $sheets;
    }

    private function getData($type) {
        $remits = array();

        foreach ($this->remittances as $remittances) {
            foreach ($remittances['donations'][$type] as $donation) {
                $remits[] = [
                        'country'             => optional($donation->twinning->overseasConference->country)->name,
                        'os_srn'              => $donation->twinning->overseasConference->id,
                        'year'                => $remittances['year'],
                        'os_name'             => $donation->twinning->overseasConference->name,
                        'central_council'     => $donation->twinning->overseasConference->central_council,
                        'particular_council'  => $donation->twinning->overseasConference->particular_council,
                        'parish'              => $donation->twinning->overseasConference->parish,
                        'lc_id'               => $donation->twinning->localConference->id,
                        'lc_name'             => $donation->twinning->localConference->name,
                        'lc_state'            => strtoupper($donation->twinning->localConference->state),
                        'recieved_at'         => $donation->remittance->date->format('d/m/Y'),
                        'uploaded_at'         => $donation->created_at->format('d/m/Y'),
                        'approved_at'         => $donation->remittance->approved_at ? $donation->remittance->approved_at->format('d/m/Y') : '-',
                        'amount'              => $donation->amount,                    ];
            }

            $this->total_amount[$type] += $remittances[$type];
        }

        array_multisort($remits);

        $i = 0;
        $prints = array();

        foreach ($remits as $t) {
            // if ( $i > 0
            //   && $prints[$i-1]['os_srn'] == $t['os_srn']
            //   && $prints[$i-1]['lc_id'] == $t['lc_id']
            // ) {
            //     $prints[] = [
            //           'year'                => $this->year-1 . '-' . $this->year,
            //           'os_srn'              => $t['os_srn'],
            //           'os_name'             => $t['os_name'],
            //           'country'             => $t['country'],
            //           'central_council'     => $t['central_council'],
            //           'particular_council'  => $t['particular_council'],
            //           'parish'              => $t['parish'],
            //           'lc_id'               => $t['lc_id'],
            //           'lc_name'             => $t['lc_name'],
            //           'lc_state'            => $t['lc_state'],
            //           'recieved_at'         => $t['recieved_at'],
            //           'uploaded_at'         => $t['uploaded_at'],
            //           'approved_at'         => $t['approved_at'],
            //           'amount'              => $t['amount'] + $prints[$i-1]['amount'],
            //       ];
            //
            //     unset($prints[$i-1]);
            // } else {
            //     $prints[] = [
            //           'year'                => $this->year-1 . '-' . $this->year,
            //           'os_srn'              => $t['os_srn'],
            //           'os_name'             => $t['os_name'],
            //           'country'             => $t['country'],
            //           'central_council'     => $t['central_council'],
            //           'particular_council'  => $t['particular_council'],
            //           'parish'              => $t['parish'],
            //           'lc_id'               => $t['lc_id'],
            //           'lc_name'             => $t['lc_name'],
            //           'lc_state'            => $t['lc_state'],
            //           'recieved_at'         => $t['recieved_at'],
            //           'uploaded_at'         => $t['uploaded_at'],
            //           'approved_at'         => $t['approved_at'],
            //           'amount'              => $t['amount'],
            //       ];
            // }

            $prints[] = [
                  'year'                => $this->year-1 . '-' . $this->year,
                  'os_srn'              => $t['os_srn'],
                  'os_name'             => $t['os_name'],
                  'country'             => $t['country'],
                  'central_council'     => $t['central_council'],
                  'particular_council'  => $t['particular_council'],
                  'parish'              => $t['parish'],
                  'lc_id'               => $t['lc_id'],
                  'lc_name'             => $t['lc_name'],
                  'lc_state'            => $t['lc_state'],
                  'recieved_at'         => $t['recieved_at'],
                  'uploaded_at'         => $t['uploaded_at'],
                  'approved_at'         => $t['approved_at'],
                  'amount'              => $t['amount'],
              ];

            $i++;
        }

        return $prints;
    }

    private function getProjectsData() {
        $remits = array();

        foreach ($this->remittances as $remittances) {
            foreach ($remittances['donations']['projects'] as $donation) {
                $remits[] = [
                    'country'             => ($donation->project->beneficiary->country) ? $donation->project->beneficiary->country->name : 'N/A',
                    'project_id'          => $donation->project->id,
                    'year'                => $remittances['year'],
                    'project_name'        => $donation->project->name,
                    'oc_id'               => ($donation->project->overseasConference) ? $donation->project->overseasConference->id : 'N/A',
                    'oc_name'             => ($donation->project->overseasConference) ? $donation->project->overseasConference->name : 'N/A',
                    'central_council'     => ($donation->project->overseasConference) ? $donation->project->overseasConference->central_council : 'N/A',
                    'particular_council'  => ($donation->project->overseasConference) ? $donation->project->overseasConference->particular_council : 'N/A',
                    'parish'              => ($donation->project->overseasConference) ? $donation->project->overseasConference->parish : 'N/A',
                    'donor_id'            => $donation->donor->id,
                    'donor_name'          => $donation->donor->name,
                    'donor_state'         => strtoupper($donation->donor->state),
                    'recieved_at'         => $donation->remittance->date->format('d/m/Y'),
                    'uploaded_at'         => $donation->created_at->format('d/m/Y'),
                    'approved_at'         => $donation->remittance->approved_at ? $donation->remittance->approved_at->format('d/m/Y') : '-',
                    'amount'              => $donation->amount,
                ];
            }

            $this->total_amount['projects'] += $remittances['projects'];
        }

        array_multisort($remits);

        $i = 0;
        $prints = array();

        foreach ($remits as $t) {
            // if ( $i > 0
            //   && $prints[$i-1]['project_id'] == $t['project_id']
            //   && $prints[$i-1]['oc_id'] == $t['oc_id']
            // ) {
            //     $prints[] = [
            //           'year'                => $this->year-1 . '-' . $this->year,
            //           'project_id'          => $t['project_id'],
            //           'project_name'        => $t['project_name'],
            //           'country'             => $t['country'],
            //           'oc_id'               => $t['oc_id'],
            //           'oc_name'             => $t['oc_name'],
            //           'central_council'     => $t['central_council'],
            //           'particular_council'  => $t['particular_council'],
            //           'parish'              => $t['parish'],
            //           'donor_id'            => $t['donor_id'],
            //           'donor_name'          => $t['donor_name'],
            //           'donor_state'         => $t['donor_state'],
            //           'recieved_at'         => $t['recieved_at'],
            //           'uploaded_at'         => $t['uploaded_at'],
            //           'approved_at'         => $t['approved_at'],
            //           'amount'              => $t['amount'] + $prints[$i-1]['amount'],
            //       ];
            //
            //     unset($prints[$i-1]);
            // } else {
            //     $prints[] = [
            //         'year'                => $this->year-1 . '-' . $this->year,
            //         'project_id'          => $t['project_id'],
            //         'project_name'        => $t['project_name'],
            //         'country'             => $t['country'],
            //         'oc_id'               => $t['oc_id'],
            //         'oc_name'             => $t['oc_name'],
            //         'central_council'     => $t['central_council'],
            //         'particular_council'  => $t['particular_council'],
            //         'parish'              => $t['parish'],
            //         'donor_id'            => $t['donor_id'],
            //         'donor_name'          => $t['donor_name'],
            //         'donor_state'         => $t['donor_state'],
            //         'recieved_at'         => $t['recieved_at'],
            //         'uploaded_at'         => $t['uploaded_at'],
            //         'approved_at'         => $t['approved_at'],
            //         'amount'              => $t['amount'],
            //       ];
            // }

            $prints[] = [
                'year'                => $this->year-1 . '-' . $this->year,
                'project_id'          => $t['project_id'],
                'project_name'        => $t['project_name'],
                'country'             => $t['country'],
                'oc_id'               => $t['oc_id'],
                'oc_name'             => $t['oc_name'],
                'central_council'     => $t['central_council'],
                'particular_council'  => $t['particular_council'],
                'parish'              => $t['parish'],
                'donor_id'            => $t['donor_id'],
                'donor_name'          => $t['donor_name'],
                'donor_state'         => $t['donor_state'],
                'recieved_at'         => $t['recieved_at'],
                'uploaded_at'         => $t['uploaded_at'],
                'approved_at'         => $t['approved_at'],
                'amount'              => $t['amount'],
              ];

            $i++;
        }

        return $prints;
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
