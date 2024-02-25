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
            <h1 style="font-style: normal">Posts<h1>
                <br>
            <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap"
                style="width: 100%">
                <thead class="">
                    <tr>
                        <th>Title</th>
                        {{-- <th>Description</th> --}}
                        <th>Status</th>
                        {{-- <th>Likes</th>
                        <th>Comments</th> --}}
                        <th>Media</th>
                        <th>Location</th>
                        <th>User</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($posts as $post)
                        <tr>
                            <td title="{{ $post['title'] }}">{{ $post['title'] }}</td>
                            {{-- <td title="{{ $post['description'] }}">{{ $post['description'] }}</td> --}}
                            <td title="{{ $post['status'] }}">{{ $post['status'] }}</td>
                            {{-- <td>{{ $post['total_likes'] }}</td>
                            <td>{{ $post['total_comments'] }}</td> --}}
                            <td>{{ count($post['media']) }}</td>
                            <td title="{{ $post['location'] }}">{{ $post['location'] }}</td>
                            <td title="{{ $post['user']['first_name'] . ' ' . $post['user']['last_name'] . ' (' . $post['user']['email'] . ')' }}"><img src="{{ $post['user']['image'] ? 'https://' . $post['user']['image'] : ' ' }}"> {{ $post['user']['first_name'] . ' ' . $post['user']['last_name'] . '(' . $post['user']['email'] . ')' }}</td>
                            <td>{{ $post['created_at'] }}</td>
                            <td>{{ $post['updated_at'] }}</td>
                            <td style="text-align: center;">
                                <i style="cursor: pointer;" title="Delete"
                                    onclick="deleteTemplateConfirmation({{ $post['id'] }})"
                                    class="fas fa-trash-alt">
                                </i>
                                <i style="cursor: pointer;" title="Delete"
                                    onclick="deleteTemplateConfirmation({{ $post['id'] }})"
                                    class="fas fa-trash-alt">
                                </i>
                                <i style="cursor: pointer;" title="Delete"
                                    onclick="deleteTemplateConfirmation({{ $post['id'] }})"
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
