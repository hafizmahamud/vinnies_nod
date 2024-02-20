<table>
    <thead>
    <tr>
      @foreach ($header as $key => $title)
          <th><b>{{ $title }}</b></th>
      @endforeach;
    </tr>
    </thead>
    <tbody>
      @foreach ($data as $donation)
          <tr>
            <td>{{ $quarter }}</td>
            <td>{{ $year }}</td>
            <td>{{ $donation->project->id }}</td>
            <td>{{ $donation->project->name }}</td>
            <td>{{ $donation->project->beneficiary->country->name }} </td>
            <td>{{ ($donation->project->overseasConference) ? $donation->project->overseasConference->id : 'N/A' }} </td>
            <td>{{ ($donation->project->overseasConference) ? $donation->project->overseasConference->name : 'N/A' }} </td>
            <td>{{ ($donation->project->overseasConference) ? $donation->project->overseasConference->central_council : 'N/A' }} </td>
            <td>{{ ($donation->project->overseasConference) ? $donation->project->overseasConference->particular_council : 'N/A' }} </td>
            <td>{{ ($donation->project->overseasConference) ? $donation->project->overseasConference->parish : 'N/A' }} </td>
            <td>{{ $donation->donor->id }}</td>
            <td>{{ $donation->donor->name }}</td>
            <td>{{ strtoupper($donation->donor->state) }}</td>
            <td>{{ $donation->remittance->date->format('d/m/Y') }}</td>
            <td>{{ $donation->created_at->format('d/m/Y') }}</td>
            <td>{{ $donation->remittance->approved_at ? $donation->remittance->approved_at->format('d/m/Y') : '-' }}</td>
            <td>{{ $donation->amount }}</td>
          </tr>
      @endforeach

      <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><b>Total</b></td>
        <td><b>{{ $total }}</b></td>
      </tr>

    </tbody>
</table>
