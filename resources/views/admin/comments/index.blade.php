@extends('layouts.app')

@section('title')
    Comments
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
                <h1 style="font-style: normal">Comments<h1>
                        <br>
                        <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap"
                            style="width: 100%">
                            <thead class="">
                                <tr>
                                    <th>ID</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Post</th>
                                    <th>User</th>
                                    <th>Child Comments</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($comments as $comment)
                                    <tr>
                                        <td title="{{ $comment['id'] }}">{{ $comment['id'] }}</td>
                                        <td title="{{ $comment['description'] }}">{{ $comment['description'] }}</td>
                                        <td title="{{ $comment['status'] }}">
                                            @php
                                                $badge = ($comment['status']=='published') ? 'primary' : ($comment['status']=='deleted' ? 'danger' : 'warning');
                                            @endphp
                                            <span class="badge bg-{{ $badge }} ">{{ $comment['status'] }}</span>
                                        </td>

                                        <td>{{ $comment['post_id'] }}</td>
                                        <td
                                            title="{{ $comment['user']['first_name'] . ' ' . $comment['user']['last_name'] . ' (' . $comment['user']['email'] . ')' }}">
                                            <img
                                                src="{{ $comment['user']['image'] ? 'https://' . $comment['user']['image'] : ' ' }}">
                                            {{ $comment['user']['first_name'] . ' ' . $comment['user']['last_name'] . '(' . $comment['user']['email'] . ')' }}
                                        </td>
                                        <td>{{ $comment['children_count'] }}</td>
                                        <td>{{ $comment['created_at'] }}</td>
                                        <td>
                                            <i style="cursor: pointer;" title="Edit"
                                                onclick="editModalOpener({{ $comment['id'] }})" class="fas fa-edit">
                                            </i>
                                            <span style="padding: 10px"></span>
                                            <i style="cursor: pointer;" title="Delete"
                                                onclick="deleteConfirmation({{ $comment['id'] }})"
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
    @include('admin.comments.edit-modal')

@endsection











@push('scripts')
    <script>
        const update_comment_url = "{{ route('admin.comments.update') }}";
        const delete_comment_url = "{{ route('admin.comments.delete') }}";

        $(document).ready(function() {
            $('#datatable').DataTable({});

            comments = @json($comments);
            key_comments = columnToKey(comments, 'id');

        });

        function deleteConfirmation(id) {
            $("#modal-custom-body").html('Comment by </b>' + key_comments[id]['user']['first_name'] + '</b>');
            $("#delete-modal-success-btn").attr('onclick', "deleteComment(" + id + ")");
            openModal("deleteModal");
        }

        function editModalOpener(id) {
            $("#edit-comment-modal-success-btn").attr('onclick', "editComment(" + id + ")");
            $("#edit-description").text(key_comments[id]['description']);
            openModal("editCommentModal");
        }

        function editComment(id) {
            showLoader();

            $.ajax({
                type: "post",
                url: update_comment_url,
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id,
                    'description': $('#edit-description').val()
                },
                success: function(data) {
                    hideLoader();
                    if (data.status) {
                        closeModal("editCommentModal");
                        toastr.success(data.message);
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        toastr.error(data['message']);
                    }
                },
                error: function(XMLHttpRequest) {
                    hideLoader();
                    message = 'Error! Failed to edit comment';
                    if(data.status < 500) {
                        message = data.responseJSON.message;
                    }
                    toastr.error(message);
                }
            }); // end of ajax function
        }

        function deleteComment(id) {
            showLoader();
            $.ajax({
                type: "post",
                url: delete_comment_url,
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                },
                success: function(data, xhr) {
                    hideLoader();
                    console.log(xhr);
                    if (xhr == "nocontent") {
                        closeModal("deleteModal");
                        toastr.info("Comment Deleted Successfully");

                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        toastr.error(data['message']);
                    }
                },
                error: function(data, XMLHttpRequest) {
                    hideLoader();
                    message = 'Error! Failed to delete comment';
                    if(data.status < 500) {
                        message = data.responseJSON.message;
                    }
                    toastr.error(message);
                }
            }); // end of ajax function
        }
    </script>
@endpush
