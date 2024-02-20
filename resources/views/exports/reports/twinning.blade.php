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
            <td>{{ $donation->twinning->overseasConference->id }}</td>
            <td>{{ $donation->twinning->overseasConference->name }}</td>
            <td>{{ optional($donation->twinning->overseasConference->country)->name }}</td>
            <td>{{ $donation->twinning->overseasConference->central_council }}</td>
            <td>{{ $donation->twinning->overseasConference->particular_council }}</td>
            <td>{{ $donation->twinning->overseasConference->parish }}</td>
            <td>{{ $donation->twinning->localConference->id }}</td>
            <td>{{ $donation->twinning->localConference->name }}</td>
            <td>{{ strtoupper($donation->twinning->localConference->state) }}</td>
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
        <td><b>Total</b></td>
        <td><b>{{ $total }}</b></td>
      </tr>

    </tbody>
</table>
