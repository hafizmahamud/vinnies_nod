@extends('layouts.app')

@section('title')
    Add New Remittance-In
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-title text-center">Add New Remittance-In</h1>
                @include('flash::message')
                @include('partials.js-alert')
                <p><sup class="text-danger">*</sup> All fields marked with a red asterisk are required.</p>
                <p><em><strong>NB:</strong> The fields marked in gray are going to be automatically generated by the system.</em></p>
                <hr>
            </div>
        </div>

        {!! Form::open(['route' => 'new-remittances.create', 'class' => 'form js-form', 'data-reset' => 1]) !!}
            @include('remittances.new.form')

            <div class="row">
                <div class="col-sm-6">
                    <button type="submit" class="btn btn-warning" data-text-default="Submit New Quarterly remittance for selected State/Territory" data-text-progress="Submitting...">Submit New Quarterly remittance for selected State/Territory</button>
                </div>
            </div>
        </form>

        <hr>

        <h4 class="form-heading">{{ RemittanceType::PROJECT }}</h4>
        <p class="text-warning">No {{ RemittanceType::PROJECT }} added yet. Following submission/creation of the new remmitance-in you will be able to add {{ RemittanceType::PROJECT }}.</p>

        <hr>

        <h4 class="form-heading">{{ RemittanceType::TWINNING }}</h4>
        <p class="text-warning">No {{ RemittanceType::TWINNING }} added yet. Following submission/creation of the new remmitance-in you will be able to add {{ RemittanceType::TWINNING }}.</p>

        <hr>

        <h4 class="form-heading">{{ RemittanceType::GRANT }}</h4>
        <p class="text-warning">No {{ RemittanceType::GRANT }} added yet. Following submission/creation of the new remmitance-in you will be able to add {{ RemittanceType::GRANT }}.</p>

        <hr>

        <h4 class="form-heading">{{ RemittanceType::COUNCIL }}</h4>
        <p class="text-warning">No {{ RemittanceType::COUNCIL }} added yet. Following submission/creation of the new remmitance-in you will be able to add {{ RemittanceType::COUNCIL }}.</p>

    </div>
@stop
