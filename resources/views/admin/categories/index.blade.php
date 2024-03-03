@extends('layouts.app')

@section('title')
    Categories
@endsection



@push('styles')
    <style>
        table {
            font-weight: 200;
            font-size: 18px;
        }

        #content {
            font-style: normal;
        }

        .dt-layout-row {
            font-weight: 300;
            font-size: 18px;
        }

        .select2-container {
            z-index: 1100;
        }

        table td {
            transition: all .5s;
            max-width: 120px;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            word-break: break-all;
            vertical-align: middle;
            text-align: center !important;
        }

        th {
            text-align: center !important;
        }
        .fa-trash-alt {
            color: red;
        }
        .fa-edit {
            color: rgb(0, 145, 255);
        }
        table td img {
            max-width: 40px;
        }
    </style>
@endpush





@section('content')
    <div class="wrapper d-flex align-items-stretch col-md-12">
        @include('partials.sidebar')

        <div class="col-md-2"></div>
        <div id="content" class="col-md-10 p-5"><i class="fa-solid fa-house">
                <h1 style="font-style: normal">Categories<h1>
                        <br>
                        <div class=" d-flex justify-content-between">
                            <div class="p-2"></div>
                            <div class="p-2">
                                <div class="tbl-btn">
                                    <button class="btn btn-primary" onclick="createModalOpener()">Add Category</a>
                                </div>
                            </div>
                        </div>
                        <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap"
                            style="width: 100%">
                            <thead class="">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Total Sub Categories</th>
                                    <th>Image</th>
                                    <th>Is Sub Category</th>
                                    <th>Parent</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr>
                                        <td title="{{ $category['id'] }}">{{ $category['id'] }}</td>
                                        <td title="{{ $category['name'] }}">{{ $category['name'] }}</td>
                                        <td title="{{ $category['sub_categories_count'] }}">
                                            {{ $category['sub_categories_count'] }} <a href="{{ $category['sub_categories_count'] != 0 ? route('admin.categories.get', ['parent_id' => $category->id ]) : '#' }}" target="{{ $category['sub_categories_count'] ? '_blank' : '' }}"><i class="fas fa-external-link-alt"></i></a></td>
                                        <td style="text-align: center;"><img
                                                src="{{ $category['icon'] ? $category['icon'] : ' ' }}"></td>
                                        <td><span
                                                class="badge bg-{{ $category['parent_id'] ? 'success' : 'danger' }} ">{{ $category['parent_id'] ? 'Yes' : 'No' }}</span>
                                        </td>
                                        <td>{{ $category['parent'] ? $category['parent']['name'] : 'Not Found' }} <a href="{{ $category['parent'] ? route('admin.categories.get', ['id' => $category->parent_id ]) : '#' }}" target="{{ $category['parent_id'] ? '_blank' : '' }}"><i class="fas fa-external-link-alt"></i></a></td>
                                        <td>{{ $category['created_at'] }}</td>
                                        <td>
                                            <i style="cursor: pointer;" title="Edit"
                                                onclick="editModalOpener({{ $category['id'] }})" class="fas fa-edit">
                                            </i>
                                            <span style="padding: 10px"></span>
                                            <i style="cursor: pointer;" title="Delete"
                                                onclick="deleteConfirmation({{ $category['id'] }})"
                                                class="fas fa-trash-alt">
                                            </i>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
        </div>
    </div>

    @include('admin.delete-modal')
    @include('admin.categories.create-modal')
    @include('admin.categories.edit-modal')
@endsection











@push('scripts')
    <script>
        const create_category_url = "{{ route('admin.categories.create') }}";
        const update_category_url = "{{ route('admin.categories.update') }}";
        const delete_category_url = "{{ route('admin.categories.delete') }}";

        $(document).ready(function() {
            $('#datatable').DataTable({});
            $('.modalSelect2').select2({
                placeholder: 'Choose Parent Category',
                allowClear: true
            });

            categories = @json($categories);
            key_categories = columnToKey(categories, 'id');

        });

        function deleteConfirmation(id) {
            $("#modal-custom-body").html('Category: <b>' + key_categories[id]['name'] + '</b>');
            $("#delete-modal-success-btn").attr('onclick', "deleteCategory(" + id + ")");
            openModal("deleteModal");
        }

        function editModalOpener(id) {
            $('#edit-parent').find('option').prop('disabled', false);
            $('#edit-parent').find('option[value="' + id + '"]').prop('disabled', true);

            $("#edit-category-modal-success-btn").attr('onclick', "editCategory(" + id + ")");
            $("#edit-name").val(key_categories[id]['name']);
            $("#edit-parent").val(key_categories[id]['parent_id']).trigger('change');

            openModal("editCategoryModal");
        }

        function createModalOpener() {
            $("#create-category-modal-success-btn").attr('onclick', "createCategory()");
            $("#create-name").val();
            $("#create-parent").val(null).trigger('change');

            openModal("createCategoryModal");
        }

        function createCategory() {
            showLoader();
            var formData = new FormData();

            var files = document.getElementById('create-file').files;
            if (files.length > 0) {
                formData.append('file', files[0]);
            }
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            formData.append('name', $('#create-name').val());
            formData.append('parent_id', $('#create-parent').val() ?? 0);


            $.ajax({
                type: "post",
                url: create_category_url,
                data: formData,
                processData: false,
                contentType: false,
                success: function(data, xhr) {
                    hideLoader();
                    if (data.status) {
                        closeModal("createCategoryModal");
                        toastr.success("Category Created Successfully!");
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        toastr.error(data['message']);
                    }
                },
                error: function(XMLHttpRequest) {
                    hideLoader();
                    message = 'Error! Failed to create category';
                    if(data.status < 500) {
                        message = data.responseJSON.message;
                    }
                    toastr.error(message);
                }
            }); // end of ajax function
        }

        function editCategory(id) {
            showLoader();
            var formData = new FormData();

            var files = document.getElementById('edit-file').files;
            if (files.length > 0) {
                formData.append('file', files[0]);
            }
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            formData.append('id', id);
            formData.append('name', $('#edit-name').val());
            formData.append('parent_id', $('#edit-parent').val() ?? 0);



            $.ajax({
                type: "post",
                url: update_category_url,
                data: formData,
                processData: false,
                contentType: false,
                success: function(data, xhr) {
                    hideLoader();
                    if (data.status) {
                        closeModal("editCategoryModal");
                        toastr.success("Category Updated Successfully");
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        toastr.error(data['message']);
                    }
                },
                error: function(XMLHttpRequest) {
                    hideLoader();
                    message = 'Error! Failed to update category';
                    if(data.status < 500) {
                        message = data.responseJSON.message;
                    }
                    toastr.error(message);
                }
            }); // end of ajax function
        }

        function deleteCategory(id) {
            showLoader();
            $.ajax({
                type: "post",
                url: delete_category_url,
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                },
                success: function(data, xhr) {
                    hideLoader();
                    console.log(xhr);
                    if (xhr == "nocontent") {
                        closeModal("deleteModal");
                        toastr.info("Category Deleted Successfully");

                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        toastr.error(data['message']);
                    }
                },
                error: function(XMLHttpRequest) {
                    hideLoader();
                    message = 'Error! Failed to delete category';
                    if(data.status < 500) {
                        message = data.responseJSON.message;
                    }
                    toastr.error(message);
                }
            }); // end of ajax function
        }
    </script>
@endpush
