<table>
    <tbody>
      <tr></tr>
      <tr></tr>
      <tr>
        <td></td><td></td><td>Remittance ID:</td><td>{{ $remittance->id }}</td>
      </tr>
      <tr>
        <td></td><td></td><td>State/Territory:</td><td>{{ strtoupper($remittance->state) }}</td>
      </tr>
      <tr>
        <td></td><td></td><td>Quarter:</td><td>Q{{ $remittance->quarter }}</td>
      </tr>
      <tr>
        <td>Overseas Remittance</td><td></td><td>Year:</td><td>{{ $remittance->year }}</td>
      </tr>
      <tr></tr>
      <tr>
        <td colspan="2"><b>To: National Council / Accounts</b></td><td colspan="2"><b>From: {{ $remittance->created_by->getFullName() }}</b></td>
      </tr>
      <tr>
        <td colspan="2"><b>Date: {{ $remittance->date->format('j F Y') }}</b></td><td></td><td></td>
      </tr>
      <tr></tr>
      <tr>
        <td colspan="4"><b>Please find listed below the amount to be sent to the National Council office for transmission to our twins overseas.</b></td>
      </tr>
      <tr></tr>

      <?php
      generateSummary('twinnings', $remittance);
      generateSummary('grants', $remittance);
      generateSummary('councils', $remittance);
      generateSummary('projects', $remittance);
      ?>
      <tr></tr>
      <tr>
        <?php $total = $remittance->getDonationTotal();?>
        <td colspan="3"><b>TOTAL TO BE REMITTED TO NATIONAL COUNCIL by {{ strtoupper($remittance->state) }} in {{ $remittance->quarter }} / {{ $remittance->year }}</b></td><td><b>{{ $total['total'] }}</b></td>
      </tr>
    </tbody>
</table>

<?php
function generateSummary($type, $remittance)
{
    switch ($type) {
        case 'twinnings':
            $heading   = 'TWINNINGS';
            $footer    = 'Total Twinnings';
            $donations = $remittance->twinningDonations;
            break;

        case 'grants':
            $heading   = 'GRANTS';
            $footer    = 'Total Grants';
            $donations = $remittance->grantDonations;
            break;

        case 'councils':
            $heading   = 'COUNCIL TO COUNCIL';
            $footer    = 'Total Council to Council';
            $donations = $remittance->councilDonations;
            break;

        case 'projects':
            $heading   = 'PROJECTS';
            $footer    = 'Total Projects';
            $donations = $remittance->projectDonations;
            break;
    }

    if ($donations->isEmpty()) {
        ?>
        <tr>
          <td></td><td></td><td><b>Counts</b></td><td><b>Amount</b></td>
        </tr>
        <tr>
          <td><b>{{ $heading }}</b></td><td><b>{{ $footer }}</b></td><td><b>0</b></td><td><b>0</b></td>
        </tr>
        <?php
    } else {
        ?>
        <tr>
          <td></td><td><b>Country</b></td><td><b>Counts</b></td><td><b>Amount</b></td>
        </tr>
        <?php
        $groupedDonations = $donations->sortBy(function ($donation, $key) use ($type) {
            if ($type == 'projects') {
                return optional($donation->project->beneficiary)->country->name;
            }

            return optional($donation->twinning->overseasConference->country)->name;
        })->groupBy(function ($donation, $key) use ($type) {
            if ($type =='projects') {
                return optional($donation->project->beneficiary)->country->name;
            }

            return optional($donation->twinning->overseasConference->country)->name;
        });

        foreach ($groupedDonations as $country => $countryDonations) {
            if ($groupedDonations->keys()->first() == $country) {
                ?>
                <tr>
                  <td><b>{{ $heading }}</b></td><td>{{ $country }}</td><td>{{ $countryDonations->count() }}</td><td>{{ $countryDonations->sum('amount') }}</td>
                </tr>
                <?php
            } else {
                ?>
                <tr>
                  <td></td><td>{{ $country }}</td><td>{{ $countryDonations->count() }}</td><td>{{ $countryDonations->sum('amount') }}</td>
                </tr>
                <?php
            }
        }
        ?>
        <tr>
          <td></td><td><b>{{ $footer }}</b></td><td><b>{{ $donations->count() }}</b></td><td><b>{{ $donations->sum('amount') }}</b></td>
        </tr>
        <?php
    }
    ?>
    <tr>
    </tr>
    <?php

}

?>
