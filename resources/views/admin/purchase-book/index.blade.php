@extends('layouts.master')
@section('content')



<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
           <h1> Purchase Book </h1>
<span class="pull-right">Fiscal Year: {{$fiscal_year}}</span>
<div class="btn-group btn-xs pull-right">
  <?php 
      $url = \Request::query();
      if($url){
        $url = \Request::getRequestUri() .'&';
      }
      else{
        $url = \Request::getRequestUri() .'?';
      }
    ?>

    <a href="{{$url}}op=pdf"  class="btn btn-success btn-xs"> <i class ="fa fa-download"></i>Pdf
    </a>&nbsp;&nbsp;
      <a href="{{$url}}op=excel"  class="btn btn-primary btn-xs"><i class ="fa fa-print"></i>Excel</a>&nbsp;&nbsp;
                  <button type="button" class="btn btn-danger btn-xs">Monthly Report</button>
                  <button type="button" class="btn btn-danger dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                  </button>
                  <ul class="dropdown-menu" role="menu">
                    <li><a href="/admin/purchase-book-bymonth/01">Baishakh</a></li>
                    <li><a href="/admin/purchase-book-bymonth/02">Jesth</a></li>
                    <li><a href="/admin/purchase-book-bymonth/03">Asar</a></li>
                    <li><a href="/admin/purchase-book-bymonth/04">Shrawan</a></li>
                    <li class="divider"></li>
                    <li><a href="/admin/purchase-book-bymonth/05">Bhadra</a></li>
                    <li><a href="/admin/purchase-book-bymonth/06">Asoj</a></li>
                    <li><a href="/admin/purchase-book-bymonth/07">Kartik</a></li>
                    <li><a href="/admin/purchase-book-bymonth/08">Mangsir</a></li>
                    <li class="divider"></li>
                    <li><a href="/admin/purchase-book-bymonth/09">Push</a></li>
                    <li><a href="/admin/purchase-book-bymonth/10">Magh</a></li>
                    <li><a href="/admin/purchase-book-bymonth/11">Falgun</a></li>
                    <li><a href="/admin/purchase-book-bymonth/12">Chaitra</a></li>
                  </ul>
                </div>
                <br/>

          
        </section>


<form method="GET" action="/admin/purchase-book/">

       <div class='row'>
        <div class='col-md-12'>

              <div class="box box-primary">
    <div class="box-header with-border">
        
        
        <div class="col-md-6 pull-left">   
           <div class="col-md-4">
           <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" placeholder="Start Date" class="form-control input-sm startdate" name="engstartdate">
                </div>
            </div>

             <div class="col-md-4">
           <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" placeholder="End Date" class="form-control input-sm enddate" name="engenddate">
                </div>
            </div>


            <button type="submit" class="btn btn-primary btn-sm" name="filter" value="eng">Show Bills</button>
            </div>

            <div class="col-md-6 pull-right">  


            <div class="col-md-4">
           <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" placeholder="सुरु  मिति " class="form-control input-sm" id='nep-startdate' name="nepstartdate">
                </div>
            </div>

             <div class="col-md-4">
           <div class="input-group">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" placeholder="अन्तिम मिति" class="form-control input-sm" id='nep-enddate' name="nependdate">
                </div>
            </div>


            <button type="submit" class="btn btn-success btn-sm" value="nep" name="filter">नतिजा </button>
            <button type="submit" class="btn btn-success btn-sm" id ="btn-filter-clear">Reset</button>
            </div>
        
</div>
   </form>
<table class="table table-hover table-bordered table-stripped" id="leads-table" cellspacing="0" width="100%">
<thead>
    <tr>

        <th colspan="3" style="text-align: center; background-color: #eee" >Invoice</th>
        <th colspan="5"></th>
        <th colspan="2" style="text-align: center;  background-color: #eee">Taxable Purchase</th>
    
    </tr>
    <tr class="bg-olive">
            <th>Date</th>
        <th>Bill No</th>
        <th>Supplier’s Name</th>
        <th>Supl. PAN Number</th>
        <th>Total Purchase</th>
        <th>Non Tax Purchase</th>
        <th>Exp. Purchase</th>
        <th>Discount</th> 
        <th>Amount</th>
        <th>Tax(Rs)</th>

    </tr>
</thead>
<tbody>
  <?php  
  $taxable_amount = 0;
  $tax_amount = 0;
  $total_purch = 0;
  $totalNontaxPurch = 0;
  $totalDiscount= 0;
  ?>
    @foreach($purchase_book as $pur_bks)
<tr>
      <td>{{ date('dS M y', strtotime($pur_bks->ord_date)) }}<br/>

                         <small> {!! TaskHelper::getNepaliDate($pur_bks->ord_date) !!}</small>

                        </td>
    <td>{{$pur_bks->bill_no}} </td>
    <td>{{$pur_bks->client->name}}</td>
        <td>{{$pur_bks->client->vat}}</td>
    <td>
      

      {{-- {{$pur_bks->taxable_amount + round($pur_bks->tax_amount,2) }} --}}
      {{$pur_bks->total}}


    </td>
    <td>{{ $pur_bks->non_taxable_amount }}</td>
    <td></td>
    <td>{{$pur_bks->discount_amount}}</td>
    <td>{{$pur_bks->taxable_amount}}</td>
    <?php  
      $taxable_amount = $taxable_amount + $pur_bks->taxable_amount;
      $tax_amount = $tax_amount +  $pur_bks->tax_amount;
      $total_purch  +=  $pur_bks->total;

      $totalNontaxPurch += $pur_bks->non_taxable_amount;
      $totalDiscount += $pur_bks->discount_amount;
    ?>
 <td>{{ round($pur_bks->tax_amount,2) }}</td>
</tr>
@foreach($pur_bks->return_bills as $rtn_bks)
<tr class="bg-danger">
  <td>{{ date('dS M y', strtotime($rtn_bks->return_date)) }}<br/>
 <small> {!! TaskHelper::getNepaliDate($rtn_bks->return_date) !!}</small>
  </td>
   <td>{{ $rtn_bks->bill_no }} </td>
   <td>{{$pur_bks->client->name}}</td>
   <td>{{ $rtn_bks->pan_no }}</td>
   <td>{{ $rtn_bks->total_amount }}</td>
   @php  $return_total_non_taxable = $rtn_bks->return_details->where('is_taxable','0')->sum('return_total')  
   @endphp
   <td>{{ $return_total_non_taxable }}</td>
   <td></td>
   <td></td>
   <td>{{ $rtn_bks->subtotal }}</td>
   <td>{{  $rtn_bks->tax_amount }}</td>
</tr>
    <?php  
      
      $tax_amount = $tax_amount -  $rtn_bks->tax_amount;
      $total_purch  -=  $rtn_bks->total_amount;

      $totalNontaxPurch -= $return_total_non_taxable;

    ?>
@endforeach
@endforeach
<tr>
    <th>Total Amount</th>
    <td></td>
     <td></td>
      <td></td>
       <td>{{ $total_purch }}</td>
        <td>{{ $totalNontaxPurch }}</td>
         <td></td>
          <td>{{ $totalDiscount }}</td>
    <td>{{$taxable_amount}}</td>
    <td>{{$tax_amount}}</td>
    </tr>

</tbody>
</table>
</div>

@endsection

@section('body_bottom')
 <script type="text/javascript" src="https://nepali-date-picker.herokuapp.com/src/jquery.nepaliDatePicker.js"> </script>
<link rel="stylesheet" href="https://nepali-date-picker.herokuapp.com/src/nepaliDatePicker.css">
    <!-- form submit -->
    @include('partials._body_bottom_submit_bug_edit_form_js')
    <script type="text/javascript">
     $(function() {
        $('.startdate').datetimepicker({
          //inline: true,
          format: 'YYYY-MM-DD',
          sideBySide: true,
          allowInputToggle: true,
         
        });

      });
       $(function() {
        $('.enddate').datetimepicker({
          //inline: true,
          format: 'YYYY-MM-DD',
          sideBySide: true,
          allowInputToggle: true,
         
        });

      });
$("#nep-startdate").nepaliDatePicker({
    dateFormat: "%y-%m-%d",
    closeOnDateSelect: true
});
$("#nep-enddate").nepaliDatePicker({
    dateFormat:"%y-%m-%d",
    closeOnDateSelect: true
});
$("#btn-filter-clear").on("click", function () {
  window.location.href = "{!! url('/') !!}/admin/purchase-book/";
});
</script>
@endsection