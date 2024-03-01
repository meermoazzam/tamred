<!-- Recover Modal-->
<div class="modal fade" id="recoverModal" tabindex="-1" role="dialog" aria-labelledby="recoverModal"
aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Recover</h5>
                <button class="close btn btn-primary" type="button" data-dismiss="modal" onclick="closeModal('recoverModal')" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Are you sure want to recover this <span id="recover-modal-custom-body"></span>?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal" onclick="closeModal('recoverModal')">Cancel</button>
                <button class="btn btn-success" id="recover-modal-success-btn" type="button" data-dismiss="modal">Recover</button>
            </div>
        </div>
    </div>
</div>
