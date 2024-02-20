<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use App\Donor;
use App\Vinnies\Money;
use App\Vinnies\Helper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DonorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        $this->authorize('read.donors');

        $data   = [];
        $user   = Auth::user();
        $donors = Donor::whereNotNull('id');

        if ($request->has('project_id')) {
            $donors->where('project_id', $request->get('project_id'));
        }

        $data['donors'] = $donors->get()->map(function($donor) use ($user) {
            return [
                'id'                  => $donor->id,
                'local_conference_id' => $donor->localConference->id,
                'local_conference_url'=> route('local-conferences.edit', $donor->localConference->id),
                'name'                => $donor->localConference->name,
                'state'               => strtoupper($donor->localConference->state),
                'total'               => (new Money($donor->contributions->sum('amount')))->value(),
                'edit_url'            => route('donors.edit', $donor),
                'delete_url'          => route('donors.delete', $donor),
                'has_contributions'   => $donor->contributions->isNotEmpty(),
                'contributions'       => $donor->contributions->map(function ($contribution) use ($donor) {
                    return [
                        'id'         => $contribution->id,
                        'date'       => optional($contribution->paid_at)->format(config('vinnies.date_format')),
                        'quarter'    => $contribution->quarter,
                        'year'       => $contribution->year,
                        'amount'     => (new Money($contribution->amount))->value(),
                        'edit_url'   => route('contributions.edit', $contribution),
                        'delete_url' => route('contributions.delete', [$donor->project, $contribution]),
                    ];
                }),
            ];
        });

        $data['can_create_donors']        = $user->hasPermissionTo('create.donors');
        $data['can_edit_donors']          = $user->hasPermissionTo('update.donors');
        $data['can_delete_donors']        = $user->hasPermissionTo('delete.donors');
        $data['can_create_contributions'] = $user->hasPermissionTo('create.contributions');
        $data['can_edit_contributions']   = $user->hasPermissionTo('update.contributions');
        $data['can_delete_contributions'] = $user->hasPermissionTo('delete.contributions');

        return response()->json($data);
    }

    public function create(Request $request)
    {
        $this->authorize('create.donors');

        $data = $request->validate([
            'project_id'          => 'required|integer|exists:projects,id',
            'local_conference_id' => 'required|integer|exists:local_conferences,id',
        ]);

        $donor = Donor::create($data);

        $project = $donor->project;
        $project->updated_by = Auth::id();
        $project->updated_at = Carbon::now();
        $project->save();
        $project->updateSortFields();

        return response()->json([
            'msg' => 'Donor successfully added',
        ]);
    }

    public function edit(Request $request, Donor $donor)
    {
        $this->authorize('update.donors');

        $data = $request->validate([
            'project_id'          => 'required|integer|exists:projects,id',
            'local_conference_id' => 'required|integer|exists:local_conferences,id',
        ]);

        $donor->update($data);

        $project = $donor->project;
        $project->updated_by = Auth::id();
        $project->updated_at = Carbon::now();
        $project->save();
        $project->updateSortFields();

        return response()->json([
            'msg' => 'Donor successfully updated',
        ]);

        //return $donor;
    }

    public function delete(Donor $donor)
    {
        $this->authorize('delete.donors');

        $donor->contributions()->delete();

        $project = $donor->project;
        $project->updated_by = Auth::id();
        $project->updated_at = Carbon::now();
        $project->save();
        $donor->delete();
        $project->updateSortFields();

        return response()->json([
            'msg' => 'Donor successfully deleted',
        ]);
    }
}
