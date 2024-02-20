<h4 class="form-heading align-bottom-form-heading">General Overseas Conference Information</h4>

<div class="row">
@if (Route::currentRouteName() != 'overseas-conferences.create')
        <div class="col-sm-2 line-up-box">
            <div class="form-group">
                <label for="id">Overseas Conference SRN (ID)</label>
                {!! Form::text('id', null, ['class' => 'form-control', 'id' => 'id', 'readonly' => 'readonly']) !!}
            </div>
        </div>
   
@endif
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
            <label for="status">OS Conf. Status <sup class="text-danger">*</sup></label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::select('status', Helper::getOSConferencesStatus(), $overseas_conference->status ?? '', ['class' => 'form-control', 'id' => 'status']) !!}
            @else
                {!! Form::select('status', Helper::getOSConferencesStatus(), $overseas_conference->status ?? '', ['class' => 'form-control', 'disabled' => 'disabled', 'id' => 'status']) !!}
            @endif

            @if ($errors->has('status'))
                <span class="help-block">
                   {{ $errors->first('status') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('aggregation_number') ? ' has-error' : '' }}">
            <label for="aggregation_number">Aggregation No.</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('aggregation_number', null, ['class' => 'form-control', 'id' => 'aggregation_number']) !!}
            @else
                {!! Form::text('aggregation_number', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'aggregation_number']) !!}
            @endif

            @if ($errors->has('aggregation_number'))
                <span class="help-block">
                {{ $errors->first('aggregation_number') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('is_active_at') ? ' has-error' : '' }}">
            <label for="is_active_at">Aggregation Date</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('is_active_at', optional($overseas_conference->is_active_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'is_active_at']) !!}
            @else
                {!! Form::text('is_active_at', optional($overseas_conference->is_active_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'is_active_at', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('is_active_at'))
                <span class="help-block">
                   {{ $errors->first('is_active_at') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('is_abeyant_at') ? ' has-error' : '' }}">
            <label for="is_abeyant_at">Date Became Abeyant</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('is_abeyant_at', optional($overseas_conference->is_abeyant_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'is_abeyant_at']) !!}
            @else
                {!! Form::text('is_abeyant_at', optional($overseas_conference->is_abeyant_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'is_abeyant_at', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('is_abeyant_at'))
                <span class="help-block">
                   {{ $errors->first('is_abeyant_at') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-2 line-up-box">
        <div class="form-group{{ $errors->has('confirmed_date_at') ? ' has-error' : '' }}">
            <label for="confirmed_date_at">Conf. Details Last Confirmed Date</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('confirmed_date_at', optional($overseas_conference->confirmed_date_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'confirmed_date_at']) !!}
            @else
                {!! Form::text('confirmed_date_at', optional($overseas_conference->confirmed_date_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'confirmed_date_at', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('confirmed_date_at'))
                <span class="help-block">
                   {{ $errors->first('confirmed_date_at') }}
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name" class="text-primary">Overseas Conference Name <sup class="text-danger">*</sup></label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('name', null, ['class' => 'form-control', 'id' => 'name']) !!}
            @else
                {!! Form::text('name', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'name']) !!}
            @endif

            @if ($errors->has('name'))
                <span class="help-block">
                {{ $errors->first('name') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('parish') ? ' has-error' : '' }}">
            <label for="parish" class="text-primary">Parish</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('parish', null, ['class' => 'form-control', 'id' => 'parish']) !!}
            @else
                {!! Form::text('parish', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'parish']) !!}
            @endif

            @if ($errors->has('parish'))
                <span class="help-block">
                   {{ $errors->first('parish') }}
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('contact_name') ? ' has-error' : '' }}">
            <label for="contact_name">Contact Name</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('contact_name', null, ['class' => 'form-control', 'id' => 'contact_name']) !!}
            @else
                {!! Form::text('contact_name', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'contact_name']) !!}
            @endif

            @if ($errors->has('contact_name'))
                <span class="help-block">
                   {{ $errors->first('contact_name') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('address_line_1') ? ' has-error' : '' }}">
            <label for="address_line_1">Address Line 1</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('address_line_1', null, ['class' => 'form-control', 'id' => 'address_line_1']) !!}
            @else
                {!! Form::text('address_line_1', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'address_line_1']) !!}
            @endif

            @if ($errors->has('address_line_1'))
                <span class="help-block">
                   {{ $errors->first('address_line_1') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('national_council') ? ' has-error' : '' }}">
            <label for="national_council">National/Superior Council</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::select('national_council', $national_council, null, ['class' => 'form-control',  'id' => 'national_council', 'placeholder' => 'Please select']) !!}
            @else
                {!! Form::select('national_council', $national_council, null, ['class' => 'form-control', 'id' => 'national_council', 'placeholder' => 'Please select', 'disabled' => 'disabled']) !!}
            @endif

            @if ($errors->has('national_council'))
                <span class="help-block">
                   {{ $errors->first('national_council') }}
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('contact_email') ? ' has-error' : '' }}">
            <label for="contact_email">Contact Email</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('contact_email', null, ['class' => 'form-control', 'id' => 'contact_email']) !!}
            @else
                {!! Form::text('contact_email', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'contact_email']) !!}
            @endif

            @if ($errors->has('contact_email'))
                <span class="help-block">
                   {{ $errors->first('contact_email') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('address_line_2') ? ' has-error' : '' }}">
            <label for="address_line_2">Address Line 2</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('address_line_2', null, ['class' => 'form-control', 'id' => 'address_line_2']) !!}
            @else
                {!! Form::text('address_line_2', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'address_line_2']) !!}
            @endif

            @if ($errors->has('address_line_2'))
                <span class="help-block">
                   {{ $errors->first('address_line_2') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('central_council') ? ' has-error' : '' }}">
            <label for="central_council">Central Council</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
              {!! Form::text('central_council', null, ['class' => 'form-control', 'id' => 'central_council']) !!}
            @else
              {!! Form::text('central_council', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'central_council']) !!}
            @endif

            @if ($errors->has('central_council'))
                <span class="help-block">
                   {{ $errors->first('central_council') }}
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('contact_phone') ? ' has-error' : '' }}">
            <label for="contact_phone">Contact Phone</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('contact_phone', null, ['class' => 'form-control', 'id' => 'contact_phone']) !!}
            @else
                {!! Form::text('contact_phone', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'contact_phone']) !!}
            @endif

            @if ($errors->has('contact_phone'))
                <span class="help-block">
                   {{ $errors->first('contact_phone') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('suburb') ? ' has-error' : '' }}">
            <label for="suburb">Suburb</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('suburb', null, ['class' => 'form-control', 'id' => 'suburb']) !!}
            @else
                {!! Form::text('suburb', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'suburb']) !!}
            @endif

            @if ($errors->has('suburb'))
                <span class="help-block">
                   {{ $errors->first('suburb') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('state') ? ' has-error' : '' }}">
            <label for="state">State</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('state', null, ['class' => 'form-control', 'id' => 'state']) !!}
            @else
                {!! Form::text('state', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'state']) !!}
            @endif

            @if ($errors->has('state'))
                <span class="help-block">
                   {{ $errors->first('state') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('particular_council') ? ' has-error' : '' }}">
            <label for="particular_council">Particular Council</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('particular_council', null, ['class' => 'form-control', 'id' => 'particular_council']) !!}
            @else
                {!! Form::text('particular_council', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'particular_council']) !!}
            @endif

            @if ($errors->has('particular_council'))
                <span class="help-block">
                   {{ $errors->first('particular_council') }}
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-2">
    </div>

    <div class="col-sm-2 col-sm-offset-2">
        <div class="form-group{{ $errors->has('postcode') ? ' has-error' : '' }}">
            <label for="postcode">Postcode</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('postcode', null, ['class' => 'form-control', 'id' => 'postcode']) !!}
            @else
                {!! Form::text('postcode', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'postcode']) !!}
            @endif

            @if ($errors->has('postcode'))
                <span class="help-block">
                   {{ $errors->first('postcode') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('country_id') ? ' has-error' : '' }}">
            <label for="country_id">Country <sup class="text-danger">*</sup></label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::select('country_id', $countries, null, ['class' => 'form-control', 'id' => 'country_id', 'placeholder' => 'Please select']) !!}
            @else
                {!! Form::select('country_id', $countries, null, ['class' => 'form-control', 'disabled' => 'disabled', 'id' => 'country_id', 'placeholder' => 'Please select']) !!}
            @endif

            @if ($errors->has('country_id'))
                <span class="help-block">
                   {{ $errors->first('country_id') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-2">
    </div>
</div>


<h4 class="form-heading">Overseas Conference Twinning Information</h4>
<div class="row">
    <div class="col-sm-2 line-up-box">
        <div class="form-group{{ $errors->has('twinning_status') ? ' has-error' : '' }}">
            <label for="twinning_status">OS Conf. Twinning Status<sup class="text-danger">*</sup></label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::select('twinning_status', Helper::getOSConferencesTwinningStatuses(), $overseas_conference->twinning_status ?? '', ['class' => 'form-control', 'id' => 'twinning_status']) !!}
            @else
                {!! Form::select('twinning_status', Helper::getOSConferencesTwinningStatuses(), $overseas_conference->twinning_status ?? '', ['class' => 'form-control', 'disabled' => 'disabled', 'id' => 'twinning_status']) !!}
            @endif

            @if ($errors->has('twinning_status'))
                <span class="help-block">
                   {{ $errors->first('twinning_status') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('twinned_at') ? ' has-error' : '' }}">
            <label for="twinned_at">Date Twinned</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('twinned_at', optional($overseas_conference->twinned_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'twinned_at']) !!} 
            @else
                {!! Form::text('twinned_at', optional($overseas_conference->twinned_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'twinned_at', 'readonly' => 'readonly']) !!} 
            @endif

            @if ($errors->has('twinned_at'))
                <span class="help-block">
                   {{ $errors->first('twinned_at') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-2 line-up-box">
        <div class="form-group{{ $errors->has('is_in_status_check') ? ' has-error' : '' }}">
            <label for="is_in_status_check">Currently in Status Check?</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
            <select class="form-control {{ ($overseas_conference->is_in_status_check == 1) ? 'status-checked' : 'status-not-checked' }}" name="is_in_status_check" id="is_in_status_check" onchange="setColor2()">
                <option class="status-checked" value="1" @selected($overseas_conference->is_in_status_check == '1')>Yes</option>
                <option class="status-not-checked" value="0" @selected($overseas_conference->is_in_status_check == '0')>No</option>
            </select>
                <!-- {!! Form::select('is_in_status_check', Helper::getSelect(), null, ['onchange' => 'changeColor(this)', 'class' => 'form-control', 'id' => 'is_in_status_check']) !!} -->
            @else
                {!! Form::select('is_in_status_check', ['0' => 'No', '1' => 'Yes'], null, ['class' => 'form-control', 'disabled' => 'disabled', 'id' => 'is_in_status_check']) !!}
            @endif

            @if ($errors->has('is_in_status_check'))
                <span class="help-block">
                   {{ $errors->first('is_in_status_check') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('status_check_reason') ? ' has-error' : '' }}">
            <label for="status_check_reason">Reason for Status Check</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::select('status_check_reason', Helper::getOSConferencesStatusCheckReason(), $overseas_conference->status_check_reason ?? '', ['class' => 'form-control', 'id' => 'status_check_reason', 'placeholder' => 'Please select']) !!}
            @else
                {!! Form::select('status_check_reason', Helper::getOSConferencesStatusCheckReason(), $overseas_conference->status_check_reason ?? '', ['class' => 'form-control', 'disabled' => 'disabled', 'id' => 'status_check_reason', 'placeholder' => 'Please select']) !!}
            @endif

            @if ($errors->has('status_check_reason'))
                <span class="help-block">
                   {{ $errors->first('status_check_reason') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('status_check_initiated_at') ? ' has-error' : '' }}">
            <label for="status_check_initiated_at" class="line-up-box">Date Current/Last Status Check Initiated</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('status_check_initiated_at', optional($overseas_conference->status_check_initiated_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'status_check_initiated_at']) !!}
            @else
                {!! Form::text('status_check_initiated_at', optional($overseas_conference->status_check_initiated_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'status_check_initiated_at', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('status_check_initiated_at'))
                <span class="help-block">
                   {{ $errors->first('status_check_initiated_at') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('last_status_check_initiated') ? ' has-error' : '' }}">
            <label for="last_status_check_initiated" class="line-up-box">Qtr Current/Last Status Check Initiated</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('last_status_check_initiated', null, ['class' => 'form-control', 'id' => 'last_status_check_initiated']) !!}
            @else
                {!! Form::text('last_status_check_initiated', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'last_status_check_initiated']) !!}
            @endif

            @if ($errors->has('last_status_check_initiated'))
                <span class="help-block">
                   {{ $errors->first('last_status_check_initiated') }}
                </span>
            @endif
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('is_active') ? ' has-error' : '' }}">
            <label for="is_active">Receiving Remittances?<sup class="text-danger">*</sup></label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::select('is_active', ['' => 'Please select', '0' => 'No Remittances', '1' => 'Remittances'], null, ['class' => 'form-control', 'id' => 'is_active']) !!}
            @else
                {!! Form::select('is_active', ['' => 'Please select', '0' => 'No Remittances', '1' => 'Remittances'], null, ['class' => 'form-control', 'disabled' => 'disabled', 'id' => 'is_active']) !!}
            @endif

            @if ($errors->has('is_active'))
                <span class="help-block">
                   {{ $errors->first('is_active') }}
                </span>
            @endif
        </div>
    </div>
   
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('untwinned_at') ? ' has-error' : '' }}">
            <label for="untwinned_at">Date Untwinned</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('untwinned_at', optional($overseas_conference->untwinned_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'untwinned_at']) !!}
            @else
                {!! Form::text('untwinned_at', optional($overseas_conference->untwinned_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'untwinned_at', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('untwinned_at'))
                <span class="help-block">
                   {{ $errors->first('untwinned_at') }}
                </span>
            @endif
        </div>
    </div>
    
    <div class="col-sm-2 line-up-box">
        <div class="form-group{{ $errors->has('is_in_surrendering') ? ' has-error' : '' }}">
            <label for="is_in_surrendering">Currently in Surrendering?</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
            <select class="form-control {{ ($overseas_conference->is_in_surrendering == 1) ? 'surrendering' : 'not-surrender' }}" name="is_in_surrendering" id="is_in_surrendering" onchange="setColor()">
                <option class="surrendering" value="1" @selected($overseas_conference->is_in_surrendering == '1')>Yes</option>
                <option class="not-surrender" value="0" @selected($overseas_conference->is_in_surrendering == '0')>No</option>
            </select>
               <!-- {!! Form::select('is_in_surrendering', ['0' => 'No', '1' => 'Yes'], null, ['onchange' => 'changeColor(this)', 'class' => 'form-control', 'id' => 'is_in_surrendering']) !!} -->
            @else
                    {!! Form::select('is_in_surrendering', ['0' => 'No', '1' => 'Yes'], null, ['class' => 'form-control', 'disabled' => 'disabled', 'id' => 'is_in_surrendering']) !!}
            @endif

            @if ($errors->has('is_in_surrendering'))
                <span class="help-block">
                   {{ $errors->first('is_in_surrendering') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('surrendering_initiated_at') ? ' has-error' : '' }}">
            <label for="surrendering_initiated_at" class="line-up-box">Date Current/Last Surrendering Initiated</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('surrendering_initiated_at', optional($overseas_conference->surrendering_initiated_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'surrendering_initiated_at']) !!}
            @else
                {!! Form::text('surrendering_initiated_at', optional($overseas_conference->surrendering_initiated_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'surrendering_initiated_at', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('surrendering_initiated_at'))
                <span class="help-block">
                   {{ $errors->first('surrendering_initiated_at') }}
                </span>
            @endif
        </div>
    </div>
    
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('surrendering_deadline_at') ? ' has-error' : '' }}">
            <label for="surrendering_deadline_at">Surrendering Deadline</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('surrendering_deadline_at', optional($overseas_conference->surrendering_deadline_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'surrendering_deadline_at']) !!}
            @else
                {!! Form::text('surrendering_deadline_at', optional($overseas_conference->surrendering_deadline_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'surrendering_deadline_at', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('surrendering_deadline_at'))
                <span class="help-block">
                   {{ $errors->first('surrendering_deadline_at') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-2 line-up-box">
        <div class="form-group{{ $errors->has('final_remittance') ? ' has-error' : '' }}">
            <label for="final_remittance">Qtr of Surrender/Final Remittance</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('final_remittance', null, ['class' => 'form-control', 'id' => 'final_remittance']) !!}
            @else
                {!! Form::text('final_remittance', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'final_remittance']) !!}
            @endif

            @if ($errors->has('final_remittance'))
                <span class="help-block">
                   {{ $errors->first('final_remittance') }}
                </span>
            @endif
        </div>
    </div>
</div>


{{-- <div class="row">
    <div class="col-sm-8">
        <div class="form-group{{ $errors->has('address_line_3') ? ' has-error' : '' }}">
            <label for="address_line_3">Address Line 3</label>
            @if ($currentUser->canEditOverseasConference($overseas_conference))
                {!! Form::text('address_line_3', null, ['class' => 'form-control', 'id' => 'address_line_3']) !!}
            @else
                {!! Form::text('address_line_3', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'address_line_3']) !!}
            @endif

            @if ($errors->has('address_line_3'))
                <span class="help-block">
                   {{ $errors->first('address_line_3') }}
                </span>
            @endif
        </div>
    </div>
</div>--}}

@if (Route::currentRouteName() != 'overseas-conferences.edit')
    <div class="row">
        @if ($overseas_conference->id !== null)
            @include('partials.comments', ['url' => route('overseas-conferences.comments', $overseas_conference)])
        @endif

        <div class="col-sm-12">
            <div class="form-group{{ $errors->has('comments') ? ' has-error' : '' }}">
                <label for="comments">Comments</label>
                @if ($currentUser->canEditOverseasConference($overseas_conference))
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

@section('scripts')

<script>
function setColor()
{
    $value = document.getElementById("is_in_surrendering").value;

    if ( $value == 1) {
        document.getElementById("is_in_surrendering").style.color = "red";
    }else{
        document.getElementById("is_in_surrendering").style.color = "black";
    }

}

function setColor2()
{
    $value = document.getElementById("is_in_status_check").value;

    if ( $value == 1) {
        document.getElementById("is_in_status_check").style.color = "red";
    }else{
        document.getElementById("is_in_status_check").style.color = "black";
    }

}

</script>
@stop