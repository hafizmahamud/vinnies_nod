<script id="mustache-donors" type="x-tmpl-mustache">
    {{#donors}}
        <table class="table-unstyled">
            <thead>
                <tr>
                    <th>AUS Conf. SRN</th>
                    <th>AUS Conf. Name</th>
                    <th class="text-center">AUS Conf. State</th>
                    <th class="text-right">Paid to Date</th>
                    <th class="bb-none"></th>
                    <th class="bb-none"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><a href="{{local_conference_url}}">{{local_conference_id}}</a></td>
                    <td><a href="{{local_conference_url}}">{{name}}</a></td>
                    <td class="text-center">{{state}}</td>
                    <td class="text-right">{{total}}</td>
                    <td class="bt-none">
                        {{#can_edit_donors}}
                        <a href="#" class="btn btn-block btn-warning" data-toggle="modal" data-target="#modal-edit-donor" data-donor-id="{{id}}" data-local-conference-id="{{local_conference_id}}" data-edit-url="{{{edit_url}}}">Change Donor</a>
                        {{/can_edit_donors}}
                    </td>
                    <td class="bt-none">
                        {{#can_delete_donors}}
                        <a href="#" class="btn btn-block btn-danger js-delete-donor" data-donor-id="{{id}}" data-delete-url="{{{delete_url}}}" data-text-progress="Deleting..." data-text-default="Delete Donor">Delete Donor</a>
                        {{/can_delete_donors}}
                    </td>
                </tr>
                {{#has_contributions}}
                    <tr>
                        <td class="bt-none"></td>
                        <td><strong>Payment Date</strong></td>
                        <td><strong>Quarter/Year</strong></td>
                        <td class="text-right"><strong>Amount Paid</strong></td>
                        <td class="bt-none"></td>
                        <td class="bt-none"></td>
                    </tr>
                {{/has_contributions}}
                {{#contributions}}
                    <tr>
                        <td class="bt-none"></td>
                        <td>{{date}}</td>
                        <td>Q{{quarter}}:{{year}}</td>
                        <td class="text-right">{{amount}}</td>
                        <td class="bt-none">
                            {{#can_edit_contributions}}
                            <a href="#" class="btn btn-xs btn-block btn-warning" data-toggle="modal" data-target="#modal-edit-contribution" data-contribution-id="{{id}}" data-local-conference-id="{{local_conference_id}}"  data-local-conference-name="{{name}}" data-edit-url="{{{edit_url}}}" data-paid-at="{{date}}" data-amount="{{amount}}">Edit Payment</a>
                            {{/can_edit_contributions}}
                        </td>
                        <td class="bt-none">
                            {{#can_delete_contributions}}
                                <a href="#" class="btn btn-xs btn-block btn-danger js-delete-contribution" data-contribution-id="{{id}}" data-delete-url="{{{delete_url}}}" data-text-progress="Deleting..." data-text-default="Delete Payment">Delete Payment</a>
                            {{/can_delete_contributions}}
                        </td>
                    </tr>
                {{/contributions}}
                {{^contributions}}
                    <tr>
                        <td class="bt-none"></td>
                        <td colspan="3">
                            <span class="text-warning">No Contributions yet from this Donor.</span>
                        </td>
                        <td class="bt-none"></td>
                        <td class="bt-none"></td>
                    </tr>
                {{/contributions}}
                {{#can_create_contributions}}
                <tr>
                    <td class="bt-none"></td>
                    <td colspan="2">
                        <a href="#" class="btn btn-block btn-primary mt-1" data-toggle="modal" data-target="#modal-create-contribution" data-id="{{id}}" data-local-conference-id="{{local_conference_id}}" data-local-conference-name="{{name}}">Add Contribution</a>
                    </td>
                    <td></td>
                    <td></td>
                    <td class="bt-none"></td>
                    <td class="bt-none"></td>
                </tr>
                {{/can_create_contributions}}
            </tbody>
        </table>
    {{/donors}}

    {{^donors}}
        {{#can_create_donors}}
            <p class="text-warning">No Donors added yet. Click on the button below to add Donors.</p>
        {{/can_create_donors}}
        {{^can_create_donors}}
            <p class="text-warning">No Donors added yet.</p>
        {{/can_create_donors}}
    {{/donors}}

    {{#can_create_donors}}
        <div class="row">
            <div class="col-sm-2">
                <a href="#" class="btn btn-primary btn-block mt-1" data-toggle="modal" data-target="#modal-create-donor">Add Donor</a>
            </div>
        </div>
    {{/can_create_donors}}
</script>
