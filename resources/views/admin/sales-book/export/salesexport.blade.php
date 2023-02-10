<table>
    <thead>
    <tr>
        <th colspan="10"></th>
    </tr>
{{--    <tr>--}}
{{--        <th></th>--}}
{{--        <th></th>--}}
{{--        <th>Tax Payer (PAN) : 60</th>--}}
{{--        <th colspan="4">Tax Payer Name : Dairy Development corporation--}}
{{--        </th>--}}
{{--        <th>year:</th>--}}
{{--        <th>tax period:</th>--}}
{{--    </tr>--}}
    <tr>
        <th style="text-align:center; font-weight:bold;">Invoice</th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th colspan="2" style="text-align:center; font-weight:bold;">Taxable</th>
        <th colspan="4" style="text-align:center; font-weight:bold;">Export</th>
    </tr>
    <tr>
        <th style="border: 1px solid; text-align:center;  font-weight:bold; width: 15px;">Date</th>
        <th style="border: 1px solid; text-align:center;font-weight:bold; width: 15px;">Invoice No.</th>
        <th style="border: 1px solid; text-align:center; font-weight:bold; width: 25px;">Customer Name</th>
        <th style="border: 1px solid; text-align:center; font-weight:bold; width: 20px;">VAT/PAN of Customer</th>
        <th style="border: 1px solid; text-align:center; font-weight:bold; width: 15px;">Goods/Services</th>
        <th style="border: 1px solid; text-align:center; font-weight:bold; width: 12px;">Quantity</th>
        <th style="border: 1px solid; text-align:center; font-weight:bold; width: 20px;">Total Amount</th>
        <th style="border: 1px solid; text-align:center; font-weight:bold; width: 25px;"> Local Tax less Sales Amount
        </th>
        <th style="border: 1px solid; text-align:center; font-weight:bold; width: 20px;"> Amount (NPR)</th>
        <th style="border: 1px solid; text-align:center; font-weight:bold; width: 15px;"> VAT (NPR)</th>
        <th style="border: 1px solid; text-align:center; font-weight:bold; width: 15px;">Amount</th>
        <th style="border: 1px solid; text-align:center; font-weight:bold; width: 15px;"> Exported Country</th>
        <th style="border: 1px solid; text-align:center; font-weight:bold; width: 20px;">Exported Pragyapan No.</th>
        <th style="border: 1px solid; text-align:center; font-weight:bold; width: 30px;">Exported Pragyapan Date</th>
    </tr>
    </thead>
    <tbody>
    @php
        $totalsalesamount=0;
        $total_nontaxable_amount=0;
        $total_taxable_amount=0;
        $total_tax=0;
    @endphp
    @foreach ($data as $item)
        <tr>
            <td style="border: 1px solid; text-align:center; font-size: 10px;">{{\App\Helpers\TaskHelper::getNepaliDate($item->bill_date)}}</td>
            <td style="border: 1px solid; text-align:center;">{{$item->bill_no}}</td>
            <td style="border: 1px solid; text-align:center;">{{$item->client->name}}</td>
            <td style="border: 1px solid; text-align:center;">{{$item->client->vat}}</td>
            <td style="border: 1px solid; text-align:center;">Goods</td>
            <td style="border: 1px solid; text-align:center;">
                @php $qty = 0;
                foreach ($item->invoicedetails as $invoicedetail) {
                    $qty += $invoicedetail->unit;
                }
                @endphp
                {{$qty}}
            </td>
            <td style="border: 1px solid; text-align:center;">{{$item->total_amount}}</td>
            <td style="border: 1px solid; text-align:center;">
                @if($item->tax_amount == 0.00) {{$item->total_amount}} @else - @endif
            <td style="border: 1px solid; text-align:center;">{{$item->taxable_amount}}</td>
            <td style="border: 1px solid; text-align:center;">{{ $item->tax_amount}}</td>
            <td style="border: 1px solid; text-align:center;">-</td>
            <td style="border: 1px solid; text-align:center;">-</td>
            <td style="border: 1px solid; text-align:center;">-</td>
            <td style="border: 1px solid; text-align:center;">-</td>
        </tr>
        @php
            $totalsalesamount +=$item->total_amount;
            $total_taxable_amount+=$item->taxable_amount;
            $total_tax+=$item->tax_amount;
            if($item->tax_amount == 0.00) {
                $total_nontaxable_amount +=$item->total_amount;
            }
        @endphp
        @if(!$item->invoicemeta->is_bill_active)
            <tr>
                <td style="border: 1px solid; text-align:center;">{{\App\Helpers\TaskHelper::getNepaliDate($item->bill_date)}}</td>
                <td style="border: 1px solid; text-align:center;">Ref of {{env('SALES_BILL_PREFIX')}}{{$item->bill_no}}
                    CN {{$item->invoicemeta->credit_note_no}}</td>
                <td style="border: 1px solid; text-align:center;">@if($item->client)
                        {{$item->client->name}}
                    @else
                        {{$item->name}}
                    @endif</td>
                <td style="border: 1px solid; text-align:center;">@if($item->client)
                        {{$item->client->vat}}
                    @else
                        {{$item->customer_pan}}
                    @endif</td>
                <td style="border: 1px solid; text-align:center;">Goods</td>
                <td style="border: 1px solid; text-align:center;">-{{$qty}}</td>
                <td style="border: 1px solid; text-align:center;">-{{$item->total_amount}}</td>
                <td style="border: 1px solid; text-align:center;">
                    @if($item->tax_amount == 0.00) -{{$item->total_amount}} @else - @endif
                <td style="border: 1px solid; text-align:center;">-{{$item->taxable_amount}}</td>
                <td style="border: 1px solid; text-align:center;">-{{ $item->tax_amount}}</td>
                <td style="border: 1px solid; text-align:center;">-</td>
                <td style="border: 1px solid; text-align:center;">-</td>
                <td style="border: 1px solid; text-align:center;">-</td>
                <td style="border: 1px solid; text-align:center;">-</td>
            </tr>
                <?php
                $totalsalesamount -= $item->total_amount;
                $total_taxable_amount -= $item->taxable_amount;
                $total_tax -= $item->tax_amount;
                if ($item->tax_amount == 0.00) {
                    $total_nontaxable_amount -= $item->total_amount;
                }
                // $taxable_amount = $taxable_amount - $item->taxable_amount;
                // $tax_amount = $tax_amount - $item->tax_amount;
                // $total_amount = $total_amount - $item->total_amount;
                ?>
        @endif
    @endforeach
    <tr>
        <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;" colspan="7">Total for sales invoice
        </td>
        <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">{{$totalsalesamount}}</td>
        <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">{{ $total_nontaxable_amount}}</td>
        <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">{{$total_taxable_amount}}</td>
        <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;">{{$total_tax}}</td>
        <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;"></td>
        <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;"></td>
        <td style="border: 1px solid; text-align:center; font-size:12px;  background-color:#999898;"></td>
    </tr>
    </tbody>
</table>
