<table class="table table-striped js-modal-table mt-0" data-url="{{ route('local-conferences.datatables') }}" data-page-length="{{ config('vinnies.pagination.local_conferences') }}" data-order-col="1" data-order-type="ASC" data-selected-id="">
    <thead>
        <tr>
            <th class="text-center" data-name="select" data-orderable="false">Select</th>
            <th class="text-center" data-name="id">AUS. SRN</th>
            <th class="text-center" data-name="name">Australian Conference Name</th>
            <th class="text-center" data-name="state" data-orderable="false">State Council</th>
            <th class="text-center" data-name="regional_council">Regional Council</th>
            <th class="text-center" data-name="diocesan_council_id">Diocesan/Central Council</th>
            <th class="text-center" data-name="parish">Parish</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<div class="row">
    <div class="col-sm-12">
        <p class="text-danger" data-error="local_conference_id"></p>
    </div>
</div>
<input type="hidden" name="project_id" value="{{ $project->id }}">
