<?php

namespace App\Http\Controllers;

use App\OldRemittance;
use App\Vinnies\Money;
use Illuminate\Http\Request;

class OldRemittanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        $this->authorize('read.old-remittances');

        return view('remittances.old.list');
    }

    public function datatables(Request $request)
    {
        $this->authorize('read.old-remittances');

        $remittances = OldRemittance::whereNotNull('id');
        $remittances = $this->sortModelFromRequest($remittances, $request);

        if (!empty($filters = $request->get('filters'))) {
            if (!empty($filters['state'])) {
                $remittances->where('state', $filters['state']);
            }

            if (!empty($filters['quarter'])) {
                $remittances->where('quarter', $filters['quarter']);
            }

            if (!empty($filters['year'])) {
                $remittances->where('year', $filters['year']);
            }
        }

        if (!empty($keyword = $request->get('search')['value'])) {
            $remittances->where(function ($query) use ($keyword) {
                $query->where('id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('state', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('allocated', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('year', 'LIKE', '%' . $keyword . '%');
            });
        }

        $remittances = $remittances->paginate(config('vinnies.pagination.old_remittances'));
        $data  = $this->getDatatableBaseData($remittances, $request);

        foreach ($remittances as $remittance) {

            //Get State/Territory Council Full Name
            if($remittance->state == 'act'){
                $state = "Canberra/Goulburn";
            }elseif($remittance->state == 'nsw'){
                $state = "New South Wales";
            }elseif($remittance->state == 'nt'){
                $state = "Northern Territory";
            }elseif($remittance->state == 'qld'){
                $state = "Queensland";
            }elseif($remittance->state == 'sa'){
                $state = "South Australia";
            }elseif($remittance->state == 'tas'){
                $state = "Tasmania";
            }elseif($remittance->state == 'vic'){
                $state = "Victoria";
            }elseif($remittance->state == 'wa'){
                $state = "Western Australia";
            }elseif($remittance->state == 'national'){
                $state = "National";
            }else{
                $state = "N/A";
            }

            $data['data'][] = [
                'id'          => $remittance->id,
                // 'state'       => strtoupper($remittance->state),
                'state'       => $state,
                'received_at' => $remittance->received_at->format(config('vinnies.date_format')),
                'quarter'     => 'Q' . $remittance->quarter,
                'year'        => $remittance->year,
                'total'       => $remittance->getFormattedTotalDonations(),
                'allocated'   => $remittance->getFormattedAllocated(),
            ];
        }

        return $data;
    }

    public function view(OldRemittance $remittance)
    {
        $this->authorize('read.old-remittances');

        return view('remittances.old.view')->with(compact('remittance'));
    }
}

