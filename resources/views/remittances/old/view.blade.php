@extends('layouts.app')

@section('title')
    Old Remittance-In View
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-title text-center">Old Remittance-In View</h1>
            </div>
        </div>

        {!! Form::model($remittance, ['url' => '#', 'class' => 'form']) !!}
            @include('remittances.old.form')
        </form>

        <hr>

        @include('remittances.old.donations')

    </div>
@stop
