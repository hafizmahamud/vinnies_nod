<h4 class="form-heading">Previous Twins</h4>

@if ($overseas_conference->twinnings->where('is_active', 0)->isNotEmpty())
    <table class="table-unstyled">
        <thead>
            <tr>
                <th>Australian Conf. ID</th>
                <th>Australian Conference Name</th>
                <th>State</th>
                <th>Australian Conf. Parish</th>
                <th>Twinning Status</th>
                <th>Twinning Period</th>
                <th>Twinning ID</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($overseas_conference->twinnings->where('is_active', 0) as $twinning)
                <tr>
                    <td><a href="{{ route('local-conferences.edit', $twinning->localConference) }}">{{ $twinning->localConference->id }}</a></td>
                    <td>{{ $twinning->localConference->name }}</td>
                    <td>{{ strtoupper($twinning->localConference->state) }}</td>
                    <td>{{ $twinning->localConference->parish }}</td>
                    <td>{{ $twinning->is_active ? 'Active' : 'Surrendered' }}</td>
                    @if ($twinning->twinning_period)    
                    <td>{{ ucfirst($twinning->twinning_period) }}</td>
                    @else
                    <td>N/A </td>
                    @endif
                    <td><a href="{{ route('twinnings.edit', $twinning) }}">{{ $twinning->id }}</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="text-warning">No Surrendered Twins found for this Overseas Conference.</p>
@endif