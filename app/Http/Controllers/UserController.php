<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use JavaScript;
use Carbon\Carbon;
use App\Vinnies\Access;
use App\Vinnies\Helper;
use App\DiocesanCouncil;
use Illuminate\Http\Request;
use App\Rules\StrongPassword;
use App\Rules\ValidEmailDomain;
use Illuminate\Validation\Rule;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Password;
use App\Activity;
use App\Vinnies\Exporter\LogExporter;
use DB;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        $this->authorize('read.users');

        $activity = Activity::where(function($q){
            $q->where('subject_type','App\User')
            ->whereNot('properties', '{"attributes":[],"old":[]}')
            ->whereNot('properties', '{"old": [], "attributes": []}')
            ->whereNot('properties', '[]');
        })
        ->take(20)->orderBy('updated_at', 'desc')->get();

        return view('users.list')->with(compact('activity'));
    }

    public function datatables(Request $request)
    {
        $this->authorize('read.users');

        $users = User::whereNotNull('id');
        $users = $this->sortModelFromRequest($users, $request);

        if (!empty($filters = $request->get('filters'))) {
            switch ($filters['status']) {
                case 'active':
                    // do nothing for now
                    $users->where('is_active', 1)->withTrashed();
                    break;

                case 'not-active':
                    // $users->onlyTrashed();
                    $users->where('is_active', 0)->withTrashed();
                    break;

                default:
                    $users->withTrashed();
                    break;
            }
        }

        if (!empty($keyword = $request->get('search')['value'])) {
            $users->where(function ($query) use ($keyword) {
                $query->where('first_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('email', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('id', 'LIKE', '%' . $keyword . '%');
            });
        }

        $users = $users->paginate(config('vinnies.pagination.users'));
        $data  = $this->getDatatableBaseData($users, $request);

        foreach ($users as $user) {
            $data['data'][] = [
                'id'         => $user->id,
                'states'     => $user->getStates(),
                'first_name' => $user->first_name,
                'last_name'  => $user->last_name,
                'role'       => $user->roles()->pluck('name')->first(),
                'mfa'        => $user->hasGoogle2FAEnabled() ? 'Activated' : '-' ,
                'last_login' => $user->last_login ? $user->last_login->format(config('vinnies.datetime_format')) : 'Never',
                'email'      => $user->email,
                'DT_RowId'   => 'row_' . $user->id
            ];
        }

        return $data;
    }

    public function showCreateForm()
    {
        $this->authorize('create.users');

        $diocesan_councils = Helper::getDiocesanCouncilsForDropdown();

        JavaScript::put([
            'diocesan_councils' => $diocesan_councils,
        ]);

        return view('users.create')->with(compact('diocesan_councils'));
    }

    public function create(Request $request)
    {
        $this->authorize('create.users');

        $roles  = Access::getRoles()->all();
        $states = Helper::getStates();

        // Validate this
        $request->validate([
            'first_name' => 'required',
            'last_name'  => 'required',
            'branch_display' => 'required',
            'has_accepted_conditions' => 'required',
            'conditions_accepted_at'  => 'sometimes|nullable|date_format:' . config('vinnies.date_format'),
            'email'          => [
                'required',
                'email',
                'unique:users',
                new ValidEmailDomain,
            ],
            'states'         => 'required|array|min:1',
            'states.*'       => [
                'required',
                Rule::in(array_keys($states))
            ],
            'dioceses'   => 'present|nullable|array|min:0',
            'dioceses.*' => 'integer|exists:diocesan_councils,id|min:0',
            'role' => [
                'required',
                Rule::in(array_keys($roles))
            ]
        ]);

        $data = $request->only(['first_name', 'last_name', 'email', 'states', 'dioceses', 'branch_display', 'has_accepted_conditions', 'conditions_accepted_at']);
        $data = $this->parseDate($data);

        sort($data['states']);
        sort($data['dioceses']);

        $data['password']   = bcrypt(str_random(12));
        $data['states']     = implode('|', $data['states']);
        $data['dioceses']   = implode('|', $data['dioceses']);
        $data['updated_by'] = Auth::id();
        $data['updated_at'] = Carbon::now();
        $data['is_new']     = true;

        $msg  = 'New user has been successfully created';
        $user = User::create($data);

        $user->syncRoles([$roles[$request->get('role')]]);
        $user->update([
            'google2fa_secret' => (new Google2FA)->generateSecretKey(),
        ]);

        Password::broker()->sendResetLink(['email' => $user->email]);

        if ($request->ajax()) {
            return response()->json([
                'msg' => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function showEditForm($id)
    {
        $this->authorize('update.users');

        $user              = User::withTrashed()->find($id);
        $selectedRole      = array_search($user->roles()->pluck('name')->first(), Access::getRoles()->all());
        $diocesan_councils = Helper::getDiocesanCouncilsForDropdown();
        $activity = Activity::where('subject_id', $id)                    
                    ->where(function($q){
                        $q->where('subject_type','App\User')
                        ->whereNot('properties', '{"attributes":[],"old":[]}')
                        ->whereNot('properties', '{"old": [], "attributes": []}')
                        ->whereNot('properties', '[]');
                    })
                    ->orWhere(function($q) use ($user){
                        $q->where('subject_type', 'App\Document')
                          ->whereIn('subject_id', $user->documents()->withTrashed()->get()->pluck('id'));
                    })
                    ->take(20)->orderBy('updated_at', 'desc')->get();
       
        JavaScript::put([
            'meta_url'          => route('users.meta', $id),
            'diocesan_councils' => $diocesan_councils,
            'documentable_type'  => 'User',
            'documentable_id'    => $id,
        ]);

        return view('users.edit')->with(compact('user', 'selectedRole', 'diocesan_councils', 'activity'));
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('update.users');

        $user   = User::withTrashed()->find($id);
        $states = Helper::getStates();
        $roles  = Access::getRoles()->all();
        $rules  = [
            'first_name'     => 'required',
            'last_name'      => 'required',
            'branch_display' => 'required',
            'has_accepted_conditions' => 'required',
            'conditions_accepted_at'  => 'sometimes|nullable|date_format:' . config('vinnies.date_format'),
            'email'          => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
                new ValidEmailDomain,
            ],
            'states' => [
                'required',
                'array',
                'min:1',
            ],
            'states.*' => [
                'required',
                Rule::in(array_keys($states))
            ],
            // 'dioceses'   => 'nullable|array|min:0',
            // 'dioceses.*' => 'integer|exists:diocesan_councils,id|min:0',
            'role' => [
                'required',
                Rule::in(array_keys($roles))
            ]
        ];

        if ($request->filled('password')) {
            $rules['password'] = [
                'min:8',
                new StrongPassword,
            ];
        }

        $request->validate($rules);

        $data = $request->only(['first_name', 'last_name', 'email', 'states', 'dioceses', 'branch_display', 'has_accepted_conditions', 'conditions_accepted_at']);
        $data = $this->parseDate($data);

        sort($data['states']);

        if ($request->has('dioceses')){
            sort($data['dioceses']);
        } else {
            $data['dioceses'] = NULL;
        }
        // sort($data['dioceses']);

        $data['states']   = implode('|', $data['states']);

        if ($request->has('dioceses')){
            $data['dioceses'] = implode('|', $data['dioceses']);
        }
        
        if ($request->get('password') == '') {
            $request->except(['password']); 
        } else {
            $data['password'] = bcrypt($request->get('password'));
        }

        $data['updated_by'] = Auth::id();
        $data['updated_at'] = Carbon::now();

        $user->update($data);
        $user->syncRoles([$roles[$request->get('role')]]);

        $msg = 'User has been successfully edited';

        if ($request->ajax()) {
            return response()->json([
                'msg' => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function deactivate(Request $request)
    {
        $this->authorize('delete.users');

        $user = User::withTrashed()->find($request->get('user'));

        if (!$user) {
            return response()->json([
                'msg' => 'Invalid user supplied.'
            ], 400);
        }
        
        $user->deactivate();

        return response()->json([
            'msg' => 'Selected user have been successfully deactivated.'
        ]);
    }

    public function reactivate(Request $request)
    {
        $this->authorize('delete.users');

        $user = User::withTrashed()->find($request->get('user'));

        if (!$user) {
            return response()->json([
                'msg' => 'Invalid user supplied.'
            ], 400);
        }
        
        $user->google2fa_enabled_at = NULL;
        $user->has_accepted_terms = 0;
        $user->conditions_accepted_at = NULL;
        $user->deleted_at = NULL;
        $user->is_active = 1;
        $user->last_login = Carbon::now();
        $user->save();

        $request['email'] = $user->email;
        Password::broker()->sendResetLink($request->only('email'));
          
        return response()->json([
            'msg' => 'Selected user have been successfully reactivated.'
        ]);
    }

    public function meta($id)
    {
        $user = User::withTrashed()->find($id);

        return response()->json($this->getLastUpdatedData($user));
    }

    private function parseDate($data)
    {
        if (!empty($data['conditions_accepted_at'])) {
            $data['conditions_accepted_at']  = Carbon::createFromFormat(config('vinnies.date_format'), $data['conditions_accepted_at']);
        } else {
            $data['conditions_accepted_at'] = null;
        }

        return $data;
    }


    public function signtos(Request $request)
    {
        $user = User::find($request->get('user'));

        if (!$user) {
            return response()->json([
                'msg' => 'Invalid user supplied.'
            ], 400);
        }

        $user->has_accepted_terms = "0";
        $user->has_accepted_conditions = "0";
        $user->conditions_accepted_at = null;
        $user->save();

        return response()->json([
            'msg' => 'Selected user have been successfully asked to re-sign the Term of Use'
        ]);
    }

    public function exportLog(Request $request)
    {        
        $activity = Activity::where(function($q){
            $q->where('subject_type','App\User')
            ->whereNot('properties', '{"attributes":[],"old":[]}')
            ->whereNot('properties', '{"old": [], "attributes": []}')
            ->whereNot('properties', '[]');
        })->take(1000)->orderBy('updated_at', 'desc')->get();

        $file_name = sprintf('Users Log - %s', date('Y.m.d'));

        return Excel::download(new LogExporter($activity, false, 'User'), $file_name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportIndividualLog($id)
    {        
        
        $user = User::withTrashed()->find($id);
        
        $activity = Activity::where('subject_id', $id)                    
                    ->where(function($q){
                        $q->where('subject_type','App\User')
                        ->whereNot('properties', '{"attributes":[],"old":[]}')
                        ->whereNot('properties', '{"old": [], "attributes": []}')
                        ->whereNot('properties', '[]');
                    })
                    ->orWhere(function($q) use ($user){
                        $q->where('subject_type', 'App\Document')
                          ->whereIn('subject_id', $user->documents()->withTrashed()->get()->pluck('id'));
                    })->take(1000)->orderBy('updated_at', 'desc')->get();

        $file_name = sprintf('User %u Log - %s', $id, date('Y.m.d'));

        return Excel::download(new LogExporter($activity, true, 'User'), $file_name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
