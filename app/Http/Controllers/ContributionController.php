<?php

namespace App\Http\Controllers;

use Auth;
use App\Project;
use Carbon\Carbon;
use App\Contribution;
use App\Vinnies\Helper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContributionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Request $request)
    {
        $this->authorize('create.contributions');

        $data = $request->validate([
            'donor_id' => 'required|integer|exists:donors,id',
            'paid_at'  => 'required|date_format:' . config('vinnies.date_format'),
            'amount'   => 'required|regex:/^\d*(\.\d{1,2})?$/',
        ]);

        $data         = $this->generateQuarterYear($data);
        $contribution = Contribution::create($data);
        $project      = Project::find($request->get('project_id'));

        $project->updated_by = Auth::id();
        $project->updated_at = Carbon::now();
        $project->save();
        $project->updateSortFields();

        $project->updatePaymentStatus($data['paid_at'])->save();

        return $contribution;
    }

    public function edit(Request $request, Contribution $contribution)
    {
        $this->authorize('update.contributions');

        $data = $request->validate([
            'paid_at'  => 'required|date_format:' . config('vinnies.date_format'),
            'amount'   => 'required|regex:/^\d*(\.\d{1,2})?$/',
        ]);

        $data         = $this->generateQuarterYear($data);
        $project      = Project::find($request->get('project_id'));

        $contribution->update($data);

        $project->updated_by = Auth::id();
        $project->updated_at = Carbon::now();
        $project->save();
        $project->updatePaymentStatus($data['paid_at'])->save();
        $project->updateSortFields();

        return $contribution;
    }

    public function delete(Project $project, Contribution $contribution)
    {
        $this->authorize('delete.contributions');

        $contribution->delete();

        $project->updated_by = Auth::id();
        $project->updated_at = Carbon::now();
        $project->save();
        $project->updatePaymentStatus()->save();
        $project->updateSortFields();

        return response()->json([
            'msg' => 'Payment successfully deleted'
        ]);
    }

    private function generateQuarterYear($data)
    {
        $data['paid_at'] = Carbon::createFromFormat(config('vinnies.date_format'), $data['paid_at']);
        $data['quarter'] = $data['paid_at']->quarter;
        $data['year']    = $data['paid_at']->year;

        return $data;
    }
}

