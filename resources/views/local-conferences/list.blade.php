@extends('layouts.app')

@section('title')
    Manage Australian Conferences (AU)
@stop

@php
    use App\User;
@endphp

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12 intro">
                <h1 class="page-title text-center">Manage Australian Conferences (AUS)</h1>
                @include('flash::message')
                @include('partials.js-alert')
            <div class="col-sm-12">
                <div class="pull-left">
                This allows you to manage all the Australian Conferences.<br /><br />
                <p class="download"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <a href="{{ Helper::getDocUrl('guide') }}">Download NOD database guide in PDF format.</a></p>
                </div>
                <div class="pull-right">
                <div class="row">
                    @if ($currentUser->hasPermissionTo('create.local-conf'))
                    <div class="col-auto">
                        <div class="text-right">
                            <a href="{{ route('local-conferences.create') }}" class="btn btn-warning">Add Australian Conference</a>
                        </div>
                    </div>
                    @endif
                    <div class="col-auto">
                        <div class="text-right">
                            <a data-toggle="modal" id="getActivity" class="btn btn-primary btn-sm mb-2 mt-1"  data-target="#logAllMessageBoard" data-url="{{ url('local_conference')}}" href="#."> Log </a>
                        </div>
                    </div>
                </div>
                </div>
            </div>
                <div class="section">
                    <h2 class="section-title">Filters</h2>
                    <form class="form-inline form-table-filter js-table-filter">
                        <div class="form-group">
                            <label for="state_council">State/Territory Council</label><br />
                            <select name="state_council" id="state_council" class="form-control">
                                <option value="">All State/Territory Councils</option>
                                @foreach (Helper::getAllStates() as $key => $state_council)
                                        <option value="{{ $key }}">{{ ucwords($state_council) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="diocesan_council_id">Diocesan/Central Council</label><br />
                            <select name="diocesan_council_id" id="diocesan_council_id" class="form-control">
                                <option value="">All Diocesan/Central Councils</option>
                                @foreach ($diocesan_councils as $state => $diocesan_council_list)
                                    <optgroup label={{ $state }}>
                                        @foreach ($diocesan_council_list as $diocesan_council_id => $diocesan_council)
                                            <option value="{{ $diocesan_council_id }}">{{ $diocesan_council}}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="regional_council">Regional Council</label><br />
                            <input name="regional_council" id="regional_council" class="form-control"></input>
                        </div>
                        <div class="form-group">
                            <label for="state">State</label><br />
                            <select name="state" id="state" class="form-control">
                                <option value="">All States</option>
                                <option value="act">ACT</option>
                                <option value="nsw">NSW</option>
                                <option value="nt">NT</option>
                                <option value="qld">QLD</option>
                                <option value="sa">SA</option>
                                <option value="tas">TAS</option>
                                <option value="vic">VIC</option>
                                <option value="wa">WA</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">Australian Conf. Status</label><br />
                            <select name="status" id="status" class="form-control">
                                <option value="">All</option>
                                <option value="active">Active</option>
                                <option value="abeyant">Abeyant</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="is_flagged">Flagged</label><br />
                            <select name="is_flagged" id="is_flagged" class="form-control">
                                <option value="">All</option>
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        <div class="mt-1 col"> 
                            <button type="submit" class="btn btn-warning" data-text-progress="Applying..." data-text-default="Apply Filters">Apply Filters</button>
                            @if ($currentUser->hasPermissionTo('export.local-conf'))
                            <a href="#" class="btn btn-default js-btn-export-local-conf">Export Filtered AUS Conferences</a>
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
                        <label class="sr-only" for="local-conference-search">Search</label>
                        <input type="text" class="form-control js-table-search-input" id="local-conference-search" placeholder="Search">
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
                <table class="table table-striped js-table" data-url="{{ route('local-conferences.datatables') }}" data-page-length="{{ config('vinnies.pagination.local_conferences') }}" data-order-col="0" data-order-type="desc">
                    <thead>
                        <tr>
                            <th class="text-center" data-name="id">AUS. SRN</th>
                            <th class="text-center" data-name="name">Australian Conference Name</th>
                            <th class="text-center" data-name="parish">Australian Conference Parish</th>
                            <th class="text-center" data-name="status">Australian Conf. Status</th>
                            <th class="text-center" data-name="state_council">State/Territory Council</th>
                            <th class="text-center" data-name="diocesan_council_id">Diocesan/Central Council</th>
                            <th class="text-center" data-name="regional_council">Regional Council</th>
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
                        <a href="{{ route('local-conferences.exportLog') }}" class="btn btn-default">Export AUS Conferences Log</a>
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
<script src="{{ Helper::asset('assets/js/local-conference.js') }}"></script>
@stop
