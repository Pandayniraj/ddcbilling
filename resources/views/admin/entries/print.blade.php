<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
  <head>
    <meta charset="UTF-8">
    <title>{{ env('APP_COMPANY')}} | {{ ucfirst(Request::segment(4)) }}</title>

    <!-- block from searh engines -->
    <meta name="robots" content="noindex">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <!-- Set a meta reference to the CSRF token for use in AJAX request -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- Bootstrap 3.3.4 -->
    <link href="{{ asset("/bower_components/admin-lte/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons 4.7.0 -->
    <link href="{{ asset("/bower_components/admin-lte/font-awesome/css/all.css") }}" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.1 -->
    <link href="{{ asset("/bower_components/admin-lte/ionicons/css/ionicons.min.css") }}" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="{{ asset("/bower_components/admin-lte/dist/css/AdminLTE.min.css") }}" rel="stylesheet" type="text/css" />


    <style>
table {
  width:100%;
}
table, th, td {
  border: 1px solid #696969;
  border-collapse: collapse;
}
th, td {
  padding: 15px;
  text-align: left;
}
table#t01 tr:nth-child(even) {
  background-color: #eee;
}
table#t01 tr:nth-child(odd) {
 background-color: #fff;
}
table#t01 th {
  background-color: #696969  ;
  color: white;
}
  

.table>thead>tr>th {
    border-bottom: 1px solid #696969 !important;
}
.table>tbody>tr>th {
    border-top: 1px solid #696969 !important;
}
.table>tbody>tr>td{
  border-top: 1px solid #696969 !important;
}
</style>


  </head>

<body onload="window.print();" cz-shortcut-listen="true" class="skin-blue sidebar-mini">

  <div class='wrapper'>

      <section class="invoice">
        <!-- title row -->
                <div class="row">
                  <div class="col-xs-12">
                    <h2 class="page-header">
                       <div class="col-xs-3">
                      <img src="{{env('APP_URL')}}/images/logo-invoice.png" style="max-width: 200px;">
                        </div>
                        <div class="col-xs-9">
                      <span class="pull-right">
                        <span>{{ ucfirst(Request::segment(4)) }}</span>
                      </span>
                    </div>
                     <hr>
                    </h2>
                   
                  </div>
                  <!-- /.col -->
                </div>
                <!-- info row -->


            <div class="col-xs-12">
                 <div class="box">
                    <div class="box-header with-border">
                      <h2 class="box-title">{{\FinanceHelper::get_entry_type_name($entries->entrytype_id)}} Voucher</h2>
                    </div>
                      <!-- /.box-header -->
                      <div class="box-body">
                          <div>
                              Number : {{$entries->number}}<br>
                              Date : {{ $entries->date }} / {{ \TaskHelper::getNepaliDate($entries->date) }}
                              <br>
                              <br>
                              <table class="table table-bordered">
                                <tbody>
                                  <tr>
                                    
                                    <th>Ledger</th>
                                    <th>Dr Amount ({{env(APP_CURRENCY)}})</th>
                                    <th>Cr Amount ({{env(APP_CURRENCY)}})</th>
                                    <th>Narration</th>
                                  </tr>
                                  @foreach($entriesitem as $items)
                                  <tr>
                                   
                                    <td>[{{$items->ledgerdetail->code}}] {{$items->ledgerdetail->name}}</td>
                                    @if($items->dc == 'D')
                                    <td>{{$items->amount}}</td>
                                    <td>-</td>
                                    @else
                                    <td>-</td>
                                    <td>{{$items->amount}}</td>
                                    @endif
                                    <td>{{$items->narration}}</td>
                                  </tr>
                                  @endforeach
                                  <tr>
                          <td></td>
                          <td><strong>Total</strong></td>
                          <td style="font-size: 16.5px"><strong>Dr {{number_format($entries->dr_total,2)}}</strong></td>
                          <td style="font-size: 16.5px"><strong>Cr {{number_format($entries->cr_total,2)}}</strong></td>
                          
                        </tr>
                                </tbody>
                              </table>
                              <br>Tag : {{ $entries->tagname->title }}<br>   
                              {{ nl2br($entries->notes) }}<br/>
                    Created at: {{ $entries->created_at }}<br/>
                    Created by: {{ $entries->user->username }}<br/>

                    <br/><br/>
                    <br/>...................

                           </div>
                      </div>
                </div>
       </div>
      

      </section>

  </div><!-- /.col -->

</body>
