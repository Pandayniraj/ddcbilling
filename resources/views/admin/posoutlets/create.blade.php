@extends('layouts.master')

@section('head_extra')
<!-- Select2 css -->-
@include('partials._head_extra_select2_css')
@endsection

@section('content')
<link href="/bower_components/admin-lte/select2/css/select2.min.css" rel="stylesheet" />
<script src="/bower_components/admin-lte/select2/js/select2.min.js"></script>

<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
    <h1>
        POS Outlets
        <small>{{$description}}</small>
    </h1>
    {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false) !!}
</section>

<form method="post" action="{{route('admin.hotel.pos-outlets.store')}}">
    {{ csrf_field() }}
    <div class="panel panel-custom">
        <div class="panel-heading">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-12">Outlet Code</label>
                        <div class="input-group ">
                            <input type="text" name="outlet_code" placeholder="Outlet Code" id="name" class="form-control" required>
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-stack-exchange"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                        <label class="control-label col-sm-12">Name</label>
                        <div class="input-group ">
                            <input type="text" name="name" placeholder="Name" id="name" class="form-control" required>
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-stack-exchange"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label class="control-label col-sm-12">Project Cat.</label>
                        <div class="input-group">
                            <select name="project_id" id="project-id" class="form-control" required>
                                <option value="">Select Project</option>
                                @foreach($projects as $project)
                                    <option value="{{$project->id}}">{{$project->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-12">Short Name</label>
                        <div class="input-group ">
                            <input type="text" name="short_name" placeholder="Short Name" id="name" class="form-control" required>
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-stack-exchange"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-12">Outlet Type</label>
                        <div class="input-group ">
                            <input type="text" name="outlet_type" placeholder="Outlet Type" id="name" class="form-control" required>
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-stack-exchange"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-12">Bank Name</label>
                        <div class="input-group ">
                            <input type="text" name="bank_name_one" value="" class="form-control" required>

                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-stack-exchange"></i></a>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-12">Bank Name (second)</label>
                        <div class="input-group ">
                            <input type="text" name="bank_name_two" value="" class="form-control" required>
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-stack-exchange"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-12">Branch Name</label>
                        <div class="input-group ">
                            <input type="text" name="bank_address_one" value="" class="form-control" required>

                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-stack-exchange"></i></a>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-12">Bank Address (second)</label>
                        <div class="input-group ">
                            <input type="text" name="bank_address_two" value="" class="form-control" required>
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-stack-exchange"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-12">Bank Account Name</label>
                        <div class="input-group ">
                            <input type="text" name="bank_ac_name_one" value="" class="form-control" required>

                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-stack-exchange"></i></a>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-12">Bank Account Name (second)</label>
                        <div class="input-group ">
                            <input type="text" name="bank_ac_name_two" value="" class="form-control" required>
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-stack-exchange"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-12">Bank Acccount No</label>
                        <div class="input-group ">
                            <input type="text" name="bank_account_one" value="" class="form-control" required>

                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-stack-exchange"></i></a>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-12">Bank Account no(second)</label>
                        <div class="input-group ">
                            <input type="text" name="bank_account_two" value="" class="form-control" required>
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-stack-exchange"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="row">

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-12">FNB Outlet</label>
                        <div class="input-group ">
                            <select name="fnb_outlet" class="form-control" required>
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-stack-exchange"></i></a>
                            </div>

                        </div>
                    </div>
                </div>

                @php
                    $groupid= \App\Models\COAgroups::select('name',id)->get();
                    $ledgerid= \App\Models\COALedgers::select('name', 'id')->get();
                @endphp
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-12">Ledger Id</label>
                        <div class="input-group ">
                            <select name="ledger_id" id="ledger_id" class="form-control ledgerid select2" required="required">
                                <option value="" disabled selected>Select Any One</option>
                                @foreach($ledgerid as $id)
                                    <option value="{{$id->id}}">{{$id->name}}</option>
                                @endforeach
                            </select>
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-stack-exchange"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-12">Bill Printer</label>
                        <div class="input-group ">
                            <input type="text" name="bill_printer" placeholder="Bill Printer" id="bill_printer" class="form-control">
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-stack-exchange"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-12">Kot Printer</label>
                        <div class="input-group ">
                            <input type="text" name="kot_printer" placeholder="Kot Printer" id="kot_printer" class="form-control">
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-stack-exchange"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-12">Bill Printer Port</label>
                        <div class="input-group ">
                            <input type="text" name="bill_printer_port" placeholder="Bill Printer Port" id="bill_printer_port" class="form-control">
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-stack-exchange"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-12">Kot Printer Port</label>
                        <div class="input-group ">
                            <input type="text" name="kot_printer_port" placeholder="Kot Printer Port" id="kot_printer_port" class="form-control">
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-stack-exchange"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-sm-6">
                    <label class="control-label col-sm-12">Bot Printer</label>
                    <div class="input-group ">
                        <input type="text" name="bot_printer" placeholder="Bot Printer" id="bot_printer_port" class="form-control" value="">
                        <div class="input-group-addon">
                            <a href="#"><i class="fa fa-stack-exchange"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    <label class="control-label col-sm-12">Bot Printer Port</label>
                    <div class="input-group ">
                        <input type="text" name="bot_printer_port" placeholder="Bot Printer Port" id="bot_printer_port" class="form-control" value="">
                        <div class="input-group-addon">
                            <a href="#"><i class="fa fa-stack-exchange"></i></a>
                        </div>
                    </div>
                </div>
            </div>



            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>Enabled<i class="imp">*</i></label>
                        <input type="hidden" name="enabled" value="0">
                        <input type="checkbox" name="enabled" value="1">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <label>For Random Customer<i class="imp">*</i></label>
                        <input type="hidden" name="forrandomcustomer" value="0">
                        <input type="checkbox" name="forrandomcustomer" value="1">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <input type="radio" id="a4" name="printformat" value="a4">
                        <label for="a4">A4 Print Format</label><br>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <input type="radio" id="thermal" name="printformat" value="thermal">
                        <label for="thermal">Thermal Print Format</label><br>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <button class="btn btn-primary" id="btn-submit-edit" type="submit">Add</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</form>

@endsection
@section('body_bottom')
    <script>
        $('.ledgerid').select2();
    </script>
@endsection
