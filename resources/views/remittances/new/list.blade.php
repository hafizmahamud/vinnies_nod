@extends('layouts.app')

@section('title')
    New System Remittances
@stop

@php
    use App\User;
@endphp

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

                <div class="row"> 
                    <div class="col-sm-6">
                        <div class="section">
                            <h1 class="page-title">New System Remittances</h1>
                        </div>
                    </div>                   
                    <div class="col-sm-6 section">
                        @if ($currentUser->hasPermissionTo('create.new-remittances'))
                        <div class="row">
                                <div class="col-auto">
                                    <div class="text-right">
                                        <a href="{{ route('new-remittances.create') }}" class="btn btn-warning">Add remittance</a>
    
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="text-right">
                                    <a
                                        data-toggle="modal"
                                        id="getActivity"
                                        class="btn btn-primary btn-sm mb-2 mt-1"
                                        data-target="#logAllMessageBoard"
                                        data-url="http://127.0.0.1:8000/remittance/"
                                        href="#."
                                    >
                                        Log
                                    </a>
                                    </div>
                                </div>
                                </div>
                        @endif
                        </div>
                </div>

                <div class="section">
                    <h2 class="section-title">Filters</h2>
                    <form class="form-inline form-table-filter js-table-filter">
                        <div class="form-group">
                            <label for="state">State/Territory Council</label>
                            <select name="state" id="state" class="form-control">
                                <option value="">All States/Territory Councils</option>
                                @foreach (Helper::getStates() as $key => $state)
                                    @if ($key != 'national')
                                        <option value="{{ $key }}">{{ $state }}</option>
                                    @else
                                        <option value="{{ $key }}">{{ $state }}</option>
                                    @endif
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
                                @foreach (range(2016, date('Y') + 1) as $year)
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
                <table class="table table-striped js-table" data-url="{{ route('new-remittances.datatables') }}" data-page-length="{{ config('vinnies.pagination.new_remittances') }}" data-order-col="0" data-order-type="desc">
                    <thead>
                        <tr>
                            <th class="text-center" data-name="id">Remittance-In ID</th>
                            <th class="text-center" data-name="state">State/Territory Council</th>
                            <th class="text-center" data-name="date">Date received</th>
                            <th class="text-center" data-name="created_at">Date created</th>
                            <th class="text-center" data-name="approved_at">Date approved</th>
                            <th class="text-center" data-name="quarter">Quarter</th>
                            <th class="text-center" data-name="year">Year</th>
                            <th class="text-center" data-name="total" data-orderable="false">Payments Total</th>
                            <th class="text-center" data-name="projects" data-orderable="false">Projects</th>
                            <th class="text-center" data-name="twinning" data-orderable="false">Twinnings</th>
                            <th class="text-center" data-name="grants" data-orderable="false">Grants</th>
                            <th class="text-center" data-name="councils" data-orderable="false">Council to Council</th>
                            <th class="text-center" data-name="is_approved">Approved/ Processed?</th>
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
    </div>
    
    <div class="modal fade bd-example-modal-lg" id="logAllMessageBoard" tabindex="-1"  role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Activity Log</h4>
                </div>
                <div class="modal-body" style="overflow-x: scroll">
                    <div class="pull-right mb-1">
                        <a href="{{ route('new-remittances.exportLog') }}" class="btn btn-default">Export New Remittances Log</a>
                    </div>
                    <table class="table table-striped js-modal-table" data-page-length="{{ config('vinnies.pagination.activity') }}" data-order-col="1" data-order-type="ASC">
                        <thead>
                            <tr>
                                <th class="text-center" data-name="id">Log ID</th>
                                <th class="text-center" data-name="event">Event Type</th>
                                <th class="text-center" data-name="subjectID">Subject ID</th>
                                <th class="text-center" data-name="updated_at">Updated At</th>
                                <th class="text-center" data-name="updated_by">Updated By</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($activity as $activities)
                            <tr>
                                <td class="text-center">{{$activities-> id}}</td>
                                <td class="text-center">{{ $activities->event }}</td>
                                <td class="text-center">{{ $activities->subject_id }}</td>   
                                <td class="text-center">{{ date('d-m-Y H:i:s', strtotime($activities->updated_at)) }}</td>
                                @php
                                    $user = User::where('id', $activities->causer_id)->withTrashed()->first();
                                @endphp
                                @if ($user)
                                    <td class="text-center">{{ $user->first_name . ' ' . $user->last_name }}</td>
                                @else
                                    <td class="text-center">{{ $activities->causer_id }}</td>
                                @endif
                            </tr> 
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script src="{{ Helper::asset('assets/js/new-remittance.js') }}"></script>
@stop
