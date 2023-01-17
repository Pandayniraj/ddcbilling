<table id="example-advanced">
    <thead>
    <?php

    ?>
    <tr><th colspan="12" align="left"><b>Distributor Name: <strong>{{$ledgers_data->name??''}}</strong><b></th></tr>
    <tr><th colspan="12" align="left"><b>Transaction Date: From: {{date('d-M-Y', strtotime($startOfYear))}} To : {{ date('d-M-Y', strtotime($endOfYear))}}</b></th></tr>
    <tr><th colspan="6" align="left"><b>Opening Balance: {{date('d-M-Y', strtotime($startOfYear))}}  @if($opening_balance['dc']=='D') Dr @else Cr @endif{{is_numeric($opening_balance['amount']) ?  number_format($opening_balance['amount'],2) : '-'}}</b></th><th colspan="6" align="left"><b>Closing Balance: {{date('d-M-Y', strtotime($endOfYear))}}  {{$closing_balance['dc']=='D'?'Dr ':'Cr '}}{{number_format($closing_balance['amount'],2)}}</b></th></tr>

    <tr>
        <th>Date</th>
        <th style="width: 40px"><h4>Miti</h4></th>
        <th>Ref</th>
        <th>Bill No.</th>
        <th>Description</th>
        <th>Cheque No.</th>
        <th>Type</th>
        <th>Tag</th>
        <th>(Dr)</th>
        <th>(Cr)</th>
        <th>Balance</th>
    </tr>
    </thead>
    <tbody>

    <?php
        $total_opening=\TaskHelper::calculate_withdc(is_numeric($opening_balance['amount'])?$opening_balance['amount']:0,$opening_balance['dc'],
        $previous_closing['amount'], $previous_closing['dc'])
    ?>
        <tr>
            <td colspan="8">Current opening balance</td>
            <td>@if($total_opening['dc']=='D') Dr {{is_numeric($total_opening['amount']) ?  number_format($total_opening['amount'],2) : '-'}}@else - @endif</td>
            <td>@if($total_opening['dc']=='C') Cr {{is_numeric($total_opening['amount']) ?  number_format($total_opening['amount'],2) : '-'}}@else - @endif</td>
            <td></td>
        </tr>
        <?php
             /* Current opening balance */
            $entry_balance['amount'] = $total_opening['amount'] ?? '';
            $entry_balance['dc'] = $total_opening['dc'] ??'';
            $dr_total=0;
            $cr_total=0;
        ?>
        @foreach($entry_items as $ei)
            <?php

                $entry_balance = TaskHelper::calculate_withdc($entry_balance['amount'], $entry_balance['dc'],
                    $ei['amount'], $ei['dc']);

                $getledger= TaskHelper::getLedger($ei->entry_id);
                $cr_total+=$ei->dc=='C'?$ei->amount:0;
                $dr_total+=$ei->dc=='D'?$ei->amount:0;
            ?>

        <tr>
            <td>{{$ei->dynamicEntry()->date}}</td>
            <td>{{TaskHelper::getNepaliDate($ei->dynamicEntry()->date) }}</td>
            <td>{{$ei->dynamicEntry()->number}}</td>
            <?php
               
               if ($ei->source == AUTO_PURCHASE_ORDER) {
                   $href = "/admin/purchase/". $ei->ref_id."?type=bills";
 
               } elseif ($ei->source == TAX_INVOICE) {
                   $href = "/admin/invoice1/". $ei->ref_id ;
             } else {
                   $href = "/admin/orders/". $ei->ref_id ;
               }
             ?>
            <td><a href="{{ $href }}" target="_blank">{{ $ei->bill_no }}</a></td>
            @php $getEntryType = $ei->dynamicEntry()->getDynamicEntryType();    @endphp
            <td>{{$getledger}} \ {{  $getEntryType['type']}} No. 

                [{{ $getEntryType['order']->bill_no }}]
                <div style="font-size: 14px;color:grey">{{$ei->narration}}</div> 
            </td>
            <td>{{$ei->cheque_no??'-'}}</td>
            <td>{{$ei->dynamicEntry()->entrytype->name??''}}</td>
            <td>
                <span class="tag" style="color:#f51421;">
					<span style="color: #f51421;">
					    {{$ei->dynamicEntry()->tagname->title}}
				    </span>
				</span>
            </td>
            @if($ei->dc=='D')
                <td>{{$ei->currency}} {{$ei->amount}}</td>
                <td>-</td>
            @else
                <td>-</td>
                <td>{{$ei->currency}} {{$ei->amount}}</td>
            @endif
            <td>
                @if($entry_balance['dc']=='D') Dr @else Cr @endif
                {{
                    is_numeric($entry_balance['amount']) ? number_format($entry_balance['amount'],2) : '-'  }}
            </td>
            <td></td>
            <td></td>
        </tr>
        @endforeach
        <tr class="tr-highlight">
                        <td colspan="8" class="text-right" style="font-weight: 600;">Total</td>
                        <td style="font-size: 14px;font-weight: 600;">
                            {{ number_format($dr_total,2) }}
                        </td>
                        <td style="font-size: 14px;font-weight: 600;">
                            {{ number_format($cr_total,2) }}
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr class="tr-highlight">
                        <td colspan="10">Current closing balance</td>
                        <td style="font-size: 14px;font-weight: 600;">@if($entry_balance['dc']=='D') Dr @else Cr @endif
                            {{ is_numeric($entry_balance['amount']) ?  number_format($entry_balance['amount'],2) : '-' }}

                        </td>
                        <td></td>
                    </tr>
    </tbody>
</table>
