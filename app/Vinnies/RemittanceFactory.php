<?php

namespace App\Vinnies;

use App\Country;
use App\Beneficiary;
use App\CouncilDonation;
use App\GrantDonation;
use App\NewRemittance;
use App\OverseasConference;
use App\ProjectDonation;
use App\TwinningDonation;
use App\Vinnies\Money;
use App\Vinnies\Helper;
use Illuminate\Support\Facades\Log;

class RemittanceFactory
{
    protected $remittances;
    protected $quarter;
    protected $year;

    public function __construct($year, $quarter)
    {
        $this->year    = $year;
        $this->quarter = $quarter;

        Country::orderBy('name', 'asc')->get()->each(function ($country) {
            $beneficiary = Beneficiary::where('country_id', $country->id)->first();

            $this->remittances[$country->id] = [
                'id'               => $country->id,
                'country'          => $country->name,
                'beneficiary'      => $beneficiary,
                'quarter'          => $this->quarter,
                'year'             => $this->year,
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
        $remittances = NewRemittance::where('quarter', $this->quarter)
            ->where('year', $this->year)
            ->where('is_approved', 1)
            ->orderBy('state', 'asc')
            ->get();

        // Repopulate all donations by country
        $remittances->each(function ($remittance) {
            if ($remittance->projectDonations->isNotEmpty()) {
                $remittance->projectDonations->each(function ($donation) {
                    $this->remittances[$donation->project->beneficiary->country_id]['donations']['projects'][] = $donation;
                });
            }

            if ($remittance->grantDonations->isNotEmpty()) {
                $remittance->grantDonations->each(function ($donation) {
                    $this->remittances[$donation->twinning->overseasConference->country_id]['donations']['grants'][] = $donation;
                });
            }

            if ($remittance->twinningDonations->isNotEmpty()) {
                $remittance->twinningDonations->each(function ($donation) {
                    $this->remittances[$donation->twinning->overseasConference->country_id]['donations']['twinning'][] = $donation;
                });
            }

            if ($remittance->councilDonations->isNotEmpty()) {
                $remittance->councilDonations->each(function ($donation) {
                    $this->remittances[$donation->twinning->overseasConference->country_id]['donations']['councils'][] = $donation;
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

    public function isAllApproved()
    {
        $remittances = NewRemittance::where('quarter', $this->quarter)
            ->where('year', $this->year)
            ->get()
            ->reject(function ($remittance) {
                return $remittance->is_approved == 0;
            });

        return $remittances->isNotEmpty();
    }

    public function hasUnapprovedRemittance()
    {
        $remittances = NewRemittance::where('quarter', $this->quarter)
            ->where('year', $this->year)
            ->get()
            ->filter(function ($remittance) {
                return $remittance->is_approved == 0;
            });

        return $remittances->isNotEmpty();
    }


    public function countTotalRemitances()
    {

        $remittances = NewRemittance::where('quarter', $this->quarter)
            ->where('year', $this->year)
            ->get();

        return $remittances->count();
    }

    public function get()
    {
        return $this->remittances;
    }

    public function setRemittances($remittances)
    {
        $this->remittances = $remittances;
    }

    public function populateRemittance()
    {
        $remittanceData = [];

        foreach ($this->remittances as $remittanceSingle) {
            $country_id = $remittanceSingle['id'];

            $sumAmountTwinning = TwinningDonation::whereRelation('twinning.overseasConference', 'country_id', $country_id)
                                                ->whereRelation('remittance', 'year', '=', $remittanceSingle['year'])
                                                ->whereRelation('remittance', 'quarter', '=', $remittanceSingle['quarter'])
                                                ->whereRelation('remittance', 'is_approved', '=', 1)
                                                ->sum('amount');

            $sumAmountProject = ProjectDonation::whereRelation('project.beneficiary', 'country_id', $country_id)
                                                ->whereRelation('remittance', 'year', '=', $remittanceSingle['year'])
                                                ->whereRelation('remittance', 'quarter', '=', $remittanceSingle['quarter'])
                                                ->whereRelation('remittance', 'is_approved', '=', 1)
                                                ->sum('amount');

            $sumAmountGrant = GrantDonation::whereRelation('twinning.overseasConference', 'country_id', $country_id)
                                                ->whereRelation('remittance', 'year', '=', $remittanceSingle['year'])
                                                ->whereRelation('remittance', 'quarter', '=', $remittanceSingle['quarter'])
                                                ->whereRelation('remittance', 'is_approved', '=', 1)
                                                ->sum('amount');

            $sumAmountCouncils = CouncilDonation::whereRelation('twinning.overseasConference', 'country_id', $country_id)
                                                ->whereRelation('remittance', 'year', '=', $remittanceSingle['year'])
                                                ->whereRelation('remittance', 'quarter', '=', $remittanceSingle['quarter'])
                                                ->whereRelation('remittance', 'is_approved', '=', 1)
                                                ->sum('amount');

            $remittanceSingle['projects'] = (new Money($sumAmountProject))->value();
            $remittanceSingle['grants'] = (new Money($sumAmountGrant))->value();
            $remittanceSingle['twinning'] = (new Money($sumAmountTwinning))->value();
            $remittanceSingle['councils'] = (new Money($sumAmountCouncils))->value();

            $remittanceSingle['total'] = $remittanceSingle['projects'] + $remittanceSingle['grants'] + $remittanceSingle['twinning'] + $remittanceSingle['councils'];
            
            $remittanceSingle['total'] = (new Money($remittanceSingle['total']))->value();

            $remittanceData[] = $remittanceSingle;
        }

        return $remittanceData;
    }
}
