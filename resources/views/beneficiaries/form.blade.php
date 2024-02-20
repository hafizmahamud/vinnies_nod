<h4 class="form-heading">General Beneficiary Information</h4>
@if (Route::currentRouteName() != 'beneficiaries.create')
    <div class="row">
        <div class="col-sm-2">
            <div class="form-group">
                <label for="id">Beneficiary ID</label>
                {!! Form::text('id', null, ['class' => 'form-control', 'id' => 'id', 'readonly' => 'readonly']) !!}
            </div>
        </div>
    </div>
@endif

<div class="row">
    <div class="col-sm-8">
        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label for="name">Beneficiary Name <sup class="text-danger">*</sup></label>
            {!! Form::text('name', null, ['class' => 'form-control', 'id' => 'name']) !!}

            @if ($errors->has('name'))
                <span class="help-block">
                   {{ $errors->first('name') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('country_id') ? ' has-error' : '' }}">
            <label for="country_id">Country <sup class="text-danger">*</sup></label>
            {!! Form::select('country_id', $countries, null, ['class' => 'form-control', 'id' => 'country_id', 'placeholder' => 'Please select']) !!}

            @if ($errors->has('country_id'))
                <span class="help-block">
                   {{ $errors->first('country_id') }}
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('contact_title') ? ' has-error' : '' }}">
            <label for="contact_title">Contact Title</label>
            {!! Form::text('contact_title', null, ['class' => 'form-control', 'id' => 'contact_title']) !!}

            @if ($errors->has('contact_title'))
                <span class="help-block">
                   {{ $errors->first('contact_title') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('contact_first_name') ? ' has-error' : '' }}">
            <label for="contact_first_name">Contact First Name</label>
            {!! Form::text('contact_first_name', null, ['class' => 'form-control', 'id' => 'contact_first_name']) !!}

            @if ($errors->has('contact_first_name'))
                <span class="help-block">
                   {{ $errors->first('contact_first_name') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('contact_last_name') ? ' has-error' : '' }}">
            <label for="contact_last_name">Contact Last Name</label>
            {!! Form::text('contact_last_name', null, ['class' => 'form-control', 'id' => 'contact_last_name']) !!}

            @if ($errors->has('contact_last_name'))
                <span class="help-block">
                   {{ $errors->first('contact_last_name') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('contact_preferred_name') ? ' has-error' : '' }}">
            <label for="contact_preferred_name">Contact Preferred Name</label>
            {!! Form::text('contact_preferred_name', null, ['class' => 'form-control', 'id' => 'contact_preferred_name']) !!}

            @if ($errors->has('contact_preferred_name'))
                <span class="help-block">
                   {{ $errors->first('contact_preferred_name') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label for="email">Email</label>
            {!! Form::text('email', null, ['class' => 'form-control', 'id' => 'email']) !!}

            @if ($errors->has('email'))
                <span class="help-block">
                   {{ $errors->first('email') }}
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-8">
        <div class="form-group{{ $errors->has('address_line_1') ? ' has-error' : '' }}">
            <label for="address_line_1">Address Line 1</label>
            {!! Form::text('address_line_1', null, ['class' => 'form-control', 'id' => 'address_line_1']) !!}

            @if ($errors->has('address_line_1'))
                <span class="help-block">
                   {{ $errors->first('address_line_1') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('suburb') ? ' has-error' : '' }}">
            <label for="suburb">Suburb</label>
            {!! Form::text('suburb', null, ['class' => 'form-control', 'id' => 'suburb']) !!}

            @if ($errors->has('suburb'))
                <span class="help-block">
                   {{ $errors->first('suburb') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('contact_position') ? ' has-error' : '' }}">
            <label for="contact_position">Contact Position</label>
            {!! Form::text('contact_position', null, ['class' => 'form-control', 'id' => 'contact_position']) !!}

            @if ($errors->has('contact_position'))
                <span class="help-block">
                   {{ $errors->first('contact_position') }}
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-8">
        <div class="form-group{{ $errors->has('address_line_2') ? ' has-error' : '' }}">
            <label for="address_line_2">Address Line 2</label>
            {!! Form::text('address_line_2', null, ['class' => 'form-control', 'id' => 'address_line_2']) !!}

            @if ($errors->has('address_line_2'))
                <span class="help-block">
                   {{ $errors->first('address_line_2') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('state') ? ' has-error' : '' }}">
            <label for="state">State</label>
            {!! Form::text('state', null, ['class' => 'form-control', 'id' => 'state']) !!}

            @if ($errors->has('state'))
                <span class="help-block">
                   {{ $errors->first('state') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
            <label for="phone">Phone</label>
            {!! Form::text('phone', null, ['class' => 'form-control', 'id' => 'phone']) !!}

            @if ($errors->has('phone'))
                <span class="help-block">
                   {{ $errors->first('phone') }}
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-8">
        <div class="form-group{{ $errors->has('address_line_3') ? ' has-error' : '' }}">
            <label for="address_line_3">Address Line 3</label>
            {!! Form::text('address_line_3', null, ['class' => 'form-control', 'id' => 'address_line_3']) !!}

            @if ($errors->has('address_line_3'))
                <span class="help-block">
                   {{ $errors->first('address_line_3') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('postcode') ? ' has-error' : '' }}">
            <label for="postcode">Post Code</label>
            {!! Form::text('postcode', null, ['class' => 'form-control', 'id' => 'postcode']) !!}

            @if ($errors->has('postcode'))
                <span class="help-block">
                   {{ $errors->first('postcode') }}
                </span>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group{{ $errors->has('fax') ? ' has-error' : '' }}">
            <label for="fax">Fax</label>
            {!! Form::text('fax', null, ['class' => 'form-control', 'id' => 'fax']) !!}

            @if ($errors->has('fax'))
                <span class="help-block">
                   {{ $errors->first('fax') }}
                </span>
            @endif
        </div>
    </div>
</div>

@if (Route::currentRouteName() != 'beneficiaries.edit')
    <div class="row">
        @if ($beneficiary->id !== null)
            @include('partials.comments', ['url' => route('beneficiaries.comments', $beneficiary)])
        @endif

        <div class="col-sm-12">
            <div class="form-group{{ $errors->has('comments') ? ' has-error' : '' }}">
                <label for="comments">Comments</label>
                {!! Form::textarea('comments', null, ['class' => 'form-control form-control-text-danger js-stretch', 'id' => 'comments', 'rows' => null, 'cols' => null]) !!}

                @if ($errors->has('comments'))
                    <span class="help-block">
                       {{ $errors->first('comments') }}
                    </span>
                @endif
            </div>
        </div>
    </div>
@endif
