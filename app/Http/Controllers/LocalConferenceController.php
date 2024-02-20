<?php

namespace App\Http\Controllers;

use Auth;
use App\Donor;
use JavaScript;
use App\Country;
use App\Project;
use Carbon\Carbon;
use App\Vinnies\Helper;
use App\DiocesanCouncil;
use App\LocalConference;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Vinnies\Exporter\LocalConferencesExporter;
use App\Activity;
use App\Vinnies\Exporter\LogExporter;

class LocalConferenceController extends Controller
{
    protected $exclude_orders = [
        'diocesan_council_id',
        'status',
    ];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        $this->authorize('read.local-conf');

        $diocesan_councils = Helper::getDiocesanCouncilsForDropdown();

        $activity = Activity::where(function($q){
            $q->where('subject_type','App\LocalConference')
            ->whereNot('properties', '{"attributes":[],"old":[]}')
            ->whereNot('properties', '{"old": [], "attributes": []}')
            ->whereNot('properties', '[]');
        })
        ->take(20)->orderBy('updated_at', 'desc')->get();

        Javascript::put([
            'localconf_export_url' => route('local-conferences.export'),
        ]);

        return view('local-conferences.list')->with(compact('diocesan_councils','activity'));
    }

    public function datatables(Request $request)
    {
        $this->authorize('read.local-conf');
        $conferences = $this->getConferencesFromRequest($request);
        $conferences = $conferences->paginate(!empty($filters['per_page']) ? $filters['per_page'] : config('vinnies.pagination.local_conferences'));
        $data        = $this->getDatatableBaseData($conferences, $request);

        foreach ($conferences as $conference) {

            $state_council = Helper::getStateNameByKey($conference->state_council);

            $data['data'][] = [
                'id'                  => $conference->id,
                'select'              => $conference->id,
                'name'                => $conference->name,
                'parish'              => $conference->parish,
                'status'              => (optional($conference)->trashed() ? 'Abeyant' : 'Active'),
                'state'               => strtoupper($conference->state),
                'state_council'       => $state_council,
                'regional_council'    => $conference->regional_council,
                'diocesan_council_id' => optional($conference->diocesanCouncil)->name,
                'DT_RowId'            => 'row_' . $conference->id,
                'is_flagged'          => (optional($conference)->trashed() ? 'Yes' : 'No'),
                'local_conferences_regional_council' => $conference->regional_council,
            ];
        }

        return $data;
    }

    public function showCreateForm()
    {
        $this->authorize('create.local-conf');

        $states            = $this->getStates();
        $states_all        = $this->getAllStates();
        $diocesan_councils = Helper::getDiocesanCouncilsForDropdown();
        $local_conference  = new LocalConference;

        return view('local-conferences.create')->with(compact('local_conference', 'states', 'states_all', 'diocesan_councils'));
    }

    public function create(Request $request)
    {
        $this->authorize('create.local-conf');

        $data = $request->validate($this->rules());
        $data = $this->parseDate($data);
        $msg  = 'Australian conference created successfully';

        $data['updated_by'] = Auth::id();
        $data['updated_at'] = Carbon::now();

        $local_conference = LocalConference::create($data);

        if ($request->filled('comments')) {
            $local_conference->comment($request->input('comments'));
            $local_conference->update(['comments' => null]);
        }

        if ($data['status'] == 'abeyant') {
            $local_conference->delete();
        }

        $local_conference->updateSortField();

        if ($request->ajax()) {
            return response()->json([
                'redirect' => route('local-conferences.edit', $local_conference),
                'msg'      => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function showEditForm($id)
    {
        // $this->authorize('update.local-conf');
        // $this->checkEditAccess($id);

        $states           = $this->getStates();
        $states_all       = $this->getAllStates();
        $local_conference = LocalConference::withTrashed()->find($id);
        $projects         = Project::whereHas('donors', function ($query) use ($local_conference) {
            $query->where('local_conference_id', $local_conference->id);
        })->get();

        $diocesan_council  = DiocesanCouncil::find($local_conference->diocesan_council_id);
        $diocesan_councils = Helper::getDiocesanCouncilsForDropdown($diocesan_council);

        if (!empty($diocesan_council) && !$diocesan_council->is_valid) {
            Javascript::put([
                'invalid_diocesan_council_id' => $diocesan_council->id,
            ]);
        }
        $activity = Activity::where('subject_id', $id)
                    ->where(function($q){
                        $q->where('subject_type','App\LocalConference')
                        ->whereNot('properties', '{"attributes":[],"old":[]}')
                        ->whereNot('properties', '{"old": [], "attributes": []}')
                        ->whereNot('properties', '[]');
                    })
                    ->orWhere(function($q) use ($local_conference){
                        $q->where('subject_type', 'App\Comment')
                          ->whereIn('subject_id', $local_conference->comments()->approved()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($local_conference){
                        $q->where('subject_type', 'App\Document')
                          ->whereIn('subject_id', $local_conference->documents()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($local_conference){
                        $q->where('subject_type', 'App\Twinning')
                          ->whereIn('subject_id', $local_conference->twinnings()->get()->pluck('id'));
                    })
                    ->take(20)->orderBy('updated_at', 'desc')->get();
   
        JavaScript::put([
            'has_invalid_diocesan_council' => (!optional($diocesan_council)->is_valid),
            'documentable_type'            => 'LocalConference',
            'documentable_id'              => $local_conference->id,
            'meta_url'                     => route('local-conferences.meta', $id),
        ]);

        return view('local-conferences.edit')->with(compact('local_conference', 'states', 'states_all', 'diocesan_councils', 'projects', 'activity'));
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('update.local-conf');
        $this->checkEditAccess($id);

        $data = $request->validate($this->rules());
        $data = $this->parseDate($data);
        $msg  = 'Australian conference updated successfully';
        $local_conference  = LocalConference::withTrashed()->find($id);

        $data['updated_by'] = Auth::id();
        $data['updated_at'] = Carbon::now();

        $local_conference->update($data);

        if ($data['status'] == 'abeyant') {
            if(!$local_conference->deleted_at){
                $local_conference->delete();
            }
        } else {
            if($local_conference->deleted_at){
                $local_conference->restore();
            }
        }

        $local_conference->updateSortField();

        if ($request->filled('comments')) {
            $local_conference->comment($request->input('comments'));
            $local_conference->update(['comments' => null]);
        }

        if ($request->ajax()) {
            return response()->json([
                'msg'      => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function addComment(Request $request, $id)
    {
        // $this->authorize('update.local-conf');

        $data = $request->validate(['comments' => 'required']);
        $msg  = 'Your comments added successfully';

        $local_conference = LocalConference::withTrashed()->find($id);

        if ($request->filled('comments')) {
            $local_conference->comment($request->input('comments'));
            $local_conference->update(['comments' => null]);
        }

        if ($request->ajax()) {
            return response()->json([
                'msg' => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    private function getStates()
    {
        $user   = Auth::user();
        $states = Helper::getStates();

        foreach ($states as $key => $state) {
            if ($user->hasRole('State User Admin') && !in_array($key, $user->states)) {
                unset($states[$key]);
            } else {
                if ($key == 'national'){
                    unset($states[$key]);
                } else {
                $states[$key] = strtoupper($key);
                }
            }
        }
        return $states;
    }

    private function getAllStates()
    {
        $states = Helper::getAllStates();

        return $states;
    }

    private function rules()
    {
        return [
            'local_conferences_regional_council' => '',
            'is_flagged'          => 'required|boolean',
            'status'              => 'required|in:active,abeyant',
            'name'                => 'required',
            'aggregation_number'  => '',
            'contact_name'        => '',
            'contact_email'       => 'sometimes|nullable|email',
            'contact_phone'       => '',
            'state' => [
                'required',
                Rule::in(array_keys(Helper::getAllStates())),
            ],
            'state_council' => [
                'required',
                Rule::in(array_keys(Helper::getAllStates())),
            ],
            'regional_council'    => '',
            'diocesan_council_id' => 'required|nullable|integer',
            'parish'              => '',
            'address_line_1'      => '',
            'address_line_2'      => '',
            //'address_line_3'      => '',
            'suburb'              => '',
            'postcode'            => '',
            'country'             => '',
            'state_address' => [
                '',
                Rule::in(array_keys(Helper::getAllStates())),
            ],
            'comments'            => '',
            'cost_code'           => '',
            'is_active_at'        => 'sometimes|nullable|date_format:' . config('vinnies.date_format'),
            'is_abeyant_at'       => 'sometimes|nullable|date_format:' . config('vinnies.date_format'),
            'last_confirmed_at'   => 'sometimes|nullable|date_format:' . config('vinnies.date_format'),
        ];
    }

    private function parseDate($data)
    {
        if (!empty($data['is_active_at'])) {
            $data['is_active_at']  = Carbon::createFromFormat(config('vinnies.date_format'), $data['is_active_at']);
        } else {
            $data['is_active_at'] = null;
        }

        if (!empty($data['is_abeyant_at'])) {
            $data['is_abeyant_at']  = Carbon::createFromFormat(config('vinnies.date_format'), $data['is_abeyant_at']);
        } else {
            $data['is_abeyant_at'] = null;
        }

        if (!empty($data['last_confirmed_at'])) {
            $data['last_confirmed_at']  = Carbon::createFromFormat(config('vinnies.date_format'), $data['last_confirmed_at']);
        } else {
            $data['last_confirmed_at'] = null;
        }

        return $data;
    }

    private function getConferencesFromRequest(Request $request)
    {
        $user = Auth::user();

        $table = (new LocalConference)->getTable();

        $conferences = LocalConference::whereNotNull($table . '.id')
            ->own()
            ->leftJoin('diocesan_councils', $table . '.diocesan_council_id', '=', 'diocesan_councils.id')
            ->select($table . '.*');

        $conferences = $this->sortModelFromRequest($conferences, $request, $table);

        if (!$user->hasRole('Full Admin')) {
            $conferences->whereIn($table . '.diocesan_council_id', $user->dioceses);
        }

        if (!empty($filters = $request->get('filters'))) {
            if (!empty($filters['state_council'])) {
                $conferences->where($table . '.state_council', $filters['state_council']);
            }

            if (!empty($filters['diocesan_council_id'])) {
                $conferences->where($table . '.diocesan_council_id', $filters['diocesan_council_id']);
            }

            if (!empty($filters['state'])) {
                $conferences->where($table . '.state', $filters['state']);
            }

            if (!empty($filters['status'])) {
                switch ($filters['status']) {
                    case 'abeyant':
                        $conferences->onlyTrashed();
                        break;
                }
            } else {
                $conferences->withTrashed();
            }

            // if (!empty($filters['is_flagged'])) {
            //     $conferences->where($table . '.is_flagged', $filters['is_flagged']);
            // }
            if (!empty($filters['is_flagged'])) {
                switch ($filters['is_flagged']) {
                    case 'yes':
                        $conferences->where($table . '.is_flagged', 1);
                        break;

                    case 'no':
                        $conferences->where($table . '.is_flagged', 0);
                        break;
                }
            }

           if (!empty($filters['regional_council'])){
            $conferences->where($table . '.regional_council', $filters['regional_council']);
           }
        }

        if (!empty($keyword = $request->get('search')['value'])) {
            $conferences->where(function ($query) use ($keyword, $table) {
                $query->where($table . '.id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere($table . '.name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere($table . '.state', 'LIKE', '%' . $keyword . '%')
                    ->orWhere($table . '.state_council', 'LIKE', '%' . $keyword . '%')
                    ->orWhere($table . '.regional_council', 'LIKE', '%' . $keyword . '%')
                    ->orWhere($table . '.parish', 'LIKE', '%' . $keyword . '%')
                    ->orWhere($table . '.contact_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere($table . '.contact_email', 'LIKE', '%' . $keyword . '%');
            });
        }

        // Custom orders
        if (!empty($request->get('order'))) {
            foreach ($request->get('order') as $order) {
                switch ($request->get('columns')[$order['column']]['name']) {
                    case 'diocesan_council_id':
                        $conferences->orderBy($table . '._diocesan_council', $order['dir']);
                        break;

                    case 'status':
                        $conferences->orderBy($table . '.deleted_at', $order['dir']);
                        break;
                }
            }
        }
        return $conferences;
    }

    public function export(Request $request)
    {
        $this->authorize('export.local-conf');
        $conferences = $this->getConferencesFromRequest($request)->with(['twinnings', 'documents'])->orderBy('id', 'desc')->get();
        $file_name = sprintf('Australian Conferences - %s', date('Y.m.d'));

        return Excel::download(new LocalConferencesExporter($conferences), $file_name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportLog(Request $request)
    {        
        $activity = Activity::where(function($q){
            $q->where('subject_type','App\LocalConference')
            ->whereNot('properties', '{"attributes":[],"old":[]}')
            ->whereNot('properties', '{"old": [], "attributes": []}')
            ->whereNot('properties', '[]');
        })->take(1000)->orderBy('updated_at', 'desc')->get();

        $file_name = sprintf('AU Conferences Log - %s', date('Y.m.d'));

        return Excel::download(new LogExporter($activity, false, 'LocalConference'), $file_name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportIndividualLog($id)
    {        
        
        $local_conference = LocalConference::withTrashed()->find($id);
        
        $activity = Activity::where('subject_id', $id)
                    ->where(function($q){
                        $q->where('subject_type','App\LocalConference')
                        ->whereNot('properties', '{"attributes":[],"old":[]}')
                        ->whereNot('properties', '{"old": [], "attributes": []}')
                        ->whereNot('properties', '[]');
                    })
                    ->orWhere(function($q) use ($local_conference){
                        $q->where('subject_type', 'App\Comment')
                          ->whereIn('subject_id', $local_conference->comments()->approved()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($local_conference){
                        $q->where('subject_type', 'App\Document')
                          ->whereIn('subject_id', $local_conference->documents()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($local_conference){
                        $q->where('subject_type', 'App\Twinning')
                          ->whereIn('subject_id', $local_conference->twinnings()->get()->pluck('id'));
                    })->take(1000)->orderBy('updated_at', 'desc')->get();

        $file_name = sprintf('AU Conference %u Log - %s', $id, date('Y.m.d'));

        return Excel::download(new LogExporter($activity, true, 'LocalConference'), $file_name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function meta($id)
    {
        $local_conference = LocalConference::withTrashed()->find($id);

        return response()->json($this->getLastUpdatedData($local_conference));
    }

    public function comments($id)
    {
        $local_conference = LocalConference::withTrashed()->find($id);

        return $local_conference->comments()
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
        $local_conference = LocalConference::withTrashed()->find($id);
        $can_access = true;


        if ($user->hasRole('State User')) {
            //List AUS Conf. based on user states
            if (!empty($user->states)) {
                $can_access = in_array($local_conference->state, $user->states);
            }

            //List AUS Conf. based on user dioceses
            if (!empty($user->dioceses)) {
                $can_access = in_array($local_conference->diocesan_council_id, $user->dioceses);
            }

            if (!$can_access) {
                abort(403);
            }
        }
    }
}
