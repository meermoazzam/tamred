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
                        <th>User</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($albums as $album)
                        <tr>
                            <td title="{{ $album['id'] }}">{{ $album['id'] }}</td>
                            <td title="{{ $album['name'] }}">{{ $album['name'] }}</td>
                            <td title="{{ $album['status'] }}">{{ $album['status'] }}</td>
                            <td>{{ $album['posts_count'] }}</td>
                            <td title="{{ $album['user']['first_name'] . ' ' . $album['user']['last_name'] . ' (' . $album['user']['email'] . ')' }}"><img src="{{ $album['user']['image'] ? 'https://' . $album['user']['image'] : ' ' }}"> {{ $album['user']['first_name'] . ' ' . $album['user']['last_name'] . '(' . $album['user']['email'] . ')' }}</td>
                            <td>{{ $album['created_at'] }}</td>
                            <td style="text-align: center;">
                                <i style="cursor: pointer;" title="Delete"
                                    onclick="deleteTemplateConfirmation({{ $album['id'] }})"
                                    class="fas fa-trash-alt">
                                </i>
                                <i style="cursor: pointer;" title="Delete"
                                    onclick="deleteTemplateConfirmation({{ $album['id'] }})"
                                    class="fas fa-trash-alt">
                                </i>
                                <i style="cursor: pointer;" title="Delete"
                                    onclick="deleteTemplateConfirmation({{ $album['id'] }})"
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
