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
            <td>{{ $donation['year'] }}</td>
            <td>{{ $donation['project_id'] }}</td>
            <td>{{ $donation['project_name'] }}</td>
            <td>{{ $donation['country'] }}</td>
            <td>{{ $donation['oc_id'] }}</td>
            <td>{{ $donation['oc_name'] }}</td>
            <td>{{ $donation['central_council'] }}</td>
            <td>{{ $donation['particular_council'] }}</td>
            <td>{{ $donation['parish'] }}</td>
            <td>{{ $donation['donor_id'] }}</td>
            <td>{{ $donation['donor_name'] }}</td>
            <td>{{ $donation['donor_state'] }}</td>
            <td>{{ $donation['recieved_at'] }}</td>
            <td>{{ $donation['uploaded_at'] }}</td>
            <td>{{ $donation['approved_at'] }}</td>
            <td>{{ $donation['amount'] }}</td>
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
        <td><b>Total</b></td>
        <td><b>{{ $total }}</b></td>
      </tr>

    </tbody>
</table>
