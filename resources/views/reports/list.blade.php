@extends('layouts.app')

@section('title')
    Reports
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12 intro">
                <h1 class="page-title text-center">Reports</h1>
                @include('flash::message')
                @include('partials.js-alert')
                <div class="row">
                  <div class="col-sm-8">
                    <p>In here you can generate remittance reports for each Overseas Council/Country.</p>
                    <p>Select the Quarter and Year and download the corresponding Excel file that needs to be sent to each Overseas National Council.</p>
                    <p class="download"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <a href="{{ Helper::getDocUrl('guide') }}">Download NOD database guide in PDF format.</a></p>
                  </div>
                  <div class="col-sm-4 text-right">
                    <a href="{{ route('reports.yearlyList') }}" ><button type="submit" class="btn btn-info">Download Yearly Summary</button></a>
                  </div>
                </div>
                <div class="section">
                    <form class="form-inline form-table-filter js-table-filter">
                        {{-- {{ csrf_field() }} --}}
                        <div class="form-group">
                            <label for="quarter">Quarter</label>
                            <select name="quarter" id="quarter" class="form-control">
                                <option value="">All</option>
                                @foreach (range(1, 4) as $quarter)
                                    <option value="{{ $quarter }}">Q{{ $quarter }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="year">Year</label>
                            <select name="year" id="year" class="form-control">
                                <option value="">All</option>
                                @foreach (range(2016, date('Y') + 1) as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-warning" data-text-progress="Applying..." data-text-default="Apply Filters">Apply Quarter/Year Selection</button>
                    </form>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-6">
                <form class="form-inline js-table-search-form">
                    <div class="form-group has-btn">
                        <label class="sr-only" for="reports-search">Search</label>
                        <input type="text" class="form-control js-table-search-input" id="report-search" placeholder="Search">
                        <button class="btn"><i class="fa fa-search" aria-hidden="true"></i></button>
                    </div>
                </form>
            </div>
            <div class="col-sm-6 text-right">
                @include('pagination.basic')
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-striped js-table" data-url="{{ route('reports.datatables') }}" data-page-length="{{ config('vinnies.pagination.reports') }}" data-order-col="0" data-order-type="desc">
                    <thead>
                        <tr>
                            {{-- <th class="text-center" data-name="id">Id</th> --}}
                            <th class="text-center" data-name="country">Country</th>
                            <th class="text-center" data-name="beneficiary">Beneficiary Name</th>
                            <th class="text-center" data-name="quarter">Quarter</th>
                            <th class="text-center" data-name="year">Year</th>
                            <th class="text-center" data-name="total"  data-orderable="false">Payments Total</th>
                            <th class="text-center" data-name="projects"  data-orderable="false">Projects</th>
                            <th class="text-center" data-name="twinning"  data-orderable="false">Twininngs</th>
                            <th class="text-center" data-name="grants"  data-orderable="false">Grants</th>
                            <th class="text-center" data-name="councils"  data-orderable="false">Council to Council</th>
                            <th class="text-center" data-name="download"  data-orderable="false">Download</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="row">
                    <div class="col-sm-12 text-center">
                        @include('pagination.table')
                    </div>
                </div>
            </div>
        </div>
   

        {{-- @if (isset($is_all_approved) && !$is_all_approved)
            <hr>
            <div class="mt-2">
                <p class="text-warning">Sorry, not all of the remittances from states and territories have been submitted and approved yet for Q{{ old('quarter', $selected_quarter) }}/{{ old('year', $selected_year) }}.</p>
                <p class="text-warning">Please check the Remittances list and approve them as required.</p>
            </div>
        @endif --}}

        {{-- @if (isset($has_unapproved) && $has_unapproved)
            <hr>
            <div class="mt-2">
                @if (isset($total_state) && $total_state != 9)
                    <p class="text-warning">For Q{{ old('quarter', $selected_quarter) }}/{{ old('year', $selected_year) }} not all states have added a remittance.</p>
                @endif
                <p class="text-warning">For Q{{ old('quarter', $selected_quarter) }}/{{ old('year', $selected_year) }} there are remittances that are not yet approved.</p>
                <p class="text-warning">Please check the Remittances list and approve them as required.</p>
            </div>
        @else
            @if (isset($total_state) && $total_state != 9)
                <hr>
                <div class="mt-2">
                  <p class="text-warning">For Q{{ old('quarter', $selected_quarter) }}/{{ old('year', $selected_year) }} not all states have added a remittance.</p>
                </div>
            @endif
        @endif --}}
  
        {{-- @if (isset($is_all_approved) && $is_all_approved) --}}
          
             
            </div>
        </div>
    </div>
@stop
@section('scripts')
<script src="{{ Helper::asset('assets/js/reports.js') }}"></script>
@stop