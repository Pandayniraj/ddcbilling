@extends('layouts.master')

@section('head_extra')
    <!-- Select2 css -->
    @include('partials._head_extra_select2_css')

    <style>
        .panel .mce-panel {
            border-left-color: #fff;
            border-right-color: #fff;
        }

        .panel .mce-toolbar,
        .panel .mce-statusbar {
            padding-left: 20px;
        }

        .panel .mce-edit-area,
        .panel .mce-edit-area iframe,
        .panel .mce-edit-area iframe html {
            padding: 0 10px;
            min-height: 350px;
        }

        .mce-content-body {
            color: #555;
            font-size: 14px;
        }

        .panel.is-fullscreen .mce-statusbar {
            position: absolute;
            bottom: 0;
            width: 100%;
            z-index: 200000;
        }

        .panel.is-fullscreen .mce-tinymce {
            height: 100%;
        }

        .panel.is-fullscreen .mce-edit-area,
        .panel.is-fullscreen .mce-edit-area iframe,
        .panel.is-fullscreen .mce-edit-area iframe html {
            height: 100%;
            position: absolute;
            width: 99%;
            overflow-y: scroll;
            overflow-x: hidden;
            min-height: 100%;
        }

        input.form-control {
            min-width: 55px !important;
        }

        select {
            min-width: 80px !important;

        }

        .p_sn {
            max-width: 3px !important;
        }

        @media only screen and (max-width: 770px) {
            input.total {
                width: 140px !important;
            }
        }

        .panel-footer {
            padding: 10px 15px;
            background-color: #fff !important;
            border-top: none !important;
            border-bottom-right-radius: 3px;
            border-bottom-left-radius: 3px;
        }

        .callout {
            border-radius: 3px;
            margin: 0;
            padding: 0;
            border-left: none !important;
        }

        .box {
            border-radius: 12px;
            background: #ffffff;
            border-top: none;
            box-shadow: rgba(0, 0, 0, 0.1) 0px 1px 3px 0px, rgba(0, 0, 0, 0.06) 0px 1px 2px 0px;
        }

        .form-control {
            border-radius: 4px !important;
            box-shadow: none;
            border-color: #d2d6de;
            height: 29px !important;
        }

        span.select2.select2-container.select2-container--default {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            height: 29px !important;
        }

        .bg-green,
        .callout.callout-success,
        .alert-success,
        .label-success,
        .modal-success .modal-body {
            background-color: #ecf0f5 !important;
            color: #000 !important;
            border-radius: 5px !important;
            border-color: #3c8dbc94 !important;
        }

        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #d2d6de !important;
            border-radius: 4px;
        }

        input,
        select {
            box-sizing: border-box;
            padding: 0px;
        }

        .table>thead>tr>th,
        .table>tbody>tr>th,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>tbody>tr>td,
        .table>tfoot>tr>td {
            border-top: none !important;
        }

        tr.bg-info.tr-heading th {
            border-right: 2px solid #fff;
            padding: 5px;
        }

        .plusicon {
            color: #3c8dbc;
        }

        .panel-footer.footer {
            float: right;
        }

        .callout a {
            color: #3c8dbc;
            text-decoration: none;
        }

        .table>tbody>tr>td,
        .table>tbody>tr>th,
        .table>tfoot>tr>td,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>thead>tr>th {
            padding: 3px !important;
        }
    </style>
@endsection

@section('content')


    <link href="/bower_components/admin-lte/select2/css/select2.min.css" rel="stylesheet" />
    <script src="/bower_components/admin-lte/select2/js/select2.min.js"></script>

    <section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
        <h1>
            <h1>
                {{ $pagetitle }}
                <small>{{ $pagedescription }}</small>
            </h1>
        </h1>
        {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false) !!}
    </section>

    <div class='row'>
        <div class='col-md-12 '>
            <div class="box">
                <div class="box-body">   
                    <div class="form-section">
                        <form method="POST" action="{{ route('admin.irddetail.update', $editirddetail->id) }}">
                            {{ csrf_field() }}
                            <div class="clearfix"></div>
                            <div class="row col-md-12">
                               
                                @php
                                    $roles = Auth::user()->roles;
                                    $admin = false;
                                    foreach ($roles as $role) {
                                        if ($role->name == 'admins') {
                                            $admin = true;
                                            break;
                                        }
                                    }
                                @endphp
                                <div class="col-md-6 form-group credit" style="">
                                    <label for="credit_limit">Api Link</label>
                                   <input type="text" name="api_link" value="{{$editirddetail->api_link??''  }}" class="form-control" required>
                                </div>
                                <div class="col-md-6 form-group credit" style="">
                                    <label for="credit_limit"> Ird Username</label>
                                   <input type="text" name="ird_username" value="{{$editirddetail->ird_username??''  }}" class="form-control" required>
                                </div>
                                
                                <div class="col-md-6 form-group credit" style="">
                                    <label for="remaining_amount">Ird Password</label>
                                   <input type="text" name="ird_password" value="{{$editirddetail->ird_password??''  }}" class="form-control" required>
                                </div>

                                <div class="col-md-6 form-group" style="">
                                    <i class="fa fa-store"></i><label for="user_id">Seller Pan</label>
                                    <input type="text" name="seller_pan" value="{{$editirddetail->seller_pan??''  }}" class="form-control" required>
                                </div>
                                <div class="col-md-6 form-group" style="">
                                    <input type="checkbox" id="is_ird" name="is_ird" value="1" {{ $editirddetail->is_ird == 1 ? 'checked' : '' }}>
                                    <label for="is_ird">Enable</label><br>
                                </div>
                                <div class="panel-footer footer">
                                    <button type="submit" class="btn btn-social btn-foursquare" id="submit">
                                        <i class="fa fa-save"></i>Update
                                    </button>
                                    <a class="btn btn-social btn-foursquare" href="/admin/irddetail"> <i class="fa fa-times"></i>
                                        Cancel </a>
                                </div>
                            </div>
                        </form>
                </div>
            </div>

        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.col -->

@endsection
>
@section('body_bottom')
    <!-- form submit -->
    @include('partials._body_bottom_submit_bug_edit_form_js')
    @include('partials._date-toggle')
    {{-- Bar code scanner --}}
@endsection
