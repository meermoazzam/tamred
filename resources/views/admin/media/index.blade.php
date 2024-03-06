@extends('layouts.app')

@section('title')
    Media
@endsection



@push('styles')
    <style>
        #content {
            font-style: normal;
        }

        .dt-layout-row {
            font-weight: 300;
            font-size: 18px;
        }

        #container {
            margin: 30px;
            border: 1px solid lightgray;
            border-radius: 10px;
            background-color: #fff; /* Background color of the div */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Diffused shadow effect */
            padding: 20px; /* Optional: adds some padding to the div content */
            text-align: center; /* Optional: centers the text horizontally */
        }

        .fa-trash-alt {
            position: absolute; /* Set absolute positioning for the icon */
            top: 10px; /* Adjust top position as needed */
            right: 10px; /* Adjust right position as needed */
            color: red;
            font-size: larger;
        }
        #container {
            position: relative; /* Set relative positioning for the container */
            width: 600px; /* Adjust width of container as needed */
            height: 450px; /* Adjust height of container as needed */
            background-color: #f0f0f0; /* Background color for visualization */
            padding: 20px; /* Optional: Add padding to the container */
        }
        img {
            border-radius: 10px;
        }
        video {
            border-radius: 10px;
            background: lightgray;
            padding-top: 20px;
        }

    </style>
@endpush





@section('content')
    <div class="wrapper d-flex align-items-stretch col-md-12">
        @include('partials.sidebar')

        <div class="col-md-2"></div>
        <div id="content" class="col-md-10 p-5"><i class="fa-solid fa-house">
            <h1 style="font-style: normal">Media (Total: {{ count($media) }})<h1>
            <div class=" d-flex justify-content-between">
                <div class="p-2"></div>
                <div class="p-2">
                    @if (request()->model_type == 'add')
                        <div class="tbl-btn">
                            <button class="btn btn-primary" onclick="createModalOpener({{ request()->model_id }}, '{{ request()->model_type }}' )">Add Media</a>
                        </div>
                    @endif
                </div>
            </div>
            <br>
            @foreach ($media as $key => $row)
                <div class="row col-md-12">
                    <div class="row col-md-6" id="container">
                        @if ($row['mediable_type'] == 'add')
                            <i class="fas fa-trash-alt" onclick="deleteMediaSetter({{$row['id']}})"></i>
                        @endif
                        <h3>No. {{ $key + 1 }} ({{ $row['type'] }})</h3>
                        <div class="col-md-6" id="description">
                            <br><br>
                            <a href="{{ $row['media_key'] }}" target="_blank"><h4>External Link</h4></a>
                            <h4>Type: {{ ucfirst($row['type']) }}</h4>
                        </div>
                        <div class="col-md-6" id="media">
                            @if ($row->type == 'image')
                                <img src="{{ $row['thumbnail_key'] ?? $row['media_key'] }}" width="225" height="325">
                            @else
                                <video width="250" height="350" poster="{{ $row['thumbnail_key'] }}" controls>
                                    <source src="{{ $row['media_key'] }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6"></div>
                </div>
                <hr>
                <br>
            @endforeach
        </div>
    </div>

    @include('admin.delete-modal')
    @include('admin.media.create-modal')

@endsection











@push('scripts')
    <script>

        const create_media_url = "{{ route('admin.media.create') }}";
        const delete_media_url = "{{ route('admin.media.delete') }}";

        model_id = @json(request()->model_id);
        model_type = @json(request()->model_type);

        $(document).ready(function() {
            $('#add-file-type').select2({});
        });

        function createModalOpener(model_id, model_type) {
            document.getElementById('create-form').reset();
            openModal('createMediaModal');
        }

        function createMedia() {
            showLoader();
            var formData = new FormData();

            var thumbnail = document.getElementById('thumbnail').files;
            var media = document.getElementById('media-input').files;

            if (thumbnail.length > 0) {
                formData.append('thumbnail', thumbnail[0]);
            }
            if (media.length > 0) {
                formData.append('media', media[0]);
            }

            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            formData.append('model_id', model_id);
            formData.append('model_type', model_type);
            formData.append('type', $('#add-file-type').val());


            $.ajax({
                type: "post",
                url: create_media_url,
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    hideLoader();
                    if (data.status) {
                        closeModal("createMediaModal");
                        toastr.success("Media Added Successfully!");
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        toastr.error(data['message']);
                    }
                },
                error: function(data) {
                    hideLoader();
                    errors = data.responseJSON.errors;
                    for (key in errors) {
                        toastr.error(errors[key][0]);
                    }
                }
            }); // end of ajax function
        }

        function deleteMediaSetter(id) {
            $("#modal-custom-body").html('media');
            $("#delete-modal-success-btn").attr('onclick', "deleteMedia(" + id + ")");
            openModal("deleteModal");
        }

        function deleteMedia(id) {
            showLoader();
            $.ajax({
                type: "post",
                url: delete_media_url,
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id
                },
                success: function(data, xhr) {
                    hideLoader();
                    if (xhr == "nocontent") {
                        closeModal("deleteModal");
                        toastr.info("Media Deleted Successfully");

                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        toastr.error(data['message']);
                    }
                },
                error: function(data) {
                    hideLoader();
                    errors = data.responseJSON.errors;
                    for (key in errors) {
                        toastr.error(errors[key][0]);
                    }
                }
            }); // end of ajax function
        }
    </script>
@endpush
