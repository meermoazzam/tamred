<div style="z-index: 1051;" class="modal fade" id="createAddModal" tabindex="-1" role="dialog" aria-labelledby="createAddModal"
    aria-hidden="true">
    <div class="modal-dialog" style="min-width: 600px;" role="document">
        <div class="modal-content" style="min-height: 30vh;">
            <div class="modal-header">
                <h5 class="modal-title">Create ad</h5>
                <button class="close btn btn-primary" type="button" data-dismiss="modal"
                    onclick="closeModal('createAddModal')" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- title author --}}
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Title</label>
                        <label style="color: red;">*</label>
                        <input class="form-control" type="text" name="title" id="create-title" placeholder="Title">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Author</label>
                        <label style="color: red;">*</label>
                        <input class="form-control" type="text" name="author" id="create-author" placeholder="Author">
                    </div>
                </div>
                <br>
                {{-- Link --}}
                <div class="form-group">
                    <label>Link</label>
                    <label style="color: red;">*</label>
                    <input class="form-control" type="text" name="link" id="create-link" placeholder="Link">
                </div>
                <br>
                {{-- dates --}}
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Start Date</label>
                        <label style="color: red;">*</label>
                        <input class="form-control" type="date" name="start_date" id="create-start_date" autocomplete="start_date">
                    </div>
                    <div class="form-group col-md-6">
                        <label>End Date</label>
                        <label style="color: red;">*</label>
                        <input class="form-control" type="date" name="end_date" id="create-end_date" autocomplete="end_date">
                    </div>
                </div>
                <br>
                {{-- age --}}
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Min Age</label>
                        <label style="color: red;">*</label>
                        <input class="form-control" type="number" name="min_age" id="create-min_age" placeholder="Min Age"
                            autocomplete="min_age">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Max Age</label>
                        <label style="color: red;">*</label>
                        <input class="form-control" type="number" name="max_age" id="create-max_age" placeholder="Max Age"
                            autocomplete="max_age">
                    </div>
                </div>
                <br>
                {{-- gender --}}
                <div class="row">
                    <div class="form-group col-md-12">
                        <label>Gender</label>
                        <label style="color: red;"></label>
                        <select id="create-addGenderSelect" name="gender" style="width: 100%">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <br>
                {{-- Lat Long --}}
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Latitude</label>
                        <label style="color: red;">*</label>
                        <input class="form-control" type="text" name="latitude" id="create-latitude" placeholder="Latitude"
                            autocomplete="latitude">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Longitude</label>
                        <label style="color: red;">*</label>
                        <input class="form-control" type="text" name="longitude" id="create-longitude" placeholder="Longitude"
                            autocomplete="longitude">
                    </div>
                </div>
                <br>

                {{-- Range --}}
                <div class="form-group">
                    <label>Range(km)</label>
                    <label style="color: red;">*</label>
                    <input class="form-control" type="text" name="range" id="create-range" placeholder="Range"
                        autocomplete="range">
                </div>
                <br>

                {{-- status --}}
                <div class="row">
                    <div class="form-group col-md-12">
                        <label>Status</label>
                        <label style="color: red;"></label>
                        <select id="create-addStatusSelect" name="status" style="width: 100%">
                            @foreach (config('constants.adds.status') as $status)
                                @if ($status != 'active')
                                    <option value="{{ $status }}">{{ ucwords($status) }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id="create-add-modal-success-btn" type="button"
                    data-dismiss="modal">Create</button>
            </div>
        </div>
    </div>
</div>
