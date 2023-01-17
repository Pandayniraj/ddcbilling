@extends('layouts.master')
@section('content')
<style type="text/css">
  .balancesheettable th,.balancesheettable td{


    padding: 4px !important;

  }
  .f-16{
    font-size: 16.5px;
  }
</style>
<?php

function CategoryTree($parent_id=null,$sub_mark='',$actype){
  $total = 0;
  $groups= \App\Models\COAgroups::orderBy('code', 'asc')->where('parent_id',$parent_id)
          ->where('org_id',auth()->user()->org_id)->get();

    if(count($groups)>0){
      foreach($groups as $group){ 

        $cashbygroup = TaskHelper::getTotalByGroups($group->id);

         if($cashbygroup[0]['dr_amount'] == null && $cashbygroup[0]['dr_amount'] == null){
           echo '<tr>
                    {{-- <td><b>'.$sub_mark.'['.$group->code.']'.$group->name.'</b></td> --}}
                    <td><b><a href="'.route('admin.chartofaccounts.detail.groups', $group->id).'" style="color:black">'.$sub_mark.'['.$group->code.']'.$group->name.'</b></td>
                    <td><b><span>0.00</span></b></td>
                 </tr>';
            }else{
                if($cashbygroup[0]['dr_amount']>$cashbygroup[0]['cr_amount']){
                 echo '<tr>
                        {{-- <td><b>'.$sub_mark.'['.$group->code.']'.$group->name.'</b></td> --}}
                        <td><b><a href="'.route('admin.chartofaccounts.detail.groups', $group->id).'" style="color:black">'.$sub_mark.'['.$group->code.']'.$group->name.'</b></td>
                        <td><b><span>Dr '.number_format(abs($cashbygroup[0]['dr_amount']-$cashbygroup[0]['cr_amount']),2).'</span></b></td>
                     </tr>';
                   }else
                   {
                   echo '<tr>
                        {{-- <td><b>'.$sub_mark.'['.$group->code.']'.$group->name.'</b></td> --}}
                        <td><b><a href="'.route('admin.chartofaccounts.detail.groups', $group->id).'" style="color:black">'.$sub_mark.'['.$group->code.']'.$group->name.'</b></td>
                        <td><b><span>Cr '.number_format(abs($cashbygroup[0]['dr_amount']-$cashbygroup[0]['cr_amount']),2).'</span></b></td>
                     </tr>';
                   }
            }

        $ledgers= \App\Models\COALedgers::orderBy('code', 'asc')->where('org_id',auth()->user()->org_id)->where('group_id',$group->id)
                ->get(); 
        if( count( $ledgers) > 0 ) {

            $submark= $sub_mark;
            $sub_mark = $sub_mark."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; 

              foreach($ledgers as $ledger){
             // $closing_balance =TaskHelper::getLedgerDebitCredit($ledger->id);
             $closing_balance =TaskHelper::getLedgerTotal($ledger->id);

             if ($closing_balance['amount'] > 0) {

                if( $closing_balance['dc'] == 'D'){

                    echo '<tr style="color: #3c8dbc;">
                    
                      <td class="bg-warning"><a href="'.route('admin.chartofaccounts.detail.ledgers', $ledger->id).'" style="color:#3c8dbc;font-size: 12px;">'.$sub_mark.'['.$ledger->code.']'.$ledger->name.'</a></td>
                       <td class="bg-warning f-16">Dr <span class="dr'.$actype.' dr'.$actype.$index.'">'.
                       $closing_balance['amount'].'</span></td>
                     </tr>';
                     $total += $closing_balance['amount'];
               }else{

                    echo '<tr style="color: #3c8dbc;">
                    
                        <td class="bg-danger"><a href="'.route('admin.chartofaccounts.detail.ledgers', $ledger->id).'" style="color:#3c8dbc;font-size: 12px;">'.$sub_mark.'['.$ledger->code.']'.$ledger->name.'</a></td>
                         <td class="bg-danger f-16">Cr <span class="cr'.$actype.'">'.
                        $closing_balance['amount'].'</span></td>
                     </tr>';
                     $total -= $closing_balance['amount'] ;
              }
            }

           }

           $sub_mark=$submark;
        }
        CategoryTree($group->id,$sub_mark."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$actype); 
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
              <a href="{{route('admin.accounts.reports.balancesheet.pdf')}}" class="btn btn-primary">PDF</a>
              <!-- <a href="{{route('admin.accounts.reports.balancesheet.excel')}}" class="btn btn-primary">Excel</a> -->
            <br>
        </section>


        

     <div class='row'>
        <div class='col-md-6'>
          <div class="box">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered balancesheettable" >
                                <thead>
                                    <tr class="bg-primary">
                                       
                                        <th>Assets</th>
                                
                                        <th>Amount(Rs)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   {{ CategoryTree(1,'','assets') }}
                                </tbody>
                                <tfoot>
                                <tr style=" font-size: 16.5px; font-weight: bold;">
                                 
                                  <th>Total</th>
                             
                                  <td class="assetsTotal"></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div> <!-- table-responsive -->
                    </div><!-- /.box-body -->
          </div><!-- /.col -->
        </div>
        <div class='col-md-6'>
          <div class="box">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered balancesheettable">
                                <thead>
                                    <tr class="bg-maroon">
                                        
                                        <th>Liabilities and Owners Equity (Cr)</th>

                                        <th>Amount (Rs)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   {{ CategoryTree(2,'','libalities') }}
                                </tbody>
                                <tfoot>
                                  <tr style=" font-size: 16.5px; font-weight: bold; background-color: silver">
                                  <td>Total Liabilities and Owners Equity</td>
                                    <td id="libalitiesTotal"> xxx </td>
                                  </tr>
                                  <tr style=" font-size: 16.5px; font-weight: bold; background-color: skyblue">
                                    <td>Profit & Loss Account (Net Profit)</td>
                                    <td id="netProfit"> xxx</td>
                                    </tr>
                                   <tr style=" font-size: 16.5px; font-weight: bold;">
                                    <th>Total</th>
                                    <td class="assetsTotal"></td>
                                  </tr>
                                </tfoot>
                            </table>
                        </div> <!-- table-responsive -->

                    </div><!-- /.box-body -->
              </div>
        </div><!-- /.col -->
       
    </div><!-- /.row -->
    
@endsection

<!-- Optional bottom section for modals etc... -->
@section('body_bottom')

<script type="text/javascript">
  $(function(){

    const allTotal = {


      drassets: 0,
      drassets1: 0,
      crassets: 0,
      drlibalities: 0,
      crlibalities: 0,

    }
    $('.drassets').each(function(){

      allTotal.drassets += Number($(this).text());

    });

    $('.crassets').each(function(){
   
      allTotal.crassets += Number($(this).text());

    });

    $('.drlibalities').each(function(){

      allTotal.drlibalities += Number($(this).text());

    });

    $('.crlibalities').each(function(){

      allTotal.crlibalities += Number($(this).text());

    });

     var assetsTotal = allTotal.crassets -  allTotal.drassets;
     var libalitiesTotal = allTotal.drlibalities - allTotal.crlibalities;

      if((assetsTotal) >= 0)
      {
           $('.assetsTotal').text('Cr '+ assetsTotal.toFixed(3)  );
      }else
      {
        var assetsTotal = -assetsTotal;
         $('.assetsTotal').text('Dr '+ assetsTotal.toFixed(3)  );
      }

      if(libalitiesTotal >= 0)
      {
        $('#libalitiesTotal').text('Dr '+libalitiesTotal.toFixed(3)  );
      }else
      {
        var libalitiesTotal = -libalitiesTotal;
       $('#libalitiesTotal').text('Cr '+libalitiesTotal.toFixed(3)  );
      }

  
    console.log(allTotal.crassets -  allTotal.drassets);

    
    $('#netProfit').text(((allTotal.crassets -  allTotal.drassets)-(allTotal.drlibalities - allTotal.crlibalities)).toFixed(3) );
  });
</script>
@endsection  
