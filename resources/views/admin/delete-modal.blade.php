<!-- Delete Modal-->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModal"
aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete</h5>
                <button class="close btn btn-primary" type="button" data-dismiss="modal" onclick="closeModal('deleteModal')" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Are you sure want to delete this <span id="modal-custom-body"></span>?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal" onclick="closeModal('deleteModal')">Cancel</button>
                <button class="btn btn-danger" id="delete-modal-success-btn" type="button" data-dismiss="modal">Delete</button>
            </div>
        </div>
    </div>
</div>
