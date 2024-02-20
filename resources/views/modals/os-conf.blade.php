<div class="modal fade" id="modal-os-conf" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Assign Overseas Conference</h4>
            </div>
            <div class="modal-body">
                <p>Please search below for the Overseas Conference, select only one of them, and then click the "Update" button.</p>
                <form class="js-modal-table-filter">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="country">Country</label>
                                <select name="country" id="country" class="form-control">
                                    <option value="">All Countries</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="is_active">OS Conf. Receiving Remittances?</label>
                                <select name="is_active" id="is_active" class="form-control">
                                    <option value="">All</option>
                                    <option value="active">Remittances</option>
                                    <option value="inactive">No Remittances</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <input type="hidden" name="per_page" value="10">
                                <button type="submit" class="btn btn-primary btn-block" data-text-progress="Applying..." data-text-default="Apply Filter">Apply Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-sm-6">
                        <form class="form-inline js-table-search-form">
                            <div class="form-group has-btn">
                                <label class="sr-only" for="modal-os-conf-search">Search</label>
                                <input type="text" class="form-control js-table-search-input" id="modal-os-conf-search" placeholder="Search">
                                <button class="btn"><i class="fa fa-search" aria-hidden="true"></i></button>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-6 text-right">
                        @include('pagination.basic')
                    </div>
                </div>

                @if (in_array(Route::currentRouteName(), ['twinnings.create', 'twinnings.edit']))
                    {!! Form::open(['route' => 'twinnings.validateOverseasConference', 'class' => 'js-modal-form']) !!}
                    <table class="table table-striped js-modal-table" data-url="{{ route('overseas-conferences.datatables') }}" data-page-length="{{ config('vinnies.pagination.overseas_conferences') }}" data-order-col="1" data-order-type="ASC" data-selected-id="{{ optional($twinning->overseasConference)->id }}">
                @else
                    {!! Form::open(['route' => 'projects.validateOverseasConference', 'class' => 'js-modal-form']) !!}
                    <table class="table table-striped js-modal-table" data-url="{{ route('overseas-conferences.datatables') }}" data-page-length="{{ config('vinnies.pagination.overseas_conferences') }}" data-order-col="1" data-order-type="ASC" data-selected-id="{{ optional($project->overseasConference)->id }}">
                @endif
                        <thead>
                            <tr>
                                <th class="text-center" data-name="select" data-orderable="false">Select</th>
                                <th class="text-center" data-name="id">OS. SRN</th>
                                <th class="text-center" data-name="name">Overseas Conference Name</th>
                                <th class="text-center" data-name="country">Country</th>
                                <th class="text-center" data-name="central_council">Central Council</th>
                                <th class="text-center" data-name="particular_council">Particular Council</th>
                                <th class="text-center" data-name="parish">Parish</th>
                                <th class="text-center" data-name="is_active">OS Conf. Receiving Remittances?</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-12">
                            <p class="text-danger" data-error="overseas_conference_id"></p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning btn-submit-modal-form" data-text-progress="Updating..." data-text-default="Update Overseas Conference">Update Overseas Conference</button>
            </div>
        </div>
    </div>
</div>
