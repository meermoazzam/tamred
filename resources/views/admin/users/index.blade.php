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
        table td {
            transition: all .5s;
            max-width: 120px;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            word-break: break-all;
        }
        th {
            text-align: center !important;
        }
        table td img{
            max-width: 20px;
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
                        <th>Name</th>
                        <th>Email</th>
                        <th>DOB</th>
                        <th>Gender</th>
                        <th>Location</th>
                        <th>country</th>
                        <th>Created At</th>
                        <th>Last Modified At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td title="{{ $user['first_name'] . ' ' . $user['last_name'] }}"><img src="{{ $user['image'] ? 'https://' . $user['image'] : ' ' }}"> {{ $user['first_name'] . ' ' . $user['last_name'] }}</td>
                            <td title="{{ $user['email'] }}">{{ $user['email'] }}</td>
                            <td title="{{ $user['date_of_birth'] }}">{{ $user['date_of_birth'] }}</td>
                            <td title="{{ $user['gender'] }}">{{ $user['gender'] }}</td>
                            <td title="{{ $user['location'] }}">{{ $user['location'] }}</td>
                            <td title="{{ $user['country'] }}">{{ $user['country'] }}</td>
                            <td>{{ $user['created_at'] }}</td>
                            <td>{{ $user['updated_at'] }}</td>
                            <td style="text-align: center;">
                                <i style="cursor: pointer;" title="Delete"
                                    onclick="deleteTemplateConfirmation({{ $user['id'] }})"
                                    class="fas fa-trash-alt">
                                </i>
                                <i style="cursor: pointer;" title="Delete"
                                    onclick="deleteTemplateConfirmation({{ $user['id'] }})"
                                    class="fas fa-trash-alt">
                                </i>
                                <i style="cursor: pointer;" title="Delete"
                                    onclick="deleteTemplateConfirmation({{ $user['id'] }})"
                                    class="fas fa-trash-alt">
                                </i>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection











@push('scripts')
<script>
$(document).ready(function () {
    if ($('#datatable').length) {
        $('#datatable').DataTable({

        });
    }
});
</script>
@endpush
