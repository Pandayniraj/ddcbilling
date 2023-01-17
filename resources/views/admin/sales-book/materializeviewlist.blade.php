<table>
    <thead>
        <tr>
            <th colspan="15" style="text-align:center; font-size:16px font-weight:bold;">Credit Note</th>
        </tr>
        
        <tr>
            <th style="border: 1px solid; text-align:center; font-size:12px; background-color:#999898;">SN</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Fiscal Year</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Outlet</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Bill No</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Customer Name</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Customer Pan</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Bill Date</th>

            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Discount Amount</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Taxable Amount</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Total Amount</th>

            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Sync With IRD</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Is Bill Printed</th>

            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Is Bill Active</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Print time</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Entered By</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Printed By</th>
            <th style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">Is Real Time</th>
        </tr>
    </thead>
    <tbody>
        @php
            $n=1;
            $totalsalesamount=0;
            $total_taxable_amount=0;
        @endphp
        @foreach ($invoice as $item)
            <tr>
                <td style="border: 1px solid; text-align:center;">{{$n++}}</td>
                <td style="border: 1px solid; text-align:center;">{{$item->fiscal_year}}</td>
                <td style="border: 1px solid; text-align:center;">{{\App\Models\PosOutlets::where('id', $item->outlet_id)->first()->name??''}}</td>
                <td style="border: 1px solid; text-align:center;">{{$item->bill_no??''}}</td>
                <td style="border: 1px solid; text-align:center;">{{$item->customer_name??''}}</td>
                <td style="border: 1px solid; text-align:center;">{{$item->customer_pan??''}}</td>
                <td style="border: 1px solid; text-align:center;">{{$item->credit_note_no??'' }}</td>
                <td style="border: 1px solid; text-align:center;">{{$item->bill_date??'' }}</td>
                <td style="border: 1px solid; text-align:center;">{{$item->discount??'' }}</td>
                <td style="border: 1px solid; text-align:center;">{{$item->taxable_amount??''  }}</td>
                <td style="border: 1px solid; text-align:center;">{{$item->total_amount??''}}</td>
                <td style="border: 1px solid; text-align:center;">{{$item->sync_with_ird}}</td>
                <td style="border: 1px solid; text-align:center;">{{ $item->is_bill_printed??'' }}</td>
                <td style="border: 1px solid; text-align:center;">{!! $item->is_bill_active??'' !!}</td>
                <td style="border: 1px solid; text-align:center;"></td>
                <td style="border: 1px solid; text-align:center;">{!! $item->entered_by??'' !!}</td>
                <td style="border: 1px solid; text-align:center;">{!! $item->is_realtime??'' !!}</td>

            </tr>
            @php
            $totalsalesamount +=$item->total_amount;
            $total_taxable_amount+=$item->taxable_amount;
            @endphp
        @endforeach
        <tr>
            <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;" colspan="9">कल मूल्य</td>
            <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">{{$totalsalesamount}}</td>
            <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">{{ $total_taxable_amount}}</td>
            <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;"></td>
            <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;"></td>
            <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;"></td>
            <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;"></td>
            <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;"></td>
            <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;"></td>
        </tr>

    </tbody>
</table>
