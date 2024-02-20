@extends('layouts.app')

@section('title')
    Edit User
@stop

@php
    use App\User;
@endphp

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-title text-center">Edit User</h1>
                @include('flash::message')
                @include('partials.js-alert')
                <p><sup class="text-danger">*</sup> All fields marked with a red asterisk are required.</p>
                <hr>
                <div class="row well">
                    <div class="col-sm-6">
                        <p><strong>User Status: </strong>
                            @if ($user->is_active)
                                <span class="text-success">Active</span>
                            @else
                                <span class="text-danger">Inactive</span>
                            @endif
                        </p>
                        <p><strong>User Last Login: </strong>{{ $user->last_login ? $user->last_login->format(config('vinnies.date_format')) : 'Never' }}</p>
                    </div>
                    <div class="col-sm-6">
                        <p><strong>MFA Status: </strong>
                            @if ($user->hasGoogle2FAEnabled())
                                <span class="text-success">Active</span>
                            @else
                                <span class="text-danger">Inactive</span>
                            @endif
                        </p>
                        @if ($user->hasGoogle2FAEnabled())
                            <form action="{{ route('2fa.admin.reset', $user) }}" method="post" >
                                @csrf
                                @method('PATCH')

                                <button type="submit" class="btn btn-danger">Disable two-factor authentication</button>
                            </form>
                        @endif
                    </div>
                </div>
                <hr>
            </div>
        </div>

        {!! Form::model($user, ['route' => ['users.edit', $user->id], 'class' => 'form js-form', 'method' => 'patch']) !!}
            <div class="row">
                <div class="col-sm-5 pull-right" style="text-align:right">
                    Last Updated on: <strong data-bind="updated_date">{{ $user->updated_at->format('d/m/Y') }}</strong> at <strong data-bind="updated_time">{{ $user->updated_at->format('H:i') }}</strong> by <strong data-bind="updated_name">@if ($user->updated_by) {{ $user->updated_by->getFullName() }} @else {{ "Anonymous" }} @endif</strong><br />
                    Created on: <strong data-bind="created_date"> {{ $user->created_at->format('d/m/Y') }} </strong> at <strong data-bind="updated_time">{{ $user->updated_at->format('H:i') }}</strong><br />
                   {{--  <a href="javascript:void(0)" class="btn btn-primary btn-sm mb-2 mt-1" data-toggle="modal" data-url="{{ route('activity.list', $user->id) }}" data-target="#modal-activity">Activity Log</a>
                    @include('modals.activity.activity') --}}
                    <a data-toggle="modal" id="getActivity" class="btn btn-primary btn-sm mb-2 mt-1"  data-target="#messageBoard" data-url="{{ url('user',['id'=>$user->id])}}" href="#."> Log </a>
                </div>
            </div>

            @include('users.form')

            <div class="row">
                <div class="col-sm-3">
                    <button type="submit" class="btn btn-warning" data-text-default="Submit user changes" data-text-progress="Submitting...">Submit user changes</button>
                </div>
                <div class="col-sm-3 col-sm-offset-6 text-right">
                    @if (!$user->is_active)
                        <button type="button" class="btn btn-danger js-btn-user-reactivate" data-user-id="{{ $user->id }}" data-text-default="Reactivate User" data-text-progress="Reactivating...">Reactivate User</button>
                    @else
                        <button type="button" class="btn btn-danger js-btn-user-deactivate" data-user-id="{{ $user->id }}" data-text-default="Deactivate user" data-text-progress="Deactivating...">Deactivate User</button>
                    @endif
                </div>
                <div class="col-sm-6 col-sm-offset-6 text-right mt-2">
                    @if ($user->has_accepted_terms == "1")
                        <button type="button" class="btn btn-danger js-btn-user-signtos" data-user-id="{{ $user->id }}" data-text-default="Re-sign Terms of Use" data-text-progress="Re-sign Term of Use...">Re-sign Terms of Use</button>
                    @else
                        <button disabled type="button" class="btn btn-danger" data-user-id="{{ $user->id }}" data-text-default="Waiting for user to Sign Terms of Use">Waiting for user to Sign Terms of Use</button>
                    @endif
                </div>
            </div>
        </form>
        <hr>


        <h4 class="form-heading">User Documents</h4>
        <div class="section-documents" data-id="{{ $user->id }}" data-type="User"><i class="fa fa-spinner fa-pulse fa-fw"></i> Loading...</div>
        
        @include('modals.document.create')
        @include('modals.document.edit')

        @include('mustache.documents')

        <p>
        <p>
    </div>

    <div class="modal fade bd-example-modal-lg" id="messageBoard" tabindex="-1"  role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Activity Log</h4>
                </div>

                <div class="modal-body" style="overflow-x: scroll">
                    <div class="pull-right mb-1">
                        <a href="{{ route('users.exportIndividualLog', $user->id) }}" class="btn btn-default">Export User Log</a>
                    </div>
                    <table class="table table-striped js-modal-table" data-page-length="{{ config('vinnies.pagination.activity') }}" data-order-col="1" data-order-type="ASC">
                        <thead>
                            <tr>
                                <th class="text-center" data-name="id">Log ID</th>
                                <th class="text-center" data-name="event">Event Type</th>
                                <th class="text-center" data-name="subjectID">Subject ID</th>
                                <th width="100" class="text-center" data-name="properties">Properties Changes</th>
                                <th class="text-center" data-name="updated_at">Updated At</th>
                                <th class="text-center" data-name="updated_by">Updated By</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($activity as $activities)
                            <tr>
                                <td>{{ $activities->id }}</td>
                                <td>{{ $activities->event }}</td>
                                <td>{{ $activities->subject_id }}</td>
                                <td>
                                    <div style="display: flex; column-gap: 3rem">
                                        <div>
                                            @if(isset($activities->properties['old']) )
                                            <h5>Old</h5>
                                                @foreach ($activities->properties['old'] as $field => $value)
                                                    @if(!is_array($activities->properties['old'][$field]))
                                                    <p><strong> {{ $field }} : </strong> {{$activities->properties['old'][$field] }} </p>
                                                    @else
                                                    <p><strong> {{ $field }} : </strong> </p>
                                                    <ul>
                                                    @foreach($activities->properties['old'][$field] as $key => $secondValue)
                                                        <li>{{ $activities->properties['old'][$field][$key] }}</li>
                                                    @endforeach
                                                    </ul>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                        <div>
                                            
                                            @if(isset($activities->properties['attributes']) )
                                            <h5>Changes</h5>
                                                @foreach ($activities->properties['attributes'] as $field => $value)
                                                    @if(!is_array($activities->properties['attributes'][$field]))
                                                    <p><strong> {{ $field }} : </strong> {{$activities->properties['attributes'][$field] }} </p>
                                                    @else
                                                    <p><strong> {{ $field }} : </strong> </p>
                                                    <ul>
                                                    @foreach($activities->properties['attributes'][$field] as $key => $secondValue)
                                                        <li>{{ $activities->properties['attributes'][$field][$key] }}</li>
                                                    @endforeach
                                                    </ul>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                  

                                </td>                          
                                <td>{{ date('d-m-Y H:i:s', strtotime($activities->updated_at)) }}</td>
                                @php
                                    $user = User::where('id', $activities->causer_id)->withTrashed()->first();
                                @endphp
                                @if ($user)
                                    <td>{{ $user->first_name . ' ' . $user->last_name }}</td>
                                @else
                                    <td>{{ $activities->causer_id }}</td>
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
<script src="{{ Helper::asset('assets/js/user.js') }}"></script>
@stop
