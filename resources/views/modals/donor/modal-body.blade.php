<p>Please search below for the Australian Conference which is Donor for this Project, select only one of them, and then click the "Update" button.</p>
<form class="js-modal-table-filter">
    <div class="row">
        <div class="col-sm-4">
            <div class="form-group">
                <label for="state">State Council</label>
                <select name="state" id="state" class="form-control">
                    <option value="">All States</option>
                    @foreach (Helper::getAUStates() as $key => $state)
                        <option value="{{ $key }}">{{ strtoupper($key) }}</option>
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
                <input type="hidden" name="per_page" value="10">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block" data-text-progress="Applying..." data-text-default="Apply Filter">Apply Filter</button>
            </div>
        </div>
    </div>
</form>
<div class="row mb-2">
    <div class="col-sm-6">
        <form class="form-inline js-table-search-form">
            <div class="form-group has-btn">
                <label class="sr-only" for="modal-donor-search">Search</label>
                <input type="text" class="form-control js-table-search-input" id="modal-donor-search" placeholder="Search">
                <button class="btn"><i class="fa fa-search" aria-hidden="true"></i></button>
            </div>
        </form>
    </div>
    <div class="col-sm-6 text-right">
        @include('pagination.basic')
    </div>
</div>
