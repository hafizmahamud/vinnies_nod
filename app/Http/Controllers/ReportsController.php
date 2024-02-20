<?php

namespace App\Http\Controllers;

use Auth;
use App\Country;
use App\ProjectDonation;
use App\NewRemittance;
use Illuminate\Http\Request;
use App\Vinnies\RemittanceFactory;
use Maatwebsite\Excel\Facades\Excel;
use App\Vinnies\RemittanceYearlyFactory;
use App\Vinnies\Exporter\ReportExporter;
use App\Vinnies\Exporter\ReportYearlyExporter;
use App\Vinnies\Exporter\ReportDateRangeExporter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection; // Import the Collection class
use Illuminate\Support\Facades\Cache;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function list(Request $request)
    {
        $this->authorize('read.reports');

        $selected_quarter = '';
        $selected_year    = '';
        $remittances      = [];

        return view('reports.list')->with(compact('selected_quarter', 'selected_year', 'remittances'));
    }

    public function create(Request $request)
    {
        $this->authorize('create.reports');

        $validator = Validator::make($request->all(),[
            'quarter' => 'required|integer',
            'year'    => 'required|integer',
        ]);
        if ($validator->fails()) {
            return redirect(url()->previous())
                    ->withErrors($validator);
        }
        $selected_quarter = $request['quarter'];
        $selected_year    = $request['year'];

        $factory = new RemittanceFactory($selected_year, $selected_quarter);
        $factory->populate();

        $is_all_approved = $factory->isAllApproved();

        $has_unapproved  = $factory->hasUnapprovedRemittance();

        $total_state = $factory->countTotalRemitances();

        $remittances     = $factory->get();
        return view('reports.list')->with(compact('selected_quarter', 'selected_year', 'is_all_approved', 'has_unapproved', 'remittances', 'total_state'));
    }

    public function yearlyList(Request $request)
    {
        $this->authorize('read.reports');

        $selected_quarter = '';
        $selected_year    = '';
        $remittances      = [];

        return view('reports.yearly-list')->with(compact('selected_quarter', 'selected_year', 'remittances'));
    }

    public function createYearlyList(Request $request)
    {
        $this->authorize('create.reports');

        $data = $request->validate([
            'quarter' => 'required|integer',
            'year'    => 'required|integer',
        ]);

        $selected_quarter = $data['quarter'];
        $selected_year    = $data['year'];

        $factory = new RemittanceYearlyFactory($selected_year, $selected_quarter);
        $factory->populate();

        $is_all_approved = $factory->isAllApproved();

        $has_unapproved  = $factory->hasUnapprovedRemittance();

        $total_state = $factory->countTotalRemitances();

        $remittances     = $factory->get();

        return view('reports.yearly-list')->with(compact('selected_quarter', 'selected_year', 'is_all_approved', 'has_unapproved', 'remittances', 'total_state'));
    }

    public function download($year, $quarter, Country $country)
    {
        $this->authorize('create.reports');

        return (new ReportExporter($year, $quarter, $country))->download();
    }

    public function downloadYearly($year)
    {
        $this->authorize('create.reports');

        return (new ReportYearlyExporter($year))->download();
    }

    public function downloadDateRange($start, $end)
    {
        $this->authorize('create.reports');

        return (new ReportDateRangeExporter($_GET['date_type'],$start, $end))->download();
    }
    public function datatables(Request $request)
    {
        $user = Auth::user();
        $this->authorize('read.new-remittances');
          $currentPage = $request->get('page');
          $year = $request->get('filters')['year'];
          $quarter = $request->get('filters')['quarter'];
          $search = $request->get('search')['value'];                 
          $sortColumn = $request->get('order')[0]['column'] ?? null;  
          $sortDirection = $request->get('order')[0]['dir'] ?? 'asc'; 
          $cacheDuration = now()->addHours(1);                        
          $cacheKey = "{$currentPage}_{$sortColumn}_{$sortDirection}_{$year}_{$quarter}_{$search}";
          $data =  Cache::remember($cacheKey, $cacheDuration, function () use ($request,$year, $quarter ,$currentPage, $search, $sortColumn, $sortDirection){
          $years =  range(2016 , (date('Y') + 1));
          $quarters =  range(1 ,4);
                foreach ($years as $y) {
                    foreach ($quarters as $q) {
                        $factory = new RemittanceFactory($y, $q);
                        $remittances = $factory->get();
                             foreach($remittances as $remittance){
                                 $data[] = [
                                              "id" => $remittance["id"],
                                              "country" => $remittance["country"],
                                              "beneficiary" => $remittance["beneficiary"]->name,
                                              "quarter" =>  $remittance["quarter"],
                                              "year" => $remittance["year"],
                                              "total" => $remittance["total"],
                                              "projects" =>$remittance["projects"],
                                              "twinning" => $remittance["twinning"],
                                              "grants"   => $remittance["grants"],
                                              "councils" => $remittance["councils"],
                                ];
                                }
                }
                }
                $data = $this->sortReports($request, $data); 
                $data = $this->filterReports($data, $search, $year, $quarter); 
                $data = $this->paginate($currentPage, $data, $request);
                $factory = new RemittanceFactory(2016, 1);
                $factory->setRemittances($data['data']);
                $data['data'] = $factory->populateRemittance();
                return $data;
            });
            return response()->json($data);
    }
    
    public function sortReports($request, $data){
        $data = collect($data);
        if (!empty($request->get('order'))) {
            foreach ($request->get('order') as $order) {
                if (in_array($request->get('columns')[$order['column']]['name'], $this->exclude_orders)) {
                    continue;
                }
                $data = $data->sortBy($request->get('columns')[$order['column']]['name'], SORT_REGULAR, $order['dir'] === 'asc');
            }
        }
        return $data->toArray();
    }

    public function filterReports($data, $search, $year, $quarter){
        $data = array_filter($data, function ($item) use ($search, $year, $quarter) {
            $matchesYear = empty($year) || stripos($item["year"], $year) !== false;
            $matchesQuarter = empty($quarter) || stripos($item["quarter"], $quarter) !== false;
            $matchesSearch = empty($search) || stripos($item["country"], $search) !== false ||
            stripos($item['beneficiary'], $search) !== false ||
                    stripos($item['quarter'], $search) !== false ||
                    stripos($item['year'], $search) !== false;
            return $matchesYear && $matchesQuarter && $matchesSearch;
        });
        return $data;
    }

    public function paginate($currentPage, $data, $request){
        $perPage = config('vinnies.pagination.reports'); 
                $totalItems = count($data);
                $paginator = new LengthAwarePaginator(
                    array_slice($data, ($currentPage - 1) * $perPage, $perPage),
                    $totalItems,
                    $perPage,
                    $currentPage,
                    ['path' => request()->url()] 
                );
                $data = [
                    'page' => $paginator->currentPage(),
                    'recordsTotal' => $paginator->total(),
                    'pagination'      => [
                        'first'   => 1,
                        'last'    => $paginator->lastPage(),
                        'current' => $paginator->currentPage(),
                        'total'   => $paginator->total(),
                        'url'     => [
                            'first' => $paginator->url(1),
                            'prev'  => $paginator->previousPageUrl(),
                            'next'  => $paginator->nextPageUrl(),
                            'last'  => $paginator->url($paginator->lastPage()),
                        ],
                    ],
                    'per_page' => $paginator->perPage(),
                    'recordsFiltered' => $paginator->total(),
                    'data' => $paginator->items(),

                    ];
                    return $data;
    }

}
