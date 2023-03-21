@extends('layouts.master')

@section('head_extra')
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
    <section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
        <h1>
            New Sales
            <small id="ajax_status"></small>
        </h1>
        {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false) !!}
    </section>

    <div class='row'>
        <div class='col-md-12 '>
            <div class="box">
                <div class="box-body">
                    <div id="orderFields" style="display: none;">
                        <table class="table">
                            <tbody id="more-tr">
                                <tr>
                                    <td class='p_sn'></td>
                                    <td>
                                        <select class="form-control product_id hiddensearchable reduce" name="product_id[]"
                                            required>
                                            <option value="">Select Product</option>
                                            @foreach ($products as $key => $pk)
                                                <option value="{{ $pk->id }}"
                                                    @if (isset($orderDetail->product_id) && $orderDetail->product_id == $pk->id) selected="selected" @endif>
                                                    {{ $pk->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="col-sm-1">
                                        <input type="number" class="form-control input-sm stock" placeholder="Stock" readonly>
                                    </td>
                                    <td class="col-sm-1">
                                        <input type="number" class="form-control input-sm quantity input-sm reduce"
                                            name="quantity[]" placeholder="Quantity" step=".01" autocomplete="off" required value="1">
                                    </td>
                                    <td>
                                        <select name='units[]' class="form-control form-control-solid input-sm units reduce"
                                            required readonly>
                                            <option value="">Units</option>
                                            @foreach ($prod_unit as $pu)
                                                <option value="{{ $pu->id }}">{{ $pu->symbol }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control input-sm price input-sm reduce"
                                            name="price[]" placeholder="Rate" autocomplete="off" readonly value="0">
                                    </td>
                                    <td>
                                        <input type="number" name="dis_amount[]"
                                            class="form-control input-sm discount_amount_line reduce" placeholder="Discount"
                                            step="any" value="0">
                                    </td>
                                    <td class="col-sm-1">
                                        <select class="form-control input-sm tax_rate_line input-sm reduce"
                                            name="tax_type[]" disabled>
                                            <option value="0">Exempt(0)</option>
                                            <option value="13">VAT(13)</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control input-sm tax_amount_line reduce"
                                            name="tax_amount[]" value="0" readonly="readonly" />
                                    </td>
                                    <td>
                                        <input type="number" class="form-control input-sm total reduce" name="total[]"
                                            placeholder="Total" style="float:left; width:70%;" step='any' readonly
                                            value="0">
                                        <a href="javascript:void(0);" style="width: 10%;">
                                            <i class="remove-this btn btn-xs btn-danger icon fa fa-trash deletable"
                                                style="float: right; color: #fff;"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <form method="POST" action="{{ route('admin.invoice.create') }}" id="create-invoice">
                        <div class="form-section">
                            {{ csrf_field() }}
                            <div class="clearfix"></div>
                            <div class="col-md-12" style="margin-top: 30px;">
                                <div class="col-md-2 form-group" style="">
                                    <i class="fa fa-store"></i><label for="user_id">Outlets<span
                                            style="color:red">(*)</span></label>
                                    <select name="outlet_id" id="outlet_id" required
                                        class="form-control label-success searchable">
                                        <option value="">Please Select</option>
                                        @foreach ($outlets as $key => $outlet)
                                            <option value="{{ $outlet->id }}"
                                                data-random="{{ $outlet->forrandomcustomer }}">{{ $outlet->name }}</option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="col-md-2" id="all_customer_type">
                                    <div class="callout">
                                        <label>Client Type<span style="color:red">(*)</span></label>
                                        <select class="searchable form-control" name="client_type" required
                                            id='client_type'>
                                            <option value="distributor">Distributor</option>
                                            <option value="retailer">Retailer</option>
                                            <option value="boothman">BoothMan</option>
                                            <option value="staff-milk">Staff Milk</option>
                                            <option value="staff">Staff Ghee</option>
                                            <option value="random_customer" selected>Direct Customer</option>
                                        </select>
                                    </div>
                                </div>   
                                <div class="col-md-2" id="random_customer_type" style="display:none;">
                                    <div class="callout">
                                        <label>Client Type <span style="color:red"> (*)</span></label>
                                        <select class="searchable form-control" name="client_type_one" id='client_type'
                                            required>
                                            <option value="random_customer" selected>Direct Customer</option>
                                        </select>
                                    </div>
                                </div>
                               


                                <div class="col-md-2" id="onclienttype" style="display: block;">
                                    <div class="callout">
                                        <label><span>Select Clients</span>
                                            <i class="imp" style="color:red">(*)</i>
                                            <span id='create_lead_or_clients'>
                                                <a href="#" onClick="openwindow('client')"><i
                                                        class="fa fa-plus plusicon"></i></a>
                                            </span></label>
                                        <select class="searchable" name="customer_id" id='client_id'>

                                            @if (isset($clients))
                                                <option value="" data-pan="" data-add="">--SELECT CLIENT--
                                                </option>
                                            @endif
                                        </select>
                                        <input type="hidden" name="source" value='client'>

                                    </div>
                                </div>
                                <div class="col-md-2" id="random_name" style="display: none;">
                                    <div class="callout">
                                        <label><span>Client Name</span> <i class="imp" style="color:red">(*)</i>
                                            <span id='create_lead_or_clients'></span></label>
                                        <input type="text" name="customer_name" class="form-control" value="Cash">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Bill Type<span style="color:red"></span></label>
                                        <select name="bill_type" class="form-control bill_type" id="bill_type"
                                            style="display:block;">
                                            <option value="">Select BILL TYPE</option>
                                            <option value="Cash" selected>Cash</option>
                                            <option value="Credit">Credit</option>
                                            <option value="Staff">Staff</option>
                                        </select>

                                        <select name="bill_type_one" class="form-control bill_type" id="forrandom"
                                            style="display:none;">
                                            <option value="">Select BILL TYPE</option>
                                            <option value="Cash" selected>Cash</option>
                                        </select>

                                        <select name="bill_type_two" class="form-control bill_type" id="billshow"
                                            style="display:none;">
                                            <option value="">Select BILL TYPE</option>
                                            <option value="Staff" selected>Staff</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Bill Date:</label>
                                        <div class="date">
                                            <input type="text"
                                                class="form-control input-sm pull-right datepicker date-toggle-nep-eng"
                                                name="bill_date" value="{{ \Carbon\Carbon::now()->toDateString() }}"
                                                id="bill_date">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2" id="due-date">
                                    <div class="form-group">
                                        <label>Due Date:</label>
                                        <div class="date">
                                            <input type="text"
                                                class="form-control pull-right datepicker date-toggle-nep-eng"
                                                name="due_date"
                                                value="{{ \Carbon\Carbon::now()->addDays(14)->toDateString() }}"
                                                id="due_date">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>PAN Number:</label>
                                        <div class="date">
                                            <input type="text" name="customer_pan" value="{{ old('customer_pan') }}"
                                                class="form-control pull-right" id="pan_no"
                                                onKeyUp="if(this.value>999999999){this.value='999999999';}else if(this.value<0){this.value='0';}"
                                                readonly>
                                        </div>
                                    </div>
                                </div>

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
                                <div class="col-md-2 form-group credit" style="display:none;">
                                    <label for="user_id"> <i class="fa fa-user"></i> Salesperson</label>
                                    @if ($admin == true)
                                        {!! Form::select('user_id', $users, \Auth::user()->id, ['class' => 'form-control input-sm', 'id' => 'user_id']) !!}
                                    @else
                                        {!! Form::select('user_id', $users, \Auth::user()->id, [
                                            'class' => 'form-control input-sm',
                                            'id' => 'user_id',
                                            'disabled' => '',
                                        ]) !!}
                                        <input type="hidden" name="user_id" id="user_id"
                                            class="form-control input-sm" value={{ \Auth::user()->id }}>
                                    @endif
                                </div>

                                <div class="col-md-2 form-group credit" id="credit-limit" style="display:none;">
                                    <label for="credit_limit"> <i class="fa fa-credit-card"></i> Credit Limit</label>
                                    {!! Form::text('credit_limit', 0, [
                                        'class' => 'form-control input-sm ',
                                        'id' => 'credit_limit',
                                        'readonly' => 'readonly',
                                    ]) !!}
                                </div>
                                <div class="col-md-2 form-group credit" id="remaining-amount" style="display:none;">
                                    <label for="remaining_amount" style="white-space: nowrap"> <i
                                            class="fa fa-money"></i> Remaining Amount</label>
                                    {!! Form::text('remaining_amount', 0, [
                                        'class' => 'form-control input-sm ',
                                        'id' => 'remaining_amount',
                                        'readonly' => 'readonly',
                                    ]) !!}
                                    <span class="text-danger credit-exceeded" style="display:none;">Credit limit and time
                                        has been exceeded
                                        exceeded</span>
                                </div>
                                <div class="col-md-2 form-group credit" id="remaining-days" style="display:none;">
                                    <label for="remaining_days" style="white-space: nowrap"> <i class="fa fa-money"></i>
                                        Remaining Days</label>
                                    {!! Form::text('remaining_days', 0, [
                                        'class' => 'form-control input-sm ',
                                        'id' => 'remaining_days',
                                        'readonly' => 'readonly',
                                    ]) !!}
                                </div>

                                <input type="hidden" name="is_renewal" value="0">
                                <input type="hidden" name="status" value="Active">
                                <input type="hidden" name="position" value="Manager">
                            </div>

                            @if (\Request::get('type') && \Request::get('type') == 'quotation')
                                <input type="hidden" name="order_type" value="quotation">
                            @elseif(\Request::get('type') && \Request::get('type') == 'invoice')
                                <input type="hidden" name="order_type" value="proforma_invoice">
                            @elseif(\Request::get('type') && \Request::get('type') == 'order')
                                <input type="hidden" name="order_type" value="order">
                            @else
                                <input type="hidden" name="order_type" value="quotation">
                            @endif

                            <div class="clearfix"></div>
                            <br />

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="bg-info tr-heading">
                                            <th style="width: 3%;">S.N</th>
                                            <th style="width: 17%;">Item*</th>
                                            <th style="width: 5%;">Stock</th>
                                            <th style="width: 5%;">Qty*</th>
                                            <th style="width: 10%;">UOM</th>
                                            <th style="width: 10%;">Unit Price *</th>
                                            <th style="width: 10%;" title="Discount">Dis</th>
                                            <th style="width: 5%;">Tax Rate</th>
                                            <th style="width: 10%;">Tax Amt</th>
                                            <th style="width: 20%;">Total</th>
                                        </tr>
                                    </thead>

                                    <tbody id='multipleDiv'>
                                        <tr class="multipleDiv"></tr>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <td colspan="3"></td>
                                            <td colspan="7">
                                                <div class="btn-group pull-right">
                                                    <a href="javascript:void(0);" class="btn btn-default btn-sm disclose"
                                                        id="addMore" style="float:right;">
                                                        <i class="fa fa-plus plusicon"></i> <span>Add Products Item</span>
                                                    </a> &nbsp;
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="9" style="text-align: right;">Amount</td>
                                            <td id="sub-total">0.00</td>
                                            <td>&nbsp; <input type="hidden" name="subtotal" id="subtotal"
                                                    value="0"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="9" style="text-align: right;">Order Discount</td>
                                            <td id='discount-amount'>0.00</td>
                                            <td>
                                                <input type="hidden" name="discount_amount" value="0"
                                                    id='discount_amount'>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="9" style="text-align: right;">Non Taxable Amount</td>
                                            <td id="non-taxable-amount">0.00</td>
                                            <td>&nbsp;<input type="hidden" name="non_taxable_amount"
                                                    id="nontaxableamount" value="0">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="9" style="text-align: right;">Taxable Amount</td>
                                            <td id="taxable-amount">0.00</td>
                                            <td>
                                                &nbsp; <input type="hidden" name="taxable_amount" id="taxableamount"
                                                    value="0">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="9" style="text-align: right;">Tax Amount</td>
                                            <td id="taxable-tax">0.00</td>
                                            <td>&nbsp; <input type="hidden" name="taxable_tax" id="taxabletax"
                                                    value="0"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" style="text-align: right; font-weight: bold;">Total Amount
                                            </td>
                                            <td id="total">0.00</td>
                                            <td>
                                                <input type="hidden" name="final_total" id="total_" value="0">

                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <br />

                            <div class="col-md-6 form-group" style="margin-top:5px;">
                                <label for="comment">Terms & Conditions Comment </label>
                                <small class="text-muted">Will be displayed on the invoice
                                </small>
                                <textarea class="form-control TextBox comment" name="comment">
                                    @if (isset($order->comment))
{{ $order->comment }}
@endif
                                </textarea>
                            </div>

                            <div class="col-md-6 form-group" style="margin-top:5px;">
                                <label for="address">Address</label>
                                <textarea class="form-control TextBox address" name="address" id='physical_address'>
                                    @if (isset($orderDetail->address))
{{ $orderDetail->address }}
@endif
                                </textarea>
                            </div>
                        </div>
                        <div class="panel-footer footer">
                            <button type="submit" class="btn btn-social btn-foursquare" id="submit2">
                                <i class="fa fa-save"></i>Save {{ $_GET['type'] }}
                            </button>
                            <a class="btn btn-social btn-foursquare" href="/admin/invoice1"> <i class="fa fa-times"></i>
                                Cancel </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class='supplier_options' style="display: none;">
        <div id='_supplier'>
            <option value="">Select Supplier</option>
            @if (isset($clients))
                @foreach ($clients as $key => $uk)
                    <option value="{{ $uk->id }}"
                        @if ($orderDetail && $uk->id == $orderDetail->customer_id) {{ 'selected="selected"' }} @endif>
                        {{ '(' . env('APP_CODE') . $uk->id . ') ' . $uk->name . ' -' . $uk->vat }}
                        ({{ $sup->locations->city ?? '-' }})
                    </option>
                @endforeach
            @endif
        </div>
        <div id='_paid_through'>
            <option value="">Select Supplier</option>
            @if (isset($paid_through))
                @foreach ($paid_through as $key => $uk)
                    <option value="{{ $uk->id }}"
                        @if ($orderDetail && $uk->id == $orderDetail->customer_id) {{ 'selected="selected"' }} @endif>
                        {{ '(' . env('APP_CODE') . $uk->id . ') ' . $uk->name . ' -' . $uk->organization }}</option>
                @endforeach
            @endif
        </div>
    </div>
@endsection

@section('body_bottom')
    <script src="/bower_components/admin-lte/select2/js/select2.min.js"></script>
    @include('partials._body_bottom_submit_bug_edit_form_js')
    @include('partials._date-toggle')
    <script>
        //updated
        $('.date-toggle-nep-eng').nepalidatetoggle();
        $('.searchable').select2();
        $(function() {
            $('.datepicker').datetimepicker({
                format: 'YYYY-MM-DD',
                sideBySide: true,
                allowInputToggle: true
            });
        });
        //document ready function 
        $(document).ready(function() {
            for (let i = 1; i < 4; i++) {
                addRow();
            }
            $("#client_type").trigger('change');
        })

        //show credit limits and others on credit and hide on others 
        $(document).on('change', '.bill_type', function() {
            let bill_type = $(this).val();
            if (bill_type == "Credit") {
                $(".credit").show();
            } else {
                $(".credit").hide();
            }
        });

        function handleClients() {
            $("#due-date").show();
            $("#addMore").show();
            $("#onclienttype").show();
            $("#random_name").hide();
            $("#billshow").hide();
            $("#bill_type").show();
            $("#forrandom").hide();
            document.getElementById("pan_no").setAttribute('readonly', true);
        }

        function handleStaffs() {
            $("#due-date").hide();
            $("#onclienttype").show();
            $("#random_name").hide();
            $("#forrandom").hide();
            document.getElementById("pan_no").removeAttribute('readonly');
        }

        function handleRandom() {
            $("#due-date").hide();
            $("#onclienttype").hide();
            $("#random_name").show();
            $("#billshow").hide();
            $("#forrandom").show();
            document.getElementById("pan_no").removeAttribute('readonly');
            $('#bill_type').hide();
        }

        //Updated except trigger changes
        $(document).on('change', '#outlet_id', function() {
            let getoutlet = $(this).val();
            let forrandom = $(this).find(":selected").data('random');
            if (forrandom == "1") {
                $('#all_customer_type').hide();
                $('#random_customer_type').show();
            } else {
                $('#all_customer_type').show();
                $('#random_customer_type').hide();
            }
            populateClients();
            populateProducts();
        });

        $(document).on('change', '#client_type', function() {
            let customer_type = $(this).val();
            handleClients();
            if (customer_type == "random_customer") {
                handleRandom();
            } else if (customer_type == "staff") {
                handleStaffs();
                $('#billshow').show();
                $('#bill_type').hide();
                $('#addMore').hide();
                $("#billshow").val('Staff');
                $("#multipleDiv").find("tr:gt(1)").remove();
            } else if (customer_type == "staff-milk") {
                handleStaffs();
                $('#billshow').hide();
                $('#bill_type').show();
            }
            populateClients();
            populateProducts();
        });

        function populateClients() {
            let customer_type = document.querySelector('#client_type').value;
            let outlet_id = document.querySelector('#outlet_id').value;
            $('#client_id').attr('disabled', true);
            $("#client_id").html('');
            $('#client_id').append(`<option value='' data-pan='' data-add=''>Select Client </option>`);

            $.get('/admin/getClientsByType?relation_type=' + customer_type + '&outlet_id=' + outlet_id,
                function(data, status) {
                    $.each(data, function(i, item) {
                        $('#client_id').append(
                            `<option value='${item.id}' data-pan='${item.vat}' data-add='${item.physical_address}'>${item.name} </option>`
                        );
                    });
                    $('#client_id').attr('disabled', false);
                    $('#client_id').trigger('change');
                });
        }

        function populateProducts() {
            let customer_type = document.querySelector('#client_type').value;
            let outlet_id = document.querySelector('#outlet_id').value;
            $(".product_id").attr('disabled', true);
            $(".product_id").html('');
            $('.product_id').append(`<option value=''>Select Product </option>`);
            $.get('/admin/get-products?relation_type=' + customer_type + '&outlet_id=' + outlet_id,
                function(data, status) {
                    $.each(data, function(i, item) {
                        $('.product_id').append(`<option value='${i}'>${item} </option>`)
                    });
                    $(".product_id").attr('disabled', false);
                    $(".product_id").trigger('change');
                });
        }

        $(document).on('change', '#client_id', function() {
            $('#pan_no').val($(this).find(":selected").data('pan'));
            $('#physical_address').val($(this).find(":selected").data('add'));
            if (this.value != '') {
                $(".quantity").each(function(index) {
                    var parentDiv = $(this).parent().parent();
                    calculateAmount(parentDiv)
                });
                let customer_id = $(this).val();
                $.get('/admin/customer/depositamount/' + customer_id, function(data, status) {
                    $('#credit_limit').val(data.credit_limit);
                    $('#remaining_amount').val(data.remaining_amount);
                    $('#remaining_days').val(data.remaining_time);
                    validateCredit(data.remaining_amount, data.remaining_time);
                });
            }
            $('.product_id').trigger("change");
        });

        function emptyProduct(parentDiv) {
            parentDiv.find('.price').val(0);
            parentDiv.find('.total').val(0);
            parentDiv.find('.tax_rate_line').val(0);
            parentDiv.find('.tax_amount_line').val(0);
            parentDiv.find('.units').val("").trigger("change");
            calcTotal();
        }

        $(document).on('change', '.product_id', function() {
            var parentDiv = $(this).parent().parent();
            parentDiv.find('.stock').val('0')

            if (this.value != '') {
                var _token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: "POST",
                    contentType: "application/json; charset=utf-8",
                    url: "/admin/products/GetProductDetailAjax/" + this.value + '?_token=' + _token +
                        '&client=' + $('#client_id').val() + '&outlet_id=' + $("#outlet_id").val(),
                    success: function(result) {
                        var obj = jQuery.parseJSON(result.data);
                        var stock = jQuery.parseJSON(result.stock);
                        if (result.client_type) var client_type = String(result.client_type);
                        else var client_type = $('#client_type').val();
                        if (obj != null) {
                            price = 0;
                            if (client_type == "distributor") {
                                price = obj.distributor_price;
                            } else if (client_type == "retailer") {
                                price = obj.retailer_price;
                            } else {
                                if (obj.direct_customer_price) price = obj.direct_customer_price;
                                else price = obj.customer_price;
                            }
                            if (stock) parentDiv.find('.stock').val(stock);
                            else parentDiv.find('.stock').val('0');

                            parentDiv.find('.price').val(price);
                            parentDiv.find('.quantity').attr('max', stock);
                            parentDiv.find('.units').val(obj.product_unit).change();
                            if (obj.is_vat == 1) {
                                parentDiv.find('.tax_rate_line').val("13").change();
                            } else parentDiv.find('.tax_rate_line').val("0").change();
                            calculateAmount(parentDiv);
                        } else {
                            toastr.error("Product not available in selected outlet");
                            emptyProduct(parentDiv);
                        }
                    },
                });
            } else {
                emptyProduct(parentDiv);
            }
            if (($("#client_type").val() == 'staff') && ($("#client_id").val() != '') && ($(this).val() != '')) {
                checkQuotaAccess($(this).val(), $("#client_id").val())
            }
        });

        function checkQuotaAccess(product_id, client_id) {
            $.ajax({
                type: 'post',
                url: "{{ route('admin.product.check-quota-access') }}",
                data: {
                    '_token': '{{ csrf_token() }}',
                    'product_id': product_id,
                    'staff_id': client_id
                },
                success: function(response) {
                    if (response.status == 'true') {
                        toastr.success(response.msg);
                        $("#submit2").attr('disabled', false);
                    } else {
                        toastr.error(response.msg);
                        $("#submit2").attr('disabled', true);
                    }
                },
                error: function(e) {
                    toastr.error("Please check selected product or staff.");
                }
            });
        }
        //Calculate amount on change product qty, price, tax functions
        function calculateAmount(parentDiv) {
            var quantity = Number(parentDiv.find('.quantity').val());
            var rate = Number(parentDiv.find('.price').val());
            var tax = Number(parentDiv.find('.tax_rate_line').val());
            let discount = Number(parentDiv.find('.discount_amount_line').val());
            var total = quantity * rate;
            let total_with_discount = total - discount;
            var tax_amount = total_with_discount * tax / 100;
            parentDiv.find('.tax_amount_line').val(tax_amount.toFixed(2));
            parentDiv.find('.total').val(total_with_discount.toFixed(2));
            calcTotal();
        }
        $(document).on('input', '.quantity', function() {
            var parentDiv = $(this).parent().parent();
            calculateAmount(parentDiv);

        });
        $(document).on('change', '.price', function() {
            var parentDiv = $(this).parent().parent();
            calculateAmount(parentDiv);
        });
        $(document).on('change', '.tax_rate_line', function() {
            var parentDiv = $(this).parent().parent();
            calculateAmount(parentDiv);
        });
        $(document).on('input', '.discount_amount_line', function() {
            var parentDiv = $(this).parent().parent();
            calculateAmount(parentDiv);
        })
        //function to check numeric 
        function isNumeric(n) {
            return !isNaN(parseFloat(n)) && isFinite(n);
        }
        // Function to calculate serial number for table
        function getSn() {
            var count = 0;
            $('#multipleDiv tr').each(function(index, val) {
                count++;
                if (index > 0) {
                    $(this).find('.p_sn').html(index);
                }
            });
            if (count == 16) {
                $('#addMore').toggleClass('disabled');
            }
        }
        //Add Row to the table 
        $("#addMore").on("click", function() {
            addRow();
        });

        function addRow() {
            $("#multipleDiv tr:last").after($('#orderFields #more-tr').html());
            $("#multipleDiv tr:last").find('.product_id').select2({
                width: '100%'
            });
            let pid = $(".multipleDiv").next('tr').find('.product_id');
            pid.select2('destroy');
            pid.select2({
                width: '100%'
            });
            getSn();
            $('#addmorProducts').show(300);
        }
        //Remove table row
        $(document).on('click', '.remove-this', function() {
            $(this).parent().parent().parent().remove();
            calcTotal();
            $("#multipleDiv .product_id").length > 0 ? $('#addmorProducts').show(300) : $('#addmorProducts').hide(
                300);
            getSn();
        });
        // Function to calculate total
        function calcTotal() {
            var subTotal = 0;
            var taxableAmount = 0;
            var nontaxableAmount = 0;
            var taxAmount = 0;
            var total = 0;
            var totalDiscount = 0;
            $(".total").each(function(index) {
                var parent = $(this).parent().parent();
                var amount = Number(parent.find('.total').val());
                var discount = Number(parent.find('.discount_amount_line').val())
                var tax_rate = Number(parent.find('.tax_amount_line').val());
                subTotal += amount;
                totalDiscount += discount
                taxAmount += tax_rate;
                total += (amount + tax_rate);
                if (tax_rate == 0) {
                    nontaxableAmount += amount;
                } else {
                    taxableAmount += amount;
                }
            });
            $('#sub-total').html(subTotal.toLocaleString());
            $('#subtotal').val(subTotal.toFixed(2));
            $('#discount-amount').text(totalDiscount);
            $('#discount_amount').val(totalDiscount);
            $('#non-taxable-amount').text(nontaxableAmount.toLocaleString());
            $('#nontaxableamount').val(nontaxableAmount);
            $('#taxable-amount').text((taxableAmount).toFixed(2).toLocaleString());
            $('#taxableamount').val((taxableAmount).toFixed(2));
            $('#taxabletax').val(taxAmount.toFixed(2));
            $('#taxable-tax').text(taxAmount.toFixed(2).toLocaleString());
            $('#total').html(total.toLocaleString());
            $('#total_').val(total.toFixed(0));


            let credit_limit = Number($('#credit_limit').val());
            var remaining_amount = credit_limit == 0 ? 0 : credit_limit - total;
            var remaining_days = Number($('#remaining_days').val());
            validateCredit(remaining_amount, remaining_days);
            $('#remaining_amount').val(remaining_amount.toFixed(2));

            if (total != 0) {
                $(':button[type="submit"]').prop('disabled', false);
            } else {
                $(':button[type="submit"]').prop('disabled', true);
            }
        }

        function validateCredit(remaining_amount, remaining_days) {
            if (remaining_amount < 0 && remaining_days) {
                $('.credit-exceeded').show();
            } else {
                $('.credit-exceeded').hide();
            }
        }
        //Add New client from + button
        function openwindow() {
            var win = window.open('/admin/clients/modals?relation_type=supplier', '_blank',
                'toolbar=yes, scrollbars=yes, resizable=yes, top=500,left=500,width=600, height=650');
        }

        function HandlePopupResult(result) {
            if (result) {
                let clients = result.clients;
                let types = $(`input[name=source]:checked`).val();
                if (types == 'lead') {
                    lead_clients = clients;
                } else {
                    customer_clients = clients;
                }
                var option = '<option value="">Select Supplier</option>';
                for (let c of clients) {
                    option = option + `<option value='${c.id}'>${c.name}</option>`;
                }
                $('#customers_id select').html(option);
                $('.supplier_options #_supplier').html(option);
                setTimeout(function() {
                    $('#client_id').select2('destroy');
                    $('#customers_id select').val(result.lastcreated);
                    $('#pan_no').val(result.pan_no);
                    $("#ajax_status").after(
                        "<span style='color:green;' id='status_update'>client sucessfully created</span>");
                    $('#status_update').delay(3000).fadeOut('slow');
                    $('#client_id').select2();
                }, 500);
            } else {
                $("#ajax_status").after("<span style='color:red;' id='status_update'>failed to create clients</span>");
                $('#status_update').delay(3000).fadeOut('slow');
            }
        }
        //Validate Credit limit, product and client type on submit
        $(document).on('click', '#submit2', function(e) {
            e.preventDefault();
            var customer_type = $("#client_type").val();
            var client_id = $("#client_id").val();
            var rem_amount = Number($('#remaining_amount').val());
            if ($("#outlet_id").val() == '' || (customer_type != 'random_customer' && (client_id == '')) || (
                    customer_type != 'random_customer' && (rem_amount < 0))) {
                if ((customer_type != 'random_customer' && (rem_amount < 0))) {
                    alert("Your credit limit is exceeded");
                } else if ($("#outlet_id").val() == '') {
                    alert('Please Select Outlet')
                } else {
                    alert('Please select client');
                }
            } else {
                if ($("#total_").val() > 0) {
                    if (confirm("Are you Sure!") == true) $("#create-invoice").submit();
                } else alert('Select any product.')
            }
        });
    </script>
@endsection
