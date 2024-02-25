<!DOCTYPE html>
<html lang="en">

<head>
    <!-- head start -->
    @php
        header('Access-Control-Allow-Origin: *');
    @endphp
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- favicon & apple-touch-icon -->
    <link rel="icon" href="{{ asset('images/tamred-icon.png') }}" type="image/png" sizes="100x100">
    <link rel="apple-touch-icon" href="{{ asset('images/tamred-fav-icon.svg') }}" type="image/png" sizes="150x150">

    <!-- Title -->
    <title>{{ config('app.name', 'Tamred') }} - @yield('title', 'Application')</title>

    @stack('fonts')

    <style>
        /* width */
        ::-webkit-scrollbar {
            width: 15px;
            overflow-y: auto;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            box-shadow: inset 0 0 5px grey;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            background: #5d5d60;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #424242;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="//cdn.datatables.net/2.0.0/css/dataTables.dataTables.min.css">

    @stack('styles')



    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js" integrity="sha512-Tn2m0TIpgVyTzzvmxLNuqbSJH3JP8jm+Cy3hvHrW7ndTDcJ1w5mBiksqDBb8GpE2ksktFvDB/ykZ0mDpsZj20w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="//cdn.datatables.net/2.0.0/js/dataTables.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    <script>
        if( $('.disable-ajax-header').length == 0 )
        {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        }
    </script>

    <script type="text/javascript">

    </script>
    @stack('scripts')
</head>

<body>
    <div id="loader" style="display: none;"></div>
    <div id="app">
        @include('partials.toastr')

        @yield('content')

        <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#" onclick="moveToTop()">
            <i class="fas fa-angle-up"></i>
        </a>
    </div>
</body>

</html>
