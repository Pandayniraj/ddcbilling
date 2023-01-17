@extends('layouts.master')
@section('content')

<style type="text/css">
  
.moto{
  font-size: 16px;
  font-weight: bolder;
}

</style>

<?php

function CategoryTree($parent_id=null,$sub_mark=''){

  $groups= \App\Models\COAgroups::orderBy('code', 'asc')
          ->where('parent_id',$parent_id)->where('org_id',auth()->user()->org_id)->get();

    if(count($groups)>0){
      foreach($groups as $group){ 

        $cashbygroup = \TaskHelper::getTotalByGroupsId($group->id);

        if($cashbygroup['dr_amount'] == null && $cashbygroup['cr_amount'] == null){

                echo '<tr>
                   
                    <td class="moto">'.$sub_mark.$group->code.'</td>
                    <td class="moto">'.$group->name.'</td> 
                    <td class="moto">'.'0.00'.'</td>
                    <td class="moto">'.'0.00'.'</td>
                    <td class="moto">'.'0.00'.'</td>
                    <td class="moto">'.'0.00'.'</td>
                   </tr>';

                }else{
                   if($cashbygroup['cr_amount'] < $cashbygroup['dr_amount']){
                     echo '<tr>
                       
                        <td class="moto ">'.$sub_mark.$group->code.'</td>
                        <td class="moto">'.$group->name.'</td> 
                        <td class="moto">'.'0.00'.'</td>
                        <td class="moto">Dr '.number_format($cashbygroup['dr_amount'],2).'</td>
                        <td class="moto">Cr '.number_format($cashbygroup['cr_amount'],2).'</td>
                        <td class="moto">Dr '.number_format(abs($cashbygroup['dr_amount']-$cashbygroup['cr_amount']),2).'</td>
                       </tr>';
                   }else{
                      echo '<tr>
                       
                        <td class="moto ">'.$sub_mark.$group->code.'</td>
                        <td class="moto">'.$group->name.'</td> 
                        <td class="moto">'.'0.00'.'</td>
                        <td class="moto">Dr '.number_format($cashbygroup['dr_amount'],2).'</td>
                        <td class="moto">Cr '.number_format($cashbygroup['cr_amount'],2).'</td>
                        <td class="moto">Cr '.number_format(abs($cashbygroup['dr_amount']-$cashbygroup['cr_amount']),2).'</td>
                       </tr>';
                   }
                 
                }
             

        $ledgers= \App\Models\COALedgers::orderBy('code', 'asc')->where('org_id',auth()->user()->org_id)->where('group_id',$group->id)->get(); 

        if(count($ledgers)>0){
            $submark= $sub_mark;
            $sub_mark = $sub_mark."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; 

              foreach($ledgers as $ledger){

                // $closing_balance =TaskHelper::getLedgerDebitCredit($ledger->id);
                $closing_balance =TaskHelper::getLedgerTotal($ledger->id);
                if ($closing_balance['amount'] > 0) {
                  if( $closing_balance['dc'] == 'D'){

                     echo '<tr style="color: #3c8dbc;">
                            
                            <td class="text-success"><a href="'.route('admin.chartofaccounts.detail.ledgers', $ledger->id).'" style="color:#3c8dbc;">'.$sub_mark.$ledger->code.'</a></td>
                            <td class="text-success"><a href="'.route('admin.chartofaccounts.detail.ledgers', $ledger->id).'" style="color:#3c8dbc;">'.$ledger->name.'</a></td>
                            <td class="text-success">'.number_format($ledger->op_balance,2).'</td>

                            <td class="text-success">Dr '.number_format($closing_balance['amount'],2).'</td>
                            <td class="text-success">Cr 0.00</td>
                            <td class="text-success">Dr '.number_format($closing_balance['amount'],2).'</td>

                       </tr>';
                  }
                  else{

                    echo '<tr style="color: #3c8dbc;">
                     
                        <td class=""><a href="'.route('admin.chartofaccounts.detail.ledgers', $ledger->id).'" style="color:#3c8dbc;">'.$sub_mark.$ledger->code.'</a></td>
                        <td class=""><a href="'.route('admin.chartofaccounts.detail.ledgers', $ledger->id).'" style="color:#3c8dbc;">'.$ledger->name.'</a></td>
                        <td class="">'.number_format($ledger->op_balance,2).'</td>
                        <td class="">Dr 0.00</td>
                        <td class="">Cr '.number_format($closing_balance['amount'],2).'</td>
                        <td class="">Cr '.number_format($closing_balance['amount'],2).'</td>
                       </tr>';
                  }
                  
                }

              
           }
           $sub_mark=$submark;
        }


        CategoryTree($group->id,$sub_mark."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"); 
      }
    }

  }
?>

<link href="{{ asset("/bower_components/admin-lte/plugins/datatables/jquery.dataTables.min.css") }}" rel="stylesheet" type="text/css" />

        <section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
            <h1>
                {{ $page_title }}
                <small>  {!! $page_description ?? "Page description" !!}</small>
            </h1>
            {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}
        </section>
        <div class="row">
         {{-- <form method="get" action="/admin/accounts/reports/trialbalance">
          <div class="col-sm-2">
            <input type="text" name="start_date" value="{{ Request::get('start_date') }}" placeholder="Start date..." class="form-control datepicker" autocomplete="off" >
          </div>
           <div class="col-sm-2">
            <input type="text" name="end_date" value="{{ Request::get('end_date') }}" placeholder="End date..." class="form-control datepicker" autocomplete="off">
          </div>
          <div class="col-sm-2">
            <button type="submit" class="btn btn-primary btn-block">Filter</button>
          </div>
        </form> --}}
      
        </div>
          <div class="row" style="margin: revert;">
        {{--  <a href="{{route('admin.accounts.reports.trialbalance.pdf')}}" class="btn btn-primary">PDF</a>
          <a href="{{route('admin.accounts.reports.trialbalance.excel')}}" class="btn btn-primary">Excel</a> --}}
      </div>
      <br/>
      <div class="box">


    <div class='row'>
        <div class='col-md-12'>
            <!-- Box -->
             <div class="box-header with-border"> 
                        
                     
                    </div>
            
                    <div class="box-body">

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-striped" id="orders-table">
                                <thead>
                                    <tr class="bg-info">
                                        
                                        <th>Account Code</th>
                                        <th>Account Name</th>
                                        <th>Opening Balance</th>
                                        <th>Debit Total({{env('APP_CURRENCY')}})</th>
                                        <th>Credit Total({{env('APP_CURRENCY')}})</th>
                                        <th>Closing Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   {{ CategoryTree() }}
                                   <tr style=" font-size: 16.5px; font-weight: bold;">

                                    <?php

                                        $assetstotal = \TaskHelper::getTotalByGroupsId(1);
                                        $equitytotal = \TaskHelper::getTotalByGroupsId(2);
                                        $incometotal = \TaskHelper::getTotalByGroupsId(3);
                                        $expensestotal = \TaskHelper::getTotalByGroupsId(4);

                                        $total_dr_amount = $assetstotal['dr_amount'] + $equitytotal['dr_amount'] + $incometotal['dr_amount'] + $expensestotal['dr_amount'];

                                        $total_cr_amount =  $assetstotal['cr_amount'] + $equitytotal['cr_amount'] + $incometotal['cr_amount'] + $expensestotal['cr_amount'];

                                     ?>
                                       <td colspan="3"></td>
                                       <td style="font-weight: 25px">Dr {{ number_format($total_dr_amount,2)}}</td>
                                       <td style="font-weight: 25px">Cr {{number_format($total_cr_amount,2)}} </td>
                                       <td style="font-weight: 25px">
                                        @if($total_dr_amount == $total_cr_amount)
                                             <i class="fa fa-check-circle"></i>
                                       @else
                                             <i class="fa fa-close"></i>
                                        @endif
                                       </td>
                                   </tr>
                                </tbody>
                            </table>

                        </div> <!-- table-responsive -->

                    </div><!-- /.box-body -->
               
        </div><!-- /.col -->

    </div><!-- /.row -->
   

@endsection

<!-- Optional bottom section for modals etc... -->
@section('body_bottom')
<link href="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.css") }}" rel="stylesheet" type="text/css" />
<link href="{{ asset("/bower_components/admin-lte/bootstrap/css/bootstrap-datetimepicker.css") }}" rel="stylesheet" type="text/css" />
<script src="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.min.js") }}"></script>
<script src="{{ asset ("/bower_components/admin-lte/plugins/daterangepicker/moment.js") }}" type="text/javascript"></script>
<script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/bootstrap-datetimepicker.js") }}" type="text/javascript"></script>


<script type="text/javascript">
  

  $(function(){
        $('.datepicker').datepicker({
          //inline: true,
          dateFormat: 'yy-mm-dd', 
          sideBySide: false,  
        });
      });

</script>

@endsection  