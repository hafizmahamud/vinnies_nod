<div class="modal fade" id="modal-edit-donor" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit Donor</h4>
            </div>
            <div class="modal-body">
                @include('modals.donor.modal-body')
                {!! Form::open(['url' => '#', 'class' => 'js-modal-form', 'method' => 'patch']) !!}
                    @include('modals.donor.modal-table')
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning btn-submit-modal-form" data-text-progress="Updating..." data-text-default="Update Australian Conference as Donor">Update Australian Conference as Donor</button>
            </div>
        </div>
    </div>
</div>
