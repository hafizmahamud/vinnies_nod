<h4 class="form-heading">Twins</h4>

@if ($local_conference->twinnings->where('is_active', 1)->isNotEmpty())
    <div class="twinning-cont">
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
                @foreach ($local_conference->twinnings->where('is_active', 1) as $twinning)
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
    </div>
@else
    <p class="text-warning">No Twins found for this Australian Conference.</p>
@endif

@if ($currentUser->hasPermissionTo('create.twinnings'))
    <a href="{{ route('twinnings.create') }}?local-conf={{ $local_conference->id }}" class="btn btn-primary js-btn-add-twinning">Add Twin</a>
@endif
