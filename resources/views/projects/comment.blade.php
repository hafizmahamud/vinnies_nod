<div class="row">
    <div class="col-sm-12">
        <div class="form-group{{ $errors->has('comments') ? ' has-error' : '' }}">
            <label for="comments">Comments</label>
            @if (!$currentUser->hasRole('State User Finance'))
            {!! Form::textarea('comments', null, ['class' => 'form-control form-control-text-danger js-stretch', 'id' => 'comments', 'rows' => null, 'cols' => null]) !!}
            @else
            {!! Form::textarea('comments', null, ['class' => 'form-control form-control-text-danger js-stretch', 'id' => 'comments', 'rows' => null, 'cols' => null, 'disabled' => 'disabled']) !!}
            @endif

            @if ($errors->has('comments'))
                <span class="help-block">
                   {{ $errors->first('comments') }}
                </span>
            @endif
        </div>

        {{--  @if ($currentUser->hasPermissionTo('update.projects')) --}}
        @if(!$currentUser->hasRole('State User Finance'))
            <div class="row mb-2">
                <div class="col-sm-3">
                    <button type="submit" class="btn btn-warning" data-text-default="Add comment" data-text-progress="Applying...">Add comment</button>
                </div>
            </div>
        @endif
        {{-- @endif --}}
    </div>
    
    @if ($project->id !== null)
        @include('partials.comments', ['url' => route('projects.comments', $project)])
    @endif
</div>
