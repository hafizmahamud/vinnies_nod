@extends('layouts.app')

@section('title')
    Edit Australian Conference
@stop

@php
    use App\User;
@endphp

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-title text-center">Australian Conferences</h1>
            </div>
            <div class="col-sm-5 note-below-title">
                <p><sup class="text-danger">*</sup> All fields marked with a red asterisk are required.</p>
                <p><em><strong>NB:</strong> The fields marked in gray are going to be <br>&emsp;&emsp;automatically generated by the system.</em></p>
            </div>
        </div>
        <hr />
        @include('flash::message')
        @include('partials.js-alert')

        {!! Form::model($local_conference, ['route' => ['local-conferences.edit', $local_conference], 'class' => 'form js-form form-edit-local-conf', 'method' => 'patch']) !!}
            <div class="col-sm-6 mb-2 pull-right" style="text-align:right">
                Last Updated on: <strong data-bind="updated_date">{{ $local_conference->updated_at->format('d/m/Y') }}</strong> at <strong data-bind="updated_time">{{ $local_conference->updated_at->format('H:i') }}</strong> by <strong data-bind="updated_name">@if ($local_conference->updated_by) {{ $local_conference->updated_by->getFullName() }} @else {{ "Anonymous" }} @endif</strong><br />
                Created on: <strong data-bind="created_date"> {{ $local_conference->created_at->format('d/m/Y') }} </strong> at <strong data-bind="updated_time">{{ $local_conference->updated_at->format('H:i') }}</strong><br />
                <a data-toggle="modal" id="getActivity" class="btn btn-primary btn-sm mb-2 mt-1"  data-target="#messageBoard" data-url="{{ url('local_conference',['id'=>$local_conference->id])}}" href="#."> Log </a>
            </div>

            @include('local-conferences.form')

            @if ($currentUser->canEditLocalConference($local_conference))
                <div class="row">
                    <div class="col-sm-3">
                        <button type="submit" class="btn btn-warning" data-text-default="Apply changes to Australian Conf." data-text-progress="Applying...">Apply changes to Australian Conf.</button>
                    </div>
                </div>
            @endif
        </form>

        <hr>
        <p>
        {!! Form::model($local_conference, ['route' => ['local-conferences.addComment', $local_conference->id], 'class' => 'form js-form-comment', 'method' => 'patch']) !!}
            @include('local-conferences.comment')
        </form>
        <hr>

        @include('local-conferences.twinning')

        <hr>

        @include('local-conferences.surrendered-twinning')

        <hr>

        @include('local-conferences.projects')

        <hr>

        <h4 class="form-heading">Australian Conference Documents</h4>

        <div class="section-documents" data-id="{{ $local_conference->id }}" data-type="LocalConference"><i class="fa fa-spinner fa-pulse fa-fw"></i> Loading...</div>
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
                        <a href="{{ route('local-conferences.exportIndividualLog', $local_conference->id) }}" class="btn btn-default">Export AU Conference Log</a>
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
                                <td>
                                    <?php
                                        $event = explode('\\',$activities->subject_type);
                                        if($event[1] != 'LocalConference'){
                                            echo $event[1];
                                        }
                                    ?>
                                    {{ $activities->event }}</td>
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
<script src="https://cdn.rawgit.com/simontaite/jquery.dirty/cb94057a/dist/jquery.dirty.js"></script>
<script src="{{ Helper::asset('assets/js/local-conference.js') }}"></script>
@stop