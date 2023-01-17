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

    .footer {
        position: fixed;
        left: 0;
        bottom: 0;
        width: 100%;
        background-color: #efefef;
        color: white;
        text-align: center;
    }
    input.form-control{
        min-width: 55px !important;
    }
    select{
        min-width: 80px !important;

    }
    .p_sn{
        max-width: 3px !important;
    }
    @media only screen and (max-width: 770px) {
        input.total{
            width: 90px !important;

        }
        .fa-trash{
            float: left !important;
            margin-left: 5px;
        }
        .hide_on_tablet{
            display: none;
        }
         .btn-group{
            margin-top: -15px;
        }
    }
    .label-default {
    background-color: transparent;
}
.form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
    background-color: transparent;
}
.form-control {
    border-radius: 5px !important;
    box-shadow: none;
    border-color: #1181e059;
}
tr.bg-info th {
    border-left: solid 2px #fff;
}
.table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td {
    border-top: none;
}
input#discount_percent {
    width: 37%;
    border-radius: 5px;
    border-color: #1181e059;
}
input#discount_amount {
    width: 37%;
    border-radius: 5px;
    border-color: #1181e059;
}
.box {
    border-radius: 12px;
    background: #ffffff;
    border-top: none;
    box-shadow: rgba(136, 165, 191, 0.48) 6px 2px 16px 0px, rgba(255, 255, 255, 0.8) -6px -2px 16px 0px;
}
.select2-container--default .select2-selection--single {
    background-color: #fff;
    border: 1px solid #1181e059 !important;
    border-radius: 5px;
    height: 30px;
}
.panel-default {
    border-color: transparent;
     box-shadow: rgba(136, 165, 191, 0.48) 6px 2px 16px 0px, rgba(255, 255, 255, 0.8) -6px -2px 16px 0px;
}
</style>
@endsection

@section('content')
<link href="/bower_components/admin-lte/select2/css/select2.min.css" rel="stylesheet" />
<script src="/bower_components/admin-lte/select2/js/select2.min.js"></script>

<div class="panel panel-default">
  <div class="panel-body">
      <section class="content-header" style="margin-top: -18px;">
    <h1>
        Tax Invoice
        <small>
            Creating new Tax invoice, compatible with IRD guidelines.
        </small>
    </h1>
    <p> Go <a target="_blank" href="/admin/orders?type=invoice"> there</a> for regular sales or non tax invoice</p>
    {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false) !!}
</section>
  </div>
</div>

<div class='row'>
    <div class='col-md-12'>
        <div class="box-body">
            <div id="orderFields" style="display: none;">
                <table class="table">
                    <tbody id="more-tr">
                        <tr>
                             <td class="p_sn"></td>
                            <td>
                                <select class="form-control input-sm select2 product_id" name="product_id[]" required="required">
                                    <option value="">Select Product</option>
                                    @foreach($products as $key => $pk)
                                    <option value="{{ $pk->id }}" @if(isset($orderDetail->product_id) && $orderDetail->product_id == $pk->id) selected="selected"@endif>{{ $pk->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control input-sm price" name="price[]" placeholder="Price" value="@if(isset($orderDetail->price)){{ $orderDetail->price }}@endif" required="required" autocomplete="off">
                            </td>
                            <td>
                                <input type="number" class="form-control input-sm quantity" name="quantity[]" placeholder="Quantity" step=".01" required="required" autocomplete="off">
                            </td>
                            <td>
                                <select class="form-control input-sm units" name="unit[]">
                                    <option value="">Unit</option>
                                    @foreach($units as $unit)
                                    <option value="{{$unit->id}}">{{$unit->name}}({{ $unit->symbol }})</option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <input type="number" class="form-control input-sm total" name="total[]" placeholder="Total" value="@if(isset($orderDetail->total)){{ $orderDetail->total }}@endif" readonly="readonly" style="float:left; width:80%;">
                                <a href="javascript::void(1);" style="width: 10%;">
                                    <i class="remove-this btn btn-xs btn-danger icon fa fa-trash deletable" style="float: right; color: #fff;"></i>
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
                                <input type="text" class="form-control input-sm product" name="custom_items_name[]" value="" placeholder="Product" autocomplete="off">
                            </td>

                            <td>
                                <input type="text" class="form-control input-sm price" name="custom_items_price[]" placeholder="Price" value="@if(isset($orderDetail->price)){{ $orderDetail->price }}@endif" required="required" autocomplete="off">
                            </td>

                            <td>
                                <input type="number" class="form-control input-sm quantity" name="custom_items_qty[]" placeholder="Quantity" step=".01" required="required" autocomplete="off">
                            </td>
                            <td>
                                <select class="form-control input-sm" name="custom_unit[]">
                                    <option value="">Unit</option>
                                    @foreach($units as $unit)
                                    <option value="{{$unit->id}}">{{$unit->name}}({{ $unit->symbol }})</option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <input type="number" class="form-control input-sm total" name="custom_total[]" placeholder="Total" value="@if(isset($orderDetail->total)){{ $orderDetail->total }}@endif" readonly="readonly" style="float:left; width:80%;">
                                <a href="javascript::void(1);" style="width: 10%;">
                                    <i class="remove-this btn btn-xs btn-danger icon fa fa-trash deletable" style="float: right; color: #fff;"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12">
                <div class="box">
                    <div class="box-body">
                    <form method="POST" action="{{route('admin.invoice.edit',$invoice->id)}}">
                        {{ csrf_field() }}

                        <div class="">

                            <div class="col-md-12">
                               
                            </div>

                            <div class="hide_on_tablet">
                                <div class="clearfix"></div>
                                <div class="col-md-12" style="margin-top: 30px;">
                                     <div class="col-md-4">
                                    <label>Select Customer
                                        <a href="javascript::void()" onclick="openwindow()" style="color: #54a75b;">
                                    [+]</a>
                                </label>
                                    <select class="customer_id select2" name="customer_id" required="required" id='client_id'>
                                        <option class="form-control input-sm input input-lg" value="">Select Customer</option>
                                        @if(isset($clients))
                                        @foreach($clients as $key => $uk)
                                        <option value="{{ $uk->id }}" @if($invoice && $uk->id ==
                                            $invoice->client_id){{ 'selected="selected"' }}@endif>{{ '('.env('APP_CODE'). $uk->id.') '.$uk->name.' -'.$uk->organization }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    &nbsp;&nbsp; 
                                </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            {!! Form::label('terms', 'Payment Terms') !!}
                                            <div class="date">
                                                <!-- <div class="input-group-addon">
                                                    <i class="fa fa-file"></i>
                                                </div> -->
                                                  {!! Form::select('terms',[
                                                'custom' => 'Custom','net15'=>'Net 15', 'net30'=>'Net 30', 'net45'=>'Net 45','net60'=>'Net 60'],
                                                $invoice->terms, ['class' => 'form-control input-sm']) !!}
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                    </div>
                                   


                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>PAN Number:</label>
                                            <div class="date">
                                                <!-- <div class="input-group-addon">
                                                    <i class="fa fa-file"></i>
                                                </div> -->
                                                <input type="text" name="customer_pan" value="{{ old('customer_pan') ??  $invoice->customer_pan }}" class="form-control input-sm pull-right" id="pan_no" onKeyUp="if(this.value>999999999){this.value='999999999';}else if(this.value<0){this.value='0';}">
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                    </div>


                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Invoice Date:</label>

                                            <div class="date">
                                                <!-- <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div> -->
                                                <input type="text" class="form-control input-sm pull-right datepicker date-toggle-nep-eng"  name="bill_date" value="{{  $invoice->bill_date}}" id="bill_date" required="">
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                    </div>


                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Due Date:</label>

                                            <div class="date">
                                                <!-- <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div> -->
                                                <input type="text" class="form-control input-sm pull-right datepicker date-toggle-nep-eng" name="due_date" value="{{ $invoice->due_date }}" id="due_date">
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                    </div>

                                </div>


                                <div class="col-md-12">

                                    <div class="col-md-2 form-group" style="">
                                        <label for="comment">Person Name</label>
                                        <input type="text" name="name" id="name" class="form-control input-sm" value="{{ $invoice->name }}">
                                    </div>

                                    <div class="col-md-2 form-group" style="">
                                        <label for="position">Position</label>
                                        <input type="text" name="position" class="form-control input-sm" id="position" value="{{ $invoice->position }}">
                                    </div>

                                    <div class="col-md-2 form-group" style="">
                                        <label for="user_id">Salesperson</label>
                                        {!! Form::select('user_id', $users, $invoice->user_id , ['class' => 'form-control input-sm input-sm', 'id'=>'user_id']) !!}
                                    </div>
                                    <div class="col-md-2 form-group" style="">
                                        <label for="user_id">Location</label>
                                        {!! Form::select('from_stock_location', [''=>'Select']+$productlocation, $invoice->from_stock_location, ['class' => 'form-control input-sm label-default']) !!}
                                    </div>

                                    <div class="col-md-2 form-group" style="">
                                        <label for="user_id">Is renewal</label>
                                        {!! Form::select('is_renewal', ['0'=>'No','1'=>'Yes'], $invoice->is_renewal, ['class' => 'form-control input-sm']) !!}
                                    </div>

                                    <div class="col-md-2 form-group">
                                        <label >Outlets</label>
                                        <select name="outlet_id" class="form-control input-sm searchable" required="">
                                           
                                            @foreach($outlets as $key=>$out)
                                                <option value="{{ $out->id }}" @if( $out->id ==  $invoice->outlet_id) selected="" @endif>
                                                    {{$out->outlet_code}} ({{$out->name}})
                                                </option>
                                            @endforeach
                                            
                                        </select>
                                    </div>
                                </div>
                             <div class="col-md-12">

                                    <div class="col-md-2 form-group" style="">
                                        <label for="position">Invoice Number</label>
                                        <input type="text" readonly="readonly" name="position" class="form-control input-sm" id="position" value="{{ $invoice->bill_no }}">
                                    </div>

                                    <div class="col-md-2 form-group" style="">
                                        <label for="position">Fiscal Year</label>
                                        <input type="text" readonly="readonly" name="position" class="form-control input-sm" id="position" value="{{ $invoice->fiscal_year }}">
                                    </div>

                                    <div class="col-md-2 form-group" style="">
                                        <label for="position"><i class="fa fa-database"></i> CBMS Server Sync </label>
                                        <input type="text" readonly="readonly" name="position" class="form-control input-sm" id="position" value="{{ $invoice->sync_with_ird }}">
                                    </div>

                                </div>
                            </div>





                            <div class="clearfix"></div><br /><br />

                            <div class="col-md-12">
                                 <div class="btn-group pull-right">
                                    <a href="javascript::void(0)" class="btn btn-primary btn-sm" id="addMore" >
                                        <i class="fa fa-plus"></i> <span>Add Products Item</span>
                                    </a> &nbsp;
                                    <a href="javascript::void(0)" class="btn btn-success btn-sm" id="addCustomMore" >
                                        <i class="fa fa-plus"></i> <span>Add Custom Products Item</span>
                                    </a>
                                </div>
                            </div>


                            <hr />
                            <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr class="bg-info">
                                        <th width="10%;">S.N</th>
                                        <th width="20%;">Particulars *</th>
                                        <th width="15%;">Price *</th>
                                        <th width="15%;">Quantity *</th>
                                        <th width="15%;">Unit</th>
                                        <!-- <th>Tax(%) *</th>
                                            <th>Tax({{ env('APP_CURRENCY') }})</th> -->
                                        <th width="25%;">Total</th>
                                    </tr>
                                </thead>

                                <tbody id='multipleDiv'>
                                    <tr class="multipleDiv">

                                    </tr>
                                    @foreach($invoice_details as $key=>$details)
                                    @if($details->is_inventory)
                                    <tr>
                                         <td class="p_sn"></td>
                                        <td>
                                            <select class="form-control input-sm select2 product_id" name="product_id[]" required="required">
                                                <option value="">Select Product</option>
                                                @foreach($products as $key => $pk)
                                                <option value="{{ $pk->id }}" @if(isset($details->product_id) && $details->product_id == $pk->id) selected="selected"@endif>{{ $pk->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control input-sm price" name="price[]" placeholder="Price" value="@if(isset($details->price)){{ $details->price }}@endif" required="required">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control input-sm quantity" name="quantity[]" placeholder="Quantity" step=".01" required="required" value="{{ $details->quantity }}">
                                        </td>

                                        <td>
                                            <select class="form-control input-sm units" name="unit[]">
                                                <option value="">Unit</option>
                                                @foreach($units as $unit)
                                                <option value="{{$unit->id}}" @if($details->unit == $unit->id)selected @endif>{{$unit->name}}({{ $unit->symbol }})</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control input-sm total" name="total[]" placeholder="Total" value="@if(isset($details->total)){{ $details->total }}@endif" readonly="readonly" style="float:left; width:80%;">
                                            <a href="javascript::void(1);" style="width: 10%;">
                                                <i class="remove-this btn btn-xs btn-danger icon fa fa-trash deletable" style="float: right; color: #fff;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @else
                                    <tr>
                                         <td class="p_sn"></td>
                                        <td>
                                            <input type="text" class="form-control input-sm product" name="custom_items_name[]" value="{{ $details->description }}" placeholder="Product">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control input-sm price" name="custom_items_price[]" placeholder="Price" value="@if(isset($details->price)){{ $details->price }}@endif" required="required">
                                        </td>

                                        <td>
                                            <input type="number" class="form-control input-sm quantity" name="custom_items_qty[]" placeholder="Quantity" step=".01" value="{{  $details->quantity }}" required="required">
                                        </td>
                                        <td>
                                            <select class="form-control input-sm" name="custom_unit[]">
                                                <option value="">Unit</option>
                                                @foreach($units as $unit)
                                                <option value="{{$unit->id}}" @if($details->unit == $unit->id)selected @endif>{{$unit->name}}({{ $unit->symbol }})</option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td>
                                            <input type="number" class="form-control input-sm total" name="custom_total[]" placeholder="Total" value="@if(isset($details->total)){{ $details->total }}@endif" readonly="readonly" style="float:left; width:80%;">
                                            <a href="javascript::void(1);" style="width: 10%;">
                                                <i class="remove-this btn btn-xs btn-danger icon fa fa-trash deletable" style="float: right; color: #fff;"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                    
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <td colspan="5" style="text-align: right;">Amount</td>
                                        <td id="sub-total">{{ $invoice->total_amount }}</td>
                                        <td>&nbsp; <input type="hidden" name="subtotal" id="subtotal" value="{{ $invoice->total_amount }}"></td>
                                    </tr>

                                    <tr>
                                        <td colspan="5" style="text-align: right;">Order Discount (%)</td>
                                        <td><input type="number" min="0" name="discount_percent" id="discount_percent" value="{{ $invoice->discount_percent }}" onKeyUp="if(this.value>99){this.value='99';}else if(this.value<0){this.value='0';}"></td>
                                        <td>&nbsp;</td>
                                    </tr>

                                     <tr>
                                        <td colspan="5" style="text-align: right;">Order Discount Amount</td>
                                        <td><input type="number" min="0" name="discount_amount" id="discount_amount" value="{{ $invoice->discount_amount }}" ></td>
                                        <td>&nbsp;</td>
                                    </tr>

                                    <tr>
                                        <td colspan="5" style="text-align: right;">Taxable Amount</td>
                                        <td id="taxable-amount">{{ $invoice->taxable_amount }}</td>
                                        <td>&nbsp; <input type="hidden" name="taxable_amount" id="taxableamount" value="{{  $invoice->taxable_amount }}"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" style="text-align: right;">Tax Amount (13%)</td>
                                        <td id="taxable-tax">{{ $invoice->tax_amount }}</td>
                                        <td>&nbsp; <input type="hidden" name="taxable_tax" id="taxabletax" value="{{  $invoice->tax_amount }}"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" style="text-align: right; font-weight: bold;font-size: 16px">Total Amount ({{ env('APP_CURRENCY')}}) </td>
                                        <td style="font-size: 16px;font-weight: bold;" id="total">{{ $invoice->total_amount }}</td>
                                        <td>
                                            <input type="hidden" name="total_tax_amount" id="total_tax_amount" value="{{  $invoice->tax_amount }}">
                                            <input type="hidden" name="final_total" id="total_" value="{{  $invoice->total_amount }}">
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                            <br />


                        <div class="hide_on_tablet">
                            <div class="col-md-6 form-group" style="margin-top:5px;">
                                <label for="comment">Customer Notes</label>
                                <textarea class="form-control input-sm TextBox comment" name="comment">@if(isset($invoice->comment)){{ $invoice->comment }}@endif</textarea>
                            </div>

                            <div class="col-md-6 form-group" style="margin-top:5px;">
                                <label for="address">Address</label>
                                <textarea id="physical_address" class="form-control input-sm TextBox address" name="address">@if(isset($invoice->address)){{ $invoice->address }}@endif</textarea>
                            </div>
                        </div>


                        </div>
                        <div class="panel-footer footer">
                            <button type="submit" class="btn btn-social btn-foursquare">
                                <i class="fa fa-save"></i>Save Invoice
                            </button>

                            <a class="btn btn-social btn-foursquare" href="/admin/invoice1"> <i class="fa fa-times"></i> Cancel </a>
                        </div>
                    </form>
                </div>
            </div>

        </div><!-- /.box-body -->
    </div><!-- /.col -->

</div><!-- /.row -->
@endsection

@section('body_bottom')
<!-- form submit -->
@include('partials._body_bottom_submit_bug_edit_form_js')
@include('partials._date-toggle')
<script type="text/javascript">
    // $(function() {
    //     $('.datepicker').datetimepicker({
    //       //inline: true,
    //       format: 'YYYY-MM-DD',
    //       sideBySide: true,
    //       allowInputToggle: true
    //     });

    //   });

</script>

<script>
        function getSn(){

       $('#multipleDiv tr').each(function(index,val){

            if(index > 0){
                $(this).find('.p_sn').html(index);
            }

       });
    }

       const dateRange = {
    <?php $currentFiscalyear = FinanceHelper::cur_fisc_yr();?>
    minDate: `{{ $currentFiscalyear->start_date }}`,
    maxDate: `{{ $currentFiscalyear->end_date }}`
}
getSn();
$('.date-toggle-nep-eng').nepalidatetoggle();
    function isNumeric(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }

    $(document).on('change', '.product_id', function() {
        var parentDiv = $(this).parent().parent();
        if (this.value != 'NULL') {
            var _token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: "POST"
                , contentType: "application/json; charset=utf-8"
                , url: "/admin/products/GetProductDetailAjax/" + this.value + '?_token=' + _token
                , success: function(result) {
                    var obj = jQuery.parseJSON(result.data);
                    parentDiv.find('.price').val(obj.price);
                    parentDiv.find('.units').val("");
                    parentDiv.find('.units').val(result.units?.id);
                    if (isNumeric(parentDiv.find('.quantity').val()) && parentDiv.find('.quantity').val() != '') {
                        var total = parentDiv.find('.quantity').val() * obj.price;
                    } else {
                        var total = obj.price;
                    }

                    var tax = parentDiv.find('.tax_rate').val();
                    if (isNumeric(tax) && tax != 0 && (total != 0 || total != '')) {
                        tax_amount = total * Number(tax) / 100;
                        parentDiv.find('.tax_amount').val(tax_amount);
                        total = total + tax_amount;
                    } else
                        parentDiv.find('.tax_amount').val('0');

                    parentDiv.find('.total').val(total);
                    calcTotal();
                }
            });
        } else {
            parentDiv.find('.price').val('');
            parentDiv.find('.total').val('');
            parentDiv.find('.tax_amount').val('');
            calcTotal();
        }
    });

    $(document).on('change', '.customer_id', function() {
        if (this.value != '') {
            $(".quantity").each(function(index) {
                var parentDiv = $(this).parent().parent();
                if (isNumeric($(this).val()) && $(this).val() != '')
                    var total = $(this).val() * parentDiv.find('.price').val();
                else
                    var total = parentDiv.find('.price').val();

                var tax = parentDiv.find('.tax_rate').val();
                if (isNumeric(tax) && tax != 0 && (total != 0 || total != '')) {
                    tax_amount = total * Number(tax) / 100;
                    parentDiv.find('.tax_amount').val(tax_amount);
                    total = total + tax_amount;
                } else
                    parentDiv.find('.tax_amount').val('0');

                if (isNumeric(total) && total != '') {
                    parentDiv.find('.total').val(total);
                    calcTotal();
                }
                //console.log( index + ": " + $(this).text() );
            });
        } else {
            $('.total').val('0');
            $('.tax_amount').val('0');
            calcTotal();
        }

        let cust_id = $(this).val();
        $.get('/admin/getpanno/' + cust_id, function(data, status) {
            $('#pan_no').val(data.pan_no);
            $('#physical_address').val(data.physical_address);
        });
    });

    $(document).on('change', '.quantity', function() {
        var parentDiv = $(this).parent().parent();
        if (isNumeric(this.value) && this.value != '') {
            if (isNumeric(parentDiv.find('.quantity').val()) && parentDiv.find('.quantity').val() != '') {
                var total = parentDiv.find('.price').val() * this.value;
            } else
                var total = '';
        } else
            var total = '';

        var tax = parentDiv.find('.tax_rate').val();
        if (isNumeric(tax) && tax != 0 && (total != 0 || total != '')) {
            tax_amount = total * Number(tax) / 100;
            parentDiv.find('.tax_amount').val(tax_amount);
            total = total + tax_amount;
        } else
            parentDiv.find('.tax_amount').val('0');

        parentDiv.find('.total').val(total);
        calcTotal();
    });

    $(document).on('change', '.price', function() {
        var parentDiv = $(this).parent().parent();
        if (isNumeric(this.value) && this.value != '') {
            if (isNumeric(parentDiv.find('.quantity').val()) && parentDiv.find('.quantity').val() != '') {
                var total = parentDiv.find('.quantity').val() * this.value;
            } else
                var total = '';
        } else
            var total = '';

        var tax = parentDiv.find('.tax_rate').val();
        if (isNumeric(tax) && tax != 0 && (total != 0 || total != '')) {
            tax_amount = total * Number(tax) / 100;
            parentDiv.find('.tax_amount').val(tax_amount);
            total = total + tax_amount;
        } else
            parentDiv.find('.tax_amount').val('0');

        parentDiv.find('.total').val(total);
        calcTotal();
    });

    $(document).on('change', '.tax_rate', function() {
        var parentDiv = $(this).parent().parent();

        if (isNumeric(parentDiv.find('.quantity').val()) && parentDiv.find('.quantity').val() != '') {
            var total = parentDiv.find('.price').val() * Number(parentDiv.find('.quantity').val());
        } else
            var total = '';

        var tax = $(this).val();
        if (isNumeric(tax) && tax != 0 && (total != 0 || total != '')) {
            tax_amount = total * Number(tax) / 100;
            parentDiv.find('.tax_amount').val(tax_amount);
            total = total + tax_amount;
        } else
            parentDiv.find('.tax_amount').val('0');

        parentDiv.find('.total').val(total);
        calcTotal();
    });


    $("#addMore").on("click", function() {
        //$($('#orderFields').html()).insertBefore(".multipleDiv");
        $(".multipleDiv").after($('#orderFields #more-tr').html());
        $(".multipleDiv").next('tr').find('.product_id').select2({
            width: '100%'
        });
         $(".multipleDiv").next('tr').find('.quantity').val('1');
    getSn();
    });
    $("#addCustomMore").on("click", function() {
        //$($('#orderFields').html()).insertBefore(".multipleDiv");
        $(".multipleDiv").after($('#CustomOrderFields #more-custom-tr').html());
        $(".multipleDiv").next('tr').find('.quantity').val('1');

        getSn();
    });

    $(document).on('click', '.remove-this', function() {
        $(this).parent().parent().parent().remove();
        calcTotal();

getSn();
    });

    function calcTotal() {
        //alert('hi');
        var subTotal = 0;
        var taxableAmount = 0;

        //var tax = Number($('#tax').val().replace('%', ''));
        var total = 0;
        var tax_amount = 0;
        var taxableTax = 0;
        $(".total").each(function(index) {
            if (isNumeric($(this).val()))
                subTotal = Number(subTotal) + Number($(this).val());
        });
        $(".tax_amount").each(function(index) {
            if (isNumeric($(this).val()))
                tax_amount = Number(tax_amount) + Number($(this).val());
        });
        $('#sub-total').html(subTotal);
        $('#subtotal').val(subTotal);

        $('#taxable-amount').html(subTotal);
        $('#taxableamount').val(subTotal);

        var discount_amount = $('#discount_amount').val();

        if (isNumeric(discount_amount) && discount_amount != 0) {

            taxableAmount = subTotal - (Number(discount_amount));

        } else {
            total = subTotal;
            taxableAmount = subTotal;
        }


        total = taxableAmount + Number(13 / 100 * taxableAmount);

        taxableTax = Number(13 / 100 * taxableAmount);

        $('#taxableamount').val(taxableAmount);
        $('#taxable-amount').html(taxableAmount);

        $('#total_tax_amount').val(tax_amount);

        $('#taxabletax').val(taxableTax);
        $('#taxable-tax').html(taxableTax);

        $('#total').html(total);
        $('#total_').val(total);
    }

    $(document).on('keyup', '#discount_amount', function() {
        calcTotal();
    });

</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.customer_id').select2();
        $('.searchable').select2();
        $('form .product_id').select2({
            width: '100%'
        });
    });

</script>

<script type="text/javascript">
    $(function() {
        $('.datepicker').datetimepicker({
            //inline: true,
            format: 'YYYY-MM-DD'
            , sideBySide: true
            , allowInputToggle: true,
                minDate: dateRange.minDate,
            maxDate: dateRange.maxDate,
        });

    });

    function openwindow() {
        var win = window.open('/admin/clients/modals?relation_type=customer', '_blank', 'toolbar=yes, scrollbars=yes, resizable=yes, top=500,left=500,width=600, height=650');
        //     var timer = setInterval(function() {
        //     if (win.closed) {
        //         clearInterval(timer);
        //         console.log("DONE");
        //     }
        // }, 500);
    }

    function HandlePopupResult(result) {
        if (result) {
            let clients = result.clients;
            var option = '';
            for (let c of clients) {
                option = option + `<option value='${c.id}'>${c.name}</option>`;
            }
            $('#client_id').html(option);
            setTimeout(function() {
                $('#client_id').val(result.lastcreated);
                $("#ajax_status").after("<span style='color:green;' id='status_update'>Client sucessfully created</span>");
                $('#status_update').delay(3000).fadeOut('slow');
            }, 500);
        } else {
            $("#ajax_status").after("<span style='color:red;' id='status_update'>failed to create clients</span>");
            $('#status_update').delay(3000).fadeOut('slow');
        }
    }


    $('#discount_percent').on('change keyup',function(){

        let val = $(this).val();
        
        if(val.trim() == ''){
            $('#discount_amount').attr('readonly',false);
        }else{
            $('#discount_amount').attr('readonly',true);
        }
        
        let subtotal = Number($('#sub-total').text());

        let discountAmount = (val / 100) * subtotal;
        
        $('#discount_amount').val(discountAmount);

        calcTotal();
    });

</script>
@endsection
