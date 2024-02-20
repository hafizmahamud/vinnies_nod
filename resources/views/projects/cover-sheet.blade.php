<style>
@page {
    margin: 25px 100px 50px;
}

.page-break {
    page-break-after: always;
}

.text-center {
    text-align: center;
}

.text-right {
    text-align: right;
}

.text-underline {
    text-decoration: underline;
}

.float-right {
    float: right;
    text-align: right;
}

.table td,
.table th {
    font-size: 13px;
}

h1,
h2,
h3,
h4,
h5,
h6,
p,
ul,
ol,
td,
th {
    font-family: Arial, sans-serif;
}

th,
td {
    border: 1px solid #222;
    padding: 10px;
}

table {
    border-collapse: collapse;
    margin-top: 25px;
}

th,w
td,
p {
    font-size: 14px;
}

.table-fixed {
    table-layout: fixed;
    width: 100%;
}

.table-fixed td,
.table-fixed th {
    font-size: 14px;
    padding: 0;
    border: none;
}

.padding-left {
    padding-left: 25px;
}

.footer {
    bottom: 0;
    left: 0;
    position: fixed;
    right: 0;
}

.counter:before { content: counter(page); }
</style>

<p class="text-center">
    <img src="{{ storage_path('img/cover-sheet-logo.jpg')}}" height="109" width="500">
</p>

<h2 class="text-center text-underline">Project Criteria</h2>

<p class="text-center">This project application meets the following criteria</p>
<p>Country: {{ $project->beneficiary->country->name }} <span class="float-right">Project ID: {{ $project->id }}</span></p>

<table class="table">
    <thead>
        <tr>
            <th class="text-center" colspan="2">Criteria</th>
            <th class="text-center">Yes</th>
            <th class="text-center">No</th>
            <th class="text-center">Not specified</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2">Has the Project been initiated by a local Vincentian who understands the needs of the community?</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td rowspan="2">Is the Project Application marked as either a “Development” or “Welfare” Project?</td>
            <td>Development</td>
            <td></td>
            <td rowspan="2"></td>
            <td rowspan="2"></td>
        </tr>
        <tr>
            <td>Welfare</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2">Does the Project encourage community self-help by ensuring that the local people participate in all aspects of the project?</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2">Does the Project provide resources to needy groups?</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2">Does the Project have clear, economic and financially viable goals set?</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2">Has a realistic timetable for implementation been set?</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2">Does the Project encourage accountability and good management?</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2">Does the Project respect human rights?</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2">Does the Project complement the developmental needs of the recipient country?</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="2">Does the Project encourage sound environmental and ecological practices?</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>

<br>
<p>The estimated cost of this Project is approximately ${{ number_format($project->au_value->value(), 2) }} (Australian)</p>
<p>This Project has been approved by the National Projects Officer ______________________</p>

<p class="footer">Project {{ $project->id }} Cover Sheet <span class="float-right">Page <span class="counter"></span></span></p>

<div class="page-break"></div>

<h2 class="text-underline">Detailed Project Info:</h2>

<table class="table-fixed">
    <tbody>
        <tr>
            <td><strong>Project ID:</strong> {{ $project->id }}</td>
            <td class="text-right"><strong>Date Received:</strong> {{ $project->received_at->format('d-F-Y') }}</td>
        </tr>
    </tbody>
</table>
<p><strong>Overseas Project ID:</strong> {{ $project->overseas_project_id ? $project->overseas_project_id : 'N/A' }}</p>
<p><strong>Project Name:</strong> {{ $project->name }}</p>
<p><strong>Beneficiary:</strong> {{ $project->beneficiary->name }}</p>

@if ($project->hasOverseasConference())
    <p><strong>Overseas Conference:</strong></p>

    <p class="padding-left">
        <strong>SRN: </strong>{{ $project->overseasConference->id }}<br>
        <strong>Name: </strong>{{ $project->overseasConference->name }}<br>
        <strong>Central Council: </strong>{{ $project->overseasConference->central_council ? $project->overseasConference->central_council : 'N/A' }}<br>
        <strong>Particular Council: </strong>{{ $project->overseasConference->particular_council ? $project->overseasConference->particular_council : 'N/A' }}<br>
        <strong>Parish: </strong>{{ $project->overseasConference->parish ? $project->overseasConference->parish : 'N/A' }}<br>
    </p>
@else
    <p><strong>Overseas Conference:</strong> N/A</p>
@endif

@if ($project->donors->isNotEmpty())
    <p><strong>Donor(s):</strong></p>

    @foreach ($project->donors as $donor)
        <p class="padding-left">
            <strong>SRN: </strong>{{ $donor->localConference->id }}<br>
            <strong>Name: </strong>{{ $donor->localConference->name }}<br>
            <strong>State Council: </strong>{{ $donor->localConference->state ?strtoupper($donor->localConference->state) : 'N/A' }}<br>
            <strong>Regional Council: </strong>{{ $donor->localConference->regional_council ? $donor->localConference->regional_council : 'N/A' }}<br>
            <strong>Diocesan/Central Council: </strong>{{ $donor->localConference->diocesanCouncil ? $donor->localConference->diocesanCouncil->name : 'N/A' }}<br>
            <strong>Parish: </strong>{{ $donor->localConference->parish ? $donor->localConference->parish : 'N/A' }}<br>
        </p>
    @endforeach
@else
    <p><strong>Donor(s):</strong> N/A</p>
@endif

<p><strong>Project Value in Overseas Currency:</strong> {{ number_format($project->local_value, 2) }} {{ $project->currency }}</p>
<p><strong>XE Exchange Rate:</strong> {{ $project->exchange_rate }}</p>
<p><strong>Project Value in AUD:</strong> ${{ number_format($project->au_value->value(), 2) }}</p>

@if (empty($comment))
    <p><strong>Comments:</strong> N/A</p>
@else
    <p><strong>Comments:</strong></p>
    {{-- 
    {!! nl2br($comment) !!} --}}
    <table>
        <thead>
        <tr>
            <td>
                Date/Time
            </td>
            <td>
                Comment
            </td>
            <td>
                User
            </td>
        </tr>
        </thead>
        @foreach ($comment as $comments)
        <tbody>
        <tr>
            <td>
            {{ $comments->created_at }}
            </td>
            <td>
            {{ $comments->comment }}
            </td>
            <td>
            {{ $comments->users->first_name }} {{ $comments->users->last_name }}
            </td>
        </tr> 
        </tbody>
        @endforeach
    </table>
    

   
@endif
