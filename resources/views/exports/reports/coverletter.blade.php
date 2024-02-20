<table>
    <tbody>
      <tr>
        <td></td><td></td><td>All Correspondence to:</td>
      </tr>
      <tr></tr>
      <tr>
        <td></td><td></td><td>PO Box 243</td>
      </tr>
      <tr>
        <td></td><td></td><td>Deakin West ACT 2600</td>
      </tr>
      <tr>
        <td></td><td></td><td>Telephone: 02 6202 1200</td>
      </tr>
      <tr></tr>
      <tr>
        <td></td><td></td><td>Email: overseasadmin@svdp.org.au</td>
      </tr>
      <tr>
        <td></td><td></td><td>ABN: 50 748 098 845</td>
      </tr>
      <tr>
        <td>{{ date('j F Y') }}</td><td></td><td></td>
      </tr>
      <tr></tr>

      <?php
      $beneficiary = $remittances['beneficiary'];
      ?>
      <tr>
        <td>{{ collect([
            $beneficiary->contact_title,
            $beneficiary->contact_first_name,
            $beneficiary->contact_last_name
        ])->filter()->implode(' ') }}</td><td></td><td></td>
      </tr>

      @if ($beneficiary->contact_position)
          <tr>
            <td>{{ $beneficiary->contact_position }}</td><td></td><td></td>
          </tr>
      @endif

      <tr>
        <td>Society of St Vincent de Paul</td><td></td><td></td>
      </tr>


      @if ($beneficiary->address_line_1)
          <tr>
            <td>{{ $beneficiary->address_line_1 }}</td><td></td><td></td>
          </tr>
      @endif

      @if ($beneficiary->address_line_2 || $beneficiary->address_line_3)
          <tr>
            <td>{{ collect([
                $beneficiary->address_line_2,
                $beneficiary->address_line_3
            ])->filter()->implode(' ') }}</td><td></td><td></td>
          </tr>
      @endif

      @if ($beneficiary->suburb || $beneficiary->state || $beneficiary->postcode)
          <tr>
            <td>{{ collect([
                $beneficiary->suburb,
                $beneficiary->state,
                $beneficiary->postcode
            ])->filter()->implode(' ') }}</td><td></td><td></td>
          </tr>
      @endif

      <tr>
        <td><b>{{ strtoupper($beneficiary->country->name) }}</b></td><td></td><td></td>
      </tr>
      <tr>
        <td>{{ $beneficiary->email }}</td><td></td><td></td>
      </tr>
      <tr></tr>
      <tr></tr>
      <tr></tr>

      <tr>
        <td colspan="3"><b>{{ sprintf('Funds transferred electronically for Remittances in Q%s / %s', $remittances['quarter'], $remittances['year']) }}</b></td>
      </tr>
      <tr></tr>

      <tr>
        <td><b>This represents the following amounts:</b></td><td></td><td><b>AUD</b></td>
      </tr>
      <tr>
        <td>TWINNING</td><td></td><td>{{ $remittances['twinning'] }}</td>
      </tr>
      <tr>
        <td>GRANTS</td><td></td><td>{{ $remittances['grants'] }}</td>
      </tr>
      <tr>
        <td>COUNCIL TO COUNCIL</td><td></td><td>{{ $remittances['councils'] }}</td>
      </tr>
      <tr>
        <td>PROJECT and Special Works</td><td></td><td>{{ $remittances['projects'] }}</td>
      </tr>
      <tr></tr>
      <tr>
        <td><b>TOTAL</b></td><td></td><td><b>{{ $remittances['total'] }}</b></td>
      </tr>
      <tr></tr>
      <tr></tr>
      <tr></tr>

      <tr>
        <td colspan="2">I acknowledge:</td><td></td>
      </tr>
      <tr>
        <td colspan="2">   receipt of the funds detailed in this remittance statement;</td><td></td>
      </tr>
      <tr>
        <td colspan="2">   that funds received will be expended for the purposes detailed on the attached remittance statement; and</td><td></td>
      </tr>
      <tr>
        <td colspan="2">   that funds received will be expended and reported in line with the requirements of the Funding Agreement signed for this Financial Year.</td><td></td>
      </tr>
      <tr></tr>
      <tr></tr>

      <tr>
        <td colspan="3">Name: ___________________________</td>
      </tr>
      <tr></tr>
      <tr>
        <td colspan="3">Position: _________________________</td>
      </tr>
      <tr></tr>
      <tr>
        <td colspan="3">Date: ____________________________</td>
      </tr>
      <tr></tr>
      <tr></tr>

      <tr>
        <td colspan="3">Please encourage your twinned conferences to communicate with their Australian twin.</td>
      </tr>
      <tr>
        <td colspan="3">Correspondence can be sent to overseasadmin@svdp.org.au</td>
      </tr>
      <tr></tr>
      <tr>
        <td colspan="3">This form to be signed and returned to overseasadmin@svdp.org.au.</td>
      </tr>
      <tr></tr>
      <tr></tr>
      <tr></tr>

      <tr>
        <td>Received</td><td></td><td></td>
      </tr>
      <tr></tr>
      <tr></tr>
      <tr>
        <td><b>..............................................................</b></td><td></td><td><b>.........................................</b></td>
      </tr>
      <tr>
        <td><b>Signature</b></td><td></td><td><b>Date</b></td>
      </tr>
    </tbody>
</table>
