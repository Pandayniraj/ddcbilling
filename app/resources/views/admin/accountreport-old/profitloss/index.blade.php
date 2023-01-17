@extends('layouts.master')
@section('content')

<?php

function CategoryTree($parent_id=null,$affects_gross,$sub_mark='',$actype){

  $groups= \App\Models\COAgroups::orderBy('code', 'asc')->where('affects_gross',$affects_gross)->where('org_id',auth()->user()->org_id)->where('parent_id',$parent_id)->get();

    if(count($groups)>0){
      foreach($groups as $group){ 
          $cashbygroup = TaskHelper::getTotalByGroups($group->id);
             if($cashbygroup[0]['dr_amount'] == null && $cashbygroup[0]['dr_amount'] == null){
               echo '<tr>
                       <td><b><a href="'.route('admin.chartofaccounts.detail.groups', $group->id).'" style="color:black">'.$sub_mark.'['.$group->code.']'.$group->name.'</b></td>
                        <td><b>0.00</b></td>
                     </tr>';
                   }
              else{
                if($cashbygroup[0]['dr_amount']>$cashbygroup[0]['cr_amount']){  
                 echo '<tr>
                       <td><b><a href="'.route('admin.chartofaccounts.detail.groups', $group->id).'" style="color:black">'.$sub_mark.'['.$group->code.']'.$group->name.'</b></td>
                        <td><b><span>Dr '.number_format(abs($cashbygroup[0]['dr_amount']-$cashbygroup[0]['cr_amount']),2).'</span></b></td>
                     </tr>';  
                  }else{
                     echo '<tr>
                       <td><b><a href="'.route('admin.chartofaccounts.detail.groups', $group->id).'" style="color:black">'.$sub_mark.'['.$group->code.']'.$group->name.'</b></td>
                        <td><b><span>Cr '.number_format(abs($cashbygroup[0]['dr_amount']-$cashbygroup[0]['cr_amount']),2).'</span></b></td>
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
                if($closing_balance['dc'] == 'D'){

                    echo '<tr style="color: #3c8dbc;">
                     
                      <td class="bg-warning"><a href="'.route('admin.chartofaccounts.detail.ledgers', $ledger->id).'" style="color:#3c8dbc;">'.$sub_mark.'['.$ledger->code.']'.$ledger->name.'</a></td>
                       <td class="bg-warning"><b>Dr <span class="dr'.$actype.$affects_gross.'">'.$closing_balance['amount'].'</span></b></td>
                     </tr>';

               }else{

                    echo '<tr style="color: #3c8dbc;">
                        
                        <td class="bg-danger"><a href="'.route('admin.chartofaccounts.detail.ledgers', $ledger->id).'" style="color:#3c8dbc;">'.$sub_mark.'['.$ledger->code.']'.$ledger->name.'</a></td>
                         <td class="bg-danger"><b>Cr <span class="cr'.$actype.$affects_gross.'">'.$closing_balance['amount'].'</span></b></td>
                     </tr>';
              }
            }

           }

           $sub_mark=$submark;
        }

        CategoryTree($group->id,$affects_gross,$sub_mark."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$actype); 
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
                <br>
                <a href="{{route('admin.accounts.reports.profitloss.pdf')}}" class="btn btn-primary">PDF</a>
                <!-- <a href="{{route('admin.accounts.reports.profitloss.excel')}}" class="btn btn-primary">Excel</a> -->
                <br>
        </section>

      <div class="box">
        <div class='row'>
            <div class='col-md-6'>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="orders-table">
                                    <thead>
                                        <tr class="bg-blue">
                                           
                                            <th>Gross Expenses (Dr)</th>
                                    
                                            <th>Amount(P)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       {{ CategoryTree(4,1,'','expenses') }}
                                    </tbody>

                                    <tr style=" font-size: 16.5px; font-weight: bold; background-color: silver">
                                    <td>Total Gross Expenses</td>
                                      <td id="expensesTotal1"> xxx </td>
                                    </tr>
                                    <tr style=" font-size: 16.5px; font-weight: bold; background-color: skyblue">
                                      <td>Gross Profit</td>
                                      <td class="grossProfit1"> xxx</td>
                                      </tr>
                                      <tr style=" font-size: 16.5px; font-weight: bold">
                                      <td>Total</td>
                                      <td class="incomesTotal1"> xxx</td>
                                      </tr>

                                </table>
                            </div> <!-- table-responsive -->
                        </div><!-- /.box-body -->
            </div><!-- /.col -->
            <div class='col-md-6'>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="orders-table">
                                    <thead>
                                        <tr class="bg-olive">
                                            
                                            <th>Gross Incomes (Cr)</th>

                                            <th>Amount (P)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       {{ CategoryTree(3,1,'','incomes') }}
                                    </tbody>
                                     <tr style=" font-size: 16.5px; font-weight: bold; background-color: silver">
                                    <td>Total Gross Income</td>
                                      <td class="incomesTotal1"> xxx </td>
                                    </tr>
                                   <!--  <tr style=" font-size: 16.5px; font-weight: bold;">
                                      <td></td>
                                      <td> </td>
                                      </tr>
                                      <tr style=" font-size: 16.5px; font-weight: bold">
                                      <td>Total</td>
                                      <td> xxx</td>
                                      </tr> -->
                                </table>
                            </div> <!-- table-responsive -->
                        </div><!-- /.box-body -->
            </div><!-- /.col -->

        </div><!-- /.row -->
         <div class='row'>
            <div class='col-md-6'>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="orders-table">
                                    <thead>
                                        <tr class="bg-blue">
                                           
                                            <th>NET Expenses (Dr)</th>
                                    
                                            <th>Amount(P)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       {{ CategoryTree(4,0,'','expenses') }}
                                    </tbody>

                                    <tr style=" font-size: 16.5px; font-weight: bold; background-color: silver">
                                    <td>Total NET Expenses</td>
                                      <td id="expensesTotal0"> xxx </td>
                                    </tr>
                                    <tr style=" font-size: 16.5px; font-weight: bold; background-color: skyblue">
                                      <td>NET Profit</td>
                                      <td class="grossProfit0"> xxx</td>
                                      </tr>
                                      <tr style=" font-size: 16.5px; font-weight: bold">
                                      <td>Total</td>
                                      <td class="netincometotal0"> xxx</td>
                                      </tr>

                                </table>
                            </div> <!-- table-responsive -->
                        </div><!-- /.box-body -->
            </div><!-- /.col -->
            <div class='col-md-6'>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="orders-table">
                                    <thead>
                                        <tr class="bg-olive">
                                            
                                            <th>NET Incomes (Cr)</th>

                                            <th>Amount (P)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       {{ CategoryTree(3,0,'','incomes') }}
                                    </tbody>
                                     <tr style=" font-size: 16.5px; font-weight: bold; background-color: silver">
                                    <td>Total NET Income</td>
                                      <td class="incomesTotal0"> xxx </td>
                                    </tr>
                                    <tr style=" font-size: 16.5px; font-weight: bold; background-color: skyblue">
                                      <td>Gross Profit</td>
                                      <td class="grossProfit1"> xxx</td>
                                    </tr>
                                      <tr style=" font-size: 16.5px; font-weight: bold">
                                      <td>Total</td>
                                      <td class="netincometotal0"> xxx</td>
                                      </tr>
                                </table>
                            </div> <!-- table-responsive -->
                        </div><!-- /.box-body -->
            </div><!-- /.col -->

        </div><!-- /.row -->
      </div>
    

@endsection

<!-- Optional bottom section for modals etc... -->
@section('body_bottom')
<script type="text/javascript">
  $(function(){

    const allTotal = {


      drexpenses1: 0,
      crexpenses1: 0,
      drincomes1: 0,
      crincomes1: 0,

      drexpenses0: 0,
      crexpenses0: 0,
      drincomes0: 0,
      crincomes0: 0,

    }
    $('.drexpenses1').each(function(){

      allTotal.drexpenses1 += Number($(this).text());

    });

     $('.drexpenses0').each(function(){

      allTotal.drexpenses0 += Number($(this).text());

    });


    $('.crexpenses1').each(function(){
   
      allTotal.crexpenses1 += Number($(this).text());

    });


    $('.crexpenses0').each(function(){
   
      allTotal.crexpenses0 += Number($(this).text());

    });

    $('.drincomes1').each(function(){

      allTotal.drincomes1 += Number($(this).text());

    });

      $('.drincomes0').each(function(){

      allTotal.drincomes0 += Number($(this).text());

    });

    $('.crincomes1').each(function(){

      allTotal.crincomes1 += Number($(this).text());

    });

    $('.crincomes0').each(function(){

      allTotal.crincomes0 += Number($(this).text());

    });

    var incomesTotal1 = allTotal.crincomes1 -  allTotal.drincomes1;
    var incomesTotal0 = allTotal.crincomes0 -  allTotal.drincomes0;
    var expensesTotal1 =  allTotal.drexpenses1 - allTotal.crexpenses1 ;
    var expensesTotal0 =  allTotal.drexpenses0 - allTotal.crexpenses0 ;
    if(incomesTotal1 >= 0){
     $('.incomesTotal1').text('Cr '+ incomesTotal1.toFixed(3)  );
   }else{
    var incomesTotal1 = -incomesTotal1;
    $('.incomesTotal1').text('Dr '+ incomesTotal1.toFixed(3)  );
   }

   if(incomesTotal0 >= 0){
     $('.incomesTotal0').text('Cr '+ incomesTotal0.toFixed(3)  );
   }else{
    var incomesTotal0 = -incomesTotal0;
    $('.incomesTotal0').text('Dr '+ incomesTotal0.toFixed(3)  );
   }

   if(expensesTotal1 >= 0){
     $('#expensesTotal1').text('Dr '+expensesTotal1.toFixed(3)  );
   }else{
    var expensesTotal1 = -expensesTotal1;
     $('#expensesTotal1').text('Cr '+expensesTotal1.toFixed(3)  );
   }

    if(expensesTotal0 >= 0){
     $('#expensesTotal0').text('Dr '+expensesTotal0.toFixed(3)  );
   }else{
    var expensesTotal0 = -expensesTotal0;
     $('#expensesTotal0').text('Cr '+expensesTotal0.toFixed(3)  );
   }


    // $('.incomesTotal').text( (allTotal.crincomes -  allTotal.drincomes).toFixed(3)  );
   
    $('.grossProfit1').text((incomesTotal1 - expensesTotal1).toFixed(3) );



    $('.grossProfit0').text(((incomesTotal0 + incomesTotal1) - (expensesTotal1 - expensesTotal0)).toFixed(3) );

    $('.netincometotal0').text((incomesTotal0 + incomesTotal1 - expensesTotal1))

    // console.log(allTotal)

  });
</script>

@endsection  
