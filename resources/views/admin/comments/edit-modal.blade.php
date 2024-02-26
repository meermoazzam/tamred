<div style="z-index: 1051;" class="modal fade" id="editCommentModal" tabindex="-1" role="dialog" aria-labelledby="editCommentModal"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="min-height: 30vh;">
            <div class="modal-header">
                <h5 class="modal-title">Edit Comment</h5>
                <button class="close btn btn-primary" type="button" data-dismiss="modal"
                    onclick="closeModal('editCommentModal')" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Description</label>
                    <label style="color: red;">*</label>
                    <textarea class="form-control" name="description" id="edit-description" rows=10></textarea>
                </div>
                <br>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id="edit-comment-modal-success-btn" type="button"
                    data-dismiss="modal">Update</button>
            </div>
        </div>
    </div>
</div>
