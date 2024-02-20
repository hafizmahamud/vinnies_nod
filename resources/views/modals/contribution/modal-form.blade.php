<div class="well">
    <p>Contribution from SRN: <strong><span data-bind="donor_local_conference_name"></span> (ID: <span data-bind="donor_local_conference_id"></span>)</strong></p>
    <p class="mb-0">For Project: <strong>{{ $project->id }}</strong></p>
</div>
<div class="row">
    <div class="col-sm-4">
        <div class="form-group">
            <label for="paid_at">Payment Date</label>
            <input type="text" class="form-control js-datepicker" name="paid_at">
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label for="quarter_year">Quarter/Year</label>
            <input type="text" class="form-control" readonly="readonly" value="" data-bind="quarter_year">
        </div>
    </div>
     <div class="col-sm-4">
        <div class="form-group">
            <label for="amount">Amount Paid</label>
            <input type="text" class="form-control" name="amount">
        </div>
    </div>
</div>

<input type="hidden" name="project_id" value="{{ $project->id }}">
<input type="hidden" name="donor_id" value="">
