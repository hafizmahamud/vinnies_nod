<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use Javascript;
use App\Country;
use App\Twinning;
use Carbon\Carbon;
use App\Vinnies\Helper;
use App\DiocesanCouncil;
use App\LocalConference;
use App\OverseasConference;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Rules\UserCanEditLocalConference;
use App\Vinnies\Exporter\TwinningsExporter;
use App\Activity;
use App\Vinnies\Exporter\LogExporter;

class TwinningController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        $this->authorize('read.twinnings');

        $diocesan_councils = Helper::getDiocesanCouncilsForDropdown();
        $countries         = Country::orderBy('name')->get();

        $activity = Activity::where(function($q){
            $q->where('subject_type','App\Twinning')
            ->whereNot('properties', '{"attributes":[],"old":[]}')
            ->whereNot('properties', '{"old": [], "attributes": []}')
            ->whereNot('properties', '[]');
        })
        ->take(20)->orderBy('updated_at', 'desc')->get();

        Javascript::put([
            'twinning_export_url' => route('twinnings.export'),
        ]);

        return view('twinnings.list')->with(compact('diocesan_councils', 'countries','activity'));
    }

    public function datatables(Request $request)
    {
        $user = Auth::user();

        $this->authorize('read.twinnings');

        $twinnings = $this->getTwinningsFromRequest($request);

        if ($user->hasRole('State User')) {
            $twinnings->whereIn('local_conferences.state', $user->states);
        }

        $twinnings = $twinnings->paginate(!empty($filters['per_page']) ? $filters['per_page'] : config('vinnies.pagination.twinnings'));
        $data      = $this->getDatatableBaseData($twinnings, $request);

        foreach ($twinnings as $twinning) {
            $data['data'][] = [
                'id'                            => $twinning->id,
                'is_active'                     => $twinning->is_active ? 'Active' : 'Surrendered',
                'local_conference_id'           => $twinning->local_conference_id,
                'local_conference_name'         => $twinning->local_conference_name,
                'local_conference_parish'       => $twinning->local_conference_parish,
                'local_conference_state'        => Helper::getStateNameByKey($twinning->local_conference_state),
                'overseas_conference_id'        => $twinning->overseas_conference_id,
                'overseas_conference_name'      => $twinning->overseas_conference_name,
                'overseas_conference_parish'    => $twinning->overseas_conference_parish,
                'overseas_conference_country'   => $twinning->overseas_conference_country,
                'overseas_conference_is_active' => $twinning->overseas_conference_is_active ? 'Remittances' : 'No Remittances', //OS Conf. Receiving Remittances?
                'DT_RowId'                      => 'row_' . $twinning->id,
            ];
        }

        return $data;
    }

    public function showCreateForm(Request $request)
    {
        $this->authorize('create.twinnings');

        $twinning   = new Twinning;
        $countries  = Country::orderBy('name')->get();
        $diocesan_councils = Helper::getFormattedDiocesanCouncils(DiocesanCouncil::where('is_valid', 1)->get());

        if ($request->get('local-conf')) {
            $local_conference = LocalConference::find($request->get('local-conf'));

            if ($local_conference) {
                $twinning->local_conference_id = $local_conference->id;
            }
        }

        if ($request->get('os-conf')) {
            $overseas_conference = OverseasConference::find($request->get('os-conf'));

            if ($overseas_conference) {
                $twinning->overseas_conference_id = $overseas_conference->id;
            }
        }

        return view('twinnings.create')->with(compact('twinning', 'countries', 'diocesan_councils'));
    }

    public function create(Request $request)
    {
        $this->authorize('create.twinnings');

        $data     = $request->validate($this->rules());
        $data     = $this->parseDate($data);
        $msg      = 'Twinning created successfully';

        $data['updated_by'] = Auth::id();
        $data['updated_at'] = Carbon::now();

        $twinning = Twinning::create($data);

        if ($request->filled('comments')) {
            $twinning->comment($request->input('comments'));
            $twinning->update(['comments' => null]);
        }

        if ($request->ajax()) {
            return response()->json([
                'redirect' => route('twinnings.edit', $twinning),
                'msg'      => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function showEditForm(Twinning $twinning)
    {
        // $this->authorize('update.twinnings');
        // $this->checkEditAccess($twinning);

        $countries = Country::orderBy('name')->get();
        $diocesan_councils = Helper::getFormattedDiocesanCouncils(DiocesanCouncil::where('is_valid', 1)->get());
        $activity = Activity::where('subject_id', $twinning->id)
                    ->where(function($q){
                        $q->where('subject_type','App\Twinning')
                        ->whereNot('properties', '{"attributes":[],"old":[]}')
                        ->whereNot('properties', '{"old": [], "attributes": []}')
                        ->whereNot('properties', '[]');
                    })
                    ->orWhere(function($q) use ($twinning){
                        $q->where('subject_type', 'App\Comment')
                          ->whereIn('subject_id', $twinning->comments()->approved()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($twinning){
                        $q->where('subject_type', 'App\Document')
                          ->whereIn('subject_id', $twinning->documents()->withTrashed()->get()->pluck('id'));
                    })
                    ->take(20)->orderBy('updated_at', 'desc')->get();

        JavaScript::put([
            'documentable_type' => 'Twinning',
            'documentable_id'   => $twinning->id,
            'meta_url'          => route('twinnings.meta', $twinning),
        ]);

        return view('twinnings.edit')->with(compact('twinning', 'countries', 'diocesan_councils','activity'));
    }

    public function edit(Request $request, Twinning $twinning)
    {
        $this->authorize('update.twinnings');
        $this->checkEditAccess($twinning);

        $data = $request->validate($this->rules());
        $data = $this->parseDate($data);
        $msg  = 'Twinning updated successfully';

        $data['updated_by'] = Auth::id();
        $data['updated_at'] = Carbon::now();

        $twinning->update($data);

        if ($request->filled('comments')) {
            $twinning->comment($request->input('comments'));
            $twinning->update(['comments' => null]);
        }

        if ($request->ajax()) {
            return response()->json([
                'msg'      => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function addComment(Request $request, Twinning $twinning)
    {
        // $this->authorize('update.twinnings');
        // $this->checkEditAccess($twinning);

        $data = $request->validate(['comments' => 'required']);
        $msg  = 'Your comments added successfully';

        if ($request->filled('comments')) {
            $twinning->comment($request->input('comments'));
            $twinning->update(['comments' => null]);
        }

        if ($request->ajax()) {
            return response()->json([
                'msg'      => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    private function rules()
    {
        return [
            'is_active' => 'required|boolean',
            'type'      => [
                'required',
                Rule::in(array_keys(Helper::getTwinningTypes()))
            ],
            'local_conference_id'    => [
                'required',
                'integer',
                'exists:local_conferences,id',
                new UserCanEditLocalConference,
            ],
            'overseas_conference_id' => 'required|integer|exists:overseas_conferences,id',
            'comments'               => '',
            'is_active_at'           => 'sometimes|nullable|date_format:' . config('vinnies.date_format'),
            'is_surrendered_at'      => 'sometimes|nullable|date_format:' . config('vinnies.date_format'),
            'twinning_period' => [
                'sometimes',
                Rule::in(array_keys(Helper::getTwinningPeriodTypeList())),
            ],
        ];
    }

    public function validateLocalConference(Request $request)
    {
        $this->authorize('read.twinnings');

        $local_conference_id = $request->validate([
            'local_conference_id' => 'required|integer|exists:local_conferences,id',
        ]);

        $conference = LocalConference::withTrashed()->find($local_conference_id)->first();

        return response()->json([
            'local_conference_id'               => $conference->id,
            'local_conference_name'             => $conference->name,
            'local_conference_url'              => route('local-conferences.edit', $conference),
            'local_conference_state'            => strtoupper($conference->state),
            'local_conference_regional_council' => $conference->regional_council,
            'local_conference_diocesan_council' => optional($conference->diocesanCouncil)->name,
            'local_conference_parish'           => $conference->parish,
        ]);

        // for empty conference id, we return empty data
        return response()->json([
            'local_conference_id'               => 'N/A',
            'local_conference_name'             => 'No Australian Conference assigned yet.',
            'local_conference_url'              => '#',
            'local_conference_state'            => 'N/A',
            'local_conference_regional_council' => 'N/A',
            'local_conference_diocesan_council' => 'N/A',
            'local_conference_parish'           => 'N/A',
        ]);
    }

    public function validateOverseasConference(Request $request)
    {
        $this->authorize('read.twinnings');

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
            'overseas_conference_is_active'          => $conference->is_active ? 'Yes' : 'No',
        ]);

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

    private function getTwinningsFromRequest(Request $request)
    {
        // Base queries
        $user = $request->user();
        $twinnings = DB::table('twinnings')
            ->join('local_conferences', 'twinnings.local_conference_id', '=', 'local_conferences.id')
            ->join('overseas_conferences', 'twinnings.overseas_conference_id', '=', 'overseas_conferences.id')
            ->join('countries', 'overseas_conferences.country_id', '=', 'countries.id')
            ->select(
                'twinnings.*',
                'local_conferences.id AS local_conference_id',
                'local_conferences.name AS local_conference_name',
                'local_conferences.parish AS local_conference_parish',
                'local_conferences.state_council AS local_conference_state',
                'local_conferences.regional_council as local_conferences_regional_council',
                'overseas_conferences.id AS overseas_conference_id',
                'overseas_conferences.name AS overseas_conference_name',
                'overseas_conferences.parish AS overseas_conference_parish',
                'overseas_conferences.is_active AS overseas_conference_is_active',
                'countries.name AS overseas_conference_country'
            );

        if ($user->hasRole('State User Admin') || $user->hasRole('State User') || $user->hasRole('Diocesan/Central Council User')) {
            $local_conferences = LocalConference::withTrashed()->own()->pluck('id');
           
            if (!empty($local_conferences)) {
                $twinnings->whereIn('local_conferences.id', $local_conferences->toArray());
            }
        }

        // Filters
        if (!empty($filters = $request->get('filters'))) {
            if (!empty($filters['local_conference_state'])) {
                $twinnings->where('local_conferences.state_council', $filters['local_conference_state']);
            }

            if (!empty($filters['local_conference_diocesan_council_id'])) {
                $twinnings->where('local_conferences.diocesan_council_id', $filters['local_conference_diocesan_council_id']);
            }

            if (!empty($filters['overseas_conference_country_id'])) {
                $twinnings->where('overseas_conferences.country_id', $filters['overseas_conference_country_id']);
            }

            if (!empty($filters['national_council'])) {
                $twinnings->where('overseas_conferences.national_council', $filters['national_council']);
            }

            if (!empty($filters['overseas_conferences_central_council'])) {
                $twinnings->where('overseas_conferences.central_council', $filters['overseas_conferences_central_council']);
            }

            if (!empty($filters['overseas_conferences_particular_council'])) {
                $twinnings->where('overseas_conferences.particular_council', $filters['overseas_conferences_particular_council']);
            }

            if (!empty($filters['overseas_conference_is_active'])) {
                switch ($filters['overseas_conference_is_active']) {
                    case 'active':
                        $twinnings->where('overseas_conferences.is_active', 1);
                        break;

                    case 'inactive':
                        $twinnings->where('overseas_conferences.is_active', 0);
                        break;
                }
            }

            if (!empty($filters['is_active'])) {
                switch ($filters['is_active']) {
                    case 'active':
                        $twinnings->where('twinnings.is_active', 1);
                        break;

                    case 'surrendered':
                        $twinnings->where('twinnings.is_active', 0);
                        break;
                }
            }

            if (!empty($filters['period'])) {
                switch ($filters['period']) {
                    case 'standard':
                        $twinnings->where('twinnings.twinning_period', 'standard');
                        break;

                    case 'temporary':
                        $twinnings->where('twinnings.twinning_period', 'temporary');
                        break;
                }
            }

            if (!empty($filters['type'])) {
                $twinnings->where('twinnings.type', $filters['type']);
            }

            if (!empty($keyword = $filters['local_conferences_regional_council'])) {
                $twinnings->where(function ($query) use ($keyword) {
                    if(substr($keyword, 0, 1) == '"' && substr($keyword, -1) == '"') {
                        $keyword = substr($keyword, 1, -1);
                        $query->where('local_conferences.regional_council', 'LIKE', $keyword);
                    } else {
                        $query->where('local_conferences.regional_council', 'LIKE', '%' . $keyword . '%');
                    }

                });
            }
        }

        // Search
        if (!empty($keyword = $request->get('search')['value'])) {
            $twinnings->where(function ($query) use ($keyword) {
                $query->where('twinnings.id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('local_conferences.id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('local_conferences.name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('local_conferences.state_council', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('overseas_conferences.id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('overseas_conferences.name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('countries.name', 'LIKE', '%' . $keyword . '%');
            });
        }

        // Order
        if (!empty($request->get('order'))) {
            $orders = [
                'id'                            => 'twinnings.id',
                'is_active'                     => 'twinnings.is_active',
                'local_conference_id'           => 'local_conferences.id',
                'local_conference_name'         => 'local_conferences.name',
                'local_conference_state_council'        => 'local_conferences.state_council',
                'overseas_conference_id'        => 'overseas_conferences.id',
                'overseas_conference_name'      => 'overseas_conferences.name',
                'overseas_conference_country'   => 'countries.name',
                'overseas_conference_is_active' => 'overseas_conferences.is_active',
            ];

            foreach ($request->get('order') as $order) {
                $column = $request->get('columns')[$order['column']]['name'];

                if (!array_key_exists($column, $orders)) {
                    continue;
                }

                $twinnings->orderBy($orders[$column], $order['dir']);
            }
        }

        return $twinnings;
    }

    public function export(Request $request)
    {
        $this->authorize('export.twinnings');
        $twinnings = $this->getTwinningsFromRequest($request)->orderBy('id', 'desc')->get();
        $file_name = sprintf('Twinnings - %s', date('Y.m.d'));

        return Excel::download(new TwinningsExporter($twinnings), $file_name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportLog(Request $request)
    {        
        $activity = Activity::where(function($q){
            $q->where('subject_type','App\Twinning')
            ->whereNot('properties', '{"attributes":[],"old":[]}')
            ->whereNot('properties', '{"old": [], "attributes": []}')
            ->whereNot('properties', '[]');
        })->take(1000)->orderBy('updated_at', 'desc')->get();

        $file_name = sprintf('Twinnings Log - %s', date('Y.m.d'));

        return Excel::download(new LogExporter($activity, false, 'Twinning'), $file_name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportIndividualLog($id)
    {        
        
        $twinning = Twinning::find($id);
        
        $activity = Activity::where('subject_id', $twinning->id)
                    ->where(function($q){
                        $q->where('subject_type','App\Twinning')
                        ->whereNot('properties', '{"attributes":[],"old":[]}')
                        ->whereNot('properties', '{"old": [], "attributes": []}')
                        ->whereNot('properties', '[]');
                    })
                    ->orWhere(function($q) use ($twinning){
                        $q->where('subject_type', 'App\Comment')
                          ->whereIn('subject_id', $twinning->comments()->approved()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($twinning){
                        $q->where('subject_type', 'App\Document')
                          ->whereIn('subject_id', $twinning->documents()->withTrashed()->get()->pluck('id'));
                    })->take(1000)->orderBy('updated_at', 'desc')->get();

        $file_name = sprintf('Twinning %u Log - %s', $id, date('Y.m.d'));

        return Excel::download(new LogExporter($activity, true, 'Twinning'), $file_name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    private function parseDate($data)
    {
        if (!empty($data['is_active_at'])) {
            $data['is_active_at']  = Carbon::createFromFormat(config('vinnies.date_format'), $data['is_active_at']);
        } else {
            $data['is_active_at'] = null;
        }

        if (!empty($data['is_surrendered_at'])) {
            $data['is_surrendered_at']  = Carbon::createFromFormat(config('vinnies.date_format'), $data['is_surrendered_at']);
        } else {
            $data['is_surrendered_at'] = null;
        }

        return $data;
    }

    public function meta(Twinning $twinning)
    {
        return response()->json($this->getLastUpdatedData($twinning));
    }

    public function comments($id)
    {
        $twinning = Twinning::find($id);

        return $twinning->comments()
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

    private function checkEditAccess(Twinning $twinning)
    {
        $user = Auth::user();

        if ($user->hasRole('State User Admin') || $user->hasRole('Diocesan/Central Council User')) {
            $can_access = in_array($twinning->local_conference_id, LocalConference::withTrashed()->own()->pluck('id')->toArray());

            if (!$can_access) {
                abort(403);
            }
        }
    }
}
