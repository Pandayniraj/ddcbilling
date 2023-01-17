 <style type="text/css">

        .moto{
            font-size: 16px;
            font-weight: bolder;
        }

    </style>

 <?php

 function CategoryTree($parent_id=null,$sub_mark='',$start_date,$end_date,$fiscal){

     $groups= \App\Models\COAgroups::orderBy('code', 'asc')
         ->where('parent_id',$parent_id)->where('org_id',auth()->user()->org_id)->get();

     if(count($groups)>0){
         foreach($groups as $group){
             $cashbygroup = \TaskHelper::getTotalByGroups($group->id,$start_date,$end_date,$fiscal);
             if($cashbygroup['dr_amount'] == null && $cashbygroup['cr_amount'] == null&& $cashbygroup['opening_balance']['amount'] == null){

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

             $ledgers= \App\Models\COALedgers::orderBy('code', 'asc')->where('group_id',$group->id)->get();

             if(count($ledgers)>0){
                 $submark= $sub_mark;
                 $sub_mark = $sub_mark."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

                 foreach($ledgers as $ledger){
                     $opening_balance =TaskHelper::getLedgersOpeningBalance($ledger,$start_date,$fiscal);
                     $dr_cr =TaskHelper::getLedgerDrCr($ledger,$fiscal);
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


             CategoryTree($group->id,$sub_mark."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$start_date,$end_date,$fiscal);
         }
     }

 }
 ?>

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
            @if(count($groups)>0)
                {{ CategoryTree(null,'',$start_date,$end_date,$fiscal) }}
                <tr style=" font-size: 16.5px; font-weight: bold;">

                    <?php


                    //                                       $total_dr_amount
                    $assetstotal = \TaskHelper::getTotalByGroups(1,$start_date,$end_date,$fiscal);
                    $equitytotal = \TaskHelper::getTotalByGroups(2,$start_date,$end_date,$fiscal);
                    $incometotal = \TaskHelper::getTotalByGroups(3,$start_date,$end_date,$fiscal);
                    $expensestotal = \TaskHelper::getTotalByGroups(4,$start_date,$end_date,$fiscal);

                    $total_dr_amount = $assetstotal['dr_amount'] + $equitytotal['dr_amount'] + $incometotal['dr_amount'] + $expensestotal['dr_amount'];

                    $total_cr_amount =  $assetstotal['cr_amount'] + $equitytotal['cr_amount'] + $incometotal['cr_amount'] + $expensestotal['cr_amount'];

                    ?>
                    <td colspan="3"></td>
                    <td style="font-weight: 25px">Dr {{ number_format($total_dr_amount,2)}}</td>
                    <td style="font-weight: 25px">Cr {{number_format($total_cr_amount,2)}} </td>
                    <td style="font-weight: 25px">
                        @if(round($total_dr_amount,2)== round($total_cr_amount,2))
                            <i class="fa fa-check-circle text-success"></i>
                        @else
                            <i class="fa fa-close text-danger"></i>
                        @endif
                    </td>
                </tr>
            @endif
            </tbody>
        </table>

