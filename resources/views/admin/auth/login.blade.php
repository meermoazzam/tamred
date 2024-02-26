@extends('layouts.app')

@section('title')
    Admin Panel
@endsection



@push('styles')
    <style>
        #content {
            /* text-align: -webkit-center; */
            margin-top: 200px
        }
        /* Bordered form */
        form {

        }

        /* Full-width inputs */
        input[type=email],
        input[type=password] {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            display: inline-block;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        /* Set a style for all buttons */
        button {
            background-color: #04AA6D;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        /* Add a hover effect for buttons */
        button:hover {
            opacity: 0.8;
        }

        /* Extra style for the cancel button (red) */
        .cancelbtn {
            width: auto;
            padding: 10px 18px;
            background-color: #f44336;
        }

        /* Center the avatar image inside this container */
        .imgcontainer {
            text-align: center;
            margin: 24px 0 12px 0;
        }

        /* Avatar image */
        img.avatar {
            width: 40%;
            border-radius: 50%;
        }

        /* Add padding to containers */
        .container {
            padding: 16px;
        }

        /* The "Forgot password" text */
        span.psw {
            float: right;
            padding-top: 16px;
        }

        b {
            text-align: left;
        }

        #remember-me {
            text-align: left;
        }

        /* Change styles for span and cancel button on extra small screens */
        @media screen and (max-width: 300px) {
            span.psw {
                display: block;
                float: none;
            }

            .cancelbtn {
                width: 100%;
            }
        }
    </style>
@endpush


@section('content')
    <div id="content" class="row col-md-12">
        <div class="col-md-4"></div>
        <div class="col-md-4">
            <form action="{{ route('login.post') }}" method="post">
                @csrf

                <div class="container">
                    <label for="email"><b>Email</b></label>
                    <input type="email" id="email" placeholder="Enter Email" name="email" required autocomplete="email">

                    <label for="password"><b>Password</b></label>
                    <input type="password" id="password" placeholder="Enter Password" name="password" required>

                    <button type="submit">Login</button>
                    <label id="remember-me">
                        <input type="checkbox" checked="checked" name="remember"> Remember me
                    </label>
                </div>
            </form>
        </div>
        <div class="col-md-4"></div>
    </div>
@endsection











@push('scripts')
@endpush
