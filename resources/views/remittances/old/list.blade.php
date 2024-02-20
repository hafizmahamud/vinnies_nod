@extends('layouts.app')

@section('title')
    Old System Remittances
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12 intro">
                <h1 class="page-title text-center">Remittances</h1>
                @include('flash::message')
                @include('partials.js-alert')
                <p>This allows you to manage all the Remittances (including the ones from the old system).</p>
                <p class="download"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <a href="{{ Helper::getDocUrl('guide') }}">Download NOD database guide in PDF format.</a></p>

                <div class="row">
                    <div class="col-sm-6">
                        <a href="{{ route('new-remittances.list') }}" class="btn btn-primary btn-block">New System Remittances</a>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('old-remittances.list') }}" class="btn btn-success btn-block">Old System Remittances</a>
                    </div>
                </div>

                <div class="section">
                    <h1 class="page-title">Old System Remittances</h1>
                    <h2 class="section-title">Filters</h2>
                    <form class="form-inline form-table-filter js-table-filter">
                        <div class="form-group">
                            <label for="state">State/Territory Council</label>
                            <select name="state" id="state" class="form-control">
                                <option value="">All States/Territory Councils</option>
                                @foreach (Helper::getAuStates() as $key => $states)
                                    <option value="{{ $key }}">{{ $states }}</option>
                                @endforeach
                            </select>
                        </div>
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
                                @foreach (range(2006, date('Y') + 1) as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-warning" data-text-progress="Applying..." data-text-default="Apply Filters">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-6">
                <form class="form-inline js-table-search-form">
                    <div class="form-group has-btn">
                        <label class="sr-only" for="old-remittances-search">Search</label>
                        <input type="text" class="form-control js-table-search-input" id="old-remittances-search" placeholder="Search">
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
                <table class="table table-striped js-table" data-url="{{ route('old-remittances.datatables') }}" data-page-length="{{ config('vinnies.pagination.old_remittances') }}" data-order-col="0" data-order-type="desc">
                    <thead>
                        <tr>
                            <th class="text-center" data-name="id">Remittance-In ID</th>
                            <th class="text-center" data-name="state">State/Territory Council</th>
                            <th class="text-center" data-name="received_at">Date received</th>
                            <th class="text-center" data-name="quarter">Quarter</th>
                            <th class="text-center" data-name="year">Year</th>
                            <th class="text-center" data-name="total" data-orderable="false">Donation Total</th>
                            <th class="text-center" data-name="allocated">Allocated</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <div class="row">
                    <div class="col-sm-12 text-right">
                        @include('pagination.table')
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script src="{{ Helper::asset('assets/js/old-remittance.js') }}"></script>
@stop
