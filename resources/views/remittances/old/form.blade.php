<h4 class="form-heading">General Old Remittance Information</h4>

<div class="row">
    <div class="col-sm-2">
        <div class="form-group">
            <label for="id">Remittance-In ID</label>
            {!! Form::text('id', null, ['class' => 'form-control', 'id' => 'id', 'readonly' => 'readonly']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-2">
        <div class="form-group">
            <label for="state">State/Territory Council</label>
            {!! Form::select('state', Helper::getAUStates(), null, ['class' => 'form-control', 'id' => 'state', 'readonly' => 'readonly']) !!}
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label for="received_at">Date Received</label>
            {!! Form::text('received_at', optional($remittance)->received_at->format(config('vinnies.date_format')), ['class' => 'form-control', 'id' => 'received_at', 'readonly' => 'readonly']) !!}
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label for="quarter_year">Quarter/Year</label>
            {!! Form::text('quarter_year', ('Q' . $remittance->quarter . ':' . $remittance->year), ['class' => 'form-control', 'id' => 'quarter_year', 'readonly' => 'readonly']) !!}
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label for="payment_method">Payment Method</label>
            {!! Form::text('payment_method', null, ['class' => 'form-control', 'id' => 'payment_method', 'readonly' => 'readonly']) !!}
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label for="cheque_number">Cheque Number (CQ#)</label>
            {!! Form::text('cheque_number', null, ['class' => 'form-control', 'id' => 'cheque_number', 'readonly' => 'readonly']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-2">
        <div class="form-group">
            <label for="total">Donation Total</label>
            {!! Form::text('total', $remittance->getFormattedTotalDonations(), ['class' => 'form-control', 'id' => 'total', 'readonly' => 'readonly']) !!}
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label for="allocated">Allocated</label>
            {!! Form::text('allocated', $remittance->getFormattedAllocated(), ['class' => 'form-control', 'id' => 'allocated', 'readonly' => 'readonly']) !!}
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label for="to_allocate">To allocate</label>
            {!! Form::text('to_allocate', $remittance->getFormattedToAllocate(), ['class' => 'form-control', 'id' => 'to_allocate', 'readonly' => 'readonly']) !!}
        </div>
    </div>
    <div class="col-sm-2">
        <div class="form-group">
            <label for="twins">Twinning Total</label>
            {!! Form::text('twins', $remittance->getTwinningTotals(), ['class' => 'form-control', 'id' => 'twins', 'readonly' => 'readonly']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <label for="comments">Comments</label>
            {!! Form::textarea('comments', null, ['class' => 'form-control form-control-text-danger js-stretch', 'id' => 'comments', 'readonly' => 'readonly', 'rows' => null, 'cols' => null]) !!}
        </div>
    </div>
</div>
