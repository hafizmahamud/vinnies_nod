<script id="mustache-project-donations" type="x-tmpl-mustache">
    @{{#has_donations}}
        <p class="text-warning">{{ RemittanceType::PROJECT }} imported. See full list below.</p>
        <table class="table-unstyled">
            <thead>
                <tr>
                    <th>Project ID</th>
                    <th>Project Name</th>
                    <th>Donor SRN</th>
                    <th>Donor Name</th>
                    <th>Country</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @{{#donations}}
                    @verbatim
                        <tr>
                            <td><a href="{{project_edit_url}}">{{project_id}}</a></td>
                            <td>{{project_name}}</td>
                            <td><a href="{{{donor_edit_url}}}">{{donor_id}}</a></td>
                            <td><a href="{{{donor_edit_url}}}">{{donor_name}}</a></td>
                            <td>{{country}}</td>
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
                    <p>File: <a href="{{{document_url}}}" target="blank">{{document_name}} ({{document_date}})</a></p>
                @endverbatim
            </div>
            @{{^is_approved}}
                <div class="col-sm-6">
                    {!! Form::open(['route' => ['new-remittances.delete', $remittance], 'class' => 'js-delete-document-form js-delete-document-form-project']) !!}
                        <input type="hidden" name="document_id" value="@{{document_id}}">
                        <input type="hidden" name="type" value="{{ RemittanceType::PROJECT }}">
                        <button class="btn btn-danger" type="submit" data-text-default="Delete file" data-text-progress="Deleting...">Delete file</button>
                    </form>
                </div>
            @{{/is_approved}}
        </div>
    @{{/has_donations}}
    @{{^has_donations}}
        @{{^is_approved}}
            <p class="text-warning">No {{ RemittanceType::PROJECT }} added yet. Please click on the browse button below then after selecting the file click on Upload Button to import {{ RemittanceType::PROJECT }}.</p>

            {!! Form::open(['route' => 'documents.create', 'class' => 'js-remittance-document-form js-remittance-document-form-project', 'files' => true]) !!}
                <div class="row mt-2">
                    <div class="col-sm-8">
                        <label for="document-projects" class="btn btn-primary">Browse for {{ RemittanceType::PROJECT }} CSV File</label>
                        <input type="file" name="document" id="document-projects" class="hidden js-input-file" data-target=".js-file-selected-projects">
                        <span class="ml-1">File selected: <span class="js-file-selected js-file-selected-projects">-</span></span>
                        <input type="hidden" name="type" value="{{ RemittanceType::PROJECT }}">
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
            <p class="text-warning">No {{ RemittanceType::PROJECT }} added yet.</p>
        @{{/is_approved}}
    @{{/has_donations}}
</script>
