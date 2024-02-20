<h4 class="form-heading">Previous Twins</h4>

@if ($local_conference->twinnings->where('is_active', 0)->isNotEmpty())
    <table class="table-unstyled">
        <thead>
            <tr>
                <th>Overseas Conf. ID</th>
                <th>Overseas Conference Name</th>
                <th>Country</th>
                <th>Twinning Status</th>
                <th>Twinning Period</th>
                <th>Receiving Remittances?</th>
                <th>Twinning ID</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($local_conference->twinnings->where('is_active', 0) as $twinning)
                <tr>
                    <td><a href="{{ route('overseas-conferences.edit', $twinning->overseasConference) }}">{{ $twinning->overseasConference->id }}</a></td>
                    <td>{{ $twinning->overseasConference->name }}</td>
                    <td>{{ optional($twinning->overseasConference->country)->name }}</td>
                    <td>{{ $twinning->is_active ? 'Active' : 'Surrendered' }}</td>
                    @if ($twinning->twinning_period)    
                    <td>{{ ucfirst($twinning->twinning_period) }}</td>
                    @else
                    <td>N/A </td>
                    @endif
                    <td>{{ $twinning->overseasConference->is_active ? 'Remittances' : 'No Remittances' }}</td>
                    <td><a href="{{ route('twinnings.edit', $twinning) }}">{{ $twinning->id }}</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="text-warning">No Surrendered Twins found for this Australian Conference.</p>
@endif
