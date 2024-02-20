<h4 class="form-heading align-bottom-form-heading">General Twinning Information</h4>
<div class="row">
    @if (Route::currentRouteName() != 'twinnings.create')
        <div class="col-sm-2">
            <div class="form-group">
                <label for="id">Twinning ID</label>
                {!! Form::text('id', null, ['class' => 'form-control', 'id' => 'id', 'readonly' => 'readonly']) !!}
            </div>
        </div>
    @endif
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('is_active') ? ' has-error' : '' }}">
            <label for="is_active">Twinning Status </label>
            @if ($currentUser->hasPermissionTo('update.twinnings'))
                {!! Form::select('is_active', ['1' => 'Active', '0' => 'Surrendered'], (Route::currentRouteName() == 'twinnings.create' ? 1 : ($twinning->is_active ? '1' : '0') ), ['class' => 'form-control', 'id' => 'is_active']) !!}
            @else
                {!! Form::select('is_active', ['1' => 'Active', '0' => 'Surrendered'], (Route::currentRouteName() == 'twinnings.create' ? 1 : ($twinning->is_active ? '1' : '0') ), ['class' => 'form-control', 'id' => 'is_active', 'disabled' => 'disabled']) !!}
            @endif

            @if ($errors->has('is_active'))
                <span class="help-block">
                   {{ $errors->first('is_active') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
            <label for="type">Twinning Type <sup class="text-danger">*</sup></label>
            @if ($currentUser->hasPermissionTo('update.twinnings'))
                {!! Form::select('type', Helper::getTwinningTypes(), null, ['class' => 'form-control', 'id' => 'type']) !!}
            @else
                {!! Form::select('type', Helper::getTwinningTypes(), null, ['class' => 'form-control', 'id' => 'type', 'disabled' => 'disabled']) !!}
            @endif

            @if ($errors->has('type'))
                <span class="help-block">
                   {{ $errors->first('type') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('twinning_period') ? ' has-error' : '' }}">
            <label for="twinning_period">Twinning Period</label>
            @if ($currentUser->hasPermissionTo('update.twinnings'))
                {!! Form::select('twinning_period', Helper::getTwinningPeriodTypeList(), $twinning->twinning_period ?? 'n/a', ['class' => 'form-control', 'id' => 'twinning_period']) !!}
            @else
                {!! Form::select('twinning_period', Helper::getTwinningPeriodTypeList(), $twinning->twinning_period ?? 'n/a', ['class' => 'form-control', 'disabled' => 'disabled', 'id' => 'twinning_period']) !!}
            @endif

            @if ($errors->has('twinning_period'))
                <span class="help-block">
                   {{ $errors->first('twinning_period') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('is_active_at') ? ' has-error' : '' }}">
            <label for="is_active_at">Date became active</label>
            @if ($currentUser->hasPermissionTo('update.twinnings'))
                {!! Form::text('is_active_at', optional($twinning->is_active_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'is_active_at']) !!}
            @else
                {!! Form::text('is_active_at', optional($twinning->is_active_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'is_active_at', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('is_active_at'))
                <span class="help-block">
                   {{ $errors->first('is_active_at') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('is_surrendered_at') ? ' has-error' : '' }}">
            <label for="is_surrendered_at">Date Surrendered</label>
            @if ($currentUser->hasPermissionTo('update.twinnings'))
                {!! Form::text('is_surrendered_at', optional($twinning->is_surrendered_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'is_surrendered_at']) !!}
            @else
                {!! Form::text('is_surrendered_at', optional($twinning->is_surrendered_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'is_surrendered_at', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('is_surrendered_at'))
                <span class="help-block">
                   {{ $errors->first('is_surrendered_at') }}
                </span>
            @endif
        </div>
    </div>
    
</div>

<hr>

<div class="row row-local-conf">
    <div class="col-xs-12 col-sm-3">
        <a href="#" class="btn btn-primary btn-block mb-2" data-toggle="modal" data-target="#modal-local-conf">Assign Australian Conference</a>
    </div>
    <div class="col-xs-12 col-sm-3 text-left">
        <label>AUS. SRN <sup class="text-danger">*</sup></label>

        @if ($twinning->hasLocalConference())
            <p><a href="{{ route('local-conferences.edit', $twinning->localConference) }}" data-bind="local_conference_url" data-bind-attr="href"><span data-bind="local_conference_id">{{ $twinning->localConference->id }}</span></a></p>
        @else
            <p class="text-muted"><a href="#" data-bind="local_conference_url" data-bind-attr="href"></a><span data-bind="local_conference_id">N/A</span></p>
        @endif

        <br />
        <label>Australian Conference Name <sup class="text-danger">*</sup></label>

        @if ($twinning->hasLocalConference())
            <p><a href="{{ route('local-conferences.edit', $twinning->localConference) }}" data-bind="local_conference_url" data-bind-attr="href"><span data-bind="local_conference_name">{{ $twinning->localConference->name }}</span></a></p>
        @else
            <p class="text-muted"><a href="#" data-bind="local_conference_url" data-bind-attr="href"></a><span data-bind="local_conference_name">N/A</span></p>
        @endif
    </div>

    <div class="col-xs-12 col-sm-3 text-left">
        <label>State Council</label>

        @if ($twinning->hasLocalConference())
            <p data-bind="local_conference_state">{{ strtoupper($twinning->localConference->state) }}</p>
        @else
            <p class="text-muted" data-bind="local_conference_state">N/A</p>
        @endif

        <br />
        <label>Diocesan/Central Council</label>

        @if ($twinning->hasLocalConference())
            @if ($twinning->localConference->diocesanCouncil)
                <p data-bind="local_conference_diocesan_council">{{ $twinning->localConference->diocesanCouncil->name }}</p>
            @else
                <p class="text-muted" data-bind="local_conference_diocesan_council">N/A</p>
            @endif
        @else
            <p class="text-muted" data-bind="local_conference_diocesan_council">N/A</p>
        @endif
    </div>

    <div class="col-xs-12 col-sm-3 text-left">
        <label>Regional Council</label>

        @if ($twinning->hasLocalConference())
            <p data-bind="local_conference_regional_council">{{ $twinning->localConference->regional_council }}</p>
        @else
            <p class="text-muted" data-bind="local_conference_regional_council">N/A</p>
        @endif

        <br />
        <label>Parish</label>

        @if ($twinning->hasLocalConference())
            <p data-bind="local_conference_parish">{{ $twinning->localConference->parish }}</p>
        @else
            <p class="text-muted" data-bind="local_conference_parish">N/A</p>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <input type="hidden" name="local_conference_id" value="{{ optional($twinning->localConference)->id }}">
        </div>
    </div>
</div>

<hr class="mt-0">

<div class="row row-os-conf">
    <div class="col-xs-12 col-sm-3">
        <a href="#" class="btn btn-primary btn-block mb-2" data-toggle="modal" data-target="#modal-os-conf">Assign Overseas Conference</a>
    </div>
    <div class="col-xs-12 col-sm-3 text-left">
        <label>OS. SRN <sup class="text-danger">*</sup></label>

        @if ($twinning->hasOverseasConference())
            <p><a href="{{ route('overseas-conferences.edit', $twinning->overseasConference) }}" data-bind="overseas_conference_url" data-bind-attr="href"><span data-bind="overseas_conference_id">{{ $twinning->overseasConference->id }}</span></a></p>
        @else
            <p class="text-muted"><a href="#" data-bind="overseas_conference_url" data-bind-attr="href"></a><span data-bind="overseas_conference_id">N/A</span></p>
        @endif

        <br />
        <label>Overseas Conference Name <sup class="text-danger">*</sup></label>

        @if ($twinning->hasOverseasConference())
            <p><a href="{{ route('overseas-conferences.edit', $twinning->overseasConference) }}" data-bind="overseas_conference_url" data-bind-attr="href"><span data-bind="overseas_conference_name">{{ $twinning->overseasConference->name }}</span></a></p>
        @else
            <p class="text-muted"><a href="#" data-bind="overseas_conference_url" data-bind-attr="href"></a><span data-bind="overseas_conference_name">N/A</span></p>
        @endif
    </div>

    <div class="col-xs-12 col-sm-3 text-left">
        <label>Country</label>

        @if ($twinning->hasOverseasConference())
            <p data-bind="overseas_conference_country">{{ optional($twinning->overseasConference->country)->name }}</p>
        @else
            <p class="text-muted" data-bind="overseas_conference_country">N/A</p>
        @endif

        <br />
        <label>Particular Council</label>

        @if ($twinning->hasOverseasConference())
            <p data-bind="overseas_conference_particular_council">{{ $twinning->overseasConference->particular_council }}</p>
        @else
            <p class="text-muted" data-bind="overseas_conference_particular_council">N/A</p>
        @endif
    </div>
    <div class="col-xs-12 col-sm-3 text-left">
        <label>Central Council</label>

        @if ($twinning->hasOverseasConference())
            <p data-bind="overseas_conference_central_council">{{ $twinning->overseasConference->central_council }}</p>
        @else
            <p class="text-muted" data-bind="overseas_conference_central_council">N/A</p>
        @endif

        <br />
        <label>Parish</label>

        @if ($twinning->hasOverseasConference())
            <p data-bind="overseas_conference_parish">{{ $twinning->overseasConference->parish }}</p>
        @else
            <p class="text-muted" data-bind="overseas_conference_parish">N/A</p>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <input type="hidden" name="overseas_conference_id" value="{{ optional($twinning->overseasConference)->id }}">
        </div>
    </div>
</div>

<hr class="mt-0">

@if (Route::currentRouteName() != 'twinnings.edit')
    <div class="row">
        @if ($twinning->id !== null)
            @include('partials.comments', ['url' => route('twinnings.comments', $twinning)])
        @endif

        <div class="col-sm-12">
            <div class="form-group{{ $errors->has('comments') ? ' has-error' : '' }}">
                <label for="comments">Comments</label>
                @if ($currentUser->hasPermissionTo('update.twinnings'))
                    {!! Form::textarea('comments', null, ['class' => 'form-control form-control-text-danger js-stretch', 'id' => 'comments', 'rows' => null, 'cols' => null]) !!}
                @else
                    {!! Form::textarea('comments', null, ['class' => 'form-control form-control-text-danger js-stretch', 'id' => 'comments', 'readonly' => 'readonly', 'rows' => null, 'cols' => null]) !!}
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
