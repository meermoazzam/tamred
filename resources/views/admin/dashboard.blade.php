@extends('layouts.app')

@section('title')
    Dashboard
@endsection



@push('styles')
    <style>

    </style>
@endpush





@section('content')
    <div class="wrapper d-flex align-items-stretch col-md-12">
        @include('partials.sidebar')

        <div class="col-md-2"></div>
        <div id="content" class="col-md-10 p-5"><i class="fa-solid fa-house">
            <h1 style="font-style: normal">Dashboard<h1>
                <br>

        </div>
    </div>
@endsection











@push('scripts')

@endpush
