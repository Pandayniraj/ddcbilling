<table>
    <thead>
        <tr>
            <th colspan="15" style="text-align:center; font-size:16px font-weight:bold;">Credit Note</th>
        </tr>
        
        <tr>
            <th style="border: 1px solid; text-align:center; font-size:12px; background-color:#999898;">SN</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Fiscal Year</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Outlet</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Bill Date</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Ref Bill No</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Cancel Date</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Credit Note No</th>

            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Cancel Reason</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Guest Name</th>

            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Guest PAN</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Total Sales</th>

            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Non Tax Sale</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Export Sale</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Taxable Amount</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Tax</th>
        </tr>
    </thead>
    <tbody>
        @php
        $n=1;
            $totalsalesamount=0;
            $total_nontaxable_amount=0;
            $total_taxable_amount=0;
            $total_tax=0;
        @endphp
        @foreach ($invoice as $item)
            <tr>
                <td style="border: 1px solid; text-align:center;">{{$n++}}</td>
                <td style="border: 1px solid; text-align:center;" >{{$item->fiscal_year}}</td>
                <td style="border: 1px solid; text-align:center;" >{{$item->outlet->name??''}}</td>
                <td style="border: 1px solid; text-align:center;">{{$item->bill_date}}</td>
                <td style="border: 1px solid; text-align:center;">{{$item->bill_no}}</td>
                <td style="border: 1px solid; text-align:center;">{{ $item->cancel_date }}</td>
                <td style="border: 1px solid; text-align:center;">{{ $item->credit_note_no }}</td>
                <td style="border: 1px solid; text-align:center;">{{ $item->invoicemeta->void_reason }}</td>
                <td style="border: 1px solid; text-align:center;">@if($item->client_id){{$item->client->name}} @else {{$item->name}} @endif</td>
                <td style="border: 1px solid; text-align:center;">@if($item->client_id) {{$item->client->vat}} @else {{$item->customer_pan}}@endif</td>
                <td style="border: 1px solid; text-align:center;">{{ number_format($item->total_amount,2)}}</td>
                <td style="border: 1px solid; text-align:center;">{{ $item->total_amount - ($item->taxable_amount+$item->tax_amount)>=1?$item->total_amount - ($item->taxable_amount+$item->tax_amount):0 }}</td>
                <td style="border: 1px solid; text-align:center;"></td>
                <td style="border: 1px solid; text-align:center;">{!! number_format($item->taxable_amount,2) !!}</td>
                <td style="border: 1px solid; text-align:center;">{!! number_format($item->tax_amount,2) !!}</td>
            </tr>
            @php
            $totalsalesamount +=$item->total_amount;
            $total_nontaxable_amount+=$item->total_amount-$item->taxable_amount-$item->tax_amount >= 1?$item->total_amount-$item->taxable_amount-$item->tax_amount:0;
            $total_taxable_amount+=$item->taxable_amount;
            $total_tax+=$item->tax_amount;
            @endphp
        @endforeach
        <tr>
            <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;" colspan="10">कल मूल्य</td>
            <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">{{$totalsalesamount}}</td>
            <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">{{ $total_nontaxable_amount}}</td>
            <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;"></td>
            <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">{{$total_taxable_amount}}</td>
            <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">{{$total_tax}}</td>
        </tr>

    </tbody>
</table>
