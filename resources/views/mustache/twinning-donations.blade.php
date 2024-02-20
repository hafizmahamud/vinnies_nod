<script id="mustache-twinning-donations" type="x-tmpl-mustache">
    @{{#has_donations}}
        <p class="text-warning">{{ RemittanceType::TWINNING }} imported. See full list below.</p>
        <table class="table-unstyled">
            <thead>
                <tr>
                    <th>Twinning ID</th>
                    <th>Twinning Status</th>
                    <th>Australian Conference Name</th>
                    <th>AUS. Conf. SRN</th>
                    <th>Overseas Conference Name</th>
                    <th>OS. Conf. SRN</th>
                    <th>Country</th>
                    <th>Receiving Remittances?</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @{{#donations}}
                    @verbatim
                        <tr>
                            <td><a href="{{{twinning_edit_url}}}">{{twinning_id}}</a></td>
                            <td>{{twinning_status}}</td>
                            <td>{{local_conference_name}}</td>
                            <td><a href="{{{local_conference_edit_url}}}">{{local_conference_id}}</a></td>
                            <td>{{overseas_conference_name}}</td>
                            <td><a href="{{{overseas_conference_edit_url}}}">{{overseas_conference_id}}</a></td>
                            <td>{{country}}</td>
                            <td>{{is_active}}</td>
                            <td>${{amount}}</td>
                        </tr>
                    @endverbatim
                @{{/donations}}
            </tbody>
        </table>
        <hr>
        <div class="row">
            <div class="col-sm-6">
                @verbatim
                    <p>File: <a href="{{{document_url}}}" target="_blank">{{document_name}} ({{document_date}})</a></p>
                @endverbatim
            </div>
            @{{^is_approved}}
                <div class="col-sm-6">
                    {!! Form::open(['route' => ['new-remittances.delete', $remittance], 'class' => 'js-delete-document-form js-delete-document-form-twinning']) !!}
                        <input type="hidden" name="document_id" value="@{{document_id}}">
                        <input type="hidden" name="type" value="{{ RemittanceType::TWINNING }}">
                        <button class="btn btn-danger" type="submit" data-text-default="Delete file" data-text-progress="Deleting...">Delete file</button>
                    </form>
                </div>
            @{{/is_approved}}
        </div>
    @{{/has_donations}}
    @{{^has_donations}}
        @{{^is_approved}}
            <p class="text-warning">No {{ RemittanceType::TWINNING }} added yet. Please click on the browse button below then after selecting the file click on Upload Button to import {{ RemittanceType::TWINNING }}.</p>

            {!! Form::open(['route' => 'documents.create', 'class' => 'js-remittance-document-form js-remittance-document-form-twinning', 'files' => true]) !!}
                <div class="row mt-2">
                    <div class="col-sm-8">
                        <label for="document-twinning" class="btn btn-primary">Browse for {{ RemittanceType::TWINNING }} CSV File</label>
                        <input type="file" name="document" id="document-twinning" class="hidden js-input-file" data-target=".js-file-selected-twinning">
                        <span class="ml-1">File selected: <span class="js-file-selected js-file-selected-twinning">-</span></span>
                        <input type="hidden" name="type" value="{{ RemittanceType::TWINNING }}">
                        <input type="hidden" name="documentable_id" value="{{ $remittance->id }}">
                        <input type="hidden" name="documentable_type" value="NewRemittance">
                    </div>
                    <div class="col-sm-4 text-right">
                        <button type="submit" class="btn btn-warning" data-text-progress="Uploading..." data-text-default="Upload the Selected file">Upload the Selected file</button>
                    </div>
                </div>
            </form>
        @{{/is_approved}}
        @{{#is_approved}}
            <p class="text-warning">No {{ RemittanceType::TWINNING }} added yet.</p>
        @{{/is_approved}}
    @{{/has_donations}}
</script>
