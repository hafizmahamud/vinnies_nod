@extends('layouts.app')

@section('title')
    Manage Beneficiaries
@stop

@php
    use App\User;
@endphp

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12 intro">
                <h1 class="page-title text-center">Manage Beneficiaries</h1>
                @include('flash::message')
                @include('partials.js-alert')
                <div class="row">
                    <div class="col-sm-6">
                        This allows you to manage all the Beneficiaries.<br /><br />
                        <p class="download"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <a href="{{ Helper::getDocUrl('guide') }}">Download NOD database guide in PDF format.</a></p>
                    </div>
            <div class="col-sm-6">
                @if ($currentUser->hasPermissionTo('create.beneficiaries'))
                   <div class="row">
                           <div class="col-auto">
                               <div class="text-right">
                                   <a href="{{ route('beneficiaries.create') }}" class="btn btn-warning">Add Beneficiaries</a>
                               </div>
                           </div>
                           <div class="col-auto">
                               <div class="text-right">
                               <a
                                   data-toggle="modal"
                                   id="getActivity"
                                   class="btn btn-primary btn-sm mb-2 mt-1"
                                   data-target="#logAllMessageBoard"
                                   data-url="http://127.0.0.1:8000/beneficiaries/"
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
                            <label for="country">Country</label>
                            <select name="country" id="country" class="form-control">
                                <option value="">All Countries</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">Show Beneficiaries that are...</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Show All, including deleted</option>
                                <option value="active">In Use</option>
                                <option value="inactive">Only Deleted</option>
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
                        <label class="sr-only" for="beneficiaries-search">Search</label>
                        <input type="text" class="form-control js-table-search-input" id="beneficiaries-search" placeholder="Search">
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
                <table class="table table-striped js-table" data-url="{{ route('beneficiaries.datatables') }}" data-page-length="{{ config('vinnies.pagination.beneficiaries') }}" data-order-col="0" data-order-type="desc">
                    <thead>
                        <tr>
                            <th class="text-center" data-name="id">Beneficiary ID</th>
                            <th class="text-center" data-name="name">Beneficiary Name</th>
                            <th class="text-center" data-name="country" data-orderable="false">Country</th>
                            <th class="text-center" data-name="contact_title">Contact Title</th>
                            <th class="text-center" data-name="contact_first_name">First Name</th>
                            <th class="text-center" data-name="contact_last_name">Last Name</th>
                            <th class="text-center" data-name="email">Email</th>
                            <th class="text-center" data-name="status" data-orderable="false">Status</th>
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
                        <a href="{{ route('beneficiaries.exportLog') }}" class="btn btn-default">Export Beneficiaries Log</a>
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
<script src="{{ Helper::asset('assets/js/beneficiary.js') }}"></script>
@stop
