{{-- <div class="modal fade" id="modal-activity" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="activity">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Activity Log</h4>
            </div>
            <div class="modal-body">
            <table class="table table-striped js-modal-table" data-url="{{ route('activity.list') }}" data-page-length="{{ config('vinnies.pagination.activity') }}" data-order-col="1" data-order-type="ASC">
                        <thead>
                            <tr>
                                <th class="text-center" data-name="id">Log ID</th>
                                <th class="text-center" data-name="event">Event Type</th>
                                <th class="text-center" data-name="subjectID">Subject ID</th>
                                <th class="text-center" data-name="properties">Properties Changes</th>
                                <th class="text-center" data-name="updated_at">Updated At</th>
                                <th class="text-center" data-name="updated_by">Updated By</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-12 text-right">
                        @include('pagination.table')
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div> --}}

<div class="modal fade" id="modal-activity" tabindex="-1"  role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Activity Log</h4>
                </div>

                <div class="modal-body">
                    <table class="table table-striped js-modal-table" data-url="{{ route('activity.list') }}" data-page-length="{{ config('vinnies.pagination.activity') }}" data-order-col="1" data-order-type="ASC">
                        <thead>
                            <tr>
                                <th class="text-center" data-name="id">Log ID</th>
                                <th class="text-center" data-name="event">Event Type</th>
                                <th class="text-center" data-name="subjectID">Subject ID</th>
                                <th class="text-center" data-name="properties">Properties Changes</th>
                                <th class="text-center" data-name="updated_at">Updated At</th>
                                <th class="text-center" data-name="updated_by">Updated By</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($activity as $activities)
                            <tr>
                                <td>{{ $activities->id }}</td>
                                <td>{{ $activities->event }}</td>
                                <td>{{ $activities->subject_id }}</td>
                                <td style="width: 25%">{{ $activities->properties }}</td>
                                <td>{{ $activities->updated_at }}</td>
                                <td>{{ $activities->users }}</td>
                            </tr> 
                            @endforeach
                        </tbody>
                    </table>
                <div class="row">
                <div class="col-sm-12 text-right">
                    @include('pagination.table')
                </div>
            /div>
        </div>
    </div>
