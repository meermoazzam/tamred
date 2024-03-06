@extends('layouts.app')

@section('title')
    Albums
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
                <h1 style="font-style: normal">Albums<h1>
                        <br>
                        <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap"
                            style="width: 100%">
                            <thead class="">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Posts</th>
                                    <th>User's ID</th>
                                    <th>User's Name</th>
                                    <th>User Email</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($albums as $album)
                                    <tr>
                                        <td title="{{ $album['id'] }}">{{ $album['id'] }}</td>
                                        <td title="{{ $album['name'] }}">{{ $album['name'] }}</td>
                                        <td title="{{ $album['status'] }}">
                                            @php
                                                $badge = ($album['status']=='default') ? 'success' : ($album['status']=='deleted' ? 'danger' : 'primary');
                                            @endphp
                                            <span class="badge bg-{{ $badge }} ">{{ $album['status'] }}</span>
                                        </td>
                                        <td>{{ $album['posts_count'] }} <a href="{{ route('admin.posts.get', ['album_id' => $album['id'] ]) }}" target="_blank"><i class="fas fa-external-link-alt"></i></a></td>
                                        <td>{{ $album['user']['id'] }} <a href="{{ route('admin.users.get', ['id' => $album['user']['id'] ]) }}" target="_blank"><i class="fas fa-external-link-alt"></i></a></td>
                                        <td title="{{ $album['user']['first_name'] . ' ' . $album['user']['last_name'] }}">
                                            {{ $album['user']['first_name'] . ' ' . $album['user']['last_name'] }}
                                        </td>
                                        <td
                                            title="{{ $album['user']['email'] }}">
                                            <img
                                                src="{{ $album['user']['image'] ? $album['user']['image'] : ' ' }}">
                                                {{ $album['user']['email'] }}
                                        </td>
                                        <td>{{ $album['created_at'] }}</td>
                                        <td>
                                            <i style="cursor: pointer;" title="Edit"
                                                onclick="editModalOpener({{ $album['id'] }})" class="fas fa-edit">
                                            </i>
                                            <span style="padding: 10px"></span>
                                            @if ($album['status'] == 'deleted')
                                                <i style="cursor: pointer;" title="Recover"
                                                    onclick="recoverConfirmation({{ $album['id'] }})"
                                                    class="fas fa-undo">
                                                </i>
                                            @else
                                                <i style="cursor: pointer;" title="Delete"
                                                    onclick="deleteConfirmation({{ $album['id'] }})"
                                                    class="fas fa-trash-alt">
                                                </i>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
        </div>
    </div>

    @include('admin.delete-modal')
    @include('admin.recover-modal')
    @include('admin.albums.edit-modal')

@endsection





@push('scripts')
    <script>
        const update_album_url = "{{ route('admin.albums.update') }}";
        const delete_album_url = "{{ route('admin.albums.delete') }}";
        const recover_album_url = "{{ route('admin.albums.recover') }}";

        $(document).ready(function() {
            $('#datatable').DataTable({});

            albums = @json($albums);
            key_albums = columnToKey(albums, 'id');

        });

        function deleteConfirmation(id) {
            $("#modal-custom-body").html('Album: <b>' + key_albums[id]['name'] + '</b>');
            $("#delete-modal-success-btn").attr('onclick', "deleteAlbum(" + id + ")");
            openModal("deleteModal");
        }

        function recoverConfirmation(id) {
            $("#recover-modal-custom-body").html('Album: <b>' + key_albums[id]['name'] + '</b>');
            $("#recover-modal-success-btn").attr('onclick', "recoverAlbum(" + id + ")");
            openModal("recoverModal");
        }

        function editModalOpener(id) {
            $("#edit-album-modal-success-btn").attr('onclick', "editAlbum(" + id + ")");
            $("#edit-name").val(key_albums[id]['name']);
            openModal("editAlbumModal");
        }

        function editAlbum(id) {
            showLoader();

            $.ajax({
                type: "post",
                url: update_album_url,
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id,
                    'name': $('#edit-name').val()
                },
                success: function(data) {
                    hideLoader();
                    if (data.status) {
                        closeModal("editAlbumModal");
                        toastr.success("Album Updated Successfully");
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        toastr.error(data['message']);
                    }
                },
                error: function(XMLHttpRequest) {
                    hideLoader();
                    message = 'Error! Failed to update album';
                    if(data.status < 500) {
                        message = data.responseJSON.message;
                    }
                    toastr.error(message);
                }
            }); // end of ajax function
        }

        function deleteAlbum(id) {
            showLoader();
            $.ajax({
                type: "post",
                url: delete_album_url,
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                },
                success: function(data, xhr) {
                    hideLoader();
                    if (xhr == "nocontent") {
                        closeModal("deleteModal");
                        toastr.info("Album Deleted Successfully");

                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        toastr.error(data['message']);
                    }
                },
                error: function(data, XMLHttpRequest) {
                    hideLoader();
                    message = 'Error! Failed to delete album';
                    if(data.status < 500) {
                        message = data.responseJSON.message;
                    }
                    toastr.error(message);
                }
            }); // end of ajax function
        }

        function recoverAlbum(id) {
            showLoader();
            $.ajax({
                type: "post",
                url: recover_album_url,
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                },
                success: function(data, xhr) {
                    hideLoader();
                    if (data.status) {
                        closeModal("recoverModal");
                        toastr.success("Album Updated Successfully");
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        toastr.error(data['message']);
                    }
                },
                error: function(data, XMLHttpRequest) {
                    hideLoader();
                    message = 'Error! Failed to delete album';
                    if(data.status < 500) {
                        message = data.responseJSON.message;
                    }
                    toastr.error(message);
                }
            }); // end of ajax function
        }
    </script>
@endpush
