@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('status'))
        <div class="alert alert-success mb-3">{{ session('status') }}</div>
    @endif

    <div class="row">
        <div class="col-sm-12">
            <div class="intro">
                <h1 class="page-title text-center">{{ config('app.name') }} System</h1>
                <p>Welcome to the new {{ config('app.name') }} System that allows you to easily maintain and manage Projects, Conferences, Twinnings and related Quarterly Remittances.</p>
                <p>Please take time to read the guide below.</p>
                <p class="download"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <strong><a href="{{ Helper::getDocUrl('guide') }}">Download NOD database guide in PDF format.</a></strong></p>
            </ul>
        </div>
    </div>
</div>
@endsection
