<?php

namespace App\Vinnies;

use App\Country;
use App\Beneficiary;
use App\NewRemittance;
use App\Vinnies\Money;
use App\Vinnies\Helper;

class RemittanceDateRangeFactory
{
    protected $remittances;
    protected $date_type;
    protected $date_start;
    protected $date_end;

    public function __construct($date_type, $date_start, $date_end)
    {
        $date_check        = explode('-', $date_start);
        $this->year        = $date_check[0];
        $this->quarter     = '';
        $this->date_type   = $date_type;
        $this->date_start  = $date_start;
        $this->date_end    = $date_end;

        Country::orderBy('name', 'asc')->get()->each(function ($country) {
            $beneficiary = Beneficiary::first();

            $this->remittances[$country->id] = [
                'country'          => $country->name,
                'beneficiary'      => $beneficiary,
                'year'             => $this->year,
                'quarter'          => $this->quarter,
                'donations'        => [
                    'projects' => [],
                    'twinning' => [],
                    'grants'   => [],
                    'councils' => [],
                ],
                'total'    => (new Money(0.00))->value(),
                'projects' => (new Money(0.00))->value(),
                'twinning' => (new Money(0.00))->value(),
                'grants'   => (new Money(0.00))->value(),
                'councils' => (new Money(0.00))->value(),
            ];
        });
    }

    public function populate()
    {
        $remittances = NewRemittance::whereBetween($this->date_type, [$this->date_start . " 00:00:00",$this->date_end . " 23:59:59"])
            ->where('is_approved', 1)
            ->orderBy('state', 'asc')
            ->orderBy('year', 'asc')
            ->orderBy('quarter', 'asc')
            ->get();

        // Repopulate all donations by country
        $remittances->each(function ($remittance) {
            // $this->year[$remittance->id] = $remittance->year;
            // $this->quarter[$remittance->id] = $remittance->quarter;

            if ($remittance->projectDonations->isNotEmpty()) {
                $remittance->projectDonations->each(function ($donation) use ($remittance) {
                    $this->remittances[$donation->project->beneficiary->country_id]['donations']['projects'][] = $donation;
                    $this->remittances[$donation->project->beneficiary->country_id]['year'] = $remittance->year;
                    $this->remittances[$donation->project->beneficiary->country_id]['quarter'] = $remittance->quarter;
                });
            }

            if ($remittance->grantDonations->isNotEmpty()) {
                $remittance->grantDonations->each(function ($donation) use ($remittance) {
                    $this->remittances[$donation->twinning->overseasConference->country_id]['donations']['grants'][] = $donation;
                    $this->remittances[$donation->twinning->overseasConference->country_id]['year'] = $remittance->year;
                    $this->remittances[$donation->twinning->overseasConference->country_id]['quarter'] = $remittance->quarter;
                });
            }

            if ($remittance->twinningDonations->isNotEmpty()) {
                $remittance->twinningDonations->each(function ($donation) use ($remittance) {
                    $this->remittances[$donation->twinning->overseasConference->country_id]['donations']['twinning'][] = $donation;
                    $this->remittances[$donation->twinning->overseasConference->country_id]['year'] = $remittance->year;
                    $this->remittances[$donation->twinning->overseasConference->country_id]['quarter'] = $remittance->quarter;
                });
            }

            if ($remittance->councilDonations->isNotEmpty()) {
                $remittance->councilDonations->each(function ($donation)  use ($remittance) {
                    $this->remittances[$donation->twinning->overseasConference->country_id]['donations']['councils'][] = $donation;
                    $this->remittances[$donation->twinning->overseasConference->country_id]['year'] = $remittance->year;
                    $this->remittances[$donation->twinning->overseasConference->country_id]['quarter'] = $remittance->quarter;
                });
            }
        });

        // Then recalculate all the totals
        foreach ($this->remittances as $country_id => $remittance) {
            if (!empty($remittance['donations']['projects'])) {
                $this->remittances[$country_id]['projects'] = (new Money(collect($remittance['donations']['projects'])->sum('amount')))->value();
            }

            if (!empty($remittance['donations']['twinning'])) {
                $this->remittances[$country_id]['twinning'] = (new Money(collect($remittance['donations']['twinning'])->sum('amount')))->value();
            }

            if (!empty($remittance['donations']['grants'])) {
                $this->remittances[$country_id]['grants']   = (new Money(collect($remittance['donations']['grants'])->sum('amount')))->value();
            }

            if (!empty($remittance['donations']['councils'])) {
                $this->remittances[$country_id]['councils'] = (new Money(collect($remittance['donations']['councils'])->sum('amount')))->value();
            }

            $this->remittances[$country_id]['total'] = $this->remittances[$country_id]['projects'] + $this->remittances[$country_id]['twinning'] + $this->remittances[$country_id]['grants'] + $this->remittances[$country_id]['councils'];
            $this->remittances[$country_id]['total'] = (new Money($this->remittances[$country_id]['total']))->value();
        }
    }

    public function get()
    {
        return $this->remittances;
    }
}
