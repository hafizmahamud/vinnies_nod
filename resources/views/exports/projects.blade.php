<table>
    <thead>
    <tr>
      @foreach ($header as $key => $title)
          @if ( $key == 0 || $key == 1 || $key == 2 || $key == 10 || $key == 16 || $key == 23 || $key == 25 || $key == 26 || $key == 33 )
          <td bgcolor="#92d050"><b>{{ $title }}</b></td>
          @else
          <th><b>{{ $title }}</b></th>
          @endif
      @endforeach;
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($projects as $project) {
        if ($project->donors->isEmpty()) {
            $data   = getData($project);
            $data[] = 'N/A';
            $data[] = 'N/A';
            $data[] = 'N/A';
            $data[] = 'N/A';
            $data[] = 'N/A';
            $data[] = 'N/A';
            $data[] = 'N/A';
            $data[] = ($project->completion_report_received ? 'Yes' : 'No');
            $data[] = empty($project->completed_at) ? 'N/A' : $project->completed_at->format(config('vinnies.date_format'));
            $data[] = empty($project->project_completion_date) ? 'N/A' : $project->project_completion_date->format(config('vinnies.date_format'));
            $data[] = '';

            ?>
            <tr>
            @foreach ($data as $key => $value)
                @if ( $key == 0 || $key == 1 || $key == 2 || $key == 10 || $key == 16 || $key == 23 || $key == 25 || $key == 26 || $key == 33 )
                <td bgcolor="#92d050">{{ $value }}</td>
                @else
                <td>{{ $value }}</td>
                @endif
            @endforeach
            </tr>
            <?php
        } else {
            foreach ($project->donors as $donor) {
                $data   = getData($project);
                $data[] = $donor->localConference->id;
                $data[] = $donor->localConference->trashed() ? 'Abeyant' : 'Active';
                $data[] = $donor->localConference->name;
                //$data[] = strtoupper($donor->localConference->state);
                $data[] = Helper::getStateNameByKey($donor->localConference->state);

                if (empty($donor->localConference->regional_council)) {
                    $data[] = 'N/A';
                } else {
                    $data[] = $donor->localConference->regional_council;
                }

                if (empty($donor->localConference->diocesanCouncil)) {
                    $data[] = 'N/A';
                } else {
                    $data[] = $donor->localConference->diocesanCouncil->name;
                }

                $data[] = $donor->contributions->sum('amount');
                $data[] = ($project->completion_report_received ? 'Yes' : 'No');
                $data[] = empty($project->completed_at) ? 'N/A' : $project->completed_at->format(config('vinnies.date_format'));
                $data[] = empty($project->project_completion_date) ? 'N/A' : $project->project_completion_date->format(config('vinnies.date_format'));
                $data[] = '';

                ?>
                <tr>
                @foreach ($data as $key => $value)
                    @if ( $key == 0 || $key == 1 || $key == 2 || $key == 10 || $key == 16 || $key == 23 || $key == 25 || $key == 26 || $key == 33 )
                    <td bgcolor="#92d050">{{ $value }}</td>
                    @else
                    <td>{{ $value }}</td>
                    @endif
                @endforeach
                </tr>
                <?php
            } //end foreach donors
        } //end if empty donors
    } //end foreach projects

    function getData($project)
    {
        return [
            '',
            '',
            $project->id,
            (empty($project->overseas_project_id) ? 'N/A' : $project->overseas_project_id),
            (empty($project->status) ? 'N/A' : ucwords(str_replace("_", " ", $project->status))),
            (empty($project->project_type) ? 'N/A' : ucwords(str_replace("_", " ", $project->project_type))),
            (ucwords($project->consolidated_status)),
            (empty($project->received_at) ? 'N/A' : $project->received_at->format(config('vinnies.date_format'))),
            (empty($project->estimated_completed_at) ? 'N/A' : $project->estimated_completed_at->format(config('vinnies.date_format'))),
            ($project->is_awaiting_support ? 'Yes' : 'No'),
            $project->name,
            $project->au_value->value(),
            $project->getBalanceOwing()->value(),
            ($project->is_fully_paid ? 'Yes' : 'No'),
            (empty($project->fully_paid_at) ? 'N/A' : $project->fully_paid_at->format(config('vinnies.date_format'))),
            $project->beneficiary ? $project->beneficiary->name : '',
            $project->beneficiary ? $project->beneficiary->country->name : '',
            $project->hasOverseasConference() ? $project->overseasConference->id : '',
            ($project->hasOverseasConference() ? ($project->overseasConference->is_active ? 'Active' : 'Inactive') : 'N/A'),
            $project->hasOverseasConference() ? $project->overseasConference->name : '',
            $project->hasOverseasConference() ? $project->overseasConference->country->name : '',
            $project->hasOverseasConference() ? $project->overseasConference->central_council : '',
            $project->hasOverseasConference() ? $project->overseasConference->particular_council : '',
        ];
    }
    ?>
    </tbody>
</table>
