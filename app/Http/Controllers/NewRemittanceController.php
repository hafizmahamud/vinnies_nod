<?php

namespace App\Http\Controllers;

use Auth;
use Hashids;
use Storage;
use Javascript;
use App\Country;
use App\Document;
use Carbon\Carbon;
use App\GrantDonation;
use App\NewRemittance;
use App\Vinnies\Money;
use App\Vinnies\Helper;
use App\CouncilDonation;
use App\ProjectDonation;
use App\TwinningDonation;
use Illuminate\Http\Request;
use App\Vinnies\RemittanceType;
use Illuminate\Validation\Rule;
use App\Rules\ValidUserRemittanceState;
use App\Vinnies\Exporter\NewRemittanceExporter;
use App\Vinnies\Importer\GrantDonationsImporter;
use App\Vinnies\Importer\CouncilDonationsImporter;
use App\Vinnies\Importer\ProjectDonationsImporter;
use App\Vinnies\Importer\TwinningDonationsImporter;
use App\Activity;
use App\Vinnies\Exporter\LogExporter;
use Maatwebsite\Excel\Facades\Excel;

class NewRemittanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        $this->authorize('read.new-remittances');

        $activity = Activity::where(function($q){
            $q->where('subject_type','App\NewRemittance')
            ->whereNot('properties', '{"attributes":[],"old":[]}')
            ->whereNot('properties', '{"old": [], "attributes": []}')
            ->whereNot('properties', '[]');
        })
        ->take(20)->orderBy('updated_at', 'desc')->get();

        return view('remittances.new.list')->with(compact('activity'));
    }

    public function datatables(Request $request)
    {
        $user = Auth::user();

        $this->authorize('read.new-remittances');

        $remittances = NewRemittance::whereNotNull('id')->own();

        if ($user->hasRole('State User')) {
            $remittances->whereIn('state', $user->states);
        }

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
                    ->orWhere('year', 'LIKE', '%' . $keyword . '%');
            });
        }

        $remittances = $remittances->paginate(config('vinnies.pagination.old_remittances'));
        $data        = $this->getDatatableBaseData($remittances, $request);

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

            $total = $remittance->getDonationTotal();

            $data['data'][] = [
                'id'            => $remittance->id,
                // 'state'         => strtoupper($remittance->state),
                'state'         => $state,
                'date'          => $remittance->date->format(config('vinnies.date_format')),
                'created_at'  => $remittance->created_at->format(config('vinnies.date_format')),
                'approved_at' => $remittance->approved_at ? $remittance->approved_at->format(config('vinnies.date_format')) : '-',
                'quarter'       => 'Q' . $remittance->quarter,
                'year'          => $remittance->year,
                'total'         => $total['total'],
                'projects'      => $total['projects'],
                'twinning'      => $total['twinning'],
                'grants'        => $total['grants'],
                'councils'      => $total['councils'],
                'is_approved'   => $remittance->is_approved ? 'Yes' : 'No',
                'DT_RowId'      => 'row_' . $remittance->id,
            ];
        }


        return $data;
    }

    public function showCreateForm(Request $request)
    {
        $this->authorize('create.new-remittances');

        $remittance = new NewRemittance;

        return view('remittances.new.create')->with(compact('remittance'));
    }

    public function create(Request $request)
    {
        $this->authorize('create.new-remittances');

        $data = $request->validate($this->rules());
        $msg  = 'New remittance-in created successfully';

        // Validate if it's a duplicate RM-6403
        // $existing_remittance =  NewRemittance::where('state', $request->get('state'))
        //     ->where('quarter', $request->get('quarter'))
        //     ->where('year', $request->get('year'))
        //     ->first();
        //
        // if ($existing_remittance) {
        //     return response()->json([
        //         'msg' => 'Error, only 1 Remittance can be added for each state/territory in a given Quarter/Year. There is already a <a href="' . route('new-remittances.edit', $existing_remittance) . '">remittance</a> created for your state for this Quarter/Year. Please enter that <a href="' . route('new-remittances.edit', $existing_remittance) . '">remittance</a> and add all the payment files in there in their specific Payment type section.',
        //         'type' => 'dialog',
        //     ], 422);
        // }

        $data['date']       = Carbon::createFromFormat(config('vinnies.date_format'), $data['date']);
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        $data['updated_at'] = Carbon::now();

        $remittance = NewRemittance::create($data);

        if ($request->filled('comments')) {
            $remittance->comment($request->input('comments'));
            $remittance->update(['comments' => null]);
        }

        if ($request->ajax()) {
            return response()->json([
                'redirect' => route('new-remittances.edit', $remittance),
                'msg'      => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function showEditForm(NewRemittance $remittance)
    {
        $this->authorize('update.new-remittances');
        $this->checkEditAccess($remittance);
        $activity = Activity::where('subject_id', $remittance->id)
                    ->where(function($q){
                        $q->where('subject_type','App\NewRemittance')
                        ->whereNot('properties', '{"attributes":[],"old":[]}')
                        ->whereNot('properties', '{"old": [], "attributes": []}')
                        ->whereNot('properties', '[]');
                    })
                    ->orWhere(function($q) use ($remittance){
                        $q->where('subject_type', 'App\Comment')
                          ->whereIn('subject_id', $remittance->comments()->approved()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($remittance){
                        $q->where('subject_type', 'App\ProjectDonation')
                          ->whereIn('subject_id', $remittance->projectDonations()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($remittance){
                        $q->where('subject_type', 'App\GrantDonation')
                          ->whereIn('subject_id', $remittance->grantDonations()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($remittance){
                        $q->where('subject_type', 'App\CouncilDonation')
                          ->whereIn('subject_id', $remittance->councilDonations()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($remittance){
                        $q->where('subject_type', 'App\TwinningDonation')
                          ->whereIn('subject_id', $remittance->twinningDonations()->withTrashed()->get()->pluck('id'));
                    })
                    ->take(20)->orderBy('updated_at', 'desc')->get();

        Javascript::put([
            'remittance_donation_url' => route('new-remittances.donations', $remittance),
            'types' => [
                'projects' => RemittanceType::PROJECT,
                'twinning' => RemittanceType::TWINNING,
                'grants'   => RemittanceType::GRANT,
                'council'  => RemittanceType::COUNCIL,
            ],
            'meta_url' => route('new-remittances.meta', $remittance),
        ]);

        return view('remittances.new.edit')->with(compact('remittance', 'activity'));
    }

    public function edit(Request $request, NewRemittance $remittance)
    {
        $this->authorize('update.new-remittances');
        $this->checkEditAccess($remittance);

        if ($remittance->is_approved) {
            $msg = 'This remittance-in is already approved, it can no longer be modified.';

            if ($request->ajax()) {
                return response()->json([
                    'msg'  => $msg,
                    'type' => 'dialog',
                ], 422);
            }

            flash($msg)->error()->important();

            return redirect()->back();
        }

        $data               = $request->validate($this->rules());
        $data['date']       = Carbon::createFromFormat(config('vinnies.date_format'), $data['date']);
        $data['updated_by'] = Auth::id();
        $data['updated_at'] = Carbon::now();
        $msg                = 'New remittance-in updated successfully';

        $remittance->update($data);

        if ($request->filled('comments')) {
            $remittance->comment($request->input('comments'));
            $remittance->update(['comments' => null]);
        }

        if ($request->ajax()) {
            return response()->json([
                'msg' => $msg
            ]);
        }

        flash($msg)->success()->important();

        return redirect()->back();
    }

    public function addComment(Request $request, NewRemittance $remittance)
    {
        $this->authorize('update.new-remittances');
        $this->checkEditAccess($remittance);

        $data = $request->validate(['comments' => 'required']);
        $msg  = 'Your comments added successfully';

        if ($remittance->is_approved) {
            $msg = 'This remittance-in is already approved, it can no longer be modified.';

            if ($request->ajax()) {
                return response()->json([
                    'msg'  => $msg,
                    'type' => 'dialog',
                ], 422);
            }

            flash($msg)->error()->important();

            return redirect()->back();
        }

        if ($request->filled('comments')) {
            $remittance->comment($request->input('comments'));
            $remittance->update(['comments' => null]);
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
            'state' => [
                'required',
                Rule::in(array_keys(Helper::getStates())),
                new ValidUserRemittanceState,
            ],
            'quarter'  => 'required|integer',
            'year'     => 'required|integer',
            'date'     => 'required|date_format:' . config('vinnies.date_format'),
            'comments' => ''
        ];
    }

    public function donations(Request $request, NewRemittance $remittance)
    {
        $this->authorize('update.new-remittances');

        // Do import if needed
        if ($request->get('document_id')) {
            $document = Document::find($request->get('document_id'));

            if ($document) {
                $response = $this->importDocument($document, $remittance);

                if (!$response->isOk()) {
                    return $response;
                }

                $type = $document->type;
            }
        }

        if ($request->has('type')) {
            $type = $request->get('type');
        }

        $remittance = $remittance->fresh();
        $details    = $this->getDonationData($type);
        $parser     = 'parse' . ucwords($details['prop']);
        $document   = Document::find($remittance->{$details['key']});

        $response = [
            'type'          => $type,
            'has_donations' => $remittance->{$details['prop']}->isNotEmpty(),
            'total'         => $remittance->getDonationTotal(),
            'is_approved'   => $remittance->is_approved,
        ];

        if ($response['has_donations']) {
            $response['donations'] = $this->{$parser}($remittance);
        }

        if ($document) {
            $response['document_id']   = $document->id;
            $response['document_name'] = basename($document->path);
            $response['document_date'] = $document->created_at->format('d/m/Y');
            $response['document_url']  = route('documents.download', Hashids::encode([$document->id]));
        }

        return response()->json($response);
    }

    public function delete(Request $request, NewRemittance $remittance)
    {
        $this->authorize('update.new-remittances');

        $document = Document::find($request->get('document_id'));
        $details  = $this->getDonationData($document->type);

        $this->deleteDocument($document, $details['model']);

        $remittance->{$details['key']} = null;
        $remittance->updated_by = Auth::id();
        $remittance->updated_at = Carbon::now();
        $remittance->save();

        return response()->json([
            'msg'  => 'Selected file successfully deleted',
            'type' => $request->get('type'),
        ]);
    }

    private function importDocument(Document $document, NewRemittance $remittance)
    {
        // return early if same id is requested
        $details = $this->getDonationData($document->type);

        if ($document->id == $remittance->{$details['key']}) {
            return;
        }

        $importer = new $details['importer'](storage_path('app/' . $document->path));

        $importer->setRemittance($remittance)
            ->setDocument($document)
            ->setRequiredHeaders($details['headers']['required'])
            ->setStateColumnName($details['headers']['state'])
            ->setAmountColumnName($details['headers']['amount'])
            ->setLogger('import-' . str_slug($details['type']));

        if ($importer->isInvalid()) {
            $msg = $importer->invalidMsg;

            unset($importer);

            return $this->fail($msg, $document);
        }

        if (!$importer->isValidHeaders()) {
            $msg = $importer->invalidHeadersMsg;

            unset($importer);

            return $this->fail($msg, $document);
        }

        if (!$importer->isValidQuarter()) {
            $msg = $importer->invalidQuarterMsg;

            unset($importer);

            return $this->fail($msg, $document);
        }

        if (!$importer->isValidYear()) {
            $msg = $importer->invalidYearMsg;

            unset($importer);

            return $this->fail($msg, $document);
        }

        if (!$importer->isValidState()) {
            $msg = $importer->invalidStateMsg;

            unset($importer);

            return $this->fail($msg, $document);
        }

        if (!$importer->isValidAmount()) {
            $msg = $importer->invalidAmountMsg;

            unset($importer);

            return $this->fail($msg, $document);
        }

        if (in_array($details['type'], [RemittanceType::TWINNING, RemittanceType::GRANT, RemittanceType::COUNCIL]) && !$importer->isValidType()) {
            $msg = $importer->invalidTwinningMsg;

            unset($importer);

            return $this->fail($msg, $document);
        }

        // All passes, now we import the actual content
        $result = $importer->import();

        if (!empty($result['failed'])) {
            $messages = [];

            foreach ($importer->invalidRows as $type => $rows) {
                foreach ($rows as $index => $row) {
                    switch ($type) {
                        case 'projects':
                            $messages[] = sprintf(
                                'The Project ID %s is not correct at row %s',
                                $row['Project ID'],
                                $index + 1
                            );
                            break;

                        case 'donors':
                            $messages[] = sprintf(
                                'The DONOR SRN %s is not correct at row %s',
                                $row['DONOR SRN'],
                                $index + 1
                            );
                            break;

                        case 'twinnings':
                            $messages[] = sprintf(
                                'The Twinning ID = %s, Australian Conf SRN = %s and Overseas Conference SRN = %s at row %s is inconsistent',
                                $row['TWINNING ID'],
                                $row['AUS CONF SRN'],
                                $row['OS CONF SRN'],
                                $index + 1
                            );
                            break;

                        case 'project_donor_pair':
                            $messages[] = sprintf(
                                'The Project ID %s and Donor ID %s at row %s is inconsistent',
                                $row['Project ID'],
                                $row['DONOR SRN'],
                                $index + 1
                            );
                            break;
                    }
                }
            }

            // Revert all new addition
            unset($importer);
            $this->deleteDocument($document, $details['model']);

            return response()->json([
                'type'    => 'dialog',
                'confirm' => false,
                'msg'     => array_shift($messages),
            ], 422);
        }

        // All good, we can do post import operation
        // First, we delete old document and data
        if (!empty($remittance->{$details['key']})) {
            $old_document = Document::find($remittance->projects_document_id);

            if ($old_document) {
                $this->deleteDocument($old_document, $details['model']);
            }
        }

        // Save new document id into database
        $remittance->{$details['key']} = $document->id;
        $remittance->updated_by = Auth::id();
        $remittance->updated_at = Carbon::now();
        $remittance->save();

        return response()->json([
            'msg' => 'CSV file successfully imported',
        ]);
    }

    private function deleteDocument(Document $document, $model)
    {
        (new $model)->where('document_id', $document->id)->delete();

        Storage::delete($document->path);
        $document->delete();
    }

    private function getDonationData($type)
    {
        switch ($type) {
            case RemittanceType::PROJECT:
                $key      = 'projects_document_id';
                $model    = ProjectDonation::class;
                $importer = ProjectDonationsImporter::class;
                $prop     = 'projectDonations';
                $headers = [
                    'required' => [
                        'QUARTER',
                        'YEAR',
                        'Project ID',
                        'DONOR SRN',
                        'DONOR STATE/TERRITORY COUNCIL',
                        'DONOR CONTRIBUTION AMOUNT AUD',
                    ],
                    'state'  => 'DONOR STATE/TERRITORY COUNCIL',
                    'amount' => 'DONOR CONTRIBUTION AMOUNT AUD',
                ];
                break;

            case RemittanceType::TWINNING:
                $key      = 'twinnings_document_id';
                $model    = TwinningDonation::class;
                $importer = TwinningDonationsImporter::class;
                $prop     = 'twinningDonations';
                $headers  = [
                    'required' => [
                        'QUARTER',
                        'YEAR',
                        'TWINNING ID',
                        'TWINNING TYPE',
                        'AUS CONF STATE/TERRITORY COUNCIL',
                        'PAYMENT AMOUNT AUD',
                        'AUS CONF SRN',
                        'OS CONF SRN',
                    ],
                    'state'  => 'AUS CONF STATE/TERRITORY COUNCIL',
                    'amount' => 'PAYMENT AMOUNT AUD',
                ];
                break;

            case RemittanceType::GRANT:
                $key      = 'grants_document_id';
                $model    = GrantDonation::class;
                $importer = GrantDonationsImporter::class;
                $prop     = 'grantDonations';
                $headers  = [
                    'required' => [
                        'QUARTER',
                        'YEAR',
                        'TWINNING ID',
                        'TWINNING TYPE',
                        'AUS CONF STATE/TERRITORY COUNCIL',
                        'PAYMENT AMOUNT AUD',
                        'AUS CONF SRN',
                        'OS CONF SRN',
                    ],
                    'state'  => 'AUS CONF STATE/TERRITORY COUNCIL',
                    'amount' => 'PAYMENT AMOUNT AUD',
                ];
                break;

            case RemittanceType::COUNCIL:
                $key      = 'councils_document_id';
                $model    = CouncilDonation::class;
                $importer = CouncilDonationsImporter::class;
                $prop     = 'councilDonations';
                $headers  = [
                    'required' => [
                        'QUARTER',
                        'YEAR',
                        'TWINNING ID',
                        'TWINNING TYPE',
                        'AUS CONF STATE/TERRITORY COUNCIL',
                        'PAYMENT AMOUNT AUD',
                        'AUS CONF SRN',
                        'OS CONF SRN',
                    ],
                    'state'  => 'AUS CONF STATE/TERRITORY COUNCIL',
                    'amount' => 'PAYMENT AMOUNT AUD',
                ];
                break;
        }

        return compact('type', 'key', 'model', 'importer', 'prop', 'headers');
    }

    private function parseProjectDonations(NewRemittance $remittance)
    {
        $data = $remittance->projectDonations->map(function ($donation) {
            return [
                'project_edit_url' => route('projects.edit', $donation->project),
                'project_id'       => $donation->project->id,
                'project_name'     => $donation->project->name,
                'donor_edit_url'   => route('local-conferences.edit', $donation->donor),
                'donor_id'         => $donation->donor->id,
                'donor_name'       => $donation->donor->name,
                'country'          => optional($donation->project->beneficiary)->country->name,
                'amount'           => (new Money($donation->amount))->value(),
            ];
        });

        return $data;
    }

    private function parseTwinningDonations(NewRemittance $remittance)
    {
        $data = $remittance->twinningDonations->map(function ($donation) {
            return [
                'twinning_edit_url'            => route('twinnings.edit', $donation->twinning),
                'twinning_id'                  => $donation->twinning->id,
                'twinning_status'              => $donation->twinning->is_active ? 'Active' : 'Surrendered',
                'local_conference_edit_url'    => route('local-conferences.edit', $donation->twinning->localConference),
                'local_conference_id'          => $donation->twinning->localConference->id,
                'local_conference_name'        => $donation->twinning->localConference->name,
                'overseas_conference_edit_url' => route('overseas-conferences.edit', $donation->twinning->overseasConference),
                'overseas_conference_id'       => $donation->twinning->overseasConference->id,
                'overseas_conference_name'     => $donation->twinning->overseasConference->name,
                'country'                      => optional($donation->twinning->overseasConference->country)->name,
                'is_active'                    => $donation->twinning->overseasConference->is_active ? 'Remittances' : 'No Remittances',
                'amount'                       => (new Money($donation->amount))->value(),
            ];
        });

        return $data;
    }

    private function parseGrantDonations(NewRemittance $remittance)
    {
        $data = $remittance->grantDonations->map(function ($donation) {
            return [
                'local_conference_edit_url'    => route('local-conferences.edit', $donation->twinning->localConference),
                'local_conference_id'          => $donation->twinning->localConference->id,
                'local_conference_name'        => $donation->twinning->localConference->name,
                'local_conference_parish'      => $donation->twinning->localConference->parish,
                'overseas_conference_edit_url' => route('overseas-conferences.edit', $donation->twinning->overseasConference),
                'overseas_conference_id'       => $donation->twinning->overseasConference->id,
                'overseas_conference_name'     => $donation->twinning->overseasConference->name,
                'country'                      => optional($donation->twinning->overseasConference->country)->name,
                'amount'                       => (new Money($donation->amount))->value(),
            ];
        });

        return $data;
    }

    private function parseCouncilDonations(NewRemittance $remittance)
    {
        $data = $remittance->councilDonations->map(function ($donation) {
            return [
                'local_conference_edit_url'    => route('local-conferences.edit', $donation->twinning->localConference),
                'local_conference_id'          => $donation->twinning->localConference->id,
                'local_conference_name'        => $donation->twinning->localConference->name,
                'overseas_conference_edit_url' => route('overseas-conferences.edit', $donation->twinning->overseasConference),
                'overseas_conference_id'       => $donation->twinning->overseasConference->id,
                'overseas_conference_name'     => $donation->twinning->overseasConference->name,
                'country'                      => optional($donation->twinning->overseasConference->country)->name,
                'amount'                       => (new Money($donation->amount))->value(),
            ];
        });

        return $data;
    }

    public function approve(NewRemittance $remittance)
    {
        $this->authorize('approve.new-remittances');

        $remittance->approve();

        $remittance->updated_by = Auth::id();
        $remittance->updated_at = Carbon::now();
        $remittance->save();

        flash('Remittance-In approved successfully.')->success()->important();

        return redirect()->back();
    }

    public function unapprove(NewRemittance $remittance)
    {
        $this->authorize('unapprove.new-remittances');

        $remittance->unapprove();

        $remittance->updated_by = Auth::id();
        $remittance->updated_at = Carbon::now();
        $remittance->save();

        flash('Remittance-In Edit Mode reinstated.')->success()->important();

        return redirect()->back();
    }

    public function fail($msg, $document)
    {
        Storage::delete($document->path);
        $document->delete();

        return response()->json([
            'type'    => 'dialog',
            'confirm' => false,
            'msg'     => $msg,
        ], 422);
    }

    public function meta(NewRemittance $remittance)
    {
        return response()->json($this->getLastUpdatedData($remittance));
    }

    public function download(NewRemittance $remittance)
    {
        // $exporter = new NewRemittanceExporter($remittance);
        // $exporter->generate()->export('xlsx');
        return (new NewRemittanceExporter($remittance))->download();
    }

    public function comments($id)
    {
        $remittance = NewRemittance::find($id);

        return $remittance->comments()
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

    public function checkEditAccess(NewRemittance $remittance)
    {
        $user = Auth::user();
        $can_access = true;

        if ($user->hasRole('State User Admin') || $user->hasRole('Diocesan/Central Council User')) {
            if (!empty($user->states)) {
                $can_access = in_array($remittance->state, $user->states);
            }

            if (!$can_access) {
                abort(403);
            }
        }
    }
    
    public function exportLog(Request $request)
    {        
        $activity = Activity::where(function($q){
            $q->where('subject_type','App\NewRemittance')
            ->whereNot('properties', '{"attributes":[],"old":[]}')
            ->whereNot('properties', '{"old": [], "attributes": []}')
            ->whereNot('properties', '[]');
        })->take(1000)->orderBy('updated_at', 'desc')->get();

        $file_name = sprintf('New Remittances Log - %s', date('Y.m.d'));

        return Excel::download(new LogExporter($activity, false, 'NewRemittance'), $file_name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
    
    public function exportIndividualLog($id)
    {        
        
        $remittance = NewRemittance::find($id);
        
        $activity = Activity::where('subject_id', $remittance->id)
                    ->where(function($q){
                        $q->where('subject_type','App\NewRemittance')
                        ->whereNot('properties', '{"attributes":[],"old":[]}')
                        ->whereNot('properties', '{"old": [], "attributes": []}')
                        ->whereNot('properties', '[]');
                    })
                    ->orWhere(function($q) use ($remittance){
                        $q->where('subject_type', 'App\Comment')
                          ->whereIn('subject_id', $remittance->comments()->approved()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($remittance){
                        $q->where('subject_type', 'App\ProjectDonation')
                          ->whereIn('subject_id', $remittance->projectDonations()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($remittance){
                        $q->where('subject_type', 'App\GrantDonation')
                          ->whereIn('subject_id', $remittance->grantDonations()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($remittance){
                        $q->where('subject_type', 'App\CouncilDonation')
                          ->whereIn('subject_id', $remittance->councilDonations()->withTrashed()->get()->pluck('id'));
                    })
                    ->orWhere(function($q) use ($remittance){
                        $q->where('subject_type', 'App\TwinningDonation')
                          ->whereIn('subject_id', $remittance->twinningDonations()->withTrashed()->get()->pluck('id'));
                    })->take(1000)->orderBy('updated_at', 'desc')->get();

        $file_name = sprintf('Remittance %u Log - %s', $id, date('Y.m.d'));

        return Excel::download(new LogExporter($activity, true, 'NewRemittance'), $file_name . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
