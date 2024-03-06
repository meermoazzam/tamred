<div style="z-index: 1051;" class="modal fade" id="createMediaModal" tabindex="-1" role="dialog" aria-labelledby="createMediaModal"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="min-height: 50vh;">
            <div class="modal-header">
                <h5 class="modal-title">Create Media</h5>
                <button class="close btn btn-primary" type="button" data-dismiss="modal"
                    onclick="closeModal('createMediaModal')" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <form id="create-form">
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-12" style="width: 90%; 5px; margin:20px; border-radius: 10px;">
                            <label>Choose Thumbnail</label>
                            <label style="color: red;">*</label>
                            <input class="form-control" type="file" name="thumbnail" id="thumbnail">
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12" style="width: 90%; 5px; margin:20px; border-radius: 10px;">
                            <label>Choose Media</label>
                            <label style="color: red;">*</label>
                            <input class="form-control" type="file" name="media-input" id="media-input">
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label>Type</label>
                            <label style="color: red;"></label>
                            <select id="add-file-type" name="add-file-type" style="width: 50%">
                                <option value="image">Image</option>
                                <option value="video">Video</option>
                            </select>
                        </div>
                    </div>
                </div>
                <br>
            </form>
            <div class="modal-footer">
                <button class="btn btn-success" id="create-media-modal-success-btn" onclick="createMedia()" type="button"
                    data-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>
