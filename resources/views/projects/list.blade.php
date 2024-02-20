@extends('layouts.app')

@section('title')
    Manage Projects
@stop

@php
    use App\User;
@endphp

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12 intro">
                <h1 class="page-title text-center">Manage Projects</h1>
                <div class="col-sm-12">
                    <div class="pull-left">
                        This allows you to manage all the Projects and their Donors and contributions.<br /><br />
                        <p class="text-warning mb-1">States and Territories representatives can browse the projects they support and the projects awaiting support.</a></p>
                        <p class="download intro-p"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <a href="{{ Helper::getDocUrl('guide') }}">Download NOD database guide in PDF format.</a></p>
                    </div>

                    <div class="pull-right">
                    <div class="row">
                        @if ($currentUser->hasPermissionTo('create.projects'))
                        <div class="col-auto">
                            <div class="text-right">
                            <a href="{{ route('projects.create') }}" class="btn btn-warning">Add Project</a>
                            </div>
                        </div>
                        @endif
                        <div class="col-auto">
                            <div class="text-right">
                                <a data-toggle="modal" id="getActivity" class="btn btn-primary btn-sm mb-2 mt-1"  data-target="#logAllMessageBoard" data-url="{{ url('projects')}}" href="#."> Log </a>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>

                @include('flash::message')
                @include('partials.js-alert')
                
                <div class="section">
                    <h2 class="section-title">Filters</h2>

                        <form class="form-inline form-table-filter js-table-filter">
                        <div class="form-group">
                            <label for="country">Country</label><br />
                            <select name="country" id="country" class="form-control" >
                                <option value="">All Countries</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">Project Status</label><br />
                            <select name="status" id="status" class="form-control">
                                <option value="">Any Status</option>
                                @foreach (Helper::getProjectsStatuses() as $key => $status)
                                    <option value="{{ $key }}">{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="project_type">Project Type</label><br />
                            <select name="project_type" id="project_type" class="form-control">
                                <option value="">Any Type</option>
                                <option value="community">Community</option>
                                <option value="special_vincetian_support">Special Vincentian Support</option>
                                <option value="emergency_relief">Emergency Relief</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="state">Donor State/Territory Council</label><br />
                            <select name="state" id="state" class="form-control">
                                <option value="">All State/Territory Councils</option>
                                @foreach (Helper::getStates() as $key => $state)
                                    <option value="{{ $key }}">{{ Helper::getStateNameByKey($key) }}</option>
                                @endforeach
                                <option value="no-donor">No Donor</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="is_awaiting_support">Awaiting Support?</label><br />
                            <select name="is_awaiting_support" id="is_awaiting_support" class="form-control">
                                <option value="">All</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="is_fully_paid">Paid?</label><br />
                            <select name="is_fully_paid" id="is_fully_paid" class="form-control">
                                <option value="">All</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        <div class="mt-1 form-group">
                            <label for="project_completed">Project Completed?</label><br />
                            <select name="project_completed" id="project_completed" class="form-control">
                                <option value="">All</option>
                                @foreach (Helper::getProjectCompleted() as $key => $project_completed)
                                    <option value="{{ $key }}">{{ $project_completed }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-1 form-group">
                            <label for="completion_report_received">Project Completion Report Received?</label><br />
                            <select name="completion_report_received" id="completion_report_received" class="form-control">
                                <option value="">All</option>
                                <option value="0">No</option>
                                <option value="1">Progress Report Received</option>
                                <option value="2">Yes</option>
                                <option value="3">Time Elapsed</option>
                            </select>
                        </div>
                        
                    <div class="mt-1 col">
                            <button type="submit" class="btn btn-warning" data-text-progress="Applying..." data-text-default="Apply Filters">Apply Filters</button>
                        @if ($currentUser->hasPermissionTo('export.projects'))
                        <a href="#" class="btn btn-default js-btn-export-projects">Export Filtered Projects</a>
                        @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-6">
                <form class="form-inline js-table-search-form">
                    <div class="form-group has-btn">
                        <label class="sr-only" for="project-search">Search</label>
                        <input type="text" class="form-control js-table-search-input" id="project-search" placeholder="Search">
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
                <table class="table table-striped js-table" data-url="{{ route('projects.datatables') }}" data-page-length="{{ config('vinnies.pagination.projects') }}" data-order-col="0" data-order-type="desc">
                    <thead>
                        <tr>
                            <th class="text-center" data-name="id">Project ID</th>
                            <th class="text-center" data-name="status">Project Status</th>
                            <th class="text-center" data-name="project_type">Project Type</th>
                            <th class="text-center" data-name="name">Project Name</th>
                            <th class="text-center" data-name="country">Country</th>
                            <th class="text-center" data-name="overseas_project_id">OS Conf. SRN</th>
                            <th class="text-center" data-name="received_at">Date Application Received</th>
                            <th class="text-center" data-name="is_awaiting_support">Awaiting Support?</th>
                            <th class="text-center" data-name="state">Donor State/Territory Council</th>
                            <th class="text-center" data-name="au_value">Value (AUD)</th>
                            <th class="text-center" data-name="is_fully_paid">Paid?</th>
                            <th class="text-center" data-name="balance_owing">Balance Owing</th>
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
                        <a href="{{ route('projects.exportLog') }}" class="btn btn-default">Export Projects Log</a>
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
<script src="{{ Helper::asset('assets/js/project.js') }}"></script>
@stop
