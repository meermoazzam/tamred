@extends('layouts.app')

@section('title')
    Dashboard
@endsection



@push('styles')
    <style>
        .posted-event-activity .card .card-row {
            display: flex;
            justify-content: start;
            align-items: center;
            border-bottom: 1px solid lightgray;
            padding: 15px 0;
        }

        .posted-event-activity .card {
            border-radius: 15px;
            padding: 20px;
            overflow-y: scroll;
            width: 100%;
            height: 300px;
            max-height: 32vh;
            box-shadow: 3px 0 7px 3px rgb(0 0 0 / 10%);
            -moz-box-shadow: 3px 0 7px 3px rgba(0, 0, 0, .1);
            -webkit-box-shadow: 3px 0 7px 3px rgb(0 0 0 / 10%);
        }

        ::-webkit-scrollbar {
            width: 0px;
        }

        i.fas.fa-pen,
        i.fas.fa-trash-alt {
            margin: 10px !important;
        }

        .header-links {
            position: absolute;
            z-index: 1;
            width: 95%;
            font-size: 30px;
        }

        .fa-download:before,
        .fa-print:before,
        .fa-ellipsis-v:before {
            color: white;
        }

        button#seidebarToggle,
        button#seidebarToggle_2 {
            background: transparent;
            border: none;
            font-size: 30px;
            text-align: inherit;
            color: darkgrey;
        }

        div#pdf_files,
        div#all_close {
            overflow-y: scroll;
            height: inherit;
        }

        .text-left_align {
            position: sticky;
            top: 0;
        }

        .card-row {
            display: flex;
        }

        img.profile-img {
            width: 75px;
        }

        h3 {
            color: gray !important;
        }

        .card-start {
            min-height: 150px;
            vertical-align: middle !important;
        }

        .referrals:hover,
        .users-tile:hover {
            text-decoration: none
        }

        .table-hover tr td:hover {
            cursor: pointer;
        }
    </style>
@endpush





@section('content')
    <div class="wrapper d-flex align-items-stretch col-md-12">
        @include('partials.sidebar')

        <div class="col-md-2"></div>
        <div id="content" class="col-md-10 p-5"><i class="fa-solid fa-house">
                <h1 style="font-style: normal">Dashboard<h1>
                        <div class="container-fluid">

                            <!-- Content Row -->
                            <div class="row">

                                <!-- Earnings (Monthly) Card Example -->
                                <div class="card-start col-xl-4 col-md-4 mb-4">
                                    <a href="{{ route('admin.users.get') }}" class="users-tile">
                                        <div class="card border-left-primary shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="col mr-2">
                                                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                                                Total Users
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <h2>{{ $total_users }}</h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="card-start col-xl-4 col-md-4 mb-4">
                                    <a href="{{ route('admin.users.get', ['status' => 'blocked']) }}" class="users-tile">
                                        <div class="card border-left-primary shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="col mr-2">
                                                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                                                Blocked Users
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <h2>{{ $total_blocked_users }}</h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="card-start col-xl-4 col-md-4 mb-4">
                                    <a href="{{ route('admin.albums.get') }}" class="users-tile">
                                        <div class="card border-left-primary shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="col mr-2">
                                                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                                                Total Albums
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <h2>{{ $total_albums }}</h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="card-start col-xl-4 col-md-4 mb-4">
                                    <a href="{{ route('admin.categories.get') }}" class="users-tile">
                                        <div class="card border-left-primary shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="col mr-2">
                                                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                                                Total Categories
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <h2>{{ $total_categories }}</h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="card-start col-xl-4 col-md-4 mb-4">
                                    <a href="{{ route('admin.categories.get', ['hasParent' => 'no']) }}" class="users-tile">
                                        <div class="card border-left-primary shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="col mr-2">
                                                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                                                Total Parent Categories
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <h2>{{ $total_parent_categories }}</h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="card-start col-xl-4 col-md-4 mb-4">
                                    <a href="{{ route('admin.categories.get', ['hasParent' => 'yes']) }}" class="users-tile">
                                        <div class="card border-left-primary shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="col mr-2">
                                                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                                                Total Sub Categories
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <h2>{{ $total_sub_categories }}</h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="card-start col-xl-4 col-md-4 mb-4">
                                    <a href="{{ route('admin.posts.get') }}" class="users-tile">
                                        <div class="card border-left-primary shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="col mr-2">
                                                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                                                Total Posts
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <h2>{{ $total_posts }}</h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="card-start col-xl-4 col-md-4 mb-4">
                                    <a href="{{ route('admin.posts.get', ['status' => 'deleted']) }}" class="users-tile">
                                        <div class="card border-left-primary shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="col mr-2">
                                                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                                                Deleted Posts
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <h2>{{ $total_deleted_posts }}</h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <div class="card-start col-xl-4 col-md-4 mb-4">
                                    <a href="{{ route('admin.comments.get') }}" class="users-tile">
                                        <div class="card border-left-primary shadow h-100 py-2">
                                            <div class="card-body">
                                                <div class="row no-gutters align-items-center">
                                                    <div class="col mr-2">
                                                        <div class="col mr-2">
                                                            <div class="h3 mb-0 font-weight-bold text-gray-800">
                                                                Total Comments
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <h2>{{ $total_comments }}</h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>


                            </div>
                        </div>
        </div>
    </div>
@endsection











@push('scripts')
@endpush
