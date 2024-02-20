<div class="modal fade" id="modal-beneficiary" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Assign Beneficiary</h4>
            </div>
            <div class="modal-body">
                <p>Please search below for the beneficiary, select it, and then click the "Update Beneficiary" button.</p>
                <form class="js-modal-table-filter">
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="country">Country</label>
                            <select name="country" id="country" class="form-control">
                                <option value="">All Countries</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block" data-text-progress="Applying..." data-text-default="Apply Filter">Apply Filter</button>
                        </div>
                    </div>
                </form>
                <div class="row mb-2">
                    <div class="col-sm-6 col-sm-offset-6 text-right mt-2">
                        @include('pagination.basic')
                    </div>
                </div>
                {!! Form::open(['route' => 'projects.validateBeneficiary', 'class' => 'js-modal-form']) !!}
                    <table class="table table-striped js-modal-table mt-0" data-url="{{ route('beneficiaries.datatables') }}" data-page-length="{{ config('vinnies.pagination.beneficiaries') }}" data-order-col="1" data-order-type="ASC" data-selected-id="{{ Route::currentRouteName() == 'projects.create' ? '' : optional($project->beneficiary)->id }}">
                        <thead>
                            <tr>
                                <th class="text-center" data-name="id" data-orderable="false">Select</th>
                                <th class="text-center" data-name="name">Beneficiary Name</th>
                                <th class="text-center" data-name="country" data-orderable="false">Country</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-12">
                            <p class="text-danger" data-error="beneficiary_id"></p>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning btn-submit-modal-form" data-text-progress="Updating..." data-text-default="Update Beneficiary">Update Beneficiary</button>
            </div>
        </div>
    </div>
</div>
