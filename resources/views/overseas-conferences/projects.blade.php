<h4 class="form-heading">Linked Projects</h4>

@if ($projects->isNotEmpty())
    <table class="table-unstyled">
        <thead>
            <tr>
                <th>Project ID</th>
                <th>Project Name</th>
                <th>OS Conf. SRN</th>
                <th>Overseas Conference Name</th>
                <th>Country</th>
                <th>Project Status</th>
                <th>Paid?</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($projects as $project)
                <tr>
                    <td><a href="{{ route('projects.edit', $project) }}">{{ $project->id }}</a></td>
                    <td>{{ $project->name }}</td>
                    <td>
                        @if ($project->hasOverseasConference())
                            <a href="{{ route('overseas-conferences.edit', $project->overseasConference) }}">{{ $project->overseasConference->id }}</a>
                        @endif
                    </td>
                    <td>
                        @if ($project->hasOverseasConference())
                            {{ $project->overseasConference->name }}
                        @endif
                    </td>
                    <td>{{ $project->beneficiary->country->name }}</td>
                    <td>
                    @if($project->status == "pending_approval")
                    Pending Approval
                    @elseif($project->status == "awaiting_support")
                    Awaiting Support
                    @elseif($project->status == "awaiting_remittance")
                    Awaiting Remittance
                    @elseif($project->status == "funded")
                    Funded
                    @elseif($project->status == "declined")
                    Declined
                    @elseif($project->status == "completed")
                    Completed
                    @endif  
                    </td>
                    <td>{{ $project->is_fully_paid ? 'Yes' : 'No' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p class="text-warning">No Projects found for this Overseas Conference.</p>
@endif
