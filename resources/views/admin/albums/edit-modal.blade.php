<div style="z-index: 1051;" class="modal fade" id="editAlbumModal" tabindex="-1" role="dialog" aria-labelledby="editAlbumModal"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="min-height: 30vh;">
            <div class="modal-header">
                <h5 class="modal-title">Edit Album</h5>
                <button class="close btn btn-primary" type="button" data-dismiss="modal"
                    onclick="closeModal('editAlbumModal')" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Name</label>
                    <label style="color: red;">*</label>
                    <input class="form-control" type="text" name="name" id="edit-name" placeholder="Album Name"
                        autocomplete="off">
                </div>
                <br>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id="edit-album-modal-success-btn" type="button"
                    data-dismiss="modal">Update</button>
            </div>
        </div>
    </div>
</div>
