<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
    <meta charset="UTF-8">
    <title>{!! env('SHORT_NAME') !!} | {{ $page_title ?? "Page Title" }}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Set a meta reference to the CSRF token for use in AJAX request -->
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <!-- Bootstrap 3.3.4 -->
    <link href="{{ asset("/bower_components/admin-lte/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet"
          type="text/css"/>
    <!-- Font Awesome Icons 4.4.0 -->
    <link href="{{ asset("/bower_components/admin-lte/font-awesome/css/font-awesome.min.css") }}" rel="stylesheet"
          type="text/css"/>
    <!-- Ionicons 2.0.1 -->
    <link href="{{ asset("/bower_components/admin-lte/ionicons/css/ionicons.min.css") }}" rel="stylesheet"
          type="text/css"/>
    <!-- Theme style -->
    <link href="{{ asset("/bower_components/admin-lte/dist/css/AdminLTE.min.css") }}" rel="stylesheet" type="text/css"/>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Application CSS-->
    <link href="{{ asset('css/all.css') }}" rel="stylesheet" type="text/css"/>

    <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('chat/css/style.css')}}">
    <style>
        .skin-blue .sidebar-menu > li.header {
            color: #fff !important;
            font-size: 15px !important;
            background: #1b4f72 !important;
            background: #3470aa;
        }

        .material-icons {
            display: inline-flex;
            vertical-align: top;
            letter-spacing: 5px !important;
            font-size: 17px !important;
        }

        .material-icons.medium {
            font-size: 17px;
        }

        .skin-red .sidebar-menu > li.header {
            color: #fff !important;
            font-size: 15px !important;
            background: maroon !important;
        }

        .ui-menu-item:hover {
            background: #007fff !important;
            border: 1px solid #003eff !important;
            color: white !important;
        }

        /*************MEGA MENU*****************/

        .dropdown-large {
            position: static !important;
        }

        .dropdown-menu-large {
            margin-left: 16px;
            margin-right: 16px;
            padding: 20px 0px;
        }

        .dropdown-menu-large > li > ul {
            padding: 0;
            margin: 0;
        }

        .dropdown-menu-large > li > ul > li {
            list-style: none;
        }

        .dropdown-menu-large > li > ul > li > a {
            display: block;
            padding: 3px 20px;
            clear: both;
            font-weight: 300;
            line-height: 1.428571429;
            color: #000000;
            white-space: normal;
        }

        .dropdown-menu-large > li ul > li > a:hover,
        .dropdown-menu-large > li ul > li > a:focus {
            text-decoration: none;
            color: #3470aa;
            background-color: #f5f5f5;
        }

        .dropdown-menu-large .disabled > a,
        .dropdown-menu-large .disabled > a:hover,
        .dropdown-menu-large .disabled > a:focus {
            color: #999999;
        }

        .dropdown-menu-large .disabled > a:hover,
        .dropdown-menu-large .disabled > a:focus {
            text-decoration: none;
            background-color: transparent;
            background-image: none;
            filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
            cursor: not-allowed;
        }

        .dropdown-menu-large .dropdown-header {
            color: #3470aa;
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .dropdown-menu-large {
                margin-left: 0;
                margin-right: 0;
            }

            .dropdown-menu-large > li {
                margin-bottom: 30px;
            }

            .dropdown-menu-large > li:last-child {
                margin-bottom: 0;
            }

            .dropdown-menu-large .dropdown-header {
                padding: 3px 15px !important;
            }
        }
    </style>
    <!-- Head -->
    @include('partials._head')
    <!-- jQuery 2.1.4 -->
    <script src="{{ asset ("/bower_components/admin-lte/plugins/jQuery/jQuery-2.1.4.min.js") }}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/bootstrap.min.js") }}"
            type="text/javascript"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset ("/bower_components/admin-lte/dist/js/app.min.js") }}" type="text/javascript"></script>

    <!-- Optionally, you can add Slimscroll and FastClick plugins.
          Both of these plugins are recommended to enhance the
          user experience. Slimscroll is required when using the
          fixed layout. -->

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Application JS-->
    <script src="{{ asset('js/all.js') }}"></script>

    <!-- Optional header section  -->
    @yield('head_extra')

</head>

<!-- Body -->
@if(isset($app_theme))
    @if($app_theme == 'green')
        @include('themes.green.partials._body')
    @elseif($app_theme == 'red')
        @include('themes.red.partials._body')
    @elseif($app_theme == 'yellow')
        @include('themes.yellow.partials._body')
    @elseif($app_theme == 'purple')
        @include('themes.purple.partials._body')
    @elseif($app_theme == 'black')
        @include('themes.black.partials._body')
    @else
        @include('partials._body')
    @endif
@else
    @include('partials._body')
@endif


</html>
