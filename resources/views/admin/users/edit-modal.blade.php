<div style="z-index: 1051;" class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModal"
    aria-hidden="true">
    <div class="modal-dialog" style="min-width: 600px;" role="document">
        <div class="modal-content" style="min-height: 30vh;">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button class="close btn btn-primary" type="button" data-dismiss="modal"
                    onclick="closeModal('editUserModal')" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>First Name</label>
                        <label style="color: red;">*</label>
                        <input class="form-control" type="text" name="first_name" maxlength="200" id="edit-first_name" placeholder="First Name"
                            autocomplete="first_name">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Last Name</label>
                        <label style="color: red;">*</label>
                        <input class="form-control" type="text" name="last_name" maxlength="200" id="edit-last_name" placeholder="Last Name"
                            autocomplete="last_name">
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <label>Bio</label>
                    <label style="color: red;">*</label>
                    <textarea class="form-control" name="bio" maxlength="1000" id="edit-bio" rows=3></textarea>
                </div>
                <br>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Nick-name</label>
                        <label style="color: red;">*</label>
                        <input class="form-control" type="text" name="nickname" id="edit-nickname" placeholder="Nickname"
                            autocomplete="off">
                    </div>
                    <div class="form-group col-md-6">
                        <label>City</label>
                        <label style="color: red;">*</label>
                        <input class="form-control" type="text" name="city" id="edit-city" placeholder="City"
                            autocomplete="city">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>State</label>
                        <label style="color: red;">*</label>
                        <input class="form-control" type="text" name="state" id="edit-state" placeholder="State"
                            autocomplete="state">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Country</label>
                        <label style="color: red;">*</label>
                        <input class="form-control" type="text" name="country" id="edit-country" placeholder="Country"
                            autocomplete="country">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Gender</label>
                        <label style="color: red;"></label>
                        <select class="modalSelect2" name="gender" id="edit-gender" style="width: 100%">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Date of Birth</label>
                        <label style="color: red;">*</label>
                        <input class="form-control" type="date" name="date_of_birth" id="edit-date_of_birth" placeholder="Date of Birth"
                            autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id="edit-user-modal-success-btn" type="button"
                    data-dismiss="modal">Update</button>
            </div>
        </div>
    </div>
</div>
