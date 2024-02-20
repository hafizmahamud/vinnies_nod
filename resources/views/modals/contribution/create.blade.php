<div class="modal fade" id="modal-create-contribution" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Contribution</h4>
            </div>
            <div class="modal-body">
                {!! Form::open(['route' => 'contributions.create', 'class' => 'js-modal-form']) !!}
                    @include('modals.contribution.modal-form')
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning btn-submit-modal-form" data-text-progress="Applying..." data-text-default="Apply Changes">Apply Changes</button>
            </div>
        </div>
    </div>
</div>