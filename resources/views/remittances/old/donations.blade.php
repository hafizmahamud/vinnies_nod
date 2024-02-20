<h4 class="form-heading">Donations Breakdown</h4>

@if ($remittance->oldDonations->isNotEmpty())
<table class="table-unstyled">
    <thead>
        <tr>
            <th class="text-center">Purpose</th>
            <th class="text-center">MYOB Account Code/Name</th>
            <th class="text-center">Beneficiary Country</th>
            <th class="text-center">Amount</th>
            <th class="text-center">No. of OS Twins</th>
            <th class="text-center">Donation Comments</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($remittance->oldDonations as $donation)
            <tr>
                <td class="text-center">{{ $donation->purpose }}</td>
                <td class="text-center">{{ $donation->myob_code }}</td>

                @if (!empty($donation->beneficiary))
                    <td class="text-center">{{ optional($donation->beneficiary)->country->name }}</td>
                @else
                    <td class="text-center"></td>
                @endif
                <td class="text-center">{{ $donation->getFormattedAmount() }}</td>
                <td class="text-center">{{ $donation->twins }}</td>
                <td class="text-center">{{ $donation->comments }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@else
    <p class="text-warning">No donations added yet.</p>
@endif
