@extends('layouts.master')

@section('head_extra')

<?php
 function CategoryTree($parent_id=null,$sub_mark='',$ledgers_data){

    $groups= \App\Models\COAgroups::orderBy('code', 'asc')->where('parent_id',$parent_id)->get();

    if(count($groups)>0){
        foreach($groups as $group){
            echo '<optgroup  label="'.$sub_mark.'['.$group->code.']'.' '.$group->name.'"></optgroup>';

            $ledgers= \App\Models\COALedgers::orderBy('code', 'asc')->where('group_id',$group->id)->get();
              if(count($ledgers>0)){
                $submark= $sub_mark;
                $sub_mark = $sub_mark."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

                 foreach($ledgers as $ledger){

                 if($ledgers_data->id == $ledger->id){
                  echo '<option selected value="'.$ledger->id.'"><strong>'.$sub_mark.'['.$ledger->code.']'.' '.$ledger->name.'</strong></option>';
                  }else{

                  echo '<option value="'.$ledger->id.'"><strong>'.$sub_mark.'['.$ledger->code.']'.' '.$ledger->name.'</strong></option>';
               }

               }
               $sub_mark=$submark;

            }
            CategoryTree($group->id,$sub_mark."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$ledgers_data);
        }
    }
 }

 ?>

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
            height:100%;
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


    </style>
@endsection

@section('content')


<link href="/bower_components/admin-lte/select2/css/select2.min.css" rel="stylesheet" />
<script src="/bower_components/admin-lte/select2/js/select2.min.js"></script>

<link href="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.css") }}" rel="stylesheet" type="text/css" />

<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
            <h1>
                 {{$page_title ?? "Page Title"}}
                <small> {{$page_description ?? "Page Description"}}
                </small>
            </h1>
            {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}
        </section>

    <div class='row'>
        <div class='col-md-12 box'>
            <div class="box-body">
                <div id="orderFields" style="display: none;">
                    <table class="table">
                        <tbody id="more-tr">
                        <tr>
                            <td>
                                <select class="form-control select2 product_id" name="product_id[]" required="required">

                                    <option value="">Select Product</option>
                                    @foreach($products as $key => $pk)
                                        <option value="{{ $pk->id }}"@if(isset($orderDetail->product_id) && $orderDetail->product_id == $pk->id) selected="selected"@endif>{{ $pk->name }}</option>
                                    @endforeach

                                </select>
                            </td>
                            <td>
                                <select name='units[]' class="form-control">
                                    <option value="">Select Units</option>
                                    @foreach($prod_unit as $pu)
                                        <option value="{{ $pu->id }}">{{ $pu->name }}({{ $pu->symbol }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" class="form-control purchase_quantity input-sm" name="purchase_quantity[]" placeholder="Quantity" min="1" value="" required="required" step="any">
                            </td>


                            <td>
                                <input type="number" class="form-control quantity input-sm" name="return_quantity[]" placeholder="Received Quantity" min="1" value="" required="required" step="any">
                            </td>

                            <td>
                                <input type="number" class="form-control purchase_price input-sm" name="purchase_price[]" placeholder="Unit Price" min="1" value="" required="required" step="any">
                            </td>
                            <td>
                                <input type="text" class="form-control line_subtotal input-sm" placeholder="Sub Total" readonly>

                            </td>
                            <td >
                                <input type="number" name="discount_percent[]" class="form-control discount_percent_line input-sm " placeholder="Discount( % )" step="any" min="0" max="99">
                            </td>
                            <td>
                                <input type="text" name="dis_amount[]" class="form-control discount_amount_line input-sm" placeholder="Discount" step="any">
                            </td>
                            <td class="col-sm-1">
                                <select class="form-control input-sm tax_rate" name="tax_rate[]">
                                    <option value="0">Exempt(0)</option>
                                    <option value="13">VAT(13)</option>
                                </select>
                                <input type="hidden" class="form-control tax_amount_line input-sm" name="tax_amount[]" placeholder="Tax Amount" min="1" value="" step="any">

                            </td>
                            <td>
                                <input type="number" class="form-control total input-sm" name="return_total[]" readonly placeholder="Total" value="" step="any">
                            </td>

                                                        <td>
                                                            <input type="text" class="form-control reason input-sm" name="reason[]" placeholder="Remarks">

                                                        </td>
                            <td>
                                <a href="javascript::void(1);">
                                    <i class="remove-this btn btn-xs btn-danger icon fa fa-trash deletable" style="color: #fff;"></i>
                                </a>
                            </td>
                        </tr>
{{--                            <tr>--}}
{{--                                <td>--}}
{{--                                  <select class="form-control select2 product_id" name="product_id[]" required="required">--}}

{{--                                      <option value="">Select Product</option>--}}
{{--                                      @foreach($products as $key => $pk)--}}
{{--                                          <option value="{{ $pk->id }}"@if(isset($orderDetail->product_id) && $orderDetail->product_id == $pk->id) selected="selected"@endif>{{ $pk->name }}</option>--}}
{{--                                      @endforeach--}}

{{--                                  </select>--}}
{{--                                </td>--}}
{{--                                <td>--}}
{{--                                    <select name='units[]' class="form-control">--}}
{{--                                        <option value="">Select Units</option>--}}
{{--                                        @foreach($prod_unit as $pu)--}}
{{--                                            <option value="{{ $pu->id }}">{{ $pu->name }}({{ $pu->symbol }})</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                </td>--}}
{{--                                <td>--}}
{{--                                    <input type="number" class="form-control quantity" name="quantity[]" placeholder="Quantity" min="1" value="1" required="required" step ='any'>--}}
{{--                                </td>--}}
{{--                                <td>--}}
{{--                                    <input type="number" class="form-control received_quantity" name="received_quantity[]" placeholder="Quantity" min="1" value="1" required="required" step ='any'>--}}
{{--                                </td>--}}
{{--                                <td>--}}
{{--                                    <input type="text" class="form-control price" name="price[]" placeholder="Fare" value="@if(isset($orderDetail->price)){{ $orderDetail->price }}@endif" required="required">--}}
{{--                                </td>--}}




{{--                                <td>--}}
{{--                                    <input type="number" class="form-control total" name="total[]" placeholder="Total" value="@if(isset($orderDetail->total)){{ $orderDetail->total }}@endif" readonly="readonly" style="float:left; width:80%;" step ='any'>--}}
{{--                                    <a href="javascript::void(1);" style="width: 10%;">--}}
{{--                                        <i class="remove-this btn btn-xs btn-danger icon fa fa-trash deletable" style="float: right; color: #fff;"></i>--}}
{{--                                    </a>--}}
{{--                                </td>--}}
{{--                            </tr>--}}
                        </tbody>
                    </table>
                </div>

                <div id="CustomOrderFields" style="display: none;">
                    <table class="table">
                        <tbody id="more-custom-tr">
                            <tr>
                                <td>
                                  <input type="text" class="form-control product" name="custom_items_name[]" value="" placeholder="Product">
                                </td>

                                <td>
                                    <input type="text" class="form-control price" name="custom_items_price[]" placeholder="Price" value="@if(isset($orderDetail->price)){{ $orderDetail->price }}@endif" required="required">
                                </td>

                                <td>
                                    <input type="number" class="form-control quantity" name="custom_items_qty[]" placeholder="Quantity" min="1" value="1" required="required" step ='any'>
                                </td>
                                <td>
                                    <select name='custome_unit[]' class="form-control">
                                      <option value="">Select Units</option>
                                      @foreach($prod_unit as $pu)
                                        <option value="{{ $pu->id }}">{{ $pu->name }}({{ $pu->symbol }})</option>
                                      @endforeach
                                    </select>
                                </td>

                                <td>
                                    <input type="number" class="form-control total" name="custom_total[]" placeholder="Total" value="@if(isset($orderDetail->total)){{ $orderDetail->total }}@endif" readonly="readonly" style="float:left; width:80%;" step ='any'>
                                    <a href="javascript::void(1);" style="width: 10%;">
                                        <i class="remove-this btn btn-xs btn-danger icon fa fa-trash deletable" style="float: right; color: #fff;"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12">

                        <form method="POST" action="/admin/grn">
                            {{ csrf_field() }}



                             <div class="row">

                                <div class="col-md-12">

                                     <div class="col-md-3">
                                        <label>PO / Purchase bill ID</label>
                                          <input type="text" class="form-control pull-right " name="purchase_bill_no" value="" id="purchase_bill_no" required="">
                                     </div>

                                      <div class="col-md-3">
                                        <label>Supplier Name:</label>
                                          <input type="text" class="form-control pull-right " name="client_name" value="" id="customer_name" readonly>
                                          <input type="hidden" name="client_id" id="client_id">
                                     </div>

                                     <div class="col-md-3">
                                        <label>GRN Date:</label>
                                          <input type="text" class="form-control pull-right datepicker" name="return_date" value="{{\Carbon\Carbon::now()->toDateString()}}" id="return_date" required="" readonly>
                                     </div>

                                     <div class="col-md-3">
                                        <label>Purchase Order Date:</label>
                                        <input type="text" class="form-control pull-right datepicker" name="purchase_order_date" value="" id="purchase_order_date" readonly>
                                     </div>

                                </div>

                                 <div class="col-md-12">

                                    <div class="col-md-3 form-group" style="">
                                        <label for="user_id">Purchase Owner</label>
                                        {!! Form::select('user_id',  $users, \Auth::user()->id, ['class' => 'form-control input-sm', 'id'=>'user_id']) !!}
                                     </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                         <label>Status:</label>
                                          <select type="text" class="form-control pull-right " name="status" id="status">
                                            <option value="parked">Parked</option>
                                            <option value="completed">Completed</option>
                                          </select>
                                        </div>
                                    <!-- /.input group -->
                                    </div>

                                    <div class="col-md-3">
                                          <label>PAN NO:</label>
                                          <input type="text" class="form-control pull-right " name="pan_no" value="{{ old('pan_no')}}" id="pan_no" readonly>
                                    </div>
                                     <div class="col-md-3">
                                         <div class="form-group">
                                             <label>Is renewal:</label>
                                             <select type="text" class="form-control pull-right " name="is_renewal" id="is_renewal">
                                                 <option value="0">No</option>
                                                 <option value="1">Yes</option>
                                             </select>
                                         </div>
                                         <!-- /.input group -->
                                     </div>
{{--                                      <div class="col-md-3">--}}
{{--                                          <label>Vat:</label>--}}
{{--                                            <select type="text" class="form-control pull-right " name="vat_type" id="vat_type">--}}
{{--                                            <option value="yes">Yes(13%)</option>--}}
{{--                                              <option value="no">No</option>--}}
{{--                                            </select>--}}
{{--                                    </div>--}}


                                 </div>
                                <div class="row">
                                  <div class="col-md-12" style="margin-left: 15px">





                                    <div class="col-md-3">
                                        <div class="form-group">
                                         <label>Department:</label>
                                           {!! Form::select('department_id', [''=>'Select']+$departments, null, ['class' => 'form-control label-default','id'=>'department_id']) !!}
                                        </div>
                                    <!-- /.input group -->
                                    </div>
                                      <div class="col-md-3">
                                          <div class="form-group">
                                              <label>Location:</label>
                                              {!! Form::select('into_stock_location', [''=>'Select']+$productlocation, null, ['class' => 'form-control label-default','id'=>'location_id']) !!}
                                          </div>
                                          <!-- /.input group -->
                                      </div>
{{--                                      <div class="col-md-3">--}}
{{--                                        <div class="form-group">--}}
{{--                                         <label>Store:</label>--}}
{{--                                           {!! Form::select('store_id', [''=>'Select']+$stores, null, ['class' => 'form-control label-default','id'=>'location_id']) !!}--}}
{{--                                        </div>--}}
{{--                                    <!-- /.input group -->--}}
{{--                                    </div>--}}
                                  </div>
                                </div>




<hr/>
                                <table class="table table-striped">
                                    <thead>
                                        <tr class="bg-maroon">
                                            <th style="width: 15%;">Item </th>
                                            <th style="width: 9%;">Unit</th>
                                            <th style="width: 8%;">Ord Qty</th>
                                            <th style="width: 8%;">Rec Qty</th>
                                            <th style="width: 9%;">Ord Price</th>
                                            <th style="width: 9%;">Rec Price</th>
{{--                                            <th style="width: 9%;">Sub Total</th>--}}
{{--                                            <th style="width: 8%;">Dis %</th>--}}
                                            <th style="width: 9%;">Dis Amt</th>
                                            <th style="width: 5%;">Tax</th>
                                            <th style="width: 11%;">Line Total</th>
                                            <th style="width: 10%;">Expiry</th>
                                            <th style="width: 10%;">Remark</th>
                                            <th style="width: 2%;"></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr class="multipleDiv">

                                        </tr>
                                    </tbody>

                                    <tfoot>

                                        <tr>
                                            <td colspan="7" style="text-align: right;">Amount</td>
                                            <td id="sub-total">0.00</td>
                                            <td>&nbsp; <input type="hidden" name="subtotal" id="subtotal" value="0"></td>
                                        </tr>
{{--                                        <tr>--}}
{{--                                            <td colspan="7" style="text-align: right;">Order Discount (%)</td>--}}
{{--                                            <td><input type="number" min="0" name="discount_percent" id="discount_amount" value="" onKeyUp="if(this.value>99){this.value='99';}else if(this.value<0){this.value='0';}" step ='any'></td>--}}
{{--                                            <td>&nbsp;</td>--}}
{{--                                        </tr>--}}

                                        <tr>
                                            <td colspan="7" style="text-align: right;">Taxable Amount</td>
                                            <td id="taxable-amount">0.00</td>
                                            <td>&nbsp; <input type="hidden" name="taxable_amount" id="taxableamount" value="0"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" style="text-align: right;">Tax Amount</td>
                                            <td id="taxable-tax">0.00</td>
                                            <td>&nbsp; <input type="hidden" name="taxable_tax" id="taxabletax" value="0"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" style="text-align: right;">Tax Free Amt</td>
                                            <td id="non-taxable-amount">0.00</td>
                                            <td>&nbsp; <input type="hidden" name="non_taxable_amount" id="nontaxableamount" value="0"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" style="text-align: right; font-weight: bold;">Total Amount</td>
                                            <td id="total">0.00</td>
                                            <td>
                                                <input type="hidden" name="total_tax_amount" id="total_tax_amount" value="0">
                                                <input type="hidden" name="final_total" id="total_" value="0">
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>

                                <br/>



                                <div class="col-md-6 form-group" style="margin-top:5px;">
                                    <label for="comment">Comment</label>
                                    <textarea class="form-control TextBox comment" name="comments" id="comments">@if(isset($order->comments)){{ $order->comments }}@endif</textarea>
                                </div>


                            </div>
                            <div class="panel-footer">
                                <button type="submit" class="btn btn-social btn-foursquare" id='btnSubmit'>
                                    <i class="fa fa-save"></i>Save GRN
                                </button>
                                <a href="/admin/grn" class="btn btn-default">Close</a>
                            </div>
                        </form>
                    </div>

            </div><!-- /.box-body -->
        </div><!-- /.col -->

    </div><!-- /.row -->
@endsection

@section('body_bottom')
    <!-- form submit -->
    @include('partials._body_bottom_submit_bug_edit_form_js')

    <script type="text/javascript">

    // $(function() {
    //     $('.datepicker').datetimepicker({
    //       //inline: true,
    //       format: 'YYYY-MM-DD',
    //       sideBySide: true,
    //       allowInputToggle: true
    //     });
    //
    //   });
  </script>

<script>

function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

$(document).on('change', '.product_id', function() {
    var parentDiv = $(this).parent().parent();
    if(this.value != 'NULL')
    {
        var _token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
              type: "POST",
              contentType: "application/json; charset=utf-8",
              url: "/admin/products/GetProductDetailAjax/"+this.value+'?_token='+_token,
              success: function (result) {
                var obj = jQuery.parseJSON(result.data);
                parentDiv.find('.price').val(obj.price);

                if(isNumeric(parentDiv.find('.quantity').val()) && parentDiv.find('.quantity').val() != '')
                {
                    var total = parentDiv.find('.quantity').val() * obj.price;
                }
                else
                {
                    var total = obj.price;
                }

                var tax = parentDiv.find('.tax_rate').val();
                if(isNumeric(tax) && tax != 0 && (total != 0 || total != ''))
                {
                    tax_amount = total * Number(tax) / 100;
                    parentDiv.find('.tax_amount').val(tax_amount);
                    total = total + tax_amount;
                }
                else
                    parentDiv.find('.tax_amount').val('0');

                parentDiv.find('.total').val(total);
                calcTotal();
              }
         });
    }
    else
    {
        parentDiv.find('.price').val('');
        parentDiv.find('.total').val('');
        parentDiv.find('.tax_amount').val('');
        calcTotal();
    }
});

$(document).on('change', '.customer_id', function() {
    if(this.value != '')
    {
        $(".quantity").each(function(index) {
            var parentDiv = $(this).parent().parent();
            if(isNumeric($(this).val()) && $(this).val() != '')
                var total = $(this).val() * parentDiv.find('.price').val();
            else
                var total = parentDiv.find('.price').val();

            var tax = parentDiv.find('.tax_rate').val();
            if(isNumeric(tax) && tax != 0 && (total != 0 || total != ''))
            {
                tax_amount = total * Number(tax) / 100;
                parentDiv.find('.tax_amount').val(tax_amount);
                total = total + tax_amount;
            }
            else
                parentDiv.find('.tax_amount').val('0');

            if(isNumeric(total) && total != '')
            {
                parentDiv.find('.total').val(total);
                calcTotal();
            }
            //console.log( index + ": " + $(this).text() );
        });
    }
    else
    {
        $('.total').val('0');
        $('.tax_amount').val('0');
        calcTotal();
    }

    let supp_id = $(this).val();

    $.get('/admin/getpanno/'+supp_id,function(data,status){
          $('#pan_no').val(data.pan_no);


    });
});

// $(document).on('change', '.quantity', function() {
//     var parentDiv = $(this).parent().parent();
//     if(isNumeric(this.value) && this.value != '')
//     {
//         if(isNumeric(parentDiv.find('.quantity').val()) && parentDiv.find('.quantity').val() != '')
//         {
//             var total = parentDiv.find('.price').val() * this.value;
//         }
//         else
//             var total = '';
//     }
//     else
//         var total = '';
//
//     var tax = parentDiv.find('.tax_rate').val();
//     if(isNumeric(tax) && tax != 0 && (total != 0 || total != ''))
//     {
//         tax_amount = total * Number(tax) / 100;
//         parentDiv.find('.tax_amount').val(tax_amount);
//         total = total + tax_amount;
//     }
//     else
//         parentDiv.find('.tax_amount').val('0');
//
//     parentDiv.find('.total').val(total);
//     calcTotal();
// });
$(document).on('input', '.quantity,.price', function() {

    var parentDiv = $(this).parent().parent();

    // if(isNumeric(this.value) && this.value != ''){

    var invoice_price = parentDiv.find('.price').val();
    var return_quantity = parentDiv.find('.quantity').val();
    // if(parseFloat(return_quantity) > parseFloat(invoice_qty)){
    //     parentDiv.find('.quantity').val(parentDiv.find('.purchase_quantity').val());
    //
    //     alert("Received Quantity Cannot Be Greater Than Ordered Quantity");
    //
    // }
    // parentDiv.find('.discount_percent_line').trigger('input')

    var total = '';
    var quantity = Number(parentDiv.find('.quantity').val());
    var price = Number( parentDiv.find('.price').val());
    var tot = quantity * price;
    var dis_per = Number(parentDiv.find('.discount_percent_line').val())*tot/100;
    parentDiv.find('.discount_amount_line').val(dis_per.toFixed(2));
    // parentDiv.find('.line_subtotal').val(tot.toFixed(2));

    var dis_amt=parentDiv.find('.discount_amount_line').val()
    if (isNumeric(parentDiv.find('.price').val()) && parentDiv.find('.price').val() != '') {
        if (isNumeric(parentDiv.find('.quantity').val()) && parentDiv.find('.quantity').val() != '') {
            total = Number(parentDiv.find('.price').val()) * Number(parentDiv.find('.quantity').val());
            total=total-dis_amt
        } else
            total = '';
    } else
        total = '';


    var tax_amount=0;
    var tax = parentDiv.find('.tax_rate').val();
    if ( total != '') {
        tax_amount = total * Number(tax) / 100;
        total = total + tax_amount;
    }

    parentDiv.find('.tax_amount_line').val(tax_amount);

    parentDiv.find('.total').val(total.toFixed(2));
    // adjustTax($(this));
    calcTotal();
    // }
    // }

});
function calcTotal() {

    var subTotal = 0;
    var taxableAmount = 0;
    var nontaxableAmount = 0;
    var total = 0;
    var tax_amount = 0;
    var discount_amount=0;
    //var tax = Number($('#tax').val().replace('%', ''));
    $(".total").each(function(index) {
        if (isNumeric($(this).val())&&$(this).val()!=''){
            var tax_rate=$(this).parent().parent().find('.tax_rate').val();
            tax_amount = Number(tax_amount) + Number($(this).parent().parent().find('.tax_amount_line').val());
            var amt_before_disc=Number($(this).parent().parent().find('.quantity').val())*Number($(this).parent().parent().find('.price').val())
            var discount=Number($(this).parent().parent().find('.discount_amount_line').val())
            discount_amount+=discount
            subTotal+=amt_before_disc
            if(tax_rate==0)
                nontaxableAmount=nontaxableAmount+(amt_before_disc-discount)
            else
            taxableAmount=taxableAmount+(amt_before_disc-discount)

        }
    });


    // var taxableTax = 0;
    // $(".purchase_price").each(function(index) {
    //     if (isNumeric($(this).val())&&$(this).val()!='')
    //         subTotal = Number(subTotal) + Number($(this).val())*Number($(this).parent().parent().find('.quantity').val());
    // });


    $('#taxable-amount').html(taxableAmount.toFixed(2));
    $('#taxableamount').val(taxableAmount.toFixed(2));

    $('#non-taxable-amount').html(nontaxableAmount.toFixed(2));
    $('#nontaxableamount').val(nontaxableAmount.toFixed(2));

    $('#taxable-tax').html(tax_amount.toFixed(2));
    $('#taxabletax').val(tax_amount.toFixed(2));

    $('#discount').html(discount_amount.toFixed(2));
    $('#discount_amount').val(discount_amount.toFixed(2));


    total = Number( tax_amount) + Number( taxableAmount )+ Number( nontaxableAmount )
    $('#sub-total').html(total.toFixed(2));
    $('#subtotal').val(total.toFixed(2));
    $('#total').html(total.toFixed(2));
    $('#total_').val(total.toFixed(2));


}
// $(document).on('change', '.price', function() {
//     var parentDiv = $(this).parent().parent();
//     if(isNumeric(this.value) && this.value != '')
//     {
//         if(isNumeric(parentDiv.find('.quantity').val()) && parentDiv.find('.quantity').val() != '')
//         {
//             var total = parentDiv.find('.quantity').val() * this.value;
//         }
//         else
//             var total = '';
//     }
//     else
//         var total = '';
//
//     var tax = parentDiv.find('.tax_rate').val();
//     if(isNumeric(tax) && tax != 0 && (total != 0 || total != ''))
//     {
//         tax_amount = total * Number(tax) / 100;
//         parentDiv.find('.tax_amount').val(tax_amount);
//         total = total + tax_amount;
//     }
//     else
//         parentDiv.find('.tax_amount').val('0');
//
//     parentDiv.find('.total').val(total);
//     calcTotal();
// });
//
// $(document).on('change', '.tax_rate', function() {
//     var parentDiv = $(this).parent().parent();
//
//     if(isNumeric(parentDiv.find('.quantity').val()) && parentDiv.find('.quantity').val() != '')
//     {
//         var total = parentDiv.find('.price').val() * Number(parentDiv.find('.quantity').val());
//     }
//     else
//         var total = '';
//
//     var tax = $(this).val();
//     if(isNumeric(tax) && tax != 0 && (total != 0 || total != ''))
//     {
//         tax_amount = total * Number(tax) / 100;
//         parentDiv.find('.tax_amount').val(tax_amount);
//         total = total + tax_amount;
//     }
//     else
//         parentDiv.find('.tax_amount').val('0');
//
//     parentDiv.find('.total').val(total);
//     calcTotal();
// });
//
// /*$('#discount').on('change', function() {
//     if(isNumeric(this.value) && this.value != '')
//     {
//         if(isNumeric($('#sub-total').val()) && $('#sub-total').val() != '')
//             parentDiv.find('.total').val($('#sub-total').val() - this.value).trigger('change');
//     }
// });
//
// $("#sub-total").bind("change", function() {
//     if(isNumeric($('#discount').val()) && $('#discount').val() != '')
//         parentDiv.find('.total').val($('#sub-total').val() - $('#discount').val());
//     else
//         parentDiv.find('.total').val($('#sub-total').val());
// });*/

$("#addMore").on("click", function () {
     //$($('#orderFields').html()).insertBefore(".multipleDiv");
     $(".multipleDiv").after($('#orderFields #more-tr').html());
     $(".multipleDiv").next('tr').find('select').select2({ width: '100%' });
});
$("#addCustomMore").on("click", function () {
     //$($('#orderFields').html()).insertBefore(".multipleDiv");
     $(".multipleDiv").after($('#CustomOrderFields #more-custom-tr').html());
});

$(document).on('click', '.remove-this', function () {
    $(this).parent().parent().parent().remove();
    calcTotal();
});

$(document).on('change', '#vat_type', function(){
    calcTotal();
});

// function calcTotal()
// {
//     //alert('hi');
//     var subTotal = 0;
//     var taxableAmount =0;
//
//     //var tax = Number($('#tax').val().replace('%', ''));
//     var total = 0;
//     var tax_amount = 0;
//     var taxableTax = 0;
//     $(".total").each(function(index) {
//         if(isNumeric($(this).val()))
//             subTotal = Number(subTotal) + Number($(this).val());
//     });
//     $(".tax_amount").each(function(index) {
//         if(isNumeric($(this).val()))
//             tax_amount = Number(tax_amount) + Number($(this).val());
//     });
//     $('#sub-total').html(subTotal);
//     $('#subtotal').val(subTotal);
//
//     $('#taxable-amount').html(subTotal);
//     $('#taxableamount').val(subTotal);
//
//     var discount_amount = $('#discount_amount').val();
//
//     var vat_type = $('#vat_type').val();
//
//     console.log(vat_type);
//
//     if(isNumeric(discount_amount) && discount_amount != 0)
//     {
//
//         taxableAmount = subTotal - (Number(discount_amount)/100 * subTotal );
//
//     }
//     else
//     {
//         total = subTotal;
//         taxableAmount = subTotal;
//     }
//
//     if(vat_type == 'no' || vat_type == '')
//     {
//
//        total = taxableAmount;
//        taxableTax =  0;
//
//     }
//     else
//     {
//
//     total = taxableAmount + Number(13/100 * taxableAmount );
//     taxableTax =  Number(13/100 * taxableAmount );
//     }
//
//
//     $('#taxableamount').val(taxableAmount);
//     $('#taxable-amount').html(taxableAmount);
//
//     $('#total_tax_amount').val(tax_amount);
//
//     $('#taxabletax').val(taxableTax);
//     $('#taxable-tax').html(taxableTax);
//
//     $('#total').html(total);
//     $('#total_').val(total);
// }

$(document).on('keyup', '#discount_amount', function () {
    calcTotal();
});
</script>

<script type="text/javascript">
         $(document).ready(function() {
    $('.customer_id').select2();
});
</script>

<script type="text/javascript">
     $(function() {
        $('.datepicker').datetimepicker({
          //inline: true,
          format: 'YYYY-MM-DD',
          sideBySide: true,
          allowInputToggle: true,

        });

      });
</script>

<script type="text/javascript">
    var refNo ='PO-'+$("#reference_no").val();

$("#reference_no_write").val(refNo);

    $(document).on('keyup', '#reference_no', function () {

        var val = $(this).val();

        if(val == null || val == ''){
         $("#errMsg").html("Already Exists");
          $('#btnSubmit').attr('disabled', 'disabled');
          return;
         }else{
          $('#btnSubmit').removeAttr('disabled');
         }

        var ref = 'PO-'+$(this).val();
        $("#reference_no_write").val(ref);
      $.ajax({
        method: "POST",
        url: "/admin/purchase/reference-validation",
        data: { "ref": ref,"_token":token }
      })
        .done(function( data ) {
          var data = jQuery.parseJSON(data);
          if(data.status_no == 1){
            $("#errMsg").html('Already Exists!');
          }else if(data.status_no == 0){
            $("#errMsg").html('Available');
          }
        });
    });

function openwindow(){
    var win =  window.open('/admin/clients/modals', '_blank','toolbar=yes, scrollbars=yes, resizable=yes, top=500,left=500,width=600, height=650');

}  function HandlePopupResult(result) {
      if(result){
        let clients = result.clients;
        let types = $(`input[name=source]:checked`).val();
        if( types == 'lead'){
            lead_clients = clients;
        }
        else{
            customer_clients = clients;
        }
        var option = '';
        for(let c of clients){
            option = option + `<option value='${c.id}'>${c.name}</option>`;
        }
        $('#customers_id select').html(option);
        setTimeout(function(){
            $('#customers_id select').val(result.lastcreated);
            $('#pan_no').val(result.pan_no);
            $("#ajax_status").after("<span style='color:green;' id='status_update'>client sucessfully created</span>");
            $('#status_update').delay(3000).fadeOut('slow');
        },500);
      }
      else{
        $("#ajax_status").after("<span style='color:red;' id='status_update'>failed to create clients</span>");
        $('#status_update').delay(3000).fadeOut('slow');
      }
    }


</script>

<script src="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.min.js") }}"></script>

<script type="text/javascript">

  $(document).ready(function() {

      $("#purchase_bill_no").autocomplete({
            source: "/admin/getPurchaseBillId",
            minLength: 1
      });
    });

   $('#purchase_bill_no').on('change',function(){
         $.ajax(
            {
             url: "/admin/getPurchaseBillInfo",
             data: { purchasebills_id: $(this).val() },
             dataType: "json",
                success: function( data ) {
                    var purchasebillsinfo = data.purchasebillsinfo;
                    var purchasedetailinfo = data.purchasedetailinfo;
                    var customer_name = data.customer_name;
                    populatePurchaseInfo(purchasebillsinfo);
                    $('#customer_name').val(customer_name).prop("readonly", true);
                    $(".multipleDiv").after(purchasedetailinfo);
                    setTimeout(()=>{
                        $('.datepicker').datetimepicker({
                            //inline: true,
                            format: 'YYYY-MM-DD',
                            sideBySide: true,
                            allowInputToggle: true
                        },100);
                    })
                }
            });

    });

    function populatePurchaseInfo(invoiceinfo){

        $('#bill_date').val(invoiceinfo.bill_date).prop("readonly", true);
        $('#due_date').val(invoiceinfo.due_date).prop("readonly", true);
        $('#name').val(invoiceinfo.name).prop("readonly", true);
        $('#position').val(invoiceinfo.position).prop("readonly", true);
        $('#client_id').val(invoiceinfo.supplier_id);
        $('#purchase_order_date').val(invoiceinfo.ord_date);
        $('#user_id').val(invoiceinfo.user_id).prop("readonly", true);
        $('#location_id').val(invoiceinfo.into_stock_location);
        $('#terms').val(invoiceinfo.terms);
        $('#pan_no').val(invoiceinfo.pan_no).prop("readonly", true);

        $('#comment').val(invoiceinfo.comment);
        $('#address').val(invoiceinfo.address);

        $('#vat_type').val(invoiceinfo.vat_type);
        $('#is_renewal').val(invoiceinfo.is_renewal);

        $('#discount_amount').val(invoiceinfo.discount_percent);

        $('#subtotal').val(invoiceinfo.subtotal);
        $('#sub-total').html(invoiceinfo.subtotal);

        $('#taxableamount').val(invoiceinfo.taxable_amount);
        $('#taxable-amount').html(invoiceinfo.taxable_amount);

        $('#nontaxableamount').val(invoiceinfo.non_taxable_amount);
        $('#non-taxable-amount').html(invoiceinfo.non_taxable_amount);

        $('#taxabletax').val(invoiceinfo.tax_amount);
        $('#taxable-tax').html(invoiceinfo.tax_amount);

        $('#total').html(invoiceinfo.total);
        $('#total_').val(invoiceinfo.total);

        return 0;
   }

   $(document).on('change','#purchase_bill_no',function(){

        $('.multipleDiv').nextAll('tr').remove();



    });


    $(document).on('change', '.quantity', function() {

        var parentDiv = $(this).parent().parent();

        if(isNumeric(this.value) && this.value != ''){

            var invoice_qty = parentDiv.find('.purchase_quantity').val();
            if(parseInt(this.value) > parseInt(invoice_qty)){
                  $(this).val(parentDiv.find('.purchase_quantity').val());

                alert("Return Quantity Cannot Be Greater Than Purchase Quantity");

            }
        }

    });


</script>
@endsection
