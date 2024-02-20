<?php

namespace App\Http\Controllers;

use Auth;
use JavaScript;
use App\Country;
use Carbon\Carbon;
use App\Beneficiary;
use App\Vinnies\Helper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Activity;
use App\Vinnies\Exporter\LogExporter;
use Maatwebsite\Excel\Facades\Excel;

class BeneficiaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        $this->authorize('read.beneficiaries');

        $countries = Country::orderBy('name')->get();

        $activity = Activity::where(function($q){
            $q->where('subject_type','App\Beneficiary')
            ->whereNot('properties', '{"attributes":[],"old":[]}')
            ->whereNot('properties', '{"old": [], "attributes": []}')
            ->whereNot('properties', '[]');
        })
        ->take(20)->orderBy('updated_at', 'desc')->get();

        return view('beneficiaries.list')->with(compact('countries', 'activity'));
    }

    public function datatables(Request $request)
    {
        $this->authorize('read.beneficiaries');

        $beneficiaries = Beneficiary::whereNotNull('id');
        $beneficiaries = $this->sortModelFromRequest($beneficiaries, $request);

        if (!empty($filters = $request->get('filters'))) {
            if (!empty($filters['country'])) {
                $country = Country::find($filters['country']);

                $beneficiaries->where('country_id', $country->id);
            }

            if (empty($filters['status'])) {
                $beneficiaries->withTrashed();
            } else {
                switch ($filters['status']) {
                    case 'inactive':
                        $beneficiaries->onlyTrashed();
                        break;
                }
            }
        }

        if (!empty($keyword = $request->get('search')['value'])) {
            $beneficiaries->where(function ($query) use ($keyword) {
                $query->where('id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('contact_first_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('contact_last_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('contact_preferred_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('address_line_1', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('address_line_2', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('address_line_3', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('suburb', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('phone', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('fax', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('email', 'LIKE', '%' . $keyword . '%');
            });
        }

        $beneficiaries = $beneficiaries->paginate(config('vinnies.pagination.beneficiaries'));
        $data  = $this->getDatatableBaseData($beneficiaries, $request);

        foreach ($beneficiaries as $beneficiary) {
            $data['data'][] = [
                'id'                 => $beneficiary->id,
                'name'               => $beneficiary->name,
                'country'            => $beneficiary->country->name,
                'contact_title'      => $beneficiary->contact_title,
                'contact_first_name' => $beneficiary->contact_first_name,
                'contact_last_name'  => $beneficiary->contact_last_name,
                'email'              => $beneficiary->email,
                'status'             => $beneficiary->trashed() ? 'Deleted' : 'In Use',
                'DT_RowId'           => 'row_' . $beneficiary->id,
            ];
        }

        return $data;
    }

    public function showCreateForm(Request $request)
    {
        $this->authorize('create.beneficiaries');

        $beneficiary = new Beneficiary;
        $countries   = Country::orderBy('name')->get()->mapWithKeys(function ($country) {
            return [
                $country->id => $country->name,
            ];
        })->toArray();

        return view('beneficiaries.create')->with(compact('beneficiary', 'countries'));
    }

    public function create(Request $request)
    {
        $this->authorize('create.beneficiaries');

        $data = $request->validate($this->rules());
        $msg  = 'Beneficiaries created successfully';
        $beneficiary = Beneficiary::create($data);

        $beneficiary->updated_by = Auth::id();
        $beneficiary->updated_at = Carbon::now();
        $beneficiary->save();

        if ($request->filled('comments')) {
            $beneficiary->comment($request->input('comments'));
            $beneficiary->update(['comments' => null]);
        }

        if ($request->ajax()) {
            return response()->json([
                'redirect' => route('beneficiaries.edit', $beneficiary),
                'msg'      => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function showEditForm(Request $request, $id)
    {
        $this->authorize('update.beneficiaries');

        $beneficiary = Beneficiary::withTrashed()->find($id);
        $countries   = Country::orderBy('name')->get()->mapWithKeys(function ($country) {
            return [
                $country->id => $country->name,
            ];
        })->toArray();
        $activity = Activity::where('subject_id', $id)
                    ->where(function($q){
                        $q->where('subject_type','App\Beneficiary')
                        ->whereNot('properties', '{"attributes":[],"old":[]}')
                        ->whereNot('properties', '{"old": [], "attributes": []}')
                        ->whereNot('properties', '[]');
                    })
                    ->orWhere(function($q) use ($beneficiary){
                        $q->where('subject_type', 'App\Comment')
                          ->whereIn('subject_id', $beneficiary->comments()->approved()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($beneficiary){
                        $q->where('subject_type', 'App\Document')
                          ->whereIn('subject_id', $beneficiary->documents()->withTrashed()->get()->pluck('id'));
                    })
                    ->take(20)->orderBy('updated_at', 'desc')->get();

        // JavaScript::put([
        //     'meta_url' => route('beneficiaries.meta', $id),
        // ]);

        JavaScript::put([
            'documentable_type' => 'Beneficiary',
            'documentable_id'   => $id,
            'meta_url'          => route('beneficiaries.meta', $id),
        ]);

        return view('beneficiaries.edit')->with(compact('beneficiary', 'countries', 'activity'));
    }

    public function edit(Request $request, $id)
    {
        $this->authorize('update.beneficiaries');

        $data = $request->validate($this->rules());
        $msg  = 'Beneficiaries edited successfully';
        $beneficiary = Beneficiary::withTrashed()->find($id);

        $beneficiary->update($data);

        $beneficiary->updated_by = Auth::id();
        $beneficiary->updated_at = Carbon::now();
        $beneficiary->save();

        if ($request->filled('comments')) {
            $beneficiary->comment($request->input('comments'));
            $beneficiary->update(['comments' => null]);
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
        $this->authorize('update.beneficiaries');

        $data = $request->validate(['comments' => 'required']);
        $msg  = 'Your comments added successfully';

        $beneficiary = Beneficiary::withTrashed()->find($id);

        if ($request->filled('comments')) {
            $beneficiary->comment($request->input('comments'));
            $beneficiary->update(['comments' => null]);
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
            'name'                   => 'required',
            'country_id'             => 'required|integer|exists:countries,id',
            'contact_title'          => '',
            'contact_first_name'     => '',
            'contact_last_name'      => '',
            'contact_preferred_name' => '',
            'email'                  => 'sometimes|nullable|email',
            'contact_position'       => '',
            'address_line_1'         => '',
            'address_line_2'         => '',
            'address_line_3'         => '',
            'suburb'                 => '',
            'state'                  => '',
            'postcode'               => '',
            'phone'                  => '',
            'fax'                    => '',
            'comments'               => '',
        ];
    }

    public function delete(Request $request)
    {
        $this->authorize('delete.beneficiaries');

        $beneficiary = Beneficiary::find($request->get('beneficiary'));

        if (!$beneficiary) {
            return response()->json([
                'msg' => 'Invalid beneficiary supplied.'
            ], 400);
        }

        $beneficiary->updated_by = Auth::id();
        $beneficiary->updated_at = Carbon::now();
        $beneficiary->save();

        $beneficiary->delete();

        return response()->json([
            'msg' => 'Selected beneficiary have been successfully deleted.'
        ]);
    }

    public function restore(Request $request)
    {
        $this->authorize('delete.beneficiaries');

        $beneficiary = Beneficiary::onlyTrashed()->find($request->get('beneficiary'));

        if (!$beneficiary) {
            return response()->json([
                'msg' => 'Invalid beneficiary supplied.'
            ], 400);
        }

        $beneficiary->updated_by = Auth::id();
        $beneficiary->updated_at = Carbon::now();
        $beneficiary->save();

        $beneficiary->restore();

        return response()->json([
            'msg' => 'Selected beneficiary have been successfully restored.'
        ]);
    }

    public function meta($id)
    {
        $beneficiary = Beneficiary::withTrashed()->find($id);

        return response()->json($this->getLastUpdatedData($beneficiary));
    }

    public function comments($id)
    {
        $beneficiary = Beneficiary::withTrashed()->find($id);

        return $beneficiary->comments()
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

    public function exportLog(Request $request)
    {        
        $activity = Activity::where(function($q){
            $q->where('subject_type','App\Beneficiary')
            ->whereNot('properties', '{"attributes":[],"old":[]}')
            ->whereNot('properties', '{"old": [], "attributes": []}')
            ->whereNot('properties', '[]');
        })->take(1000)->orderBy('updated_at', 'desc')->get();

        $file_name = sprintf('Beneficiaries Log - %s', date('Y.m.d'));

        return Excel::download(new LogExporter($activity, false, 'Beneficiary'), $file_name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportIndividualLog($id)
    {        
        
        $beneficiary = Beneficiary::withTrashed()->find($id);
        
        $activity = Activity::where('subject_id', $id)
                    ->where(function($q){
                        $q->where('subject_type','App\Beneficiary')
                        ->whereNot('properties', '{"attributes":[],"old":[]}')
                        ->whereNot('properties', '{"old": [], "attributes": []}')
                        ->whereNot('properties', '[]');
                    })
                    ->orWhere(function($q) use ($beneficiary){
                        $q->where('subject_type', 'App\Comment')
                          ->whereIn('subject_id', $beneficiary->comments()->approved()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($beneficiary){
                        $q->where('subject_type', 'App\Document')
                          ->whereIn('subject_id', $beneficiary->documents()->withTrashed()->get()->pluck('id'));
                    })->take(1000)->orderBy('updated_at', 'desc')->get();

        $file_name = sprintf('Beneficiary %u Log - %s', $id, date('Y.m.d'));

        return Excel::download(new LogExporter($activity, true, 'Beneficiary'), $file_name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
