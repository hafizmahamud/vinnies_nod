@extends('layouts.app')

@section('title')
    Manage Resources
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12 intro">
                <h1 class="page-title text-center">Resources</h1>
                @include('flash::message')
                @include('partials.js-alert')

                <p>Click <a href="https://svdpnc.sharepoint.com/sites/NC-OPPGroup/SitePages/OPP-Resources.aspx" target="_blank">here</a> to access the Overseas Partnerships Program Resources page.</p>
                <p class="download"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <a href="{{ Helper::getDocUrl('guide') }}">Download NOD database guide in PDF format.</a></p>

            </div>
        </div>
    </div>
@stop