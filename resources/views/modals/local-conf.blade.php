<div class="modal fade" id="modal-local-conf" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Assign Australian Conference</h4>
            </div>
            <div class="modal-body">
                <p>Please search below for the Australian Conference, select only one of them, and then click the "Update" button.</p>
                <form class="js-modal-table-filter">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="state">State Council</label>
                                <select name="state" id="state" class="form-control">
                                    <option value="">All States</option>
                                    @foreach (Helper::getStates() as $key => $state)
                                        @if ($key == 'national')
                                            <option value="{{ $key }}">{{ ucwords($key) }}</option>
                                        @else
                                            <option value="{{ $key }}">{{ strtoupper($key) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="diocesan_council_id">Diocesan/Central Council</label>
                                <select name="diocesan_council_id" id="diocesan_council_id" class="form-control">
                                    <option value="">All Diocesan/Central Councils</option>
                                    @foreach ($diocesan_councils as $state => $diocesan_council_list)
                                        <optgroup label={{ $state }}>
                                            @foreach ($diocesan_council_list as $diocesan_council)
                                                <option value="{{ $diocesan_council['id'] }}">{{ $diocesan_council['name'] }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
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
                                <label class="sr-only" for="modal-local-conf-search">Search</label>
                                <input type="text" class="form-control js-table-search-input" id="modal-local-conf-search" placeholder="Search">
                                <button class="btn"><i class="fa fa-search" aria-hidden="true"></i></button>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-6 text-right">
                        @include('pagination.basic')
                    </div>
                </div>

                {!! Form::open(['route' => 'twinnings.validateLocalConference', 'class' => 'js-modal-form']) !!}
                    <table class="table table-striped js-modal-table" data-url="{{ route('local-conferences.datatables') }}" data-page-length="{{ config('vinnies.pagination.local_conferences') }}" data-order-col="1" data-order-type="ASC" data-selected-id="{{ optional($twinning->localConference)->id }}">
                        <thead>
                            <tr>
                                <th class="text-center" data-name="select" data-orderable="false">Select</th>
                                <th class="text-center" data-name="id">AUS. SRN</th>
                                <th class="text-center" data-name="name">Australian Conference Name</th>
                                <th class="text-center" data-name="state">State Council</th>
                                <th class="text-center" data-name="regional_council">Regional Council</th>
                                <th class="text-center" data-name="diocesan_council">Diocesan/Central Council</th>
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
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning btn-submit-modal-form" data-text-progress="Updating..." data-text-default="Update Australian Conference">Update Australian Conference</button>
            </div>
        </div>
    </div>
</div>
