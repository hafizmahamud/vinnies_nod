@extends('layouts.app')

@section('title')
    Manage Overseas Conferences
@stop

@php
    use App\User;
@endphp

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12 intro">
                <h1 class="page-title text-center">Manage Overseas Conferences</h1>
                <div class="col-sm-12">
                    <div class="pull-left">
                        This allows you to manage all the Overseas Conferences.<br /><br />
                        <p class="download"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <a href="{{ Helper::getDocUrl('guide') }}">Download NOD database guide in PDF format.</a></p>
                    </div>

                    <div class="pull-right">                        
                        <div class="row">
                            @if ($currentUser->hasPermissionTo('create.os-conf'))
                            <div class="col-auto">
                                <div class="text-right">
                                    <a href="{{ route('overseas-conferences.create') }}" class="btn btn-warning">Add Overseas Conference</a>
                                </div>
                            </div>
                            @endif
                            <div class="col-auto">
                                <div class="text-right">
                                    <a data-toggle="modal" id="getActivity" class="btn btn-primary btn-sm mb-2 mt-1"  data-target="#logAllMessageBoard" data-url="{{ url('overseas-conferences')}}" href="#."> Log </a>
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
                        <div class="col">
                            <div class="form-group mb-1">
                                <label for="country">Country</label><br />
                                <select name="country" id="country" class="form-control">
                                    <option value="">All Countries</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-1">
                                <label for="national_council">National/Superior Council</label><br />
                                <select name="national_council" id="national_council" class="form-control">
                                    <option value="">All</option>
                                    @foreach ($national_council as $national)
                                        <option value="{{ $national->id }}">{{ $national->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-1">
                            <label for="central_council">Central Council</label><br />
                            <input name="central_council" id="central_council" class="form-control"></input>
                            </div>

                            <div class="form-group mb-1">
                            <label for="particular_council">Particular Council</label><br />
                            <input name="particular_council" id="particular_council" class="form-control"></input>
                            </div>

                            <div class="form-group mb-1">
                                <label for="status">OS Conf. Status</label><br />
                                <select name="status" id="status" class="form-control">
                                    <option value="">Any Status</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="self_sufficient">Self-sufficient</option>
                                    <option value="abeyant">Abeyant</option>
                                    <option value="n/a">Unknown</option>
                                </select>
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group mb-1">
                                <label for="twinning_status">OS Conf. Twinning Status</label><br />
                                <select name="twinning_status" id="twinning_status" class="form-control">
                                    <option value=" ">Any Status</option>

                                    @foreach (Helper::getOSConferencesTwinningStatuses() as $key => $status)
                                        <option value="{{ $key }}">{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-1">
                                <label for="is_active">OS Conf. Receiving Remittances?</label><br />
                                <select name="is_active" id="is_active" class="form-control">
                                    <option value="">All</option>
                                    <option value="active">Remittances</option>
                                    <option value="inactive">No Remittances</option>
                                </select>
                            </div>

                            <div class="form-group mb-1">
                                <label for="twinning_state">Twinning State/Territory Council</label><br />
                                <select name="twinning_state" id="twinning_state" class="form-control">
                                    <option value="">All State/Territory Councils</option>
                                    @foreach ($states as $key => $value)
                                        <option value="{{ $key }}">{{ Helper::getStateNameByKey($key) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group mb-1">
                                <label for="is_in_status_check">Currently In Status Check?</label><br />
                                <select name="is_in_status_check" id="is_in_status_check" class="form-control">
                                    <option value="">All</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            
                            <div class="form-group mb-1">
                                <label for="status_check_initiated_at">Status Check Initiated</label><br />
                                <select name="status_check_initiated_at" id="status_check_initiated_at" class="form-control">
                                    <option value="">All</option>
                                    <option value="less">Less than 90 days ago</option>
                                    <option value="more">Over 90 days ago</option>
                                    <option value="none">No Date Available</option>
                                </select>
                            </div>
                            <div class="form-group mb-1">
                                <label for="reason_status_check">Reason for Status Check</label><br />
                                <select name="reason_status_check" id="reason_status_check" class="form-control">
                                    <option value="">All</option>
                                    @foreach (Helper::getOSConferencesStatusCheckReason() as $key => $reason)
                                        <option value="{{ $key }}">{{ $reason }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-1">
                                <label for="is_in_surrendering">Currently in Surrendering?</label><br />
                                <select name="is_in_surrendering" id="is_in_surrendering" class="form-control">
                                    <option value="">All</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div class="form-group mb-1">
                                <label for="surrendering_deadline_at">Surrendering Deadline</label><br />
                                <select name="surrendering_deadline_at" id="surrendering_deadline_at" class="form-control">
                                    <option value="">All</option>
                                    <option value="no">Not yet overdue</option>
                                    <option value="yes">Deadline overdue</option>
                                    <option value="none">No Date Available</option>
                                </select>
                            </div>

                        </div>
                           
                        <button type="submit" class="btn btn-warning" data-text-progress="Applying..." data-text-default="Apply Filters">Apply Filters</button>
                        @if ($currentUser->hasPermissionTo('export.os-conf'))
                        <a href="#" class="btn btn-default js-btn-export-os-conf">Export Filtered Overseas Conferences</a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-6">
                <form class="form-inline js-table-search-form">
                    <div class="form-group has-btn">
                        <label class="sr-only" for="overseas-conference-search">Search</label>
                        <input type="text" class="form-control js-table-search-input" id="overseas-conference-search" placeholder="Search">
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
                <table class="table table-striped js-table" data-url="{{ route('overseas-conferences.datatables') }}" data-page-length="{{ config('vinnies.pagination.overseas_conferences') }}" data-order-col="0" data-order-type="desc">
                    <thead>
                        <tr>
                            <th class="text-center" data-name="id">OS. SRN</th>
                            <th class="text-center" data-name="name">Overseas Conference Name</th>
                            <th class="text-center" data-name="parish">Overseas Conference Parish</th>
                            <th class="text-center" data-name="status">OS Conf. Status</th>
                            <th class="text-center" data-name="country">Country</th>
                            <th class="text-center" data-name="central_council">Central Council</th>
                            <th class="text-center" data-name="particular_council">Particular Council</th>
                            <th class="text-center" data-name="twinning_status">OS Conf. Twinning Status</th>
                            <th class="text-center" data-name="is_active">OS Conf. Receiving Remittances?</th>
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
                        <a href="{{ route('overseas-conferences.exportLog') }}" class="btn btn-default">Export Overseas Conferences Log</a>
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
<script src="{{ Helper::asset('assets/js/overseas-conference.js') }}"></script>
@stop
