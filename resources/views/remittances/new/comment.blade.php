<div class="row">
    <div class="col-sm-12">
        <div class="form-group{{ $errors->has('comments') ? ' has-error' : '' }}">
            <label for="comments">Remittance Comments</label>
            {!! Form::textarea('comments', null, ['class' => 'form-control form-control-text-danger js-stretch', 'id' => 'comments', 'rows' => null, 'cols' => null]) !!}

            @if ($errors->has('comments'))
                <span class="help-block">
                   {{ $errors->first('comments') }}
                </span>
            @endif
        </div>

        <div class="row mb-2">
            <div class="col-sm-6">
                <button type="submit" class="btn btn-warning" data-text-default="Add comment" data-text-progress="Applying...">Add comment</button>
            </div>
        </div>
    </div>
    
    @if ($remittance->id !== null)
        @include('partials.comments', ['url' => route('new-remittances.comments', $remittance)])
    @endif
</div>
