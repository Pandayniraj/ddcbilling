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
        .table > thead > tr > th,
        .table > tbody > tr > th,
        .table > tfoot > tr > th,
        .table > thead > tr > td,
        .table > tbody > tr > td,
        .table > tfoot > tr > td {
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
        .table > tbody > tr > td,
        .table > tbody > tr > th,
        .table > tfoot > tr > td,
        .table > tfoot > tr > th,
        .table > thead > tr > td,
        .table > thead > tr > th {
            padding: 3px !important;
        }
    </style>
    {{--    <link href="/bower_components/admin-lte/select2/css/select2.min.css" rel="stylesheet" />--}}
@endsection

@section('content')
    <section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
        <h1>
            New Sales {{ $_GET['type'] }}
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
                                    <select class="form-control product_id hiddensearchable reduce" name="product_id[]">
                                        <option value="">Select Product</option>
                                        @foreach ($products as $key => $pk)
                                            <option value="{{ $pk->id }}"
                                                    @if (isset($orderDetail->product_id) && $orderDetail->product_id == $pk->id) selected="selected" @endif>
                                                {{ $pk->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="col-sm-1">
                                    <input type="number" class="form-control input-sm quantity input-sm reduce"
                                           name="quantity[]" placeholder="Quantity" step=".01" autocomplete="off">
                                </td>

                                <td>
                                    <select name='units[]' class="form-control input-sm units reduce" readonly>
                                        <option value="">Units</option>
                                        @foreach ($prod_unit as $pu)
                                            <option value="{{ $pu->id }}">{{ $pu->symbol }}</option>
                                        @endforeach
                                    </select>
                                </td>

                                <td>
                                    <input type="text" class="form-control input-sm price input-sm reduce"
                                           name="price[]" placeholder="Rate"
                                           value="@if (isset($orderDetail->price)) {{ $orderDetail->price }} @endif"
                                           autocomplete="off" readonly>
                                </td>

                                <td>
                                    <input type="number" name="dis_amount[]"
                                           class="form-control input-sm discount_amount_line reduce"
                                           placeholder="Discount"
                                           step="any">
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
                                           name="tax_amount[]" value="0" readonly="readonly"/>
                                </td>

                                <td>
                                    <input type="number" class="form-control input-sm total reduce" name="total[]"
                                           placeholder="Total"
                                           value="@if (isset($orderDetail->total)) {{ $orderDetail->total }} @endif"
                                           style="float:left; width:70%;" step='any' readonly>
                                    <a href="javascript::void(1);" style="width: 10%;">
                                        <i class="remove-this btn btn-xs btn-danger icon fa fa-trash deletable"
                                           style="float: right; color: #fff;"></i>
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div id="CustomOrderFields" style="display: none;">
                        <table class="table">
                            <tbody id="more-custom-tr">
                            <tr>
                                <td class="p_sn"></td>
                                <td>
                                    <input type="text" class="form-control input-sm product"
                                           name="custom_items_name[]" value="" placeholder="Product"
                                           autocomplete="off">
                                </td>
                                <td class="col-sm-1">
                                    <input type="number" class="form-control input-sm quantity input-sm"
                                           name="custom_items_qty[]" placeholder="Quantity" step=".01"
                                           autocomplete="off">
                                </td>

                                <td>
                                    <input type="text" class="form-control input-sm price"
                                           name="custom_items_price[]" placeholder="Price"
                                           value="@if (isset($orderDetail->price)) {{ $orderDetail->price }} @endif"
                                           autocomplete="off">
                                </td>
                                @if (\Auth::user()->hasRole('admins'))
                                    <td>
                                        <input type="number" name="custom_dis_amount[]"
                                               class="form-control input-sm discount_amount_line"
                                               placeholder="Discount">
                                    </td>
                                @endif
                                <td>
                                    <select name='custom_unit[]' class="form-control input-sm">
                                        <option value="">Units</option>
                                        @foreach ($prod_unit as $pu)
                                            <option value="{{ $pu->id }}">{{ $pu->symbol }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="col-sm-1">
                                    <select class="form-control input-sm tax_rate_line" name="custom_tax_type[]">
                                        <option value="0">Exempt(0)</option>
                                        <option value="13">VAT(13)</option>
                                    </select>
                                </td>

                                <td>
                                    <input type="number" class="form-control input-sm tax_amount_line"
                                           name="custom_tax_amount[]" value="0" readonly="readonly">
                                </td>
                                <td>
                                    <input type="number" class="form-control input-sm total" name="custom_total[]"
                                           placeholder="Total"
                                           value="@if (isset($orderDetail->total)) {{ $orderDetail->total }} @endif"
                                           style="float:left; width:70%;" step='any'>
                                    <a href="javascript::void(1);" style="width: 10%;">
                                        <i class="remove-this btn btn-xs btn-danger icon fa fa-trash deletable"
                                           style="float: right; color: #fff;"></i>
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="form-section">
                        <form method="POST" action="{{ route('admin.invoice.create') }}" id="create-invoice">
                            {{ csrf_field() }}
                            <div class="clearfix"></div>
                            <div class="col-md-12" style="margin-top: 30px;">
                                <div class="col-md-2 form-group" style="">
                                    <i class="fa fa-store"></i><label for="user_id">Outlets<span
                                            style="color:red">(*)</span></label>
                                    @if (\Auth::user()->hasRole('admins'))
                                        {!! Form::select('outlet_id',$outlets, null, [
                                            'class' => 'form-control label-success searchable',
                                            'placeholder' => 'Please Select',
                                            'id' => 'outlet_id',
                                            'required',
                                        ]) !!}
                                    @else
                                        <select name="outlet_id" class="form-control searchable" id="outlet_id"
                                                required>
                                            <option value="">select</option>
                                            @foreach ($outlets as $outlet)
                                                @if ($outlet)
                                                    <option value={{ $outlet->id }} selected>
                                                        {{ $outlet->name ?? '' }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                <div class="col-md-2" style="display:block;" id="long">
                                    <div class="callout">
                                        <label id='select_label'>Client Type<span style="color:red">(*)</span></label>
                                        <select class="customer_type select2 form-control" name="client_type"
                                                required id='customer_type' style="display: block;">
                                            <option value="">--SELECT TYPE--</option>
                                            <option value="distributor">Distributor</option>
                                            <option value="retailer">Retailer</option>
                                            <option value="boothman">BoothMan</option>
                                            {{-- <option value="direct_customer">Direct Customer</option>--}}
                                            <option value="staff">Staff Milk</option>
                                            <option value="staffghee">Staff Ghee</option>
                                            {{-- <option value="customer">Customer</option>--}}
                                            <option value="random_customer">Random Customer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2" style="display:none" id="short">
                                    <div class="callout">
                                        <label id='select_label'>Client Type <span style="color:red"> (*)</span></label>
                                        <select class="customer_type select2 form-control" name="client_type_one"
                                                id='customers_type'>
                                            <option value="">--SELECT TYPE--</option>
                                            <option value="random_customer">Random Customer</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-2" id="onclienttype" style="display: block;">
                                    <div class="callout">
                                        <label id='select_label'><span>Select Clients</span> <i class="imp"
                                                                                                style="color:red">(*)</i>
                                            <span
                                                id='create_lead_or_clients'>
                                                <a href="#" onClick="openwindow('client')"><i
                                                        class="fa fa-plus plusicon"></i></a>
                                            </span></label>
                                        <select class="customer_id select2" name="customer_id"
                                                id='client_id'>

                                            @if (isset($clients))
                                                <option value="">--SELECT CLIENT--</option>
                                            @endif
                                        </select>
                                        <input type="hidden" name="source" value='client'>

                                    </div>
                                </div>
                                <div class="col-md-2" id="show" style="display: none;">
                                    <div class="callout">
                                        <label id='select_label'><span>Client Name</span> <i class="imp"
                                                                                             style="color:red">(*)</i>
                                            <span
                                                id='create_lead_or_clients'>
                                            </span></label>
                                        <input type="text" name="customer_name" class="form-control" value="Cash">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Bill Type<span style="color:red"></span></label>
                                        <select name="bill_type" class="form-control" id="bill_type"
                                                style="display:block;">
                                            <option value="">Select BILL TYPE</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Credit">Credit</option>
                                            <option value="Staff">Staff</option>
                                        </select>

                                        <select name="bill_type_one" class="form-control" id="forrandom"
                                                style="display:none;">
                                            <option value="">Select BILL TYPE</option>
                                            <option value="Cash">Cash</option>
                                        </select>

                                        <select name="bill_type_two" class="form-control" id="billshow"
                                                style="display:none;">
                                            <option value="">Select BILL TYPE</option>
                                            <option value="Staff">Staff</option>
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

                                <div class="col-md-2">
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
                                <div class="col-md-2 form-group credit" style="">
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

                                <div class="col-md-2 form-group credit" style="">
                                    <label for="credit_limit"> <i class="fa fa-credit-card"></i> Credit Limit</label>
                                    {!! Form::text('credit_limit', null, [
                                        'class' => 'form-control input-sm ',
                                        'id' => 'credit_limit',
                                        'readonly' => 'readonly',
                                    ]) !!}
                                </div>
                                <div class="col-md-2 form-group credit">
                                    <label for="remaining_amount" style="white-space: nowrap"> <i
                                            class="fa fa-money"></i> Remaining Amount</label>
                                    {!! Form::text('remaining_amount', null, [
                                        'class' => 'form-control input-sm ',
                                        'id' => 'remaining_amount',
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
                            <br/><br/>

                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                    <tr class="bg-info tr-heading">
                                        <th style="width: 5%;">S.N</th>
                                        <th style="width: 20%;">Item*</th>
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
                                        <td colspan="6">
                                            <div class="btn-group pull-right">
                                                <a href="javascript:void(0);" class="btn btn-default btn-sm disclose"
                                                   id="addMore" style="float:right;">
                                                    <i class="fa fa-plus plusicon"></i> <span>Add Products Item</span>
                                                </a> &nbsp;
                                                {{-- <a href="javascript:void(0);" class="btn btn-default btn-sm" id="addCustomMore"   title='Inventory is not updated with custome product' >
                                                    <i class="fa fa-plus plusicon"></i> <span>Customised items</span>
                                                </a> --}}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" style="text-align: right;">Amount</td>
                                        <td id="sub-total">0.00</td>
                                        <td>&nbsp; <input type="hidden" name="subtotal" id="subtotal"
                                                          value="0"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" style="text-align: right;">Order Discount</td>
                                        <td id='discount-amount'>0.00</td>
                                        <td>
                                            <input type="hidden" name="discount_amount" value="0"
                                                   id='discount_amount'>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" style="text-align: right;">Non Taxable Amount</td>
                                        <td id="non-taxable-amount">0.00</td>
                                        <td>&nbsp;<input type="hidden" name="non_taxable_amount"
                                                         id="nontaxableamount" value="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" style="text-align: right;">Taxable Amount</td>
                                        <td id="taxable-amount">0.00</td>
                                        <td>
                                            &nbsp; <input type="hidden" name="taxable_amount" id="taxableamount"
                                                          value="0">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" style="text-align: right;">Tax Amount</td>
                                        <td id="taxable-tax">0.00</td>
                                        <td>&nbsp; <input type="hidden" name="taxable_tax" id="taxabletax"
                                                          value="0"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" style="text-align: right; font-weight: bold;">Total Amount
                                        </td>
                                        <td id="total">0.00</td>
                                        <td>
                                            <input type="hidden" name="total_tax_amount" id="total_tax_amount"
                                                   value="0">
                                            <input type="hidden" name="final_total" id="total_" value="0">
                                        </td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <br/>

                            <div class="col-md-6 form-group" style="margin-top:5px;">
                                <label for="comment">Terms & Conditions Comment </label>
                                <small class="text-muted">Will be displayed on the invoice
                                </small>
                                <textarea class="form-control TextBox comment" name="comment">
                                    @if (isset($order->comment)) {{ $order->comment }} @endif
                                </textarea>
                            </div>

                            <div class="col-md-6 form-group" style="margin-top:5px;">
                                <label for="address">Address</label>
                                <textarea class="form-control TextBox address" name="address" id='physical_address'>
                                    @if (isset($orderDetail->address)) {{ $orderDetail->address }} @endif
                                </textarea>
                            </div>
                    </div>
                    <div class="panel-footer footer">
                        <button type="submit" class="btn btn-social btn-foursquare" id="submit">
                            <i class="fa fa-save"></i>Save {{ $_GET['type'] }}
                        </button>
                        <a class="btn btn-social btn-foursquare" href="/admin/invoice1"> <i class="fa fa-times"></i>
                            Cancel </a>
                    </div>
                    </form>
                </div>
            </div>

        </div>
        <!-- /.box-body -->
    </div>
    <div class='supplier_options' style="display: none;">
        <div id='_supplier'>
            <option value="">Select Supplier</option>
            @if (isset($clients))
                @foreach ($clients as $key => $uk)
                    <option value="{{ $uk->id }}"
                    @if ($orderDetail && $uk->id == $orderDetail->customer_id)
                        {{ 'selected="selected"' }}
                        @endif>
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
                    @if ($orderDetail && $uk->id == $orderDetail->customer_id)
                        {{ 'selected="selected"' }}
                        @endif>
                        {{ '(' . env('APP_CODE') . $uk->id . ') ' . $uk->name . ' -' . $uk->organization }}</option>
                @endforeach
            @endif
        </div>
    </div>
@endsection

@section('body_bottom')
    <script src="/bower_components/admin-lte/select2/js/select2.min.js"></script>

    <!-- form submit -->
    @include('partials._body_bottom_submit_bug_edit_form_js')
    @include('partials._date-toggle')
    <script>
        $('.date-toggle-nep-eng').nepalidatetoggle();
        const dateRange = {
            <?php $currentFiscalyear = FinanceHelper::cur_fisc_yr(); ?>
            minDate: `{{ $currentFiscalyear->start_date }}`,
            maxDate: `{{ $currentFiscalyear->end_date }}`
        }
        @if (\Request::get('type') == 'bills' || \Request::get('type') == 'assets')
        $('select[name=customer_id]').prop('required', true);
        @endif

        $('.customer_id').select2();
        $('.customer_type').select2();
        $('.searchable').select2();

        function adjustTotalNonTaxable() {
            var taxableAmount = 0;

            var nontaxableAmount = 0;

            var taxAmount = 0;

            var taxableAmount = 0;

            var nontaxableAmount = 0;
            $('.tax_rate_line').each(function () {

                let parent = $(this).parent().parent();

                let tax_rate = Number(parent.find('.tax_amount_line').val());

                var total = Number(parent.find('.total').val());

                if ($(this).val() == 0) {
                    nontaxableAmount += total;
                } else {
                    taxableAmount += total;
                    taxAmount += tax_rate;
                }
            });

            $('#non-taxable-amount').text(nontaxableAmount.toLocaleString());

            $('#nontaxableamount').val(nontaxableAmount);

            $('#taxable-amount').text((taxableAmount).toFixed(2).toLocaleString());

            $('#taxableamount').val((taxableAmount).toFixed(2));

            $('#taxabletax').val(taxAmount.toFixed(2));

            $('#taxable-tax').text(taxAmount.toFixed(2).toLocaleString());

            var totalDiscount = 0;
            $('.discount_amount_line').each(function () {
                totalDiscount += Number($(this).val());
            });
            $('#discount-amount').text(totalDiscount);
            $('#discount_amount').val(totalDiscount);

        }

        function adjustTax(ev) {
            let parent = ev.parent().parent();
            let total = Number(parent.find('.total').val());
            let discount = Number(parent.find('.discount_amount_line').val());
            let total_with_discount = total - discount;
            parent.find('.total').val(total_with_discount);
            let tax_rate = Number(parent.find('.tax_rate_line').val());
            let tax_amount = (tax_rate / 100 * total_with_discount);
            parent.find('.tax_amount_line').val(tax_amount.toFixed(2));
            let amount_with_tax = total_with_discount;
            parent.find('.total').val(amount_with_tax);
        }

        $(document).on('change', '.tax_rate_line', function () {
            let parent = $(this).parent().parent();
            parent.find('.quantity').trigger('change');
        });

        $(document).on('change', '.discount_amount_line', function () {
            let parent = $(this).parent().parent();
            parent.find('.quantity').trigger('change');
        })


        function isNumeric(n) {
            return !isNaN(parseFloat(n)) && isFinite(n);
        }

        $(document).on('change', '.product_id', function () {
            var parentDiv = $(this).parent().parent();
            if (this.value != 'NULL') {
                var _token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: "POST",
                    contentType: "application/json; charset=utf-8",
                    url: "/admin/products/GetProductDetailAjax/" + this.value + '?_token=' + _token + '&client=' + $('#client_id').val() + '&outlet_id=' + $("#outlet_id").val(),
                    success: function (result) {
                        var obj = jQuery.parseJSON(result.data);
                        var stock = jQuery.parseJSON(result.stock);
                        if (result.client_type) var client_type = String(result.client_type);
                        else var client_type = $('#customer_type').val();
                        if (obj != null) {
                            console.log(obj, client_type);
                            price = 0;
                            if (client_type == "distributor") {
                                price = obj.distributor_price;
                            } else if (client_type == "retailer") {
                                price = obj.retailer_price;
                            } else {
                                if (obj.direct_customer_price) price = obj.direct_customer_price;
                                else price = obj.customer_price;
                            }
                            parentDiv.find('.price').val(price);
                            parentDiv.find('.quantity').attr('max', stock);
                            parentDiv.find('.units').val(obj.product_unit).change();
                            if (obj.is_vat == 1) {
                                parentDiv.find('.tax_rate_line').val("13").change();
                            } else {
                                parentDiv.find('.tax_rate_line').val("0").change();
                            }
                            if (isNumeric(parentDiv.find('.quantity').val()) && parentDiv.find('.quantity').val() != '') {
                                var total = Number(parentDiv.find('.quantity').val()) * Number(price);
                            } else {
                                var total = price;
                            }
                            var tax = parentDiv.find('.tax_rate_line').val();
                            if (isNumeric(tax) && tax != 0 && (total != 0 || total != '')) {
                                tax_amount = Number(total) * Number(tax) / 100;
                                parentDiv.find('.tax_amount_line').val(tax_amount);
                                total = total;
                            } else
                                parentDiv.find('.tax_amount_line').val('0');
                            parentDiv.find('.total').val(total);
                            calcTotal();
                        } else {
                            parentDiv.find('.price').val('');
                            parentDiv.find('.quantity').attr('max', 0);
                            parentDiv.find('.tax_rate_line').val(0);
                            parentDiv.find('.tax_amount_line').val(0);
                            parentDiv.find('.total').val('');
                        }
                    }
                });
            } else {
                parentDiv.find('.price').val('');
                parentDiv.find('.total').val('');
                parentDiv.find('.tax_amount_line').val('');
                calcTotal();
            }
        });

        $(document).on('change', '.customer_id', function () {
            if (this.value != '') {
                $(".quantity").each(function (index) {
                    var parentDiv = $(this).parent().parent();
                    if (isNumeric($(this).val()) && $(this).val() != '')
                        var total = $(this).val() * parentDiv.find('.price').val();
                    else
                        var total = parentDiv.find('.price').val();

                    var tax = parentDiv.find('.tax_rate_line').val();
                    if (isNumeric(tax) && tax != 0 && (total != 0 || total != '')) {
                        tax_amount = total * Number(tax) / 100;
                        parentDiv.find('.tax_amount_line').val(tax_amount);
                        total = total;
                    } else
                        parentDiv.find('.tax_amount').val('0');

                    if (isNumeric(total) && total != '') {
                        parentDiv.find('.total').val(total);
                        calcTotal();
                    }
                });
                let customer_id = $(this).val();
                $.get('/admin/customer/depositamount/' + customer_id, function (data, status) {
                    $('#bank_deposit').val(data.deposit_amount);
                    $('#credit_limit').val(data.credit_limit);
                    $('#remaining_amount').val(data.remaining_amount);
                });
            } else {
                $('.total').val('0');
                $('.tax_amount_line').val('0');
                calcTotal();
            }

            let supp_id = $(this).val();
            $.get('/admin/getpanno/' + supp_id, function (data, status) {
                $('#pan_no').val(data.pan_no);
                $('#physical_address').val(data.physical_address);
            });
            $('.product_id').trigger("change");
        });
        $(document).on('change', '#outlet_id', function () {
            let getoutlet = $(this).val();
            $(".product_id").trigger('change');

            // console.log(getoutlet);
            $.get("/admin/get/randomcustomer/outlet?id=" + getoutlet, function (data) {
                if (data.data.forrandomcustomer == 1) {
                    document.getElementById("long").style.display = "none";
                    document.getElementById("short").style.display = "block";
                } else {
                    document.getElementById("long").style.display = "block";
                    document.getElementById("short").style.display = "none";
                }
            });
        });

        $(document).ready(function () {
            $("#customer_type").val('random_customer');
            $("#forrandom").val('Cash');
            $("#customer_type").trigger('change');
        })

        $(document).on('change', '#customer_type', function () {
            document.querySelectorAll("#client_id option").forEach(opt => {
                opt.disabled = true;
            });
            let customer_type = $(this).val();

            if (customer_type == "random_customer") {
                document.getElementById("onclienttype").style.display = "none";
                document.getElementById("show").style.display = "block";
                document.getElementById("pan_no").removeAttribute('readonly');
                document.getElementById("billshow").style.display = "none";
                document.getElementById("forrandom").style.display = "block";
                $('#bill_type').hide();
            } else if (customer_type == "staff") {
                document.getElementById("onclienttype").style.display = "block";
                document.getElementById("show").style.display = "none";
                document.getElementById("pan_no").removeAttribute('readonly');
                document.getElementById("billshow").style.display = "block";
                document.getElementById("bill_type").style.display = "none";
                document.getElementById("forrandom").style.display = "none";
            } else {
                document.getElementById("onclienttype").style.display = "block";
                document.getElementById("show").style.display = "none";
                document.getElementById("pan_no").setAttribute('readonly', true);
                document.getElementById("billshow").style.display = "none";
                document.getElementById("bill_type").style.display = "block";
                document.getElementById("forrandom").style.display = "none";
            }
            $.get('/admin/getClients?relation_type=' + customer_type, function (data, status) {

                $("#client_id").empty();
                var newOption = new Option('SELECT', '', false, false);
                $('#client_id').append(newOption);
                $.each(data, function (i, item) {
                    var newOption = new Option(item, i, false, false);
                    $('#client_id').append(newOption);
                });
                document.querySelectorAll("#client_id option").forEach(opt => {
                    opt.disabled = false;
                });
            });
        });
        $(document).on('change', '#customers_type', function () {
            let customer_type = $(this).val();

            if (customer_type == "random_customer") {
                document.getElementById("onclienttype").style.display = "none";
                document.getElementById("show").style.display = "block";
                document.getElementById("pan_no").removeAttribute('readonly');
                document.getElementById("billshow").style.display = "none";
                document.getElementById("forrandom").style.display = "block";
                $('#bill_type').hide();
            } else if (customer_type == "staff") {
                document.getElementById("onclienttype").style.display = "block";
                document.getElementById("show").style.display = "none";
                document.getElementById("pan_no").removeAttribute('readonly');
                document.getElementById("billshow").style.display = "block";
                document.getElementById("bill_type").style.display = "none";
            } else {
                document.getElementById("onclienttype").style.display = "block";
                document.getElementById("show").style.display = "none";
                document.getElementById("pan_no").setAttribute('readonly', true);
                document.getElementById("billshow").style.display = "none";
                document.getElementById("bill_type").style.display = "block";
                document.getElementById("forrandom").style.display = "none";
            }
            $.get('/admin/getClients?relation_type=' + customer_type, function (data, status) {

                $("#client_id").empty();
                var newOption = new Option('SELECT', '', false, false);
                $('#client_id').append(newOption);
                $.each(data, function (i, item) {
                    var newOption = new Option(item, i, false, false);
                    $('#client_id').append(newOption);
                });
            });
        });

        $("#create-invoice").submit(function(e) {
            var customer_type = $("#customer_type").val();
            var client_id = $("#client_id").val();
            if(customer_type != 'random_customer') {
                if(client_id == '') {
                    e.preventDefault();
                    alert('Please select client');
                }
            }
        });

        $(document).on('change', '#client_id', function () {
            $.ajax({
                type: "POST",
                contentType: "application/json; charset=utf-8",
                url: "/admin/invoice-check-unpaid-bill/" + $(this).val(),
                success: function (response) {
                    if ((response.status === 'unpaid') || (response.status === 'partial')) {
                        alert('Please pay previous bill first to continue');
                        $('#submit').attr('disabled', true);
                        $('#create-invoice').bind('submit', function (e) {
                            e.preventDefault();
                        });
                    } else {
                        $("#submit").removeAttr('disabled', false);
                        $('#create-invoice').unbind('submit');
                    }
                }
            });
        });

        $(document).on('change', '#bill_type', function () {
            let bill_type = $(this).val();
            if (bill_type == "Credit") {
                $(".credit").show();
            } else {
                $(".credit").hide();
                $('#bank_deposit').val(0);
                $('#credit_limit').val(0);
                $('#deposit_amount').val(0);
                $('#remaining_amount').val(0);
            }

        });
        $(document).on('change', '#bill_type_one', function () {
            let bill_type = $(this).val();
            if (bill_type == "Credit") {
                $(".credit").show();
            } else {
                $(".credit").hide();
                $('#bank_deposit').val(0);
                $('#credit_limit').val(0);
                $('#deposit_amount').val(0);
                $('#remaining_amount').val(0);
            }

        });
        $(document).on('change', '#bill_type_two', function () {
            let bill_type = $(this).val();
            if (bill_type == "Credit") {
                $(".credit").show();
            } else {
                $(".credit").hide();
                $('#bank_deposit').val(0);
                $('#credit_limit').val(0);
                $('#deposit_amount').val(0);
                $('#remaining_amount').val(0);
            }

        });
        $(document).on('change', '#deposit_amount', function () {
            let deposit_amount = $(this).val();
            let credit_limit = $('#credit_limit').val();

            if (deposit_amount > 0) {
                let remaining_amount = Number(deposit_amount) + Number(credit_limit);
                $('#remaining_amount').val(remaining_amount);
            } else {
                $('#remaining_amount').val(credit_limit);
            }
            calcTotal();
        });

        $(document).on('change', '.quantity', function () {
            var parentDiv = $(this).parent().parent();
            if (isNumeric(this.value) && this.value != '') {
                if (isNumeric(parentDiv.find('.quantity').val()) && parentDiv.find('.quantity').val() != '') {
                    var total = parentDiv.find('.price').val() * this.value;
                } else
                    var total = '';
            } else {
                var total = '';
            }
            var tax = parentDiv.find('.tax_rate_line').val();
            if (isNumeric(tax) && tax != 0 && (total != 0 || total != '')) {
                tax_amount = total * Number(tax) / 100;
                parentDiv.find('.tax_amount_line').val(tax_amount);
                total = total;
            } else
                parentDiv.find('.tax_amount_line').val('0');
            parentDiv.find('.total').val(total);
            adjustTax($(this));
            calcTotal();
        });

        $(document).on('change', '.price', function () {


            var parentDiv = $(this).parent().parent();
            if (isNumeric(this.value) && this.value != '') {
                if (isNumeric(parentDiv.find('.quantity').val()) && parentDiv.find('.quantity').val() != '') {
                    var total = parentDiv.find('.quantity').val() * this.value;
                } else
                    var total = '';
            } else
                var total = '';

            var tax = parentDiv.find('.tax_rate_line').val();
            if (isNumeric(tax) && tax != 0 && (total != 0 || total != '')) {
                tax_amount = total * Number(tax) / 100;
                parentDiv.find('.tax_amount_line').val(tax_amount);
                total = total;
            } else
                parentDiv.find('.tax_amount_line').val('0');

            parentDiv.find('.total').val(total);
            adjustTax($(this));
            calcTotal();
        });

        $(document).on('change', '.total', function () {

            var parentDiv = $(this).parent().parent();
            if (isNumeric(this.value) && this.value != '') {
                var total = this.value;
                if (isNumeric(parentDiv.find('.quantity').val()) && parentDiv.find('.quantity').val() != '') {
                    var price = total / parentDiv.find('.quantity').val();
                } else
                    var price = '';
            } else
                var price = '';

            var tax = parentDiv.find('.tax_rate_line').val();
            if (isNumeric(tax) && tax != 0 && (total != 0 || total != '')) {
                tax_amount = total * Number(tax) / 100;
                parentDiv.find('.tax_amount_line').val(tax_amount);
                total = total;
            } else
                parentDiv.find('.tax_amount_line').val('0');

            parentDiv.find('.price').val(price);
            adjustTax($(this));
            calcTotal();
        });

        $(document).on('change', '.tax_rate_line', function () {
            var parentDiv = $(this).parent().parent();

            if (isNumeric(parentDiv.find('.quantity').val()) && parentDiv.find('.quantity').val() != '') {
                var total = parentDiv.find('.price').val() * Number(parentDiv.find('.quantity').val());
            } else
                var total = '';

            var tax = $(this).val();
            if (isNumeric(tax) && tax != 0 && (total != 0 || total != '')) {
                tax_amount = Math.round(total * Number(tax) / 100);
                parentDiv.find('.tax_amount_line').val(tax_amount);
                total = Math.round(total);
            } else
                parentDiv.find('.tax_amount_line').val('0');

            parentDiv.find('.total').val(total);
            calcTotal();
        });


        function getSn() {
            var count = 0;
            $('#multipleDiv tr').each(function (index, val) {
                count++;

                if (index > 0) {
                    $(this).find('.p_sn').html(index);


                }

            });
            if (count == 16) {
                $('#addMore').toggleClass('disabled');
            }

        }

        $(document).ready(function () {
            for (let i = 1; i < 4; i++) {

                $(".multipleDiv").after($('#orderFields #more-tr').html());
                $(".multipleDiv").next('tr').find('.product_id').select2({
                    width: '100%'
                });
                let pid = $(".multipleDiv").next('tr').find('.product_id');
                pid.select2('destroy');
                pid.select2({
                    width: '100%',
                });
                $(".multipleDiv").next('tr').find('.quantity').val('1');

                getSn();
                $('#addmorProducts').show(300);
            }
        });

        $("#addMore").on("click", function () {
            // $(".multipleDiv").after($('#orderFields #more-tr').html());
            $("#multipleDiv tr:last").after($('#orderFields #more-tr').html());
            // $(".multipleDiv").next('tr').find('.product_id').select2({
            $("#multipleDiv tr:last").find('.product_id').select2({
                width: '100%'
            });
            let pid = $(".multipleDiv").next('tr').find('.product_id');
            pid.select2('destroy');
            pid.select2({
                width: '100%',
            });
            $(".multipleDiv").next('tr').find('.quantity').val('1');

            getSn();
            $('#addmorProducts').show(300);
        });
        $("#addCustomMore").on("click", function () {
            $(".multipleDiv").after($('#CustomOrderFields #more-custom-tr').html());
            $(".multipleDiv").next('tr').find('.quantity').val('1');
            getSn();
        });

        $(document).on('click', '.remove-this', function () {
            $(this).parent().parent().parent().remove();
            calcTotal();
            $("#multipleDiv .product_id").length > 0 ? $('#addmorProducts').show(300) : $('#addmorProducts').hide(
                300);
            getSn();
        });

        $(document).on('change', '#vat_type', function () {
            calcTotal();
        });

        function calcTotal() {

            adjustTotalNonTaxable();
            var subTotal = 0;
            var taxableAmount = 0;
            var total = 0;
            var tax_amount = 0;
            var taxableTax = 0;
            $(".total").each(function (index) {
                if (isNumeric($(this).val()))
                    subTotal = Number(subTotal) + Number($(this).val());
            });
            $(".tax_amount_line").each(function (index) {
                if (isNumeric($(this).val()))
                    tax_amount = Math.round(Number(tax_amount) + Number($(this).val()));
            });
            $('#sub-total').html(subTotal.toLocaleString());
            $('#subtotal').val(subTotal);

            total = Number($('#nontaxableamount').val()) + Number($("#taxableamount").val()) +
                Number($('#taxabletax').val());

            var discount_amount = $('#discount_amount').val();

            var vat_type = $('#vat_type').val();


            //anamol
            $('#total_tax_amount').val(tax_amount);

            $('#total').html(total.toLocaleString());
            $('#total_').val(total);
            let credit_limit = $('#credit_limit').val();
            remaining_amount = Number(credit_limit) - Number(total);
            $('#remaining_amount').val(remaining_amount);
            // remaining_amount=Number($('#remaining_amount').val());

            var bill_type = $('#bill_type').val();

            // if(bill_type == "Credit"){
            //     if(total <= remaining_amount && total !=''){
            //     $(':button[type="submit"]').prop('disabled', false);
            //     }else{
            //     $(':button[type="submit"]').prop('disabled', true);
            // }

            // }else{
            if (total != '') {
                $(':button[type="submit"]').prop('disabled', false);
            } else {
                $(':button[type="submit"]').prop('disabled', true);
            }
            // }


        }

        $(document).on('keyup', '#discount_amount', function () {
            calcTotal();
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function () {

            $('.project_id').select2();
        });
    </script>

    <script type="text/javascript">
        $(function () {
            $('.datepicker').datetimepicker({
                format: 'YYYY-MM-DD',
                sideBySide: true,
                allowInputToggle: true
            });

        });
    </script>

    <script type="text/javascript">
        var refNo = 'PO-' + $("#reference_no").val();

        $("#reference_no_write").val(refNo);

        $(document).on('keyup', '#reference_no', function () {

            var val = $(this).val();

            if (val == null || val == '') {
                $("#errMsg").html("Already Exists");
                $('#btnSubmit').attr('disabled', 'disabled');
                return;
            } else {
                $('#btnSubmit').removeAttr('disabled');
            }

            var ref = 'PO-' + $(this).val();
            $("#reference_no_write").val(ref);
            $.ajax({
                method: "POST",
                url: "/admin/purchase/reference-validation",
                data: {
                    "ref": ref,
                    "_token": token
                }
            })
                .done(function (data) {
                    var data = jQuery.parseJSON(data);
                    if (data.status_no == 1) {
                        $("#errMsg").html('Already Exists!');
                    } else if (data.status_no == 0) {
                        $("#errMsg").html('Available');
                    }
                });
        });

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
                setTimeout(function () {
                    $('.customer_id').select2('destroy');
                    $('#customers_id select').val(result.lastcreated);
                    $('#pan_no').val(result.pan_no);
                    $("#ajax_status").after(
                        "<span style='color:green;' id='status_update'>client sucessfully created</span>");
                    $('#status_update').delay(3000).fadeOut('slow');
                    $('.customer_id').select2();
                }, 500);
            } else {
                $("#ajax_status").after("<span style='color:red;' id='status_update'>failed to create clients</span>");
                $('#status_update').delay(3000).fadeOut('slow');
            }
        }

        $(document).on('hidden.bs.modal', '#modal_dialog', function (e) {
            $('#modal_dialog .modal-content').html('');
        });

        function handleProductModel(result) {
            var lastcreated = result.lastcreated;
            $('#modal_dialog').modal('hide');
            $('select.product_id').each(function () {
                let options = `<option value='${lastcreated.id}'>${lastcreated.name}</option>`
                $(this).append(options);
            });
            setTimeout(function () {
                alert("Product SuccessFully Added");
            }, 500);

        }

        $('#discount_type').change(function () {
            if ($(this).val() == 'a') {
                $('.discount_type').text('Order Discount (Amount)')
            } else {
                $('.discount_type').text('Order Discount (%)')
            }
            $('#discount_amount').val('')
            calcTotal();

        });
        $("input[name=supplier_type]").change(function () {
            if (!$(this).is(":checked")) {
                return;
            }
            $('.customer_id').select2('destroy');
            if ($(this).val() == 'supplier') {
                let option = $('.supplier_options #_supplier').html();
                $('select[name=customer_id]').html(option);
                $('#create_supplier').show();
            } else {
                let option = $('.supplier_options #_paid_through').html();
                $('select[name=customer_id]').html(option);
                $('#create_supplier').hide();
            }
            $('.customer_id').select2();

        });
        $('input[name=supplier_type]').trigger('change');

        $('#selectdatetype').val('nep');

        $('#selectdatetype').trigger('change');
    </script>
    {{-- Bar code scanner --}}
    <script type="text/javascript">
        var keybuffer = '';

        function press(event) {
            if (event.which === 13) {
                bar_val = Number(keybuffer);
                keybuffer = '';

                let prevItem = $(`#prod-${bar_val}`);
                if (prevItem.length > 0) {
                    let prevQty = prevItem.find('.quantity');
                    let newQty = Number(prevQty.val()) + 1;

                    prevQty.val(newQty);
                    let pid = prevItem.find('.product_id');
                    product_detail(pid);
                    return;
                }

                $(".multipleDiv").after($('#orderFields #more-tr').html());
                $(".multipleDiv").next('tr').find('.product_id').select2({
                    width: '100%'
                });

                $(".multipleDiv").next('tr').attr('id', `prod-${bar_val}`);

                let pid = $(".multipleDiv").next('tr').find('.product_id');
                pid.select2('destroy');
                pid.val(bar_val);
                pid.select2({
                    width: '100%',
                });
                $(".multipleDiv").next('tr').find('.quantity').val('1');

                getSn();
                product_detail(pid);
            }
            var number = event.which - 48;
            if (number < 0 || number > 9) {
                return;
            }
            keybuffer += number;
        }

        $(document).on("keypress", press);

        function product_detail(pid) {
            var parentDiv = $(pid).parent().parent();
            if (pid.val() != 'NULL') {
                var _token = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: "POST",
                    contentType: "application/json; charset=utf-8",
                    url: "/admin/products/GetProductDetailAjax/" + pid.val() + '?_token=' + _token + '&client=' + $('#client_id').val() + '&outlet_id=' + $("#outlet_id").val(),
                    success: function (result) {
                        var obj = jQuery.parseJSON(result.data);
                        var price = 0;
                        if ($("#customer_type").val() == "distributor") {
                            price = obj.distributor_price;
                        } else if ($("#customer_type").val() == "retailer") {
                            price = obj.retailer_price;
                        } else {
                            price = obj.customer_price;
                        }
                        parentDiv.find('.price').val(price);
                        parentDiv.find('.units').val("");
                        parentDiv.find('.units').val(result.units?.id);
                        if (isNumeric(parentDiv.find('.quantity').val()) && parentDiv.find('.quantity').val() != '') {
                            var total = parentDiv.find('.quantity').val() * price;
                        } else {
                            var total = price;
                        }

                        var tax = parentDiv.find('.tax_rate_line').val();
                        if (isNumeric(tax) && tax != 0 && (total != 0 || total != '')) {
                            tax_amount = total * Number(tax) / 100;
                            parentDiv.find('.tax_amount_line').val(tax_amount);
                            total = total;
                        } else
                            parentDiv.find('.tax_amount_line').val('0');

                        parentDiv.find('.total').val(total);
                        calcTotal();
                    }
                });
            } else {
                parentDiv.find('.price').val('');
                parentDiv.find('.total').val('');
                parentDiv.find('.tax_amount_line').val('');
                calcTotal();
            }
        }
    </script>
@endsection
