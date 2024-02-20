<h4 class="form-heading align-bottom-form-heading">General Australian Conference Information</h4>
<div class="row">
    @if (Route::currentRouteName() != 'local-conferences.create')
        <div class="col-sm-2 line-up-box">
            <div class="form-group">
                <label for="id">Australian Conference SRN (ID)</label>
                {!! Form::text('id', null, ['class' => 'form-control', 'id' => 'id', 'readonly' => 'readonly']) !!}
            </div>
        </div>
    @endif
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
            <label for="status">Australian Conf. Status <sup class="text-danger">*</sup></label>
            @if ($currentUser->canEditLocalConference($local_conference))
                {!! Form::select('status', ['active' => 'Active', 'abeyant' => 'Abeyant'], (Route::currentRouteName() == 'local-conferences.create' ? null : (optional($local_conference)->trashed() ? 'abeyant' : 'active')), ['class' => 'form-control', 'id' => 'status']) !!}
            @else
                {!! Form::select('status', ['active' => 'Active', 'abeyant' => 'Abeyant'], (Route::currentRouteName() == 'local-conferences.create' ? null : (optional($local_conference)->trashed() ? 'abeyant' : 'active')), ['class' => 'form-control', 'id' => 'status', 'disabled' => 'disabled']) !!}
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
            @if ($currentUser->canEditLocalConference($local_conference))
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
            @if ($currentUser->canEditLocalConference($local_conference))
                {!! Form::text('is_active_at', optional($local_conference->is_active_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'is_active_at']) !!}
            @else
                {!! Form::text('is_active_at', optional($local_conference->is_active_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'is_active_at', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('is_active_at'))
                <span class="help-block">
                   {{ $errors->first('is_active_at') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('is_active_at') ? ' has-error' : '' }}">
            <label for="is_abeyant_at">Date Became Abeyant</label>
            @if ($currentUser->canEditLocalConference($local_conference))
                {!! Form::text('is_abeyant_at', optional($local_conference->is_abeyant_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'is_abeyant_at']) !!}
            @else
                {!! Form::text('is_abeyant_at', optional($local_conference->is_abeyant_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'is_abeyant_at', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('is_abeyant_at'))
                <span class="help-block">
                   {{ $errors->first('is_abeyant_at') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2 line-up-box">
        <div class="form-group{{ $errors->has('last_confirmed_at') ? ' has-error' : '' }}">
            <label for="last_confirmed_at">Conf. Details Last Confirmed Date</label>
            @if ($currentUser->canEditLocalConference($local_conference))
                {!! Form::text('last_confirmed_at', optional($local_conference->last_confirmed_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker-disable-future', 'id' => 'last_confirmed_at']) !!}
            @else
                {!! Form::text('last_confirmed_at', optional($local_conference->last_confirmed_at)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker-disable-future', 'id' => 'last_confirmed_at', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('last_confirmed_at'))
                <span class="help-block">
                   {{ $errors->first('last_confirmed_at') }}
                </span>
            @endif
        </div>
    </div>
   
    @if (Route::currentRouteName() != 'local-conferences.create')
        <div class="col-sm-1">
    @else
        <div class="col-sm-1">
    @endif
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name" class="text-primary">Australian Conference Name <sup class="text-danger">*</sup></label>
            @if ($currentUser->canEditLocalConference($local_conference))
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

    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('parish') ? ' has-error' : '' }}">
            <label for="parish" class="text-primary">Parish</label>
            @if ($currentUser->canEditLocalConference($local_conference))
                {!! Form::text('parish', null, ['class' => 'form-control', 'id' => 'parish']) !!}
            @else
                {!! Form::text('parish', null, ['class' => 'form-control', 'id' => 'parish', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('parish'))
                <span class="help-block">
                   {{ $errors->first('parish') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-2">  
        <div class="form-group{{ $errors->has('cost_code') ? ' has-error' : '' }}">
            <label for="cost_code">S/T Cost Code</label>
            @if ($currentUser->canEditLocalConference($local_conference))
                {!! Form::text('cost_code', null, ['class' => 'form-control', 'id' => 'cost_code']) !!}
            @else
                {!! Form::text('cost_code', null, ['class' => 'form-control', 'readonly' => 'readonly', 'id' => 'cost_code']) !!}
            @endif

            @if ($errors->has('cost_code'))
                <span class="help-block">
                {{ $errors->first('cost_code') }}
                </span>
            @endif
        </div>
        
    </div>

    
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('is_flagged') ? ' has-error' : '' }}">
            <label for="is_flagged">Flagged? <sup class="text-danger">*</sup></label>
            @if ($currentUser->canEditLocalConference($local_conference))
                {!! Form::select('is_flagged', ['0' => 'No', '1' => 'Yes'], null, ['class' => 'form-control', 'id' => 'is_flagged']) !!}
            @else
                {!! Form::select('is_flagged', ['0' => 'No', '1' => 'Yes'], null, ['class' => 'form-control', 'id' => 'is_flagged', 'disabled' => 'disabled']) !!}
            @endif

            @if ($errors->has('is_flagged'))
                <span class="help-block">
                    {{ $errors->first('is_flagged') }}
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('contact_name') ? ' has-error' : '' }}">
                <label for="contact_name">Contact Name</label>
                @if ($currentUser->canEditLocalConference($local_conference))
                    {!! Form::text('contact_name', null, ['class' => 'form-control', 'id' => 'contact_name']) !!}
                @else
                    {!! Form::text('contact_name', null, ['class' => 'form-control', 'id' => 'contact_name', 'readonly' => 'readonly']) !!}
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
            @if ($currentUser->canEditLocalConference($local_conference))
                {!! Form::text('address_line_1', null, ['class' => 'form-control', 'id' => 'address_line_1']) !!}
            @else
                {!! Form::text('address_line_1', null, ['class' => 'form-control', 'id' => 'address_line_1', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('address_line_1'))
                <span class="help-block">
                   {{ $errors->first('address_line_1') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('state_council') ? ' has-error' : '' }}">
            <label for="state_council">State/Territory Council <sup class="text-danger">*</sup></label>
            @if ($currentUser->canEditLocalConference($local_conference))
                {!! Form::select('state_council', $states_all, null, ['class' => 'form-control', 'id' => 'state_council', 'placeholder' => 'Please select']) !!}
            @else
                {!! Form::select('state_council', $states_all, null, ['class' => 'form-control', 'id' => 'state_council', 'placeholder' => 'Please select', 'disabled' => 'disabled']) !!}
            @endif

            @if ($errors->has('state'))
                <span class="help-block">
                   {{ $errors->first('state') }}
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('contact_email') ? ' has-error' : '' }}">
                    <label for="contact_email">Contact Email</label>
                    @if ($currentUser->canEditLocalConference($local_conference))
                        {!! Form::text('contact_email', null, ['class' => 'form-control', 'id' => 'contact_email']) !!}
                    @else
                        {!! Form::text('contact_email', null, ['class' => 'form-control', 'id' => 'contact_email', 'readonly' => 'readonly']) !!}
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
            @if ($currentUser->canEditLocalConference($local_conference))
                {!! Form::text('address_line_2', null, ['class' => 'form-control', 'id' => 'address_line_2']) !!}
            @else
                {!! Form::text('address_line_2', null, ['class' => 'form-control', 'id' => 'address_line_2', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('address_line_2'))
                <span class="help-block">
                   {{ $errors->first('address_line_2') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('diocesan_council_id') ? ' has-error' : '' }}">
            <label for="diocesan_council_id">Diocesan/Central Council <sup class="text-danger">*</sup></label>
            @if ($currentUser->canEditLocalConference($local_conference))
                {!! Form::select('diocesan_council_id', $diocesan_councils, null, ['class' => 'form-control', 'id' => 'diocesan_council_id', 'placeholder' => 'Please select']) !!}
            @else
                {!! Form::select('diocesan_council_id', $diocesan_councils, null, ['class' => 'form-control', 'id' => 'diocesan_council_id', 'placeholder' => 'Please select', 'disabled' => 'disabled']) !!}
            @endif

            @if ($errors->has('diocesan_council_id'))
                <span class="help-block">
                   {{ $errors->first('diocesan_council_id') }}
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('contact_phone') ? ' has-error' : '' }}">
            <label for="contact_phone">Contact Phone</label>
            @php
            if (empty($local_conference->contact_phone) && !empty($local_conference->address_line_3)) { // set the contact phone from address_line_3
                $contact_phone = $local_conference->address_line_3;
            } else {
                $contact_phone = null;
            }
            @endphp
            @if ($currentUser->canEditLocalConference($local_conference))
                {!! Form::text('contact_phone', $contact_phone, ['class' => 'form-control', 'id' => 'contact_phone']) !!}
            @else
                {!! Form::text('contact_phone', $contact_phone, ['class' => 'form-control', 'id' => 'contact_phone', 'readonly' => 'readonly']) !!}
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
            @if ($currentUser->canEditLocalConference($local_conference))
                {!! Form::text('suburb', null, ['class' => 'form-control', 'id' => 'suburb']) !!}
            @else
                {!! Form::text('suburb', null, ['class' => 'form-control', 'id' => 'suburb', 'readonly' => 'readonly']) !!}
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
            @if ($currentUser->canEditLocalConference($local_conference))
                {!! Form::select('state', $states, null, ['class' => 'form-control', 'id' => 'state', 'placeholder' => 'Please select']) !!}
            @else
                {!! Form::select('state', $states, null, ['class' => 'form-control', 'id' => 'state', 'placeholder' => 'Please select', 'disabled' => 'disabled']) !!}
            @endif

            @if ($errors->has('state'))
                <span class="help-block">
                   {{ $errors->first('state') }}
                </span>
            @endif
        </div>
    </div>

    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('regional_council') ? ' has-error' : '' }}">
            <label for="regional_council">Regional Council</label>
            @if ($currentUser->canEditLocalConference($local_conference))
                {!! Form::text('regional_council', null, ['class' => 'form-control', 'id' => 'regional_council']) !!}
            @else
                {!! Form::text('regional_council', null, ['class' => 'form-control', 'id' => 'regional_council', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('regional_council'))
                <span class="help-block">
                   {{ $errors->first('regional_council') }}
                </span>
            @endif
        </div>
    </div>
</div>

{{-- <div class="row">
    <div class="col-sm-8">
        <div class="form-group{{ $errors->has('address_line_3') ? ' has-error' : '' }}">
            <label for="address_line_3">Address Line 3</label>
            @if ($currentUser->canEditLocalConference($local_conference))
                {!! Form::text('address_line_3', null, ['class' => 'form-control', 'id' => 'address_line_3']) !!}
            @else
                {!! Form::text('address_line_3', null, ['class' => 'form-control', 'id' => 'address_line_3', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('address_line_3'))
                <span class="help-block">
                   {{ $errors->first('address_line_3') }}
                </span>
            @endif
        </div>
    </div>
</div> --}}

<div class="row">
    <div class="col-sm-4"></div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('postcode') ? ' has-error' : '' }}">
            <label for="postcode">Postcode</label>
            @if ($currentUser->canEditLocalConference($local_conference))
                {!! Form::text('postcode', null, ['class' => 'form-control', 'id' => 'postcode']) !!}
            @else
                {!! Form::text('postcode', null, ['class' => 'form-control', 'id' => 'postcode', 'readonly' => 'readonly']) !!}
            @endif

            @if ($errors->has('postcode'))
                <span class="help-block">
                   {{ $errors->first('postcode') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('postcode') ? ' has-error' : '' }}">
            <label for="postcode">Country</label>
            @if ($currentUser->canEditLocalConference($local_conference))
            {!! Form::select('country', Helper:: getCountry(), $local_conferences->country ?? '', ['class' => 'form-control', 'id' => 'country']) !!}
            @else
            {!! Form::select('country', Helper:: getCountry(), $local_conferences->country ?? '', ['class' => 'form-control', 'disabled' => 'disabled', 'id' => 'country']) !!}
            @endif

            @if ($errors->has('postcode'))
                <span class="help-block">
                   {{ $errors->first('postcode') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-3"></div>
</div>

@if (Route::currentRouteName() != 'local-conferences.edit')
    <div class="row">
        @if ($local_conference->id !== null)
            @include('partials.comments', ['url' => route('local-conferences.comments', $local_conference)])
        @endif

        <div class="col-sm-12">
            <div class="form-group{{ $errors->has('comments') ? ' has-error' : '' }}">
                <label for="comments">Comments</label>
                @if ($currentUser->canEditLocalConference($local_conference))
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
