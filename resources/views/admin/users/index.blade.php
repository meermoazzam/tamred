@extends('layouts.app')

@section('title')
    Dashboard
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
            <h1 style="font-style: normal">Users<h1>
                <br>
            <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap"
                style="width: 100%">
                <thead class="">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>DOB</th>
                        <th>Gender</th>
                        <th>Post's</th>
                        <th title="Followers/Following">F/F</th>
                        <th>Joined At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td title="{{ $user['id'] }}">{{ $user['id'] }}</td>
                            <td title="{{ $user['first_name'] . ' ' . $user['last_name'] }}"><img src="{{ $user['image'] ? 'https://' . $user['image'] : ' ' }}"> {{ $user['first_name'] . ' ' . $user['last_name'] }}</td>
                            <td title="{{ $user['email'] }}">{{ $user['email'] }}</td>
                            <td title="{{ $user['date_of_birth'] }}">{{ $user['date_of_birth'] }}</td>
                            <td title="{{ $user['gender'] }}">{{ $user['gender'] }}</td>
                            <td title="{{ $user['post_count'] }}">{{ $user['post_count'] }}</td>
                            <td>{{ $user['following_count'] . '/' . $user['following_count'] }}</td>
                            <td>{{ $user['created_at'] }}</td>
                            <td>
                                <i style="cursor: pointer;" title="Edit"
                                    onclick="editModalOpener({{ $user['id'] }})" class="fas fa-edit">
                                </i>
                                <span style="padding: 10px"></span>
                                <i style="cursor: pointer;" title="Delete"
                                    onclick="deleteConfirmation({{ $user['id'] }})" class="fas fa-trash-alt">
                                </i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @include('admin.delete-modal')
    @include('admin.users.edit-modal')

@endsection











@push('scripts')
    <script>
        const update_user_url = "{{ route('admin.users.update') }}";
        const delete_user_url = "{{ route('admin.users.delete') }}";

        $(document).ready(function() {
            $('#datatable').DataTable({});
            $('.modalSelect2').select2({
                placeholder: 'Choose Gender',
                allowClear: true
            });

            users = @json($users);
            key_users = columnToKey(users, 'id');

        });

        function deleteConfirmation(id) {
            $("#modal-custom-body").html('User: </b>' + key_users[id]['first_name'] + '</b>');
            $("#delete-modal-success-btn").attr('onclick', "deleteUser(" + id + ")");
            openModal("deleteModal");
        }

        function editModalOpener(id) {
            $("#edit-user-modal-success-btn").attr('onclick', "editUser(" + id + ")");
            $("#edit-first_name").val(key_users[id]['first_name']);
            $("#edit-last_name").val(key_users[id]['last_name']);
            $("#edit-bio").text(key_users[id]['bio']);
            $("#edit-nickname").val(key_users[id]['nickname']);
            $("#edit-date_of_birth").val(key_users[id]['date_of_birth']);
            $("#edit-city").val(key_users[id]['city']);
            $("#edit-state").val(key_users[id]['state']);
            $("#edit-country").val(key_users[id]['country']);
            $("#edit-gender").val(key_users[id]['gender']).trigger('change');
            openModal("editUserModal");
        }

        function editUser(id) {
            showLoader();

            $.ajax({
                type: "post",
                url: update_user_url,
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id,
                    'first_name': $("#edit-first_name").val(),
                    'last_name': $("#edit-last_name").val(),
                    'bio': $("#edit-bio").val(),
                    'nickname': $("#edit-nickname").val(),
                    'date_of_birth': $("#edit-date_of_birth").val(),
                    'city': $("#edit-city").val(),
                    'gender': $("#edit-gender").val(),
                    'state': $("#edit-state").val(),
                    'country': $("#edit-country").val(),
                },
                success: function(data) {
                    hideLoader();
                    if (data.status) {
                        closeModal("editUserModal");
                        toastr.success(data.message);
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        toastr.error(data['message']);
                    }
                },
                error: function(data, XMLHttpRequest) {
                    hideLoader();
                    console.log(data.responseJSON);maxlength="20"
                    message = 'Error! Failed to update user';
                    if(data.status < 500) {
                        message = data.responseJSON.message;
                    }
                    toastr.error(message);
                }
            }); // end of ajax function
        }

        function deleteUser(id) {
            showLoader();
            $.ajax({
                type: "post",
                url: delete_user_url,
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                },
                success: function(data, xhr) {
                    hideLoader();
                    console.log(xhr);
                    if (xhr == "nocontent") {
                        closeModal("deleteModal");
                        toastr.info("User Deleted Successfully");

                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        toastr.error(data['message']);
                    }
                },
                error: function(data, XMLHttpRequest) {
                    hideLoader();
                    message = 'Error! Failed to delete user';
                    if(data.status < 500) {
                        message = data.responseJSON.message;
                    }
                    toastr.error(message);
                }
            }); // end of ajax function
        }
    </script>
@endpush
