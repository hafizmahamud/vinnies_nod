<h4 class="form-heading">General Project Info</h4>
<div class="row">
    @if (Route::currentRouteName() != 'projects.create')
        <div class="col-sm-1">
            <div class="form-group{{ $errors->has('id') ? ' has-error' : '' }}">
                <label for="id">Project ID</label>
                {!! Form::text('id', null, ['class' => 'form-control', 'id' => 'id', 'readonly' => 'readonly']) !!}

                @if ($errors->has('id'))
                    <span class="help-block">
                       {{ $errors->first('id') }}
                    </span>
                @endif
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group {{ $errors->has('overseas_project_id') ? ' has-error' : '' }}">
                <label for="overseas_project_id">Overseas Project ID</label>
                @if ($currentUser->hasPermissionTo('update.projects'))
                    {!! Form::text('overseas_project_id', null, ['class' => 'form-control', 'id' => 'overseas_project_id']) !!}
                @else
                    {!! Form::text('overseas_project_id', null, ['class' => 'form-control', 'id' => 'overseas_project_id', 'readonly' => 'readonly']) !!}
                @endif

                @if ($errors->has('overseas_project_id'))
                    <span class="help-block">
                    {{ $errors->first('overseas_project_id') }}
                    </span>
                @endif
            </div>
        </div>
    
        @if ($currentUser->hasPermissionTo('download.projects'))
            @if (Route::currentRouteName() != 'projects.create')
                <div class="col-sm-3 col-sm-offset-6">
            @else
                <div class="col-sm-3 col-sm-offset-6">
            @endif
                <div class="form-group">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <a href="{{ Route::currentRouteName() == 'projects.create' ? '#' : route('projects.download', ['project' => $project->id]) }}" class="btn btn-primary btn-block">Download Cover Sheet PDF</a>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
<div class="row">
    <div class="col-sm-3">
        <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name" class="text-primary">Project Name <sup class="text-danger">*</sup></label>
            @if ($currentUser->hasPermissionTo('update.projects'))
                {!! Form::text('name', null, ['class' => 'form-control', 'id' => 'name']) !!}
            @else
                {!! Form::text('name', null, ['class' => 'form-control', 'id' => 'name', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('name'))
                <span class="help-block">
                   {{ $errors->first('name') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-2 line-up-box">
        <div class="form-group {{ $errors->has('received_at') ? ' has-error' : '' }}">
            <label for="received_at">Date Application Received <sup class="text-danger">*</sup></label>
            @if ($currentUser->hasPermissionTo('update.projects'))
                {!! Form::text('received_at', optional($project->received_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'received_at']) !!}
            @else
                {!! Form::text('received_at', optional($project->received_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'received_at', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('received_at'))
                <span class="help-block">
                   {{ $errors->first('received_at') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2 line-up-box">
        <div class="form-group {{ $errors->has('estimated_completed_at') ? ' has-error' : '' }}">
            <label for="received_at">Estimated Completion Date</label>
            @if ($currentUser->hasPermissionTo('update.projects'))
                {!! Form::text('estimated_completed_at', optional($project->estimated_completed_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'estimated_completed_at']) !!}
            @else
                {!! Form::text('estimated_completed_at', optional($project->estimated_completed_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'estimated_completed_at', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('estimated_completed_at'))
                <span class="help-block">
                   {{ $errors->first('estimated_completed_at') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2 line-up-box">
        <div class="form-group{{ $errors->has('consolidated_status') ? ' has-error' : '' }}">
            <label for="consolidated_status">DFAT Consolidated List Approved?</label>
            @if ($currentUser->hasPermissionTo('update.projects'))
                {!! Form::select('consolidated_status', Helper::getProjectsConsolidatedStatuses(), $project->consolidated_status ?? 'n/a', ['class' => 'form-control', 'id' => 'consolidated_status']) !!}
            @else
                {!! Form::select('consolidated_status', Helper::getProjectsConsolidatedStatuses(), $project->consolidated_status ?? 'n/a', ['class' => 'form-control', 'disabled' => 'disabled', 'id' => 'consolidated_status']) !!}
            @endif

            @if ($errors->has('consolidated_status'))
                <span class="help-block">
                   {{ $errors->first('consolidated_status') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('project_type') ? ' has-error' : '' }}">
            <label for="project_type">Project Type</label>
            @if ($currentUser->hasPermissionTo('update.projects'))
                {!! Form::select('project_type', Helper::getProjectType(), $project->project_type ?? 'n/a', ['class' => 'form-control', 'id' => 'project_type']) !!}
            @else
                {!! Form::select('project_type', Helper::getProjectType(), $project->project_type ?? 'n/a', ['class' => 'form-control', 'disabled' => 'disabled', 'id' => 'project_type']) !!}
            @endif

            @if ($errors->has('project_type'))
                <span class="help-block">
                   {{ $errors->first('project_type') }}
                </span>
            @endif
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-sm-3">
        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
            <label for="status">Project Status <sup class="text-danger">*</sup></label>
            @if ($currentUser->hasPermissionTo('update.projects'))
                {!! Form::select('status', Helper::getProjectsStatuses(), $project->status ?? 'n/a', ['class' => 'form-control', 'id' => 'status']) !!}
            @else
                {!! Form::select('status', Helper::getProjectsStatuses(), $project->status ?? 'n/a', ['class' => 'form-control', 'disabled' => 'disabled', 'id' => 'status']) !!}
            @endif

            @if ($errors->has('status'))
                <span class="help-block">
                   {{ $errors->first('status') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('project_completed') ? ' has-error' : '' }}">
            <label for="project_completed">Project Completed?</label>
            @if ($currentUser->hasPermissionTo('update.projects'))
                {!! Form::select('project_completed', ['no' => 'No', 'yes' => 'Yes'], null, ['class' => 'form-control', 'id' => 'project_completed']) !!}
            @else
                {!! Form::select('project_completed', ['no' => 'No', 'yes' => 'Yes'], null, ['class' => 'form-control', 'disabled' => 'disabled', 'id' => 'project_completed']) !!}
            @endif

            @if ($errors->has('project_completed'))
                <span class="help-block">
                   {{ $errors->first('project_completed') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('project_completion_date') ? ' has-error' : '' }}">
            <label for="project_completion_date">Project Completion Date</label>
            @if ($currentUser->hasPermissionTo('update.projects'))
                {!! Form::text('project_completion_date', optional($project->project_completion_date)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'project_completion_date']) !!}
            @else
                {!! Form::text('project_completion_date', optional($project->project_completion_date)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'project_completion_date', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('project_completion_date'))
                <span class="help-block">
                   {{ $errors->first('project_completion_date') }}
                </span>
            @endif
        </div>
    </div>
    
    <div class="col-sm-2 line-up-box">
        <div class="form-group{{ $errors->has('completion_report_received') ? ' has-error' : '' }}">
            <label for="completion_report_received">Project Completion Report Received?</label>
            @if ($currentUser->hasPermissionTo('update.projects'))
                {!! Form::select('completion_report_received', ['0' => 'No','1' => 'Progress Report Received', '2' => 'Yes', '3' => 'Time Elapsed'], null, ['class' => 'form-control', 'id' => 'completion_report_received']) !!}
            @else
                {!! Form::select('completion_report_received', ['0' => 'No','1' => 'Progress Report Received', '2' => 'Yes', '3' => 'Time Elapsed'], null, ['class' => 'form-control', 'id' => 'completion_report_received', 'disabled' => 'disabled']) !!}
            @endif

            @if ($errors->has('completion_report_received'))
                <span class="help-block">
                   {{ $errors->first('completion_report_received') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-2 line-up-box">
        <div class="form-group{{ $errors->has('completed_at') ? ' has-error' : '' }}">
            <label for="completed_at">Project Completion Report Received Date</label>
            @if ($currentUser->hasPermissionTo('update.projects'))
                {!! Form::text('completed_at', optional($project->completed_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'completed_at']) !!}
            @else
                {!! Form::text('completed_at', optional($project->completed_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'completed_at', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('completed_at'))
                <span class="help-block">
                   {{ $errors->first('completed_at') }}
                </span>
            @endif
        </div>
    </div>
</div>


<hr>

<div class="row">
    <div class="col-xs-12 col-sm-3">
        <div class="form-group">
            <a href="#" class="btn btn-primary btn-block" data-toggle="modal" data-target="#modal-beneficiary">Assign Beneficiary</a>
            <input type="hidden" name="beneficiary_id" value="{{ $project->beneficiary_id }}">
        </div>
    </div>
    <div class="col-xs-12 col-sm-4">
        <label>Beneficiary Name <sup class="text-danger">*</sup></label>

        @if (Route::currentRouteName() == 'projects.create')
            <p><a href="#" target="_blank" data-bind="beneficiary_url" data-bind-attr="href"></a><span data-bind="beneficiary_name">No Beneficiary assigned yet.</span></p>
        @else
            <p><a href="{{ route('beneficiaries.edit', $project->beneficiary) }}" target="_blank" data-bind="beneficiary_url" data-bind-attr="href"><span data-bind="beneficiary_name">{{ optional($project->beneficiary)->name }}</span></a></p>
        @endif
    </div>
    <div class="col-xs-12 col-sm-2">
        <label>Project Country <sup class="text-danger">*</sup></label>

        @if (Route::currentRouteName() == 'projects.create')
            <p><span data-bind="beneficiary_country">N/A</span></p>
        @else
            <p><span data-bind="beneficiary_country">{{ optional(optional($project->beneficiary)->country)->name }}</span></p>
        @endif
    </div>
</div>

<hr>

<div class="row row-os-conf">
    <div class="col-xs-12 col-sm-3">
        <div class="form-group">
            <a href="#" class="btn btn-primary btn-block" data-toggle="modal" data-target="#modal-os-conf">Assign OS Conf.</a>
            <input type="hidden" name="overseas_conference_id" value="{{ optional($project->overseasConference)->id }}">
        </div>
    </div>
    <div class="col-xs-12 col-sm-2">
        <label>OS. SRN</label>

        @if ($project->hasOverseasConference())
            <p><a href="{{ route('overseas-conferences.edit', $project->overseasConference) }}" data-bind="overseas_conference_url" data-bind-attr="href"><span data-bind="overseas_conference_id">{{ $project->overseasConference->id }}</span></a></p>
        @else
            <p class="text-muted"><a href="#" data-bind="overseas_conference_url" data-bind-attr="href"></a><span data-bind="overseas_conference_id">N/A</span></p>
        @endif

        <br />
        <label>Overseas Conference Name</label>

        @if ($project->hasOverseasConference())
            <p><a href="{{ route('overseas-conferences.edit', $project->overseasConference) }}" data-bind="overseas_conference_url" data-bind-attr="href"><span data-bind="overseas_conference_name">{{ $project->overseasConference->name }}</span></a></p>
        @else
            <p class="text-muted"><a href="#" data-bind="overseas_conference_url" data-bind-attr="href"></a><span data-bind="overseas_conference_name">No Overseas Conference assigned yet.</span></p>
        @endif
    </div>
    
    <div class="col-xs-12 col-sm-1">
        <label>Country</label>

        @if ($project->hasOverseasConference())
            <p data-bind="overseas_conference_country">{{ $project->overseasConference->country->name }}</p>
        @else
            <p class="text-muted" data-bind="overseas_conference_country">N/A</p>
        @endif
    </div>
    <div class="col-xs-12 col-sm-2">
        <label>Central Council</label>

        @if ($project->hasOverseasConference())
            <p data-bind="overseas_conference_central_council">{{ $project->overseasConference->central_council }}</p>
        @else
            <p class="text-muted" data-bind="overseas_conference_central_council">N/A</p>
        @endif

        <br />
        <label>Particular Council</label>

        @if ($project->hasOverseasConference())
            <p data-bind="overseas_conference_particular_council">{{ $project->overseasConference->particular_council }}</p>
        @else
            <p class="text-muted" data-bind="overseas_conference_particular_council">N/A</p>
        @endif
    </div>

    <div class="col-xs-12 col-sm-2">
        <label>Parish</label>

        @if ($project->hasOverseasConference())
            <p data-bind="overseas_conference_parish">{{ $project->overseasConference->parish }}</p>
        @else
            <p class="text-muted" data-bind="overseas_conference_parish">N/A</p>
        @endif

        <br />
        <label>Recipient</label>
        @if ($project->hasOverseasConference())
            <p data-bind="overseas_conference_completion_report_received">{{ $project->overseasConference->completion_report_received ? 'Active' : 'Inactive' }}</p>
        @else
            <p class="text-muted" data-bind="overseas_conference_completion_report_received">N/A</p>
        @endif
    </div>
</div>

<hr>

<div class="row">
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('currency') ? ' has-error' : '' }}">
            <label for="currency">Overseas Currency <sup class="text-danger">*</sup></label>
            @if ($currentUser->hasPermissionTo('update.projects'))
                {!! Form::select('currency', Helper::getCurrencies(), null, ['class' => 'form-control', 'id' => 'currency']) !!}
            @else
                {!! Form::select('currency', Helper::getCurrencies(), null, ['class' => 'form-control', 'id' => 'currency', 'disabled' => 'disabled']) !!}
            @endif

            @if ($errors->has('currency'))
                <span class="help-block">
                   {{ $errors->first('currency') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2 line-up-box">
        <div class="form-group{{ $errors->has('local_value') ? ' has-error' : '' }}">
            <label for="local_value">Project Value in OS Currency <sup class="text-danger">*</sup></label>
            @if ($currentUser->hasPermissionTo('update.projects'))
                {!! Form::text('local_value', null, ['class' => 'form-control', 'id' => 'local_value']) !!}
            @else
                {!! Form::text('local_value', null, ['class' => 'form-control', 'id' => 'local_value', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('local_value'))
                <span class="help-block">
                   {{ $errors->first('local_value') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('exchange_rate') ? ' has-error' : '' }}">
            <label for="exchange_rate">XE Exchange Rate <sup class="text-danger">*</sup></label>
            {!! Form::text('exchange_rate', null, ['class' => 'form-control', 'id' => 'exchange_rate', 'data-bind' => 'exchange_rate']) !!}

            @if ($errors->has('exchange_rate'))
                <span class="help-block">
                   {{ $errors->first('exchange_rate') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('au_value') ? ' has-error' : '' }}">
            <label for="au_value">Project Value in AUD <sup class="text-danger">*</sup></label>
            {!! Form::text('au_value', (Route::currentRouteName() == 'projects.create' ? null : $project->au_value->value()), ['class' => 'form-control', 'id' => 'au_value', 'data-bind' => 'au_value']) !!}

            @if ($errors->has('au_value'))
                <span class="help-block">
                   {{ $errors->first('au_value') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('is_awaiting_support') ? ' has-error' : '' }}">
            <label for="is_awaiting_support">Awaiting Support? <sup class="text-danger">*</sup></label>
            @if ($currentUser->hasPermissionTo('update.projects'))
                {!! Form::select('is_awaiting_support', ['0' => 'No', '1' => 'Yes'], (Route::currentRouteName() == 'projects.create' ? 1 : null), ['class' => 'form-control', 'id' => 'is_awaiting_support']) !!}
            @else
                {!! Form::select('is_awaiting_support', ['0' => 'No', '1' => 'Yes'], (Route::currentRouteName() == 'projects.create' ? 1 : null), ['class' => 'form-control', 'id' => 'is_awaiting_support', 'disabled' => 'disabled']) !!}
            @endif

            @if ($errors->has('is_awaiting_support'))
                <span class="help-block">
                   {{ $errors->first('is_awaiting_support') }}
                </span>
            @endif
        </div>
    </div>
    
</div>

<div class="row">
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('total_paid') ? ' has-error' : '' }}">
            <label for="total_paid">Total Paid to Date</label>
            {!! Form::text('total_paid', $project->getTotalPaid()->value(), ['class' => 'form-control', 'id' => 'total_paid', 'readonly' => 'readonly', 'data-bind' => 'total_paid']) !!}

            @if ($errors->has('total_paid'))
                <span class="help-block">
                   {{ $errors->first('total_paid') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('balance_owing') ? ' has-error' : '' }}">
            <label for="balance_owing">Balance Owing</label>
            {!! Form::text('balance_owing', $project->getBalanceOwing()->value(), ['class' => 'form-control', 'id' => 'balance_owing', 'readonly' => 'readonly', 'data-bind' => 'balance_owing']) !!}

            @if ($errors->has('balance_owing'))
                <span class="help-block">
                   {{ $errors->first('balance_owing') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label for="is_fully_paid">Fully Paid?</label>
            @if ($currentUser->hasPermissionTo('update.projects'))
                {!! Form::select('is_fully_paid', ['0' => 'No', '1' => 'Yes'], $project->is_fully_paid, ['class' => 'form-control', 'id' => 'is_fully_paid'] ) !!}
            @else
                {!! Form::select('is_fully_paid', ['0' => 'No', '1' => 'Yes'], $project->is_fully_paid, ['class' => 'form-control', 'id' => 'is_fully_paid', 'disabled' => 'disabled'] ) !!}
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('fully_paid_at') ? ' has-error' : '' }}">
            <label for="fully_paid_at">Date when Fully Paid</label>
            {!! Form::text('fully_paid_at', optional($project->fully_paid_at)->format(config('vinnies.date_format')), ['class' => 'form-control', 'id' => 'fully_paid_at', 'readonly' => 'readonly', 'data-bind' => 'fully_paid_at']) !!}

            @if ($errors->has('fully_paid_at'))
                <span class="help-block">
                   {{ $errors->first('fully_paid_at') }}
                </span>
            @endif
        </div>
    </div>
</div>

@if (Route::currentRouteName() != 'projects.edit')
    <div class="row">
        @if ($project->id !== null)
            @include('partials.comments', ['url' => route('projects.comments', $project)])
        @endif

        <div class="col-sm-12">
            <div class="form-group{{ $errors->has('comments') ? ' has-error' : '' }}">
                <label for="comments">Comments</label>
                @if ($currentUser->hasPermissionTo('update.projects'))
                    {!! Form::textarea('comments', null, ['class' => 'form-control form-control-text-danger js-stretch', 'id' => 'comments', 'rows' => null, 'cols' => null]) !!}
                @else
                    {!! Form::textarea('comments', null, ['class' => 'form-control form-control-text-danger js-stretch', 'id' => 'comments', 'rows' => null, 'cols' => null,  'readonly' => 'readonly']) !!}
                @endif

                @if ($errors->has('comments'))
                    <span class="help-block">
                       {{ $errors->first('comments') }}
                    </span>
                @endif
            </div>
        </div>
    </div>
@endif
