<?php

namespace App\Http\Controllers;

use PDF;
use Auth;
use JavaScript;
use App\Comment;
use App\Project;
use App\Country;
use Carbon\Carbon;
use App\Beneficiary;
use App\Vinnies\Money;
use App\Vinnies\Helper;
use App\DiocesanCouncil;
use App\OverseasConference;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Vinnies\Exporter\ProjectsExporter;
use App\Activity;
use App\Vinnies\Exporter\LogExporter;

class ProjectController extends Controller
{
    protected $exclude_orders = [
        'country',
        'state',
        'balance_owing',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request, Project $project)
    {
        $table = (new Project)->getTable();

        $this->authorize('read.projects');

        $countries = Country::orderBy('name')->get();

        $activity = Activity::where(function($q){
            $q->where('subject_type','App\Project')
            ->whereNot('properties', '{"attributes":[],"old":[]}')
            ->whereNot('properties', '{"old": [], "attributes": []}')
            ->whereNot('properties', '[]');
        })
        ->take(20)->orderBy('updated_at', 'desc')->get();

        Javascript::put([
            'project_export_url' => route('projects.export'),
        ]);

        return view('projects.list')->with(compact('countries','activity'));
    }

    public function datatables(Request $request)
    {
        $user = Auth::user();

        $this->authorize('read.projects');

        $projects = $this->getProjectsFromRequest($request);
    

        $projects = $projects->paginate(config('vinnies.pagination.projects'));
        $data     = $this->getDatatableBaseData($projects, $request);
        

        foreach ($projects as $project) {
            $data['data'][] = [
                'id'                  => $project->id,
                'status'              => ucwords(str_replace("_", " ", $project->status)),
                'project_type'        => ucwords(str_replace("_", " ", $project->project_type)),
                'name'                => $project->name,
                'country'             => optional(optional($project->beneficiary)->country)->name,
                'overseas_project_id' => $project->overseas_conference_id ?? 'N/A',
                'received_at'         => optional($project->received_at)->format(config('vinnies.date_format')),
                'is_awaiting_support' => $project->is_awaiting_support ? 'Yes' : 'No',
                'state'               => optional($project->getDonorStates())->implode(' / '),
                'au_value'            => $project->au_value->value(),
                'is_fully_paid'       => $project->is_fully_paid ? 'Yes' : 'No',
                'balance_owing '      => $project->getBalanceOwing()->value(),
                'DT_RowId'            => 'row_' . $project->id,
            ];
        }

        return $data;
    }

    public function showCreateForm()
    {
        $this->authorize('create.projects');

        $countries     = Country::orderBy('name')->get();
        $beneficiaries = Beneficiary::orderBy('name')->get();
        $project       = new Project;

        JavaScript::put([
            'project_info_url' => route('projects.info'),
        ]);

        return view('projects.create')->with(compact('project', 'countries', 'beneficiaries'));
    }

    public function create(Request $request)
    {
        $this->authorize('create.projects');

        $data    = $request->validate($this->rules());
        $data    = $this->cleanup($data);
        $project = Project::create($data);
        // $data    = $this->populateRate($project, $data);
        $msg     = 'Project created successfully';

        $data['updated_by'] = Auth::id();

        $project->update($data);
        $project->updatePaymentStatus()->save();
        $project->updateSortFields();

        if ($request->filled('comments')) {
            $project->comment($request->input('comments'));
            $project->update(['comments' => null]);
        }

        if ($request->ajax()) {
            return response()->json([
                'redirect' => route('projects.edit', $project),
                'msg'      => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function showEditForm(Project $project)
    {
        $this->authorize('read.projects');
        $this->checkEditAccess($project->id);

        $countries         = Country::orderBy('name')->get();
        $beneficiaries     = Beneficiary::orderBy('name')->get();
        $diocesan_councils = Helper::getFormattedDiocesanCouncils(DiocesanCouncil::where('is_valid', 1)->get());

        $activity = Activity::where('subject_id', $project->id)
                    ->where(function($q){
                        $q->where('subject_type','App\Project')
                        ->whereNot('properties', '{"attributes":[],"old":[]}')
                        ->whereNot('properties', '{"old": [], "attributes": []}')
                        ->whereNot('properties', '[]');
                    })
                    ->orWhere(function($q) use ($project){
                        $q->where('subject_type', 'App\Comment')
                          ->whereIn('subject_id', $project->comments()->approved()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($project){
                        $q->where('subject_type', 'App\Donor')
                          ->whereIn('subject_id', $project->donors()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($project){
                        $q->where('subject_type', 'App\Contribution')
                          ->whereIn('subject_id', $project->contributions()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($project){
                        $q->where('subject_type', 'App\Document')
                          ->whereIn('subject_id', $project->documents()->withTrashed()->get()->pluck('id'));
                    })
                    ->take(20)->orderBy('updated_at', 'desc')->get();

        JavaScript::put([
            'project_info_url'   => route('projects.info', $project),
            'project_donors_url' => route('donors.list'),
            'documentable_type'  => 'Project',
            'documentable_id'    => $project->id,
            'meta_url'           => route('projects.meta', $project->id),
        ]);

        return view('projects.edit')->with(compact('project', 'countries', 'beneficiaries', 'diocesan_councils', 'activity'));
    }

    public function edit(Request $request, Project $project)
    {
        $this->authorize('update.projects');
        $this->checkEditAccess($project->id);

        $data = $request->validate($this->rules());
        $data = $this->cleanup($data);
        // $data = $this->populateRate($project, $data);
        $msg  = 'Project updated successfully';
        $data['updated_by'] = Auth::id();
        
        $project->update($data);
        $project->updatePaymentStatus(false, $data['is_fully_paid'])->save();
        $project->updateSortFields();

        if ($request->filled('comments')) {
            $project->comment($request->input('comments'));
            $project->update(['comments' => null]);
        }

        if ($request->ajax()) {
            return response()->json([
                'msg' => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function addComment(Request $request, Project $project)
    {
        // $this->authorize('update.projects');

        $data = $request->validate(['comments' => 'required']);
        $msg  = 'Your comments added successfully';

        if ($request->filled('comments')) {
            $project->comment($request->input('comments'));
            $project->update(['comments' => null]);
        }

        if ($request->ajax()) {
            return response()->json([
                'msg' => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function download(Project $project, Comment $comment)
    {
        $this->authorize('download.projects');

        $comment = Comment::where('commentable_type','=', 'App\Project')
        ->where('commentable_id','=',$project->id)
        ->get();

        $filename = sprintf('Project Cover Sheet - %s.pdf', $project->id);

        if (request()->get('preview')) {
            return view('projects.cover-sheet')->with(compact('project', 'comment'));
        } else {
            $pdf = PDF::setOptions(['defaultFont' => 'Times-Roman']);
            $pdf->loadView('projects.cover-sheet', compact('project', 'comment'));

            return $pdf->download($filename);
        }
    }

    private function rules()
    {
        return [
            'name'                   => 'required',
            'beneficiary_id'         => 'required|integer|exists:beneficiaries,id',
            'status' => [
                'sometimes',
                Rule::in(array_keys(Helper::getProjectsStatuses())),
            ],
            'consolidated_status' => [
                'sometimes',
                Rule::in(array_keys(Helper::getProjectsConsolidatedStatuses())),
            ],
            'completion_report_received' => [
                'sometimes',
                Rule::in(array(0,1,2,3)),
            ],
            'overseas_conference_id' => '',
            'overseas_project_id'    => '',
            'currency'               => [
                'required',
                Rule::in(array_keys(Helper::getCurrencies()))
            ],
            'local_value'             => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'exchange_rate'           => 'required|numeric',
            'au_value'                => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'is_awaiting_support'     => 'required|boolean',
            'comments'                => '',
            'received_at'             => 'required|date_format:' . config('vinnies.date_format'),
            'completed_at'            => 'sometimes|nullable|date_format:' . config('vinnies.date_format'),
            'estimated_completed_at'  => 'sometimes|nullable|date_format:' . config('vinnies.date_format'),
            'project_completion_date' => 'sometimes|nullable|date_format:' . config('vinnies.date_format'),
            'project_completed'       => '',
            'is_fully_paid'           => 'boolean',
            'project_type'          =>    '',
        ];
    }

    private function cleanup($data)
    {
        $data['received_at']  = Carbon::createFromFormat(config('vinnies.date_format'), $data['received_at']);

        if (!empty($data['completed_at'])) {
            $data['completed_at']  = Carbon::createFromFormat(config('vinnies.date_format'), $data['completed_at']);
        } else {
            $data['completed_at'] = null;
        }

        if (!empty($data['estimated_completed_at'])) {
            $data['estimated_completed_at']  = Carbon::createFromFormat(config('vinnies.date_format'), $data['estimated_completed_at']);
        } else {
            $data['estimated_completed_at'] = null;
        }

        if (!empty($data['project_completion_date'])) {
            $data['project_completion_date']  = Carbon::createFromFormat(config('vinnies.date_format'), $data['project_completion_date']);
        } else {
            $data['project_completion_date'] = null;
        }

        return $data;
    }

    private function populateRate(Project $project, $data)
    {
        $data['exchange_rate'] = $project->exchange_rate;
        $data['au_value']      = $project->au_value->value();

        // If currency is changed, we retrieve new exchange rate
        // if ($project->currency != $data['currency'] || !$data['exchange_rate']) {
        //     $money = new Money($data['local_value'], $data['currency']);

        //     $local_value = request()->has('local_value') ? request()->get('local_value') : $project->local_value;
        //     $exchange = $money->getExchangeRate();
        //     $data['exchange_rate'] = $exchange;
        //     $data['au_value']      = round((double) ($local_value * $exchange), 2);
        // }

        // If only local value changed, we recalculate au value
        // if ($project->currency == $data['currency'] && $project->local_value != $data['local_value'] && is_null($project->exchange_rate)) {
        //     $data['au_value'] = Helper::formatDecimal($data['local_value'] / $project->exchange_rate);
        // }

        return $data;
    }

    public function info(Request $request, Project $project)
    {
        $this->authorize('read.projects');

        $data = [];

        $data['currency']    = $request->has('currency') ? $request->get('currency') : $project->currency;
        $data['local_value'] = $request->has('local_value') ? $request->get('local_value') : $project->local_value;
        $data['total_paid']  = $project->getTotalPaid()->value();
        $data['local_value'] = (new Money($data['local_value'], $data['currency']))->value();

        $data = $this->populateRate($project, $data);

        if (!empty($data['au_value'])) {
            $project->au_value = $data['au_value'];
        }

        $project->updatePaymentStatus();

        $data['balance_owing'] = $project->getBalanceOwing()->value();
        $data['fully_paid_at'] = optional($project->fully_paid_at)->format(config('vinnies.date_format'));
        $data['is_fully_paid'] = $project->is_fully_paid ? 'Yes' : 'No';

        return response()->json($data);
    }

    public function validateBeneficiary(Request $request)
    {
        $this->authorize('read.projects');

        $beneficiary_id = $request->validate([
            'beneficiary_id' => 'required|integer|exists:beneficiaries,id',
        ]);

        $beneficiary = Beneficiary::find($beneficiary_id)->first();

        return response()->json([
            'beneficiary_id'      => $beneficiary->id,
            'beneficiary_name'    => $beneficiary->name,
            'beneficiary_url'     => route('beneficiaries.edit', $beneficiary),
            'beneficiary_country' => $beneficiary->country->name,
        ]);
    }

    public function validateOverseasConference(Request $request)
    {
        $this->authorize('read.projects');

        if ($request->has('overseas_conference_id')) {
            $overseas_conference_id = $request->validate([
                'overseas_conference_id' => 'required|integer|exists:overseas_conferences,id',
            ]);

            $conference = OverseasConference::find($overseas_conference_id)->first();

            return response()->json([
                'overseas_conference_id'                 => $conference->id,
                'overseas_conference_name'               => $conference->name,
                'overseas_conference_url'                => route('overseas-conferences.edit', $conference),
                'overseas_conference_country'            => $conference->country->name,
                'overseas_conference_central_council'    => $conference->central_council,
                'overseas_conference_particular_council' => $conference->particular_council,
                'overseas_conference_parish'             => $conference->parish,
                'overseas_conference_is_active'          => $conference->is_active ? 'Active' : 'Inactive',
            ]);
        }

        // for empty conference id, we return empty data
        return response()->json([
            'overseas_conference_id'                 => 'N/A',
            'overseas_conference_name'               => 'No Overseas Conference assigned yet.',
            'overseas_conference_url'                => '#',
            'overseas_conference_country'            => 'N/A',
            'overseas_conference_central_council'    => 'N/A',
            'overseas_conference_particular_council' => 'N/A',
            'overseas_conference_parish'             => 'N/A',
            'overseas_conference_is_active'          => 'N/A',
        ]);
    }

    private function getProjectsFromRequest($request)
    {
        $user = Auth::user();

        $table = (new Project)->getTable();

        if ($user->hasRole('Diocesan/Central Council User')) {
            $projects = Project::whereNotNull($table . '.id')
            ->leftJoin('beneficiaries', $table . '.beneficiary_id', '=', 'beneficiaries.id')
            ->leftJoin('countries', 'beneficiaries.country_id', '=', 'countries.id')
            ->select($table . '.*')
            ->whereNotIn($table . '.status', ['declined','pending_approval']);
        } elseif ($user->hasRole('Full Admin')) {
            $projects = Project::whereNotNull($table . '.id')
            ->leftJoin('beneficiaries', $table . '.beneficiary_id', '=', 'beneficiaries.id')
            ->leftJoin('countries', 'beneficiaries.country_id', '=', 'countries.id')
            ->leftjoin('donors', $table . '.id', '=', 'donors.project_id')
            ->leftjoin('local_conferences', 'donors.local_conference_id', 'local_conferences.id')
            ->select($table . '.*');
        } else {
            $projects = Project::whereNotNull($table . '.id')
            ->leftJoin('beneficiaries', $table . '.beneficiary_id', '=', 'beneficiaries.id')
            ->leftJoin('countries', 'beneficiaries.country_id', '=', 'countries.id')
            ->leftjoin('donors', $table . '.id', '=', 'donors.project_id')
            ->leftjoin('local_conferences', 'donors.local_conference_id', 'local_conferences.id')
            ->select($table . '.*')
            ->where('local_conferences.state_council', $user->states);
        }

        $projects = $this->sortModelFromRequest($projects, $request, $table);
        
        if (!empty($filters = $request->get('filters'))) {

            if (!empty($filters['status'])) {
                switch ($filters['status']) {
                    case 'pending_approval':
                        $projects->where($table . '.status', 'pending_approval');
                        break;

                    case 'declined':
                        $projects->where($table . '.status', 'declined');
                        break;

                    case 'awaiting_support':
                        $projects->where($table . '.status', 'awaiting_support');
                        break;
                    
                    case 'awaiting_remittance':
                        $projects->where($table . '.status', 'awaiting_remittance');
                        break;

                    case 'funded':
                        $projects->where($table . '.status', 'funded');
                        break;

                    case 'completed':
                        $projects->where($table . '.status', 'completed');
                        break;
                }
            }

            if (!empty($filters['country'])) {
                $projects->with('beneficiary');

                $country = Country::find($filters['country']);

                $projects->whereHas('beneficiary', function ($query) use ($country) {
                    $query->where('beneficiaries.country_id', $country->id);
                });
            }

            if (!empty($filters['is_fully_paid'])) {
                switch ($filters['is_fully_paid']) {
                    case 'yes':
                        $projects->where($table . '.is_fully_paid', 1);
                        break;

                    case 'no':
                        $projects->where($table . '.is_fully_paid', 0);
                        break;
                }
            }

            if (!empty($filters['project_completed'])) {
                switch ($filters['project_completed']) {
                    case 'yes':
                        $projects->where($table . '.project_completed', 'Yes');
                        break;

                    case 'no':
                        $projects->where($table . '.project_completed', 'No');
                        break;
                }
            }

            if (!empty($filters['state'])) {
                $projects->with('donors');

                switch ($filters['state']) {
                    case 'no-donor':
                        $projects->doesntHave('donors');
                        break;

                    default: // normal states
                        $projects->whereHas('donors', function ($query) use ($filters) {
                            $query->whereHas('localConference', function ($query2) use ($filters) {
                                $query2->where('state_council', $filters['state']);
                            });
                        });

                        break;
                }
            }

            if (!empty($filters['is_awaiting_support'])) {
                switch ($filters['is_awaiting_support']) {
                    case 'yes':
                        $projects->where($table . '.is_awaiting_support', 1);
                        break;

                    case 'no':
                        $projects->where($table . '.is_awaiting_support', 0);
                        break;
                }
            }

            if (!empty($filters['completion_report_received'])) {
                switch ($filters['completion_report_received']) {
                    case '0':
                        $projects->where($table . '.completion_report_received', 0);
                        break;

                    case '1':
                        $projects->where($table . '.completion_report_received', 1);
                        break;

                    case '2':
                        $projects->where($table . '.completion_report_received', 2);
                        break;

                    case '3':
                        $projects->where($table . '.completion_report_received', 3);
                        break;
                }
            } 

            if (!empty($filters['project_type'])) {
                $projects->where('project_type', $filters['project_type']);
            }
        }

        if (!empty($keyword = $request->get('search')['value'])) {
            $projects->where(function ($query) use ($keyword, $table) {
                $query->where($table . '.id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere($table . '.name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere($table . '.received_at', 'LIKE', '%' . $keyword . '%')
                    ->orWhere($table . '.overseas_project_id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere($table . '.au_value', 'LIKE', '%' . $keyword . '%');
            });
        }

        // Custom orders
        if (!empty($request->get('order'))) {
            foreach ($request->get('order') as $order) {
                switch ($request->get('columns')[$order['column']]['name']) {
                    case 'country':
                        $projects->orderBy('countries.name', $order['dir']);
                        break;

                    case 'state':
                        $projects->orderBy($table . '._states', $order['dir']);
                        break;

                    case 'balance_owing':
                        $projects->orderBy($table . '._balance_owing', $order['dir']);
                        break;
                }
            }
        }
        return $projects;
    }

    public function export(Request $request)
    {
        $this->authorize('export.projects');
        $projects   = $this->getProjectsFromRequest($request)->orderBy('id', 'desc')->get();
        $file_name = sprintf('Projects - %s', date('Y.m.d'));

        return Excel::download(new ProjectsExporter($projects), $file_name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportLog(Request $request)
    {        
        $activity = Activity::where(function($q){
            $q->where('subject_type','App\Project')
            ->whereNot('properties', '{"attributes":[],"old":[]}')
            ->whereNot('properties', '{"old": [], "attributes": []}')
            ->whereNot('properties', '[]');
        })->take(1000)->orderBy('updated_at', 'desc')->get();

        $file_name = sprintf('Projects Log - %s', date('Y.m.d'));

        return Excel::download(new LogExporter($activity, false, 'Project'), $file_name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportIndividualLog($id)
    {        
        
        $project = Project::find($id);
        
        $activity = Activity::where('subject_id', $project->id)
                    ->where(function($q){
                        $q->where('subject_type','App\Project')
                        ->whereNot('properties', '{"attributes":[],"old":[]}')
                        ->whereNot('properties', '{"old": [], "attributes": []}')
                        ->whereNot('properties', '[]');
                    })
                    ->orWhere(function($q) use ($project){
                        $q->where('subject_type', 'App\Comment')
                          ->whereIn('subject_id', $project->comments()->approved()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($project){
                        $q->where('subject_type', 'App\Donor')
                          ->whereIn('subject_id', $project->donors()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($project){
                        $q->where('subject_type', 'App\Contribution')
                          ->whereIn('subject_id', $project->contributions()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($project){
                        $q->where('subject_type', 'App\Document')
                          ->whereIn('subject_id', $project->documents()->withTrashed()->get()->pluck('id'));
                    })->take(1000)->orderBy('updated_at', 'desc')->get();

        $file_name = sprintf('Project %u Log - %s', $id, date('Y.m.d'));

        return Excel::download(new LogExporter($activity, true, 'Project'), $file_name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function meta(Project $project)
    {
        return response()->json($this->getLastUpdatedData($project));
    }

    public function test(Request $request)
    {
        return view('projects.test');
    }

    public function comments($id)
    {
        $project = Project::find($id);

        return $project->comments()
            ->orderBy('created_at', 'DESC')
            ->get()
            ->map(function ($comment) {
                return [
                    'name'    => $comment->commentator->getFullName(),
                    'date'    => $comment->created_at->format('Y-m-d H:i:s'),
                    'diff'    => $comment->created_at->format('d M Y') . ' (' . $comment->created_at->diffForHumans() . ')',
                    'comment' => $comment->comment,
                ];
            })
            ->toArray();
    }

    private function checkEditAccess($id)
    {
        $user = Auth::user();
        $project = Project::find($id);
        $can_access = true;

        if ($user->hasRole('Diocesan/Central Council User')) {
            if (!empty($project->status)) {
                $can_access = !in_array($project->status, ['declined','pending_approval']);
            }

            if (!$can_access) {
                abort(403);
            }
        }
    }
}
