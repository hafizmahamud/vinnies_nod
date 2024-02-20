@extends('layouts.app')

@section('title')
    Reports
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12 intro">
                <h1 class="page-title text-center">Yearly Reports</h1>
                @include('flash::message')
                @include('partials.js-alert')
                <p>In here you can generate remittance reports for each Overseas Council/Country.</p>
                <p>Select the Quarter and Year and download the corresponding Excel file that needs to be sent to each Overseas National Council.</p>
                <p class="download"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> <a href="{{ Helper::getDocUrl('guide') }}">Download NOD database guide in PDF format.</a></p>

                <div class="section">
                    <form id="yearlyReports" class="form-inline form-table-filter js-table-filter yearly-reports" action="{{ route('reports.downloadYearly', [date('Y'), 1, 1])}}" method="put">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="year">Financial Year {{$selected_year}}</label>
                            <select name="year" id="year" class="form-control">
                                <option value="">Please select</option>
                                @php
                                  $selected_year ? $selected_year = $selected_year : $selected_year = date('Y');
                                @endphp

                                @foreach (range(2017, date('Y')) as $year)
                                    @if (old('year', $selected_year) == $year)
                                        <option value="{{ $year }}" selected>{{ $year - 1 }} - {{ $year }}</option>
                                    @else
                                        <option value="{{ $year }}">{{ $year - 1 }} - {{ $year }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <button id="btnSubmit" type="submit" class="btn btn-warning" data-text-progress="Applying..." data-text-default="Apply Filters" value="{{ date('Y') }}">Download Financial Year</button>
                    </form>
                </div>

                @php
                $now    = new DateTime;
                $before = clone $now;
                $before = $before->modify( '-3 month' );
                @endphp

                <div class="section">
                    <form id="dateRangeReports" class="form-inline form-table-filter js-table-filter date-range-reports" action="{{ route('reports.downloadDateRange', [$before->format( 'Y-m-d' ), $now->format( 'Y-m-d' )])}}" method="put">
                        {{ csrf_field() }}
                        <div class="form-group">
                          <label for="quarter">Date <sup class="text-danger">*</sup></label>
                          {!! Form::select('date_type', ['date' => 'Received', 'approved_at' => 'Approved'], null, ['class' => 'form-control', 'id' => 'date_type']) !!}

                          <label for="date_start">Date Start <sup class="text-danger">*</sup></label>

                          {!! Form::text('date_start', optional($before)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'date_start']) !!}

                          @if ($errors->has('date_start'))
                            <span class="help-block">
                              {{ $errors->first('date_start') }}
                            </span>
                          @endif

                          <label for="date_end">Date End <sup class="text-danger">*</sup></label>
                          {!! Form::text('date_end', optional($now)->format(config('vinnies.date_format')), ['class' => 'form-control js-datepicker', 'id' => 'date_end']) !!}

                          @if ($errors->has('date_end'))
                            <span class="help-block">
                              {{ $errors->first('date_end') }}
                            </span>
                          @endif

                        </div>

                        <button id="btnSubmit" type="submit" class="btn btn-warning" data-text-progress="Applying..." data-text-default="Apply Filters">Download Calendar Year</button>
                    </form>
                </div>

            </div>
        </div>

    </div>
@stop
