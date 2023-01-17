@extends('layouts.master')
@section('content')

<style type="text/css">

.moto{
  /*font-size: 14px;*/
  font-weight: bolder;
}
table{
    font-size: 12px!important;
}
</style>

<?php
 $total_opening_dr=0;
 $total_opening_cr=0;
?>
<?php

function CategoryTree($parent_id=null,$sub_mark='',$start_date,$end_date,$fiscal,$total_opening_dr=0,$total_opening_cr=0){

  $groups= \App\Models\COAgroups::orderBy('code', 'asc')
          ->where('parent_id',$parent_id)->where('org_id',auth()->user()->org_id)->get();

    if(count($groups)>0){
      foreach($groups as $group){
        $cashbygroup = \TaskHelper::getTotalByGroups($group->id,$start_date,$end_date,$fiscal);

        if($cashbygroup['dr_amount'] == null && $cashbygroup['cr_amount'] == null&& $cashbygroup['opening_balance']['amount'] == 0){

                echo '<tr data-toggle="collapse"
                 data-target=".ledgers'.$group->id.'" class="accordion-toggle '.(($group->id==1||$group->id==2||$group->id==3||$group->id==4)?'bg-info':'').'" style="cursor: pointer">

                    <td class="moto">'.$sub_mark.$group->code.'</td>
                        <td class="moto">'.$sub_mark.$group->name.(count($group->ledgers)>0?'<i class="fa fa-chevron-down" style="font-size: 11px"></i>':'').'</td>
                    <td class="moto">'.'0.00'.'</td>
                    <td class="moto">'.'0.00'.'</td>
                    <td class="moto">'.'0.00'.'</td>
                    <td class="moto">'.'0.00'.'</td>
                   </tr>';

        }else{
            if($cashbygroup['opening_balance']['dc']=='D'){
                $total_opening_dr+=$cashbygroup['opening_balance']['amount'];
            }else{

                $total_opening_cr+=$cashbygroup['opening_balance']['amount'];
            }

            $sum=$cashbygroup['dr_amount']-$cashbygroup['cr_amount'];
            $closing_balance=$cashbygroup['opening_balance']['dc']=='D'?($sum+$cashbygroup['opening_balance']['amount']):
            ($sum-$cashbygroup['opening_balance']['amount']);

            if($closing_balance>0){

                  echo '<tr data-toggle="collapse" data-target=".ledgers'.$group->id.'" class="accordion-toggle '.(($group->id==1||$group->id==2||$group->id==3||$group->id==4)?'bg-info':'').'" style="cursor: pointer">
                        <td class="moto ">'.$sub_mark.$group->code.'</td>
                        <td class="moto">'.$sub_mark.$group->name.(count($group->ledgers)>0?'<i class="fa fa-chevron-down" style="font-size: 11px"></i>':'').'</td>
                        <td class="moto">'.($cashbygroup['opening_balance']['dc']=='D'?'Dr ':'Cr ').number_format(abs($cashbygroup['opening_balance']['amount']),2).'</td>
                        <td class="moto">Dr '.number_format($cashbygroup['dr_amount'],2).'</td>
                        <td class="moto">Cr '.number_format($cashbygroup['cr_amount'],2).'</td>
                        <td class="moto">Dr '.number_format(abs($closing_balance),2).'</td>
                       </tr>';

               }
            else{

                 echo '<tr data-toggle="collapse" data-target=".ledgers'.$group->id.'" class="accordion-toggle '.(($group->id==1||$group->id==2||$group->id==3||$group->id==4)?'bg-info':'').'" style="cursor: pointer">
                    <td class="moto">'.$sub_mark.$group->code.'</td>
                        <td class="moto">'.$sub_mark.$group->name.(count($group->ledgers)>0?'<i class="fa fa-chevron-down" style="font-size: 11px"></i>':'').'</td>
                    <td class="moto">'.($cashbygroup['opening_balance']['dc']=='D'?'Dr ':'Cr ').number_format(abs($cashbygroup['opening_balance']['amount']),2).'</td>
                    <td class="moto">Dr '.number_format($cashbygroup['dr_amount'],2).'</td>
                    <td class="moto">Cr '.number_format($cashbygroup['cr_amount'],2).'</td>
                    <td class="moto">Cr '.number_format(abs($closing_balance),2).'</td>
                   </tr>';


               }

        }
          $ledger_table=new \App\Models\COALedgers();
          $prefix='';

          if ($fiscal){
              $current_fiscal=\App\Models\Fiscalyear::where('current_year', 1)->first();
              $fiscal_year = $fiscal?  $fiscal->fiscal_year: $current_fiscal->fiscal_year ;
              if ($fiscal_year!=$current_fiscal->fiscal_year){
                  $prefix=\App\Models\Fiscalyear::where('fiscal_year',$fiscal_year)->first()->numeric_fiscal_year.'_';
                  $new_coa=$prefix.'coa_ledgers';
                  $ledger_table->setTable($new_coa);
              }
          }

        $ledgers= $ledger_table->orderBy('code', 'asc')->where('group_id',$group->id)->get();

        if(count($ledgers)>0){
            $submark= $sub_mark;
            $sub_mark = $sub_mark."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

              foreach($ledgers as $ledger){
                $opening_balance =TaskHelper::getLedgersOpeningBalance($ledger,$start_date,$fiscal);
                $dr_cr =TaskHelper::getLedgerDrCr($ledger,$fiscal,$start_date,$end_date);
                  $closing_balance=\App\Helpers\TaskHelper::getLedgerClosing($opening_balance,$dr_cr['dr_total'],$dr_cr['cr_total']);

//                if ($closing_balance['amount'] > 0) {
                  if( $closing_balance['dc'] == 'D'){
                     echo '<tr style="color: #009551;" class="hiddenRow accordian-body collapse ledgers'.$ledger->group_id.'">

                            <td class="text-success"><a href="/admin/accounts/reports/ledger_statement?ledger_id='. $ledger->id.'">'.$sub_mark.$ledger->code.'</a></td>
                            <td class="text-success"><a href="/admin/accounts/reports/ledger_statement?ledger_id='.$ledger->id.'">'.$ledger->name.'</a></td>
                            <td class="text-success">'.($opening_balance['dc']=='D'?'Dr ':'Cr ').number_format($opening_balance['amount'],2).'</td>

                            <td class="text-success">Dr '.number_format($dr_cr['dr_total'],2).'</td>
                            <td class="text-success">Cr '.number_format($dr_cr['cr_total'],2).'</td>
                            <td class="text-success">Dr '.number_format($closing_balance['amount'],2).'</td>

                       </tr>';
                  }
                  else{
//                      $total_cr_amounts=$total_cr_amounts+$closing_balance['cr_total']??0;


                      echo '<tr style="color: #009551;" class="hiddenRow accordian-body collapse ledgers'.$ledger->group_id.'">

                        <td class=""><a href="/admin/accounts/reports/ledger_statement?ledger_id='.$ledger->id.'">'.$sub_mark.$ledger->code.'</a></td>
                        <td class=""><a href="/admin/accounts/reports/ledger_statement?ledger_id='.$ledger->id.'"">'.$ledger->name.'</a></td>
                        <td class="">'.($opening_balance['dc']=='D'?'Dr ':'Cr ').number_format($opening_balance['amount'],2).'</td>
                       <td class="text-success">Dr '.number_format($dr_cr['dr_total'],2).'</td>
                            <td class="text-success">Cr '.number_format($dr_cr['cr_total'],2).'</td>
                        <td class="">Cr '.number_format($closing_balance['amount'],2).'</td>
                       </tr>';
                  }

//                }


           }
           $sub_mark=$submark;
        }

        CategoryTree($group->id,$sub_mark."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$start_date,$end_date,$fiscal,$total_opening_dr,$total_opening_cr);
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
          <form method="get" action="/admin/accounts/reports/trialbalance">
              <div style="display: inline-flex;float: left;padding-left: 20px">
                  <div class="form-group">
                      <label>Fiscal Year</label>
                      <div class="input-group">
                          <select id="fiscal_year_id" class="form-control input-sm" name="fiscal_year" required="required">
                              @foreach($allFiscalYear as $key => $pk)
                                  <option value="{{ $pk->fiscal_year }}" {{$fiscal_year==$pk->fiscal_year?'selected':''}}>{{ $pk->fiscal_year }}</option>
                              @endforeach
                          </select>
                      </div>
                      <!-- /.input group -->
                  </div>
              </div>
              <div class="col-md-2">
                  <div class="form-group">
                      <label>Start Date</label>
                      <div class="input-group">
                          <input id="ReportStartdate" type="text" name="start_date" class="form-control input-sm datepicker date-toggle-nep-eng1" value="{{ $start_date }}">
                          <div class="input-group-addon">
                              <i>
                                  <div class="fa fa-info-circle" data-toggle="tooltip" title="Note : Leave start date as empty if you want statement from the start of the financial year.">
                                  </div>
                              </i>
                          </div>
                      </div>
                      <!-- /.input group -->
                  </div>
                  <!-- /.form group -->
              </div>
              <div class="col-md-2">
                  <div class="form-group">
                      <label>End Date</label>

                      <div class="input-group">
                          <input id="ReportEnddate" type="text" name="end_date" class="form-control input-sm datepicker date-toggle-nep-eng1" value="{{ $end_date }}">
                          <div class="input-group-addon">
                              <i>
                                  <div class="fa fa-info-circle" data-toggle="tooltip" title="Note : Leave end date as empty if you want statement till the end of the financial year.">
                                  </div>
                              </i>
                          </div>
                      </div>
                      <!-- /.input group -->
                  </div>
                  <!-- /.form group -->
              </div>
              <div>

                  <!-- /.form group -->
              </div><div style="padding: 0;display: inline-block">
              <div>
              <label for="">
                  <label for=""></label>
              </label>
              </div>

              <button type="submit" class="btn btn-primary btn-sm">Filter</button>

          </div>
              <div class="" style="padding:0;padding-left: 5px;display: inline-block">
                  <div>
                      <label for="">
                          <label for=""></label>
                      </label>
                  </div>
              <a href="{{route('admin.accounts.reports.trialbalance.excel',['start_date'=>$start_date,'end_date'=>$end_date,'fiscal_year'=>$fiscal_year])}}" class="btn btn-success btn-sm">Excel</a>

          </div>
        </form>
{{--            <div class="col-sm-1" style="">--}}
{{--                <label for="">--}}
{{--                    <label for=""></label>--}}
{{--                </label>--}}
{{--            --}}{{--            <a href="{{route('admin.accounts.reports.trialbalance.pdf',['start_date'=>Request::get('start_date'),'end_date'=>Request::get('end_date')])}}" class="btn btn-primary">PDF</a>--}}
{{--            <a href="{{route('admin.accounts.reports.trialbalance.excel',['start_date'=>$start_date,'end_date'=>$end_date])}}" class="btn btn-success">Excel</a>--}}
{{--            </div>--}}
        </div>
      <div class="box">


    <div class='row'>
        <div class='col-md-12'>
            <!-- Box -->
             <div class="box-header with-border">


                    </div>

                    <div class="box-body">

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="orders-table">
                                <thead>
                                    <tr class="bg-primary">

                                        <th style="width: 165px;">Account Code</th>
                                        <th style="width: 22%;">Account Name</th>
                                        <th>Opening Balance</th>
                                        <th>Debit Total({{env('APP_CURRENCY')}})</th>
                                        <th>Credit Total({{env('APP_CURRENCY')}})</th>
                                        <th>Closing Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if(count($groups)>0)
                                {{ CategoryTree(null,'',$start_date,$end_date,$fiscal) }}


                                    <?php
                                        $assetstotal = \TaskHelper::getTotalByGroups(1,$start_date,$end_date,$fiscal);
                                        $equitytotal = \TaskHelper::getTotalByGroups(2,$start_date,$end_date,$fiscal);
                                        $incometotal = \TaskHelper::getTotalByGroups(3,$start_date,$end_date,$fiscal);
                                        $expensestotal = \TaskHelper::getTotalByGroups(4,$start_date,$end_date,$fiscal);

                                      $total_dr_amount = $assetstotal['dr_amount'] + $equitytotal['dr_amount'] + $incometotal['dr_amount'] + $expensestotal['dr_amount'];

                                        $total_cr_amount =  $assetstotal['cr_amount'] + $equitytotal['cr_amount'] + $incometotal['cr_amount'] + $expensestotal['cr_amount'];

                                        if($assetstotal['opening_balance']['dc']=="D"){
                                            $total_opening_dr+=$assetstotal['opening_balance']['amount'];
                                        }else{
                                            $total_opening_cr+=$assetstotal['opening_balance']['amount'];
                                        }

                                        if($equitytotal['opening_balance']['dc']=="D"){
                                            $total_opening_dr+=$equitytotal['opening_balance']['amount'];
                                        }else{
                                            $total_opening_cr+=$equitytotal['opening_balance']['amount'];
                                        }

                                        if($incometotal['opening_balance']['dc']=="D"){
                                            $total_opening_dr+=$incometotal['opening_balance']['amount'];
                                        }else{
                                            $total_opening_cr+=$incometotal['opening_balance']['amount'];
                                        }

                                        if($expensestotal['opening_balance']['dc']=="D"){
                                            $total_opening_dr+=$expensestotal['opening_balance']['amount'];
                                        }else{
                                            $total_opening_cr+=$expensestotal['opening_balance']['amount'];
                                        }
                                        $total_opening='00.00';
                                        if($total_opening_dr>$total_opening_cr){
                                            $total_opening='Dr '.($total_opening_dr-$total_opening_cr);
                                        }else{
                                            $total_opening='Cr '.($total_opening_cr-$total_opening_dr);
                                        }
                                     ?>
                                     <tr style=" font-size: 16.5px; font-weight: bold;">
                                        <td colspan="2">Opening Difference</td>
                                        <td>{{$total_opening}}</td>
                                        <td></td>
                                        <td></td>
                                        <td>{{$total_opening}}</td>
                                    </tr>

                                   <tr style=" font-size: 16.5px; font-weight: bold;">

                                       <td colspan="3"></td>
{{--                                        @dd($total_dr_amount);--}}
                                       <td style="font-weight: 25px">Dr {{number_format($total_dr_amount,2)}}</td>
                                       <td style="font-weight: 25px">Cr {{number_format($total_cr_amount,2)}}</td>
                                       <td style="font-weight: 25px">
                                        @if(round($total_dr_amount,2)== round($total_cr_amount,2))
                                             <i class="fa fa-check-circle text-success"></i>
                                       @else
                                             <i class="fa fa-close text-danger"></i> {{number_format($total_dr_amount-$total_cr_amount,2)}}

                                           @endif
                                       </td>
                                   </tr>
                                    @endif
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

@include('partials._date-toggle')
<script type="text/javascript">


  $(function(){
    $('.date-toggle-nep-eng1').nepalidatetoggle();
        $('.datepicker').datepicker({
          //inline: true,
          dateFormat: 'yy-mm-dd',
          sideBySide: false,
        });
      });
  $(document).ready(function() {
      $('.customer_id').select2();
  });
  $(document).on('change','#fiscal_year_id',function () {
      var fiscal_year = $(this).val()
      var fiscal_detail = ''
      var all_fiscal_years = {!! json_encode($allFiscalYear); !!}
      all_fiscal_years.forEach((item) => {
          if (item.fiscal_year == fiscal_year)
              fiscal_detail = item
      });

      $('#ReportStartdate').val(fiscal_detail.start_date)
      $('#ReportEnddate').val(fiscal_detail.end_date)

  })
</script>

@endsection
