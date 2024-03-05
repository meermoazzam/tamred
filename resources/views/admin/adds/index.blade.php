@extends('layouts.app')

@section('title')
    Adds
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
                <h1 style="font-style: normal">Adds<h1>
                        <div class=" d-flex justify-content-between">
                            <div class="p-2"></div>
                            <div class="p-2">
                                <div class="tbl-btn">
                                    <button class="btn btn-primary" onclick="createModalOpener()">Create New Add</a>
                                </div>
                            </div>
                        </div>
                        <br>
                        <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap"
                            style="width: 100%">
                            <thead class="">
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Status</th>
                                    <th>Link</th>
                                    <th>Media</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Gender</th>
                                    <th>Age(Min)</th>
                                    <th>Age(Max)</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Range</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($adds as $add)
                                    <tr>
                                        <td title="{{ $add['id'] }}">{{ $add['id'] }}</td>
                                        <td title="{{ $add['title'] }}">{{ $add['title'] }}</td>
                                        <td title="{{ $add['author'] }}">{{ $add['author'] }}</td>
                                        <td title="{{ $add['status'] }}">
                                            @php
                                                if ($add['status'] == 'active') {
                                                    $badge = 'success';
                                                } elseif ($add['status'] == 'expired') {
                                                    $badge = 'warning';
                                                } elseif ($add['status'] == 'deleted') {
                                                    $badge = 'danger';
                                                } else {
                                                    $badge = 'secondary';
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $badge }} ">{{ $add['status'] }}</span>
                                        </td>
                                        <td title="{{ $add['link'] }}">{{ $add['link'] }}</td>
                                        <td>{{ $add['media_count'] }}</td>
                                        <td>{{ $add['start_date'] }}</td>
                                        <td>{{ $add['end_date'] }}</td>
                                        <td>{{ $add['gender'] }}</td>
                                        <td>{{ $add['min_age'] }}</td>
                                        <td>{{ $add['max_age'] }}</td>
                                        <td>{{ $add['latitude'] }}</td>
                                        <td>{{ $add['longitude'] }}</td>
                                        <td>{{ $add['range'] }}km</td>
                                        <td>
                                            <i style="cursor: pointer;" title="Edit"
                                                onclick="editModalOpener({{ $add['id'] }})" class="fas fa-edit">
                                            </i>
                                            <span style="padding: 10px"></span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
        </div>
    </div>

    @include('admin.adds.edit-modal')
    @include('admin.adds.create-modal')

@endsection











@push('scripts')
    <script>
        const create_add_url = "{{ route('admin.adds.create') }}";
        const update_add_url = "{{ route('admin.adds.update') }}";

        $(document).ready(function() {
            $('#datatable').DataTable({});
            $('#addGenderSelect').select2({});
            $('#addStatusSelect').select2({});
            $('#create-addGenderSelect').select2({});
            $('#create-addStatusSelect').select2({});

            adds = @json($adds);
            key_adds = columnToKey(adds, 'id');

        });

        function createModalOpener() {
            $("#create-add-modal-success-btn").attr('onclick', "createAdd()");
            $("#create-title").val('');
            $("#create-author").val('');
            $("#create-link").val('');
            $("#create-start_date").val();
            $("#create-end_date").val();
            $("#create-addGenderSelect").val('male').trigger('change');
            $("#create-min_age").val(8);
            $("#create-max_age").val(100);
            $("#create-latitude").val('');
            $("#create-longitude").val('');
            $("#create-range").val(500);
            $("#create-addStatusSelect").val('active').trigger('change');
            openModal("createAddModal");
        }

        function editModalOpener(id) {
            $("#edit-add-modal-success-btn").attr('onclick', "editAdd(" + id + ")");
            $("#edit-title").val(key_adds[id]['title']);
            $("#edit-author").val(key_adds[id]['author']);
            $("#edit-link").val(key_adds[id]['link']);
            $("#edit-start_date").val(key_adds[id]['start_date']);
            $("#edit-end_date").val(key_adds[id]['end_date']);
            $("#addGenderSelect").val(key_adds[id]['gender']).trigger('change');
            $("#edit-min_age").val(key_adds[id]['min_age']);
            $("#edit-max_age").val(key_adds[id]['max_age']);
            $("#edit-latitude").val(key_adds[id]['latitude']);
            $("#edit-longitude").val(key_adds[id]['longitude']);
            $("#edit-range").val(key_adds[id]['range']);
            $("#addStatusSelect").val(key_adds[id]['status']).trigger('change');
            openModal("editAddModal");
        }

        function createAdd() {
            showLoader();

            $.ajax({
                type: "post",
                url: create_add_url,
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'title': $("#create-title").val(),
                    'author': $("#create-author").val(),
                    'link': $("#create-link").val(),
                    'start_date': $("#create-start_date").val(),
                    'end_date': $("#create-end_date").val(),
                    'gender': $("#create-addGenderSelect").val(),
                    'min_age': $("#create-min_age").val(),
                    'max_age': $("#create-max_age").val(),
                    'latitude': $("#create-latitude").val(),
                    'longitude': $("#create-longitude").val(),
                    'range': $("#create-range").val(),
                    'status': $("#create-addStatusSelect").val()
                },
                success: function(data) {
                    hideLoader();
                    if (data.status) {
                        closeModal("createAddModal");
                        toastr.success(data.message);
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
        function editAdd(id) {
            showLoader();

            $.ajax({
                type: "post",
                url: update_add_url,
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'id': id,
                    'title': $("#edit-title").val(),
                    'author': $("#edit-author").val(),
                    'link': $("#edit-link").val(),
                    'start_date': $("#edit-start_date").val(),
                    'end_date': $("#edit-end_date").val(),
                    'gender': $("#addGenderSelect").val(),
                    'min_age': $("#edit-min_age").val(),
                    'max_age': $("#edit-max_age").val(),
                    'latitude': $("#edit-latitude").val(),
                    'longitude': $("#edit-longitude").val(),
                    'range': $("#edit-range").val(),
                    'status': $("#addStatusSelect").val()
                },
                success: function(data) {
                    hideLoader();
                    if (data.status) {
                        closeModal("editAddModal");
                        toastr.success(data.message);
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
