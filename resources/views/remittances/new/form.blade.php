<h4 class="form-heading">General Remittance In Information</h4>
@if (Route::currentRouteName() != 'new-remittances.create')
    @if ($remittance->is_approved && $currentUser->hasPermissionTo('unapprove.new-remittances'))
        <div class="row">
           <div class="col-sm-10 col-sm-offset-2 text-right">
                <p class="text-warning">This Remittance was Approved and Processed on {{ $remittance->approved_at->format(config('vinnies.date_format')) }}, it can no longer be modified.</p>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-sm-2">
            <div class="form-group">
                <label for="id">Remittance ID</label>
                {!! Form::text('id', null, ['class' => 'form-control', 'id' => 'id', 'readonly' => 'readonly']) !!}
            </div>
        </div>

        @if ($remittance->is_approved)
            @if ($currentUser->hasPermissionTo('unapprove.new-remittances'))
                <div class="col-sm-3 pull-right">
                    <label>&nbsp;</label>
                    <a href="{{ route('new-remittances.unapprove', $remittance) }}" class="btn btn-danger btn-block js-btn-unapprove-remittance">Reinstate Edit Mode</a>
                </div>
            @else
                <div class="col-sm-10 text-right">
                    <p class="text-warning">This Remittance was Approved and Processed on {{ optional($remittance->approved_at)->format(config('vinnies.date_format')) }}, it can no longer be modified.</p>
                </div>
            @endif
        @else
            @if ($currentUser->hasPermissionTo('approve.new-remittances'))
                <div class="col-sm-4 pull-right">
                    <label>&nbsp;</label>
                    <a href="{{ route('new-remittances.approve', $remittance) }}" class="btn btn-primary btn-block js-btn-approve-remittance">Approve and Process this Remittance</a>
                </div>
            @endif
        @endif
    </div>
@endif

<div class="row">
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('state') ? ' has-error' : '' }}">
            <label for="state">State/Territory Council <sup class="text-danger">*</sup></label>
            @if (!$remittance->is_approved)
                {!! Form::select('state', Helper::getStates(), null, ['class' => 'form-control', 'id' => 'state', 'placeholder' => 'Please select']) !!}
            @else
                {!! Form::select('state', Helper::getStates(), null, ['class' => 'form-control', 'id' => 'state', 'placeholder' => 'Please select', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('state'))
                <span class="help-block">
                   {{ $errors->first('state') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('quarter') ? ' has-error' : '' }}">
            <label for="quarter">Quarter <sup class="text-danger">*</sup></label>
            @if (!$remittance->is_approved)
                {!! Form::select('quarter', ['1' => 'Q1', '2' => 'Q2', '3' => 'Q3', '4' => 'Q4'], null, ['class' => 'form-control', 'id' => 'quarter', 'placeholder' => 'Select']) !!}
            @else
                {!! Form::select('quarter', ['1' => 'Q1', '2' => 'Q2', '3' => 'Q3', '4' => 'Q4'], null, ['class' => 'form-control', 'id' => 'quarter', 'placeholder' => 'Select' , 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('quarter'))
                <span class="help-block">
                   {{ $errors->first('quarter') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('year') ? ' has-error' : '' }}">
            <label for="year">Year <sup class="text-danger">*</sup></label>
            @if (!$remittance->is_approved)
                {!! Form::selectRange('year', 2016, date('Y') + 1, null, ['class' => 'form-control', 'id' => 'year', 'placeholder' => 'Select']) !!}
            @else
                {!! Form::selectRange('year', 2016, date('Y') + 1, null, ['class' => 'form-control', 'id' => 'year', 'placeholder' => 'Select', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('year'))
                <span class="help-block">
                   {{ $errors->first('year') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
            <label for="date">Date <sup class="text-danger">*</sup></label>
            @if (!$remittance->is_approved)
                {!! Form::text('date', optional($remittance->date)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'date']) !!}
            @else
                {!! Form::text('date', optional($remittance->date)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'date', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('date'))
                <span class="help-block">
                   {{ $errors->first('date') }}
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-2">
        <div class="form-group">
            <label for="donation_total">Payments Total</label>
            {!! Form::text('donation_total', '0.00', ['class' => 'form-control', 'id' => 'donation_total', 'readonly' => 'readonly', 'data-bind' => 'total']) !!}
        </div>
    </div>
    <div class="col-sm-1">
        <div class="form-group">
            <label for="projects">Projects</label>
            {!! Form::text('projects', '0.00', ['class' => 'form-control', 'id' => 'projects', 'readonly' => 'readonly', 'data-bind' => 'projects']) !!}
        </div>
    </div>
    <div class="col-sm-1">
        <div class="form-group">
            <label for="twinning">Twinning</label>
            {!! Form::text('twinning', '0.00', ['class' => 'form-control', 'id' => 'twinning', 'readonly' => 'readonly', 'data-bind' => 'twinning']) !!}
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label for="grants">Grants</label>
            {!! Form::text('grants', '0.00', ['class' => 'form-control', 'id' => 'grants', 'readonly' => 'readonly', 'data-bind' => 'grants']) !!}
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label for="council_to_council">Council to Council</label>
            {!! Form::text('council_to_council', '0.00', ['class' => 'form-control', 'id' => 'council_to_council', 'readonly' => 'readonly', 'data-bind' => 'councils']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-2 line-up-box">
        <div class="form-group">
            <label for="projects_count">Projects Payments Count Count</label>
            {!! Form::text('projects_count', '0', ['class' => 'form-control', 'id' => 'allocated', 'readonly' => 'readonly', 'data-bind' => 'projects_count']) !!}
        </div>
    </div>
    <div class="col-sm-2 line-up-box">
        <div class="form-group">
            <label for="twinnings_count">Twinnings Payments Count</label>
            {!! Form::text('twinnings_count', '0', ['class' => 'form-control', 'id' => 'allocated', 'readonly' => 'readonly', 'data-bind' => 'twinnings_count']) !!}
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label for="grants_count">Grants Payments Count</label>
            {!! Form::text('grants_count', '0', ['class' => 'form-control', 'id' => 'allocated', 'readonly' => 'readonly', 'data-bind' => 'grants_count']) !!}
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label for="councils_count">CTC Payments Count</label>
            {!! Form::text('councils_count', '0', ['class' => 'form-control', 'id' => 'allocated', 'readonly' => 'readonly', 'data-bind' => 'councils_count']) !!}
        </div>
    </div>
    <div class="col-sm-3 pull-right">
        <label>&nbsp;</label>
        @if (Route::currentRouteName() == 'new-remittances.edit')
            <a href="{{ route('new-remittances.download', $remittance) }}" class="btn btn-warning btn-block">Remittance cover sheet</a>
        @endif
    </div>
</div>

@if (Route::currentRouteName() != 'new-remittances.edit')
    <div class="row">
        @if ($remittance->id !== null)
            @include('partials.comments', ['url' => route('new-remittances.comments', $remittance)])
        @endif

        <div class="col-sm-12">
            <div class="form-group{{ $errors->has('comments') ? ' has-error' : '' }}">
                <label for="comments">Remittance Comments</label>
                @if (!$remittance->is_approved)
                    {!! Form::textarea('comments', null, ['class' => 'form-control form-control-text-danger js-stretch', 'id' => 'comments', 'rows' => null, 'cols' => null]) !!}
                @else
                    {!! Form::textarea('comments', null, ['class' => 'form-control form-control-text-danger js-stretch', 'id' => 'comments', 'rows' => null, 'cols' => null, 'readonly' => 'readonly']) !!}
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
