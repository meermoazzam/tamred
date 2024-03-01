<div style="z-index: 1051;" class="modal fade" id="editPostModal" tabindex="-1" role="dialog" aria-labelledby="editPostModal"
    aria-hidden="true">
    <div class="modal-dialog" style="min-width: 600px;" role="document">
        <div class="modal-content" style="min-height: 30vh;">
            <div class="modal-header">
                <h5 class="modal-title">Edit Post</h5>
                <button class="close btn btn-primary" type="button" data-dismiss="modal"
                    onclick="closeModal('editPostModal')" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Title</label>
                    <label style="color: red;">*</label>
                    <textarea class="form-control" name="title" maxlength="1000" id="edit-title" rows=2></textarea>
                </div>
                <br>
                <div class="form-group">
                    <label>Description</label>
                    <label style="color: red;">*</label>
                    <textarea class="form-control" name="description" maxlength="10000" id="edit-description" rows=4></textarea>
                </div>
                <br>
                <div class="form-group">
                    <label>Location</label>
                    <label style="color: red;">*</label>
                    <textarea class="form-control" name="location" maxlength="255" id="edit-location" rows=2></textarea>
                </div>
                <br>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Latitude</label>
                        <label style="color: red;">*</label>
                        <input class="form-control" type="text" name="latitude" maxlength="20" id="edit-latitude" placeholder="Latitude"
                            autocomplete="latitude">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Longitude</label>
                        <label style="color: red;">*</label>
                        <input class="form-control" type="text" name="longitude" maxlength="20" id="edit-longitude" placeholder="Longitude"
                            autocomplete="longitude">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>City</label>
                        <label style="color: red;">*</label>
                        <input class="form-control" type="text" name="city" maxlength="200" id="edit-city" placeholder="City"
                            autocomplete="city">
                    </div>
                    <div class="form-group col-md-6">
                        <label>State</label>
                        <label style="color: red;">*</label>
                        <input class="form-control" type="text" name="state" maxlength="200" id="edit-state" placeholder="State"
                            autocomplete="state">
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <label>Country</label>
                    <label style="color: red;">*</label>
                    <input class="form-control" type="text" name="country" maxlength="200" id="edit-country" placeholder="Country"
                        autocomplete="country">
                </div>
                <br>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label>Status</label>
                        <label style="color: red;"></label>
                        <select id="postStatusSelect" name="status" style="width: 100%">
                            @foreach (config('constants.posts.status') as $status)
                                @if ($status != 'draft')
                                    <option value="{{ $status }}">{{ ucwords($status) }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id="edit-post-modal-success-btn" type="button"
                    data-dismiss="modal">Update</button>
            </div>
        </div>
    </div>
</div>
