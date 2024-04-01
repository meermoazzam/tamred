@extends('layouts.app')

@section('title')
    Explore Screen Data
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
        <div class='row col-md-12'>
            <div class="col-md-2"></div>
            <div class="col-md-10 p-4">
                <p style="font-style: normal; font-size: 36px;">Explore Screen Data<p>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Title</label>
                            <label style="color: red;"></label>
                            <input class="form-control" type="text" name="title" id="title" value="{{ isset($data['title']) ? $data['title'] : '' }}">

                            <label>Title Italian</label>
                            <label style="color: red;"></label>
                            <input class="form-control" type="text" name="title_italian" id="title_italian" value="{{ isset($data['title_italian']) ? $data['title_italian'] : '' }}">
                        </div>
                        <br>
                        <br>
                        <div class="form-group">
                            <label>Description</label>
                            <label style="color: red;"></label>
                            <input class="form-control" type="text" name="description" id="description" value="{{ isset($data['description']) ? $data['description'] : '' }}">

                            <label>Description Italian</label>
                            <label style="color: red;"></label>
                            <input class="form-control" type="text" name="description_italian" id="description_italian" value="{{ isset($data['description_italian']) ? $data['description_italian'] : '' }}">
                        </div>

                        <div class="form-group">
                            <label>Choose Image</label>
                            <label style="color: red;"></label>
                            <input class="form-control" type="file" name="media" id="media" accept="image/*">
                        </div>
                    </div>

                    <div class="col-md-5" id="media">
                        <img src="{{ isset($data['url']) ? $data['url'] : '' }}" style="max-width: 400px; max-height: 400px" >
                    </div>
                </div>
                <br>
                <br>
                <button class="btn btn-success" id="update-button" type="button" onclick="updateData(this)">Update</button>
            </div>
        </div>

    </div>

@endsection











@push('scripts')
    <script>

        const update_data_url = "{{ route('admin.explorescreendata.add') }}";

        function updateData(e) {
            showLoader();

            var formData = new FormData();

            var media = document.getElementById('media').files;
            if (media.length > 0) {
                formData.append('file', media[0]);
            }

            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            formData.append('title', $('#title').val());
            formData.append('title_italian', $('#title_italian').val());
            formData.append('description', $('#description').val());
            formData.append('description_italian', $('#description_italian').val());

            $.ajax({
                type: "post",
                url: update_data_url,
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    hideLoader();
                    if (data.status) {
                        toastr.success("Updated Successfully!");
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
