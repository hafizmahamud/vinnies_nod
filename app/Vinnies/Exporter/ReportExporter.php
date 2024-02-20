<?php
namespace App\Vinnies\Exporter;

use App\Country;
use App\Vinnies\RemittanceFactory;

use App\Vinnies\Exporter\Sheets\CouncilsSheet;
use App\Vinnies\Exporter\Sheets\TwinningSheet;
use App\Vinnies\Exporter\Sheets\ProjectsSheet;
use App\Vinnies\Exporter\Sheets\CoverLetterSheet;

use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class ReportExporter implements WithMultipleSheets, WithProperties, Responsable
{
    use Exportable;

    private $fileName;
    private $writerType = Excel::XLSX;

    protected $year;
    protected $quarter;
    protected $country;
    protected $remittances;

    public function __construct($year, $quarter, Country $country)
    {
        $factory = new RemittanceFactory($year, $quarter);
        $factory->populate();

        $remittances       = $factory->get();
        $this->remittances = $remittances[$country->id];

        $this->year     = $year;
        $this->quarter  = $quarter;
        $this->country  = $country;

        $this->title    = sprintf(
            'Remittance Reports for %s (Q%s %s) - %s',
            $country->name,
            $quarter,
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

        $sheets[] = new CoverLetterSheet($this->remittances, $this->year, $this->quarter, $this->country, 'Cover Letter', 'twinning');
        $sheets[] = new TwinningSheet($this->remittances, $this->year, $this->quarter, $this->country, 'Twinning', 'twinning');
        $sheets[] = new TwinningSheet($this->remittances, $this->year, $this->quarter, $this->country, 'Grants', 'grants');
        $sheets[] = new TwinningSheet($this->remittances, $this->year, $this->quarter, $this->country, 'Council to Council', 'councils');
        $sheets[] = new ProjectsSheet($this->remittances, $this->year, $this->quarter, $this->country, 'Projects & Special Works', 'projects');

        return $sheets;
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
