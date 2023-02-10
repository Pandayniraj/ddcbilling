<body>
@php
    $grand_amount=0;
    $grand_vat_amount=0;
    $grand_total_amount=0;
@endphp
<div class="row">
    <table style="width:100%;" class="center">
        <tr>
            <th colspan="6">{{ $organization->organization_name }}</th>
        </tr>
        <tr>
            <th colspan="6">{{ $outletname->name}}</th>
        </tr>
        <tr>
            <th colspan="6">Daily Sales Detail Report</th>
        </tr>
        <tr>
            <th colspan="4">
                Detail Report From {{ $nepalistartdate }} to {{ $nepalienddate }}
            </th>
            <td colspan="2">
                Print Date:{{(\App\Helpers\TaskHelper::getNepaliDate(\Carbon\Carbon::today()->toDateString()))}}
            </td>
        </tr>
        <tr>
            <td colspan="6"></td>
        </tr>
        @foreach ($detail_transaction as $bill_type=>$values )
            <thead>
            <tr>
                <th colspan="6" style="font-weight: 600;">Payment Method: {{$bill_type}}</th>
            </tr>
            </thead>
            @php
                $billtype_amount=0;
                $billtype_vat_amount=0;
                $billtype_total_amount=0;
            @endphp
            @foreach ($values as $client_id=>$invoice )
                <thead>
                <tr>
                    <th style="text-align:center; font-weight: 600;" colspan="3">Customer
                        name: {{\App\Models\Client::find($client_id)->name??""}}</th>
                    <th style="text-align:center;" colspan="3">Prepared By: {{$created_by??""}}</th>
                </tr>
                <tr>
                    <th style="text-align:center; width: 25px;">Bill No</th>
                    <th style="text-align:center; width: 25px;">Item Name</th>
                    <th style="text-align:center; width: 25px;">Quantity</th>
                    <th style="text-align:center; width: 25px;">Amount</th>
                    <th style="text-align:center; width: 25px;">Vat Amt</th>
                    <th style="text-align:center; width: 25px;">Total Amount</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $customer_amount=0;
                    $customer_vat_amount=0;
                    $customer_total_amount=0;
                @endphp
                @foreach($invoice as $temp_invoicedetail)
                    @foreach($temp_invoicedetail->invoicedetails as $details)
                        <tr>
                            <td style="text-align:center;">{{$details->order->outlet->short_name}}
                                /{{$details->order->fiscal_year}}/00{{$details->order->bill_no }}</td>
                            <td style="text-align:center;">{{$details->product->name}}</td>
                            <td style="text-align:right;">{{$details->quantity}}</td>
                            <td style="text-align:right;">{{$details->total-$details->tax_amount}}</td>
                            <td style="text-align:right;">{{$details->tax_amount}}</td>
                            <td style="text-align:right;">{{$details->total}}</td>
                            @php
                                $customer_amount+=($details->total-$details->tax_amount);
                                $customer_vat_amount+=$details->tax_amount;
                                $customer_total_amount+=$details->total;
                            @endphp
                        </tr>
                    @endforeach
                @endforeach
                <tr>
                    <td style="text-align:center;" colspan="3">Total</td>
                    <td style="text-align:right;">{{$customer_amount}}</td>
                    <td style="text-align:right;">{{$customer_vat_amount}}</td>
                    <td style="text-align:right;">{{$customer_total_amount}}</td>
                    @php
                        $billtype_amount+=$customer_amount;
                        $billtype_vat_amount+=$customer_vat_amount;
                        $billtype_total_amount+=$customer_total_amount;
                    @endphp
                </tr>
                @if(!$loop->last)
                    <tr>
                        <td colspan="6"></td>
                    </tr>
                @endif
                @endforeach
                <tr>
                    <td style="text-align:center; font-weight: 600;" colspan="3">Total of {{$bill_type}} only</td>
                    <td style="text-align:right; font-weight: 600;">{{$billtype_amount}}</td>
                    <td style="text-align:right; font-weight: 600;">{{$billtype_vat_amount}}</td>
                    <td style="text-align:right; font-weight: 600;">{{$billtype_total_amount}}</td>
                    @php
                        $grand_amount+=$billtype_amount;
                        $grand_vat_amount+=$billtype_vat_amount;
                        $grand_total_amount+=$billtype_total_amount;
                    @endphp
                </tr>

                @if(!$loop->last)
                <tr><td colspan="6"></td></tr> @endif
            @endforeach
            <thead>
            <tr>
                <th style="text-align:left; font-weight: 600;" colspan="3">Grand Total</th>
                <th style="text-align:right; font-weight: 600;">{{$grand_amount}}</th>
                <th style="text-align:right; font-weight: 600;">{{$grand_vat_amount}}</th>
                <th style="text-align:right; font-weight: 600;">{{$grand_total_amount}}</th>
            </tr>
            </thead>
    </table>
</div>

</body>

</html>
