@extends('layouts.app')

@section('title')
    Posts
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
                <h1 style="font-style: normal">Posts<h1>
                        <br>
                        <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap"
                            style="width: 100%">
                            <thead class="">
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    {{-- <th>Likes</th> --}}
                                    <th>Comments</th>
                                    <th>Media</th>
                                    <th>Location</th>
                                    <th>User</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($posts as $post)
                                    <tr>
                                        <td>{{ $post['id'] }}</td>
                                        <td title="{{ $post['title'] }}">{{ $post['title'] }}</td>
                                        <td title="{{ $post['status'] }}">
                                            @php
                                                if ($post['status'] == 'published') {
                                                    $badge = 'success';
                                                } elseif ($post['status'] == 'archived') {
                                                    $badge = 'warning';
                                                } elseif ($post['status'] == 'deleted') {
                                                    $badge = 'danger';
                                                } else {
                                                    $badge = 'secondary';
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $badge }} ">{{ $post['status'] }}</span>
                                        </td>
                                        {{-- <td>{{ $post['total_likes'] }}</td> --}}
                                        <td>{{ $post['total_comments'] }} <a href="{{ route('admin.comments.get', ['post_id' => $post->id ]) }}" target="_blank"><i class="fas fa-external-link-alt"></i></a></td>
                                        <td>{{ count($post['media']) }}</td>
                                        <td title="{{ $post['location'] }}">{{ $post['location'] }}</td>
                                        <td
                                            title="{{ $post['user']['first_name'] . ' ' . $post['user']['last_name'] . ' (' . $post['user']['email'] . ')' }}">
                                            <img
                                                src="{{ $post['user']['image'] ? $post['user']['image'] : ' ' }}">
                                            {{ $post['user']['first_name'] . ' ' . $post['user']['last_name'] . '(' . $post['user']['email'] . ')' }}
                                        </td>
                                        <td>{{ $post['created_at'] }}</td>
                                        <td>
                                            <i style="cursor: pointer;" title="Edit"
                                                onclick="editModalOpener({{ $post['id'] }})" class="fas fa-edit">
                                            </i>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
        </div>
    </div>

    @include('admin.posts.edit-modal')
@endsection











@push('scripts')
    <script>
        const update_post_url = "{{ route('admin.posts.update') }}";

        $(document).ready(function() {
            $('#datatable').DataTable({});
            $('#postStatusSelect').select2({});

            posts = @json($posts);
            key_posts = columnToKey(posts, 'id');

        });

        function editModalOpener(id) {
            $("#edit-post-modal-success-btn").attr('onclick', "editPost(" + id + ")");
            $("#edit-title").text(key_posts[id]['title']);
            $("#edit-description").text(key_posts[id]['description']);
            $("#edit-location").val(key_posts[id]['location']);
            $("#edit-city").val(key_posts[id]['city']);
            $("#edit-latitude").val(key_posts[id]['latitude']);
            $("#edit-longitude").val(key_posts[id]['longitude']);
            $("#edit-state").val(key_posts[id]['state']);
            $("#edit-country").val(key_posts[id]['country']);
            $("#postStatusSelect").val(key_posts[id]['status']).trigger('change');
            openModal("editPostModal");
        }

        function editPost(id) {
            showLoader();

            $.ajax({
                type: "post",
                url: update_post_url,
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id,
                    'title': $("#edit-title").val(),
                    'description': $("#edit-description").val(),
                    'location': $("#edit-location").val(),
                    'city': $("#edit-city").val(),
                    'latitude': $("#edit-latitude").val(),
                    'longitude': $("#edit-longitude").val(),
                    'state': $("#edit-state").val(),
                    'country': $("#edit-country").val(),
                    'status': $("#postStatusSelect").val(),
                },
                success: function(data) {
                    hideLoader();
                    if (data.status) {
                        closeModal("editPostModal");
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
                    console.log(data.responseJSON);
                    maxlength = "20"
                    message = 'Error! Failed to update post';
                    if (data.status < 500) {
                        message = data.responseJSON.message;
                    }
                    toastr.error(message);
                }
            }); // end of ajax function
        }
    </script>
@endpush
