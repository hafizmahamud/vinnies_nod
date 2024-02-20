<?php

namespace App\Http\Controllers;

use Auth;
use Javascript;
use App\Country;
use App\Project;
use Carbon\Carbon;
use App\Vinnies\Helper;
use App\LocalConference;
use App\OverseasConference;
use App\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Vinnies\Exporter\OverseasConferencesExporter;
use App\Activity;
use App\Vinnies\Exporter\LogExporter;

class OverseasConferenceController extends Controller
{
    protected $exclude_orders = [
        'country',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        $this->authorize('read.os-conf');

        $countries = Country::orderBy('name')->get();
        $national_council = Beneficiary::get();
        $states = $this->getStates();

        $activity = Activity::where(function($q){
            $q->where('subject_type','App\OverseasConference')
            ->whereNot('properties', '{"attributes":[],"old":[]}')
            ->whereNot('properties', '{"old": [], "attributes": []}')
            ->whereNot('properties', '[]');
        })
        ->take(20)->orderBy('updated_at', 'desc')->get();

        Javascript::put([
            'osconf_export_url' => route('overseas-conferences.export'),
        ]);

        return view('overseas-conferences.list')->with(compact('countries', 'states', 'national_council','activity'));
    }

    public function datatables(Request $request)
    {
        $user = Auth::user();

        $this->authorize('read.os-conf');

        $conferences = $this->getConferencesFromRequest($request);

        // if ($user->hasRole('State User')) {
        if(!$user->hasRole('Full Admin')){
            $dioceses = '[' .  implode(",", $user->dioceses) .']';
            
            $conferences->whereHas('twinnings', function ($query) use ($user) {
                $query->whereHas('localConference', function ($query2) use ($user) {
                    $query2->where(function($query3) use ($user) {
                        $query3->where(function($query4) use ($user) {
                            $query4->where('state_council', $user->states)
                                    ->whereIn('diocesan_council_id', $user->dioceses);
                        })->orWhere(function($query5) use ($user) {
                            $query5->where('state_council', $user->states)
                                    ->whereNull('diocesan_council_id');
                        });
                    });
                });
            });
        }

        $conferences = $conferences->paginate(!empty($filters['per_page']) ? $filters['per_page'] : config('vinnies.pagination.overseas_conferences'));
        $data        = $this->getDatatableBaseData($conferences, $request);

        foreach ($conferences as $conference) {
            $data['data'][] = [
                'id'                 => $conference->id,
                'select'             => $conference->id,
                'name'               => $conference->name,
                'status'             => array_get(Helper::getOSConferencesStatus(), $conference->status),
                'country'            => optional($conference->country)->name,
                'central_council'    => $conference->central_council,
                'particular_council' => $conference->particular_council,
                'parish'             => $conference->parish,
                'twinning_status'    => array_get(Helper::getOSConferencesTwinningStatuses(), $conference->twinning_status),
                'is_active'          => $conference->is_active ? 'Remittances' : 'No Remittances', //OS Conf. Receiving Remittances?  
                'DT_RowId'           => 'row_' . $conference->id,
            ];
        }

        return $data;
    }

    public function showCreateForm()
    {
        $this->authorize('create.os-conf');

        $countries = Country::orderBy('name')->get()->mapWithKeys(function ($country) {
            return [
                $country->id => $country->name,
            ];
        })->toArray();

        $national_council = Beneficiary::get()->mapWithKeys(function ($national) {
            return [
                $national->id => $national->name,
            ];
        })->toArray();

        $overseas_conference = new OverseasConference;

        return view('overseas-conferences.create')->with(compact('overseas_conference', 'countries', 'national_council'));
    }

    public function create(Request $request)
    {
        $this->authorize('create.os-conf');

        $data = $this->prepare($request->validate($this->rules()));
        $msg  = 'Overseas conference created successfully';

        $data['updated_by'] = Auth::id();
        $data['updated_at'] = Carbon::now();

        $overseas_conference = OverseasConference::create($data);

        if ($request->filled('comments')) {
            $overseas_conference->comment($request->input('comments'));
            $overseas_conference->update(['comments' => null]);
        }

        if ($request->ajax()) {
            return response()->json([
                'redirect' => route('overseas-conferences.edit', $overseas_conference),
                'msg'      => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function showEditForm(OverseasConference $overseas_conference)
    {
        $this->authorize('read.os-conf');

        $countries = Country::orderBy('name')->get()->mapWithKeys(function ($country) {
            return [
                $country->id => $country->name,
            ];
        })->toArray();

        $national_council = Beneficiary::get()->mapWithKeys(function ($national) {
            return [
                $national->id => $national->name,
            ];
        })->toArray();
        $activity = Activity::where('subject_id', $overseas_conference->id)
                    ->where(function($q){
                        $q->where('subject_type','App\OverseasConference')
                        ->whereNot('properties', '{"attributes":[],"old":[]}')
                        ->whereNot('properties', '{"old": [], "attributes": []}')
                        ->whereNot('properties', '[]');
                    })
                    ->orWhere(function($q) use ($overseas_conference){
                        $q->where('subject_type', 'App\Comment')
                          ->whereIn('subject_id', $overseas_conference->comments()->approved()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($overseas_conference){
                        $q->where('subject_type', 'App\Document')
                          ->whereIn('subject_id', $overseas_conference->documents()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($overseas_conference){
                        $q->where('subject_type', 'App\Twinning')
                          ->whereIn('subject_id', $overseas_conference->twinnings()->get()->pluck('id'));
                    })
                    ->take(20)->orderBy('updated_at', 'desc')->get();

        $projects = Project::where('overseas_conference_id', $overseas_conference->id)->get();

        JavaScript::put([
            'documentable_type' => 'OverseasConference',
            'documentable_id'   => $overseas_conference->id,
            'meta_url'          => route('overseas-conferences.meta', $overseas_conference),
        ]);

        return view('overseas-conferences.edit')->with(compact('overseas_conference', 'countries', 'projects','national_council', 'activity'));
    }

    public function edit(Request $request, OverseasConference $overseas_conference)
    {
        $this->authorize('update.os-conf');

        if (!auth()->user()->canEditOverseasConference($overseas_conference)) {
            $msg = 'You do not have permission to edit this Overseas Conference';

            if ($request->ajax()) {
                return response()->json([
                    'type'    => 'dialog',
                    'confirm' => false,
                    'msg'     => $msg
                ], 422);
            }

            flash($msg)->error()->important();

            return redirect()->back();
        }

        $data = $this->prepare($request->validate($this->rules()));
        $msg  = 'Overseas conference updated successfully';

        $data['updated_by'] = Auth::id();
        $data['updated_at'] = Carbon::now();

        $overseas_conference->update($data);

        if ($request->filled('comments')) {
            $overseas_conference->comment($request->input('comments'));
            $overseas_conference->update(['comments' => null]);
        }

        if ($request->ajax()) {
            return response()->json([
                'msg' => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function addComment(Request $request, OverseasConference $overseas_conference)
    {
        // $this->authorize('update.os-conf');
        $user = $request->user();

        if (!auth()->user()->canEditOverseasConference($overseas_conference) && !$user->hasRole('State User') && !$user->hasRole('State User Admin')) {
            $msg = 'You do not have permission to edit this Overseas Conference';

            if ($request->ajax()) {
                return response()->json([
                    'type'    => 'dialog',
                    'confirm' => false,
                    'msg'     => $msg
                ], 422);
            }

            flash($msg)->error()->important();

            return redirect()->back();
        }

        $data = $request->validate(['comments' => 'required']);
        $msg  = 'Your comments added successfully';

        if ($request->filled('comments')) {
            $overseas_conference->comment($request->input('comments'));
            $overseas_conference->update(['comments' => null]);
        }

        if ($request->ajax()) {
            return response()->json([
                'msg' => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    private function rules()
    {
        return [
            'is_active'                   => 'required|boolean',
            'is_in_status_check'          => 'required|boolean',
            'status_check_reason' => [
                'required',
                Rule::in(array_keys(Helper::getOSConferencesStatusCheckReason())),
            ],
            'is_in_surrendering'          => 'required|boolean',
            'name'                        => 'required',
            'aggregation_number'          => '',
            'contact_name'                => '',
            'contact_email'               => 'sometimes|nullable|email',
            'contact_phone'               => '',
            'country_id'                  => 'required|integer|exists:countries,id',
            'status' => [
                'required',
                Rule::in(array_keys(Helper::getOSConferencesStatus())),
            ],
            'twinning_status' => [
                'required',
                Rule::in(array_keys(Helper::getOSConferencesTwinningStatuses())),
            ],
            'particular_council'          => '',
            'central_council'             => '',
            'national_council'            => '',
            'parish'                      => '',
            'address_line_1'              => '',
            'address_line_2'              => '',
            'address_line_3'              => '',
            'suburb'                      => '',
            'state'                       => '',
            'postcode'                    => '',
            'comments'                    => '',
            'twinned_at'                  => 'sometimes|nullable|date_format:' . config('vinnies.date_format'),
            'untwinned_at'                => 'sometimes|nullable|date_format:' . config('vinnies.date_format'),
            'is_abeyant_at'               => 'sometimes|nullable|date_format:' . config('vinnies.date_format'),
            'is_active_at'                => 'sometimes|nullable|date_format:' . config('vinnies.date_format'),
            'status_check_initiated_at'   => 'sometimes|nullable|date_format:' . config('vinnies.date_format'),
            'surrendering_initiated_at'   => 'sometimes|nullable|date_format:' . config('vinnies.date_format'),
            'surrendering_deadline_at'    => 'sometimes|nullable|date_format:' . config('vinnies.date_format'),
            'confirmed_date_at'           => 'sometimes|nullable|date_format:' . config('vinnies.date_format'),
            'last_status_check_initiated' => '',
            'final_remittance'            => '',
        ];
    }

    private function prepare($data)
    {
        if (!empty($data['twinned_at'])) {
            $data['twinned_at'] = Carbon::createFromFormat(config('vinnies.date_format'), $data['twinned_at']);
        } else {
            $data['twinned_at'] = null;
        }

        if (!empty($data['untwinned_at'])) {
            $data['untwinned_at'] = Carbon::createFromFormat(config('vinnies.date_format'), $data['untwinned_at']);
        } else {
            $data['untwinned_at'] = null;
        }

        if (!empty($data['is_active_at'])) {
            $data['is_active_at'] = Carbon::createFromFormat(config('vinnies.date_format'), $data['is_active_at']);
        } else {
            $data['is_active_at'] = null;
        }

        if (!empty($data['status_check_initiated_at'])) {
            $data['status_check_initiated_at'] = Carbon::createFromFormat(config('vinnies.date_format'), $data['status_check_initiated_at']);
        } else {
            $data['status_check_initiated_at'] = null;
        }

        if (!empty($data['surrendering_initiated_at'])) {
            $data['surrendering_initiated_at'] = Carbon::createFromFormat(config('vinnies.date_format'), $data['surrendering_initiated_at']);
        } else {
            $data['surrendering_initiated_at'] = null;
        }

        if (!empty($data['surrendering_deadline_at'])) {
            $data['surrendering_deadline_at'] = Carbon::createFromFormat(config('vinnies.date_format'), $data['surrendering_deadline_at']);
        } else {
            $data['surrendering_deadline_at'] = null;
        }

        if (!empty($data['confirmed_date_at'])) {
            $data['confirmed_date_at'] = Carbon::createFromFormat(config('vinnies.date_format'), $data['confirmed_date_at']);
        } else {
            $data['confirmed_date_at'] = null;
        }

        if (!empty($data['is_abeyant_at'])) {
            $data['is_abeyant_at'] = Carbon::createFromFormat(config('vinnies.date_format'), $data['is_abeyant_at']);
        } else {
            $data['is_abeyant_at'] = null;
        }

        return $data;
    }

    private function getConferencesFromRequest(Request $request)
    {
        $table = (new OverseasConference)->getTable();
        
        $conferences = OverseasConference::whereNotNull($table . '.id')
            ->join('countries', $table . '.country_id', '=', 'countries.id')
            ->select($table . '.*');

        $conferences = $this->sortModelFromRequest($conferences, $request, $table);

        if (!empty($filters = $request->get('filters'))) {
            if (!empty($filters['country'])) {
                $country = Country::find($filters['country']);

                $conferences->where($table . '.country_id', $country->id);
            }

            if (!empty($filters['twinning_state'])) {
                $conferences->whereHas('twinnings', function ($query) use ($filters) {
                    $query->whereHas('localConference', function ($query2) use ($filters) {
                        $query2->where('state_council', $filters['twinning_state']);
                    });
                });
            }

            if (!empty($filters['national_council'])) {
                $conferences->where('overseas_conferences.national_council', $filters['national_council']);
            }

            if (!empty($filters['is_active'])) {
                switch ($filters['is_active']) {
                    case 'active':
                        $conferences->where($table . '.is_active', 1);
                        break;

                    case 'inactive':
                        $conferences->where($table . '.is_active', 0);
                        break;
                }
            }

            if (!empty($filters['is_in_status_check'])) {
                switch ($filters['is_in_status_check']) {
                    case 'yes':
                        $conferences->where($table . '.is_in_status_check', 1);
                        break;

                    case 'no':
                        $conferences->where($table . '.is_in_status_check', 0);
                        break;
                }
            }

            if (!empty($filters['reason_status_check'])) {
                switch ($filters['reason_status_check']) {
                    case 'no_communication_received':
                        $conferences->where($table . '.status_check_reason', 'no_communication_received');
                        break;

                    case 'au_twin_abeyant':
                        $conferences->where($table . '.status_check_reason', 'au_twin_abeyant');
                        break;

                    case 'n/a':
                        $conferences->where($table . '.status_check_reason', 'n/a');
                        break;
                }
            }

            if (!empty($filters['status_check_initiated_at'])) {
                switch ($filters['status_check_initiated_at']) {
                    case 'less':
                        $conferences->where($table . '.status_check_initiated_at', '>', Carbon::now()->subDays(90));
                        break;

                    case 'more':
                        $conferences->where($table . '.status_check_initiated_at', '<=', Carbon::now()->subDays(90));
                        break;

                    case 'none':
                        $conferences->whereNull($table . '.status_check_initiated_at');
                        break;
                }
            }

            if (!empty($filters['is_in_surrendering'])) {
                switch ($filters['is_in_surrendering']) {
                    case 'yes':
                        $conferences->where($table . '.is_in_surrendering', 1);
                        break;

                    case 'no':
                        $conferences->where($table . '.is_in_surrendering', 0);
                        break;
                }
            }

            if (!empty($filters['surrendering_deadline_at'])) {
                switch ($filters['surrendering_deadline_at']) {
                    case 'no':
                        $conferences->where($table . '.surrendering_deadline_at', '>', Carbon::now());
                        break;

                    case 'yes':
                        $conferences->where($table . '.surrendering_deadline_at', '<=', Carbon::now());
                        break;

                    case 'none':
                        $conferences->whereNull($table . '.surrendering_deadline_at');
                        break;
                }
            }

            if (!empty($filters['confirmed_date_at'])) {
                switch ($filters['confirmed_date_at']) {
                    case 'no':
                        $conferences->where($table . '.confirmed_date_at', '>', Carbon::now());
                        break;

                    case 'yes':
                        $conferences->where($table . '.confirmed_date_at', '<=', Carbon::now());
                        break;

                    case 'none':
                        $conferences->whereNull($table . '.confirmed_date_at');
                        break;
                }
            }

            if (!empty($filters['twinning_status'])) {
                $conferences->where('twinning_status', $filters['twinning_status']);
            }

            if (!empty($filters['status'])) {
                $conferences->where('status', $filters['status']);
            }

            if (!empty($filters['central_council'])){
                $conferences->where($table . '.central_council', $filters['central_council']);
               }

            if (!empty($filters['particular_council'])){
                $conferences->where($table . '.particular_council', $filters['particular_council']);
            }
        }

        if (!empty($keyword = $request->get('search')['value'])) {
            $conferences->where(function ($query) use ($keyword, $table) {
                $query->where($table . '.id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere($table . '.name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere($table . '.central_council', 'LIKE', '%' . $keyword . '%')
                    ->orWhere($table . '.particular_council', 'LIKE', '%' . $keyword . '%')
                    ->orWhere($table . '.parish', 'LIKE', '%' . $keyword . '%')
                    ->orWhere($table . '.aggregation_number', 'LIKE', '%' . $keyword . '%')
                    ->orWhere($table . '.contact_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere($table . '.contact_email', 'LIKE', '%' . $keyword . '%')
                    ->orWhere($table . '.contact_phone', 'LIKE', '%' . $keyword . '%');
            });
        }

        // Custom orders
        if (!empty($request->get('order'))) {
            foreach ($request->get('order') as $order) {
                switch ($request->get('columns')[$order['column']]['name']) {
                    case 'country':
                        $conferences->orderBy('countries.name', $order['dir']);
                        break;
                }
            }
        }

        return $conferences;
    }

    public function export(Request $request)
    {
        $this->authorize('export.os-conf');
        $conferences = $this->getConferencesFromRequest($request)->with(['twinnings', 'documents'])->orderBy('id', 'desc')->get();
        $file_name = sprintf('Overseas Conferences - %s', date('Y.m.d'));

        return Excel::download(new OverseasConferencesExporter($conferences), $file_name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportLog(Request $request)
    {        
        $activity = Activity::where(function($q){
            $q->where('subject_type','App\OverseasConference')
            ->whereNot('properties', '{"attributes":[],"old":[]}')
            ->whereNot('properties', '{"old": [], "attributes": []}')
            ->whereNot('properties', '[]');
        })->take(1000)->orderBy('updated_at', 'desc')->get();

        $file_name = sprintf('Overseas Conferences Log - %s', date('Y.m.d'));

        return Excel::download(new LogExporter($activity, false, 'OverseasConference'), $file_name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportIndividualLog($id)
    {        
        
        $overseas_conference = OverseasConference::find($id);
        
        $activity = Activity::where('subject_id', $overseas_conference->id)
                    ->where(function($q){
                        $q->where('subject_type','App\OverseasConference')
                        ->whereNot('properties', '{"attributes":[],"old":[]}')
                        ->whereNot('properties', '{"old": [], "attributes": []}')
                        ->whereNot('properties', '[]');
                    })
                    ->orWhere(function($q) use ($overseas_conference){
                        $q->where('subject_type', 'App\Comment')
                          ->whereIn('subject_id', $overseas_conference->comments()->approved()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($overseas_conference){
                        $q->where('subject_type', 'App\Document')
                          ->whereIn('subject_id', $overseas_conference->documents()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($overseas_conference){
                        $q->where('subject_type', 'App\Twinning')
                          ->whereIn('subject_id', $overseas_conference->twinnings()->get()->pluck('id'));
                    })->take(1000)->orderBy('updated_at', 'desc')->get();

        $file_name = sprintf('Overseas Conference %u Log - %s', $id, date('Y.m.d'));

        return Excel::download(new LogExporter($activity, true, 'OverseasConference'), $file_name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function meta(OverseasConference $overseas_conference)
    {
        return response()->json($this->getLastUpdatedData($overseas_conference));
    }

    private function getStates()
    {
        $user   = Auth::user();
        $states = Helper::getStates();

        foreach ($states as $key => $state) {
            if ($user->hasRole('State User Admin') && !in_array($key, $user->states)) {
                unset($states[$key]);
            } else {
                if ($key == 'national') {
                    $states[$key] = ucwords($key);
                } else {
                    $states[$key] = strtoupper($key);
                }
            }
        }

        return $states;
    }

    public function comments($id)
    {
        $overseas_conference = OverseasConference::find($id);

        return $overseas_conference->comments()
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
}
