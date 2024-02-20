@extends('layouts.app')

@section('title')
    Manage Twinnings
@stop

@php
    use App\User;
@endphp

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12 intro">
                <h1 class="page-title text-center">Manage Twinnings</h1>
                <div class="col-sm-12">
                    <div class="pull-left">
                        This allows you to manage all the Twinning relationships between Australian (local) Conferences and Overseas Conferences.<br /><br />
                        <p class="download"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <a href="{{ Helper::getDocUrl('guide') }}">Download NOD database guide in PDF format.</a></p>
                    </div>

                    <div class="pull-right">                
                        <div class="row">
                            @if ($currentUser->hasPermissionTo('create.twinnings'))
                            <div class="col-auto">
                                <div class="text-right">
                                    <a href="{{ route('twinnings.create') }}" class="btn btn-warning">Add Twinnings</a>
                                </div>
                            </div>
                            @endif
                            <div class="col-auto">
                                <div class="text-right">
                                    <a data-toggle="modal" id="getActivity" class="btn btn-primary btn-sm mb-2 mt-1"  data-target="#logAllMessageBoard" data-url="{{ url('twinnings')}}" href="#."> Log </a>
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
                    <div class="col mb-1">
                        <div class="form-group">
                            <label for="local_conference_state">Australian Conf. State/Territory Council</label><br>
                            <select name="local_conference_state" id="local_conference_state" class="form-control">
                                <option value="">All State/Territory Councils</option>
                                @foreach (Helper::getStates() as $key => $state)
                                        <option value="{{ $key }}">{{ ucwords($state) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="local_conference_diocesan_council_id">Australian Conf. Diocesan/Central Council</label><br>
                            <select name="local_conference_diocesan_council_id" id="local_conference_diocesan_council_id" class="form-control">
                                <option value="">All Diocesan/Central Councils</option>
                                @foreach ($diocesan_councils as $state => $diocesan_council_list)
                                    <optgroup label={{ $state }}>
                                        @foreach ($diocesan_council_list as $diocesan_council_id => $diocesan_council)
                                            <option value="{{ $diocesan_council_id }}">{{ $diocesan_council }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="local_conferences_regional_council">Australian Conf. Regional Council</label><br>
                            <input name="local_conferences_regional_council" id="local_conferences_regional_council" class="form-control"></input>
                        </div>
                    </div>
                    <div class="col mb-1">
                        <div class="form-group">
                            <label for="overseas_conference_country_id">OS Conf. Country</label><br>
                            <select name="overseas_conference_country_id" id="overseas_conference_country_id" class="form-control">
                                <option value="">All Countries</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    
                        <div class="form-group">
                            <label for="national_council">OS Conf. National/Superior Council</label><br>
                            <select name="national_council" id="national_council" class="form-control w-100">
                                <option value="">All</option>
                                @foreach (Helper::getNationalCouncil() as $key => $national_council)
                                    <option value="{{ $key }}">{{ $national_council }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        
                        <div class="form-group">
                            <label for="overseas_conferences_central_council">OS Conf. Central Council</label><br>
                            <input name="overseas_conferences_central_council" id="overseas_conferences_central_council" class="form-control"></input>
                        </div>
                        <div class="form-group">
                            <label for="overseas_conferences_particular_council">OS Conf. Particular Council</label><br>
                            <input name="overseas_conferences_particular_council" id="overseas_conferences_particular_council" class="form-control"></input>
                        </div>
                    </div>
                    <div class="col mb-1">
                        <div class="form-group">
                            <label for="is_active">Twinning Status</label><br>
                            <select name="is_active" id="is_active" class="form-control">
                                <option value="">All</option>
                                <option value="active">Active</option>
                                <option value="surrendered">Surrendered</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="overseas_conference_is_active">OS Conf. Receiving Remittances?</label><br>
                            <select name="overseas_conference_is_active" id="overseas_conference_is_active" class="form-control">
                                <option value="">All</option>
                                <option value="active">Remittances</option>
                                <option value="inactive">No Remittances</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="type">Twinning Type</label><br>
                            <select name="type" id="type" class="form-control">
                                <option value="">All</option>
                                <option value="standard">Standard Twinning</option>
                                <option value="council-to-council">Council to Council Twinning</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="period">Twinning Period</label><br>
                            <select name="period" id="period" class="form-control">
                                <option value="">All</option>
                                <option value="standard">Standard</option>
                                <option value="temporary">Temporary</option>
                            </select>
                        </div>
                    </div>
                        <div class="form-group mb-1">
                            <button type="submit" class="btn btn-warning" data-text-progress="Applying..." data-text-default="Apply Filters">Apply Filters</button>
                            @if ($currentUser->hasPermissionTo('export.twinnings'))
                            <a href="#" class="btn btn-default js-btn-export-twinnings">Export Filtered Twinnings</a>
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
                        <label class="sr-only" for="twinnings-search">Search</label>
                        <input type="text" class="form-control js-table-search-input" id="twinnings-search" placeholder="Search">
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
                <table class="table table-striped js-table" data-url="{{ route('twinnings.datatables') }}" data-page-length="{{ config('vinnies.pagination.twinnings') }}" data-order-col="0" data-order-type="desc">
                    <thead>
                        <tr>
                            <th class="text-center" data-name="id">Twinning ID</th>
                            <th class="text-center" data-name="is_active">Twinning Status</th>
                            <th class="text-center" data-name="local_conference_id">AUS. SRN</th>
                            <th class="text-center" data-name="local_conference_name">Australian Conference Name</th>
                            <th class="text-center" data-name="loc_parish">Australian Conf. Parish</th>
                            <th class="text-center" data-name="local_conference_state">State/Territory Council</th>
                            <th class="text-center" data-name="overseas_conference_id">OS. SRN</th>
                            <th class="text-center" data-name="overseas_conference_name">Overseas Conference Name</th>
                            <th class="text-center" data-name="oc_parish">Overseas Conf. Parish</th>
                            <th class="text-center" data-name="overseas_conference_country">Country</th>
                            <th class="text-center" data-name="overseas_conference_is_active">OS Conf. Receiving Remittances?</th>
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
                        <a href="{{ route('twinnings.exportLog') }}" class="btn btn-default">Export Twinnings Log</a>
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
<script src="{{ Helper::asset('assets/js/twinning.js') }}"></script>
@stop
