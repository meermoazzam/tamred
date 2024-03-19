<div style="z-index: 1051;" class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModal"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="min-height: 50vh;">
            <div class="modal-header">
                <h5 class="modal-title">Edit Category</h5>
                <button class="close btn btn-primary" type="button" data-dismiss="modal"
                    onclick="closeModal('editCategoryModal')" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Name</label>
                    <label style="color: red;">*</label>
                    <input class="form-control" type="text" name="name" id="edit-name" placeholder="Category Name"
                        autocomplete="off">
                </div>
                <br>
                <div class="form-group">
                    <label>Name In Italian</label>
                    <label style="color: red;">*</label>
                    <input class="form-control" type="text" name="italian-name" id="edit-italian-name" placeholder="Category Name in Italian"
                        autocomplete="off">
                </div>
                <br>
                <div class="form-group">
                    <label>Choose File/Icon</label>
                    <label style="color: red;"></label>
                    <input class="form-control" type="file" name="icon" id="edit-file" accept="image/*">
                </div>
                <br>
                <div class="form-group">
                    <label>Choose Parent Category</label>
                    <label style="color: red;"></label>
                    <select class="modalSelect2" name="parent" id="edit-parent" style="width: 100%">
                        @foreach ($categories as $category)
                            <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <br>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id="edit-category-modal-success-btn" type="button"
                    data-dismiss="modal">Update</button>
            </div>
        </div>
    </div>
</div>
