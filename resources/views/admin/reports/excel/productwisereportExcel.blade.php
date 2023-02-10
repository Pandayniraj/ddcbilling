<table>
    <thead>
    <tr>
        <th colspan="5">{{ $organization->organization_name }}</th>
    </tr>
    <tr>
        <th colspan="5">{{$outletname->name}}</th>
    </tr>
    <tr>
        <th colspan="5">Sales Report From {{ $nepalistartdate }} to {{ $nepalienddate }}</th>
    </tr>
    <tr>
        <td colspan="18"></td>
    </tr>
    <tr>
        <th style="width: 5px;" rowspan=2>S.N</th>
        <th style="width: 30px;" rowspan=2>Particular</th>
        <th style="width: 15px;" rowspan=2>Opening Stock</th>
        <th style="width: 10px;" rowspan=2>Cold Store</th>
        <th style="width: 10px;" rowspan=2>Total</th>
        <th style="width: 30px; text-align: center;" colspan="3">Distributor</th>
        <th style="width: 30px; text-align: center;" colspan="3">Retailer</th>
        <th style="width: 30px; text-align: center;" colspan="3">Boothman</th>
        <th style="width: 30px; text-align: center;" colspan="3">Direct Customer</th>
        <th style="width: 30px; text-align: center;" colspan="3">Staff</th>
        <th style="width: 15px;" rowspan="2">Coldstore Return</th>
        <th style="width: 15px;" rowspan="2">Closing Stock</th>
        <th style="width: 30px; text-align: center;" colspan="3">Total Sales</th>
    </tr>
    <tr>
        <th style="width: 10px;">Qty</th>
        <th style="width: 10px;">Amount</th>
        <th style="width: 10px;">VAT</th>
        <th style="width: 10px;">Qty</th>
        <th style="width: 10px;">Amount</th>
        <th style="width: 10px;">VAT</th>
        <th style="width: 10px;">Qty</th>
        <th style="width: 10px;">Amount</th>
        <th style="width: 10px;">VAT</th>
        <th style="width: 10px;">Qty</th>
        <th style="width: 10px;">Amount</th>
        <th style="width: 10px;">VAT</th>
        <th style="width: 10px;">Qty</th>
        <th style="width: 10px;">Amount</th>
        <th style="width: 10px;">VAT</th>
        <th style="width: 10px;">Qty</th>
        <th style="width: 10px;">Amount</th>
        <th style="width: 10px;">VAT</th>
    </tr>
    </thead>
    <tbody>
    @php
        $dist_col_amount=0;
        $dist_col_vat=0;
        $retail_col_amount=0;
        $retail_col_vat=0;
        $boothman_col_amount=0;
        $boothman_col_vat=0;
        $dc_col_amount=0;
        $dc_col_vat=0;
        $staff_col_amount=0;
        $staff_col_vat=0;
    @endphp
    @foreach ($products as  $productid=>$productname)

        <tr>
            @php
                $alya=0;
                $cold_store=$stock[$productid][0]->quantity;
                $total=$cold_store+$alya;
            @endphp
            <td>{{ $loop->index+1 }}</td>
            <td>{{ $productname }}</td>
            <td>{{ $alya }}</td>
            <td>{{$cold_store  }}</td>
            <td>{{ $total }}</td>
            {{-- //distributor --}}
            @php
                //dd($data);
                  $distributordata=$data[$productid]['distributor'];
                  $dist_qty=0;
                  $dist_amount=0;
                  $dist_vat=0;
                  foreach ($distributordata as $value) {

                    $dist_qty+=$value->quantity;
                    $dist_amount+=$value->total_amount;
                    $dist_vat+=$value->tax_amount;

                    $dist_col_amount+=$value->total_amount;
                    $dist_col_vat+=$value->tax_amount;
                  }
            @endphp
            <td>{{ $dist_qty }}</td>
            <td>{{ $dist_amount }}</td>
            <td>{{ $dist_vat }}</td>
            {{-- //retailer --}}
            @php
                $retailerdata=$data[$productid]['retailer'];
                $retailer_qty=0;
                $retailer_amount=0;
                $retailer_vat=0;
                foreach ($retailerdata as $value) {
                  $retailer_qty+=$value->quantity;
                  $retailer_amount+=$value->total_amount;
                  $retailer_vat+=$value->tax_amount;

                  $retail_col_amount+=$value->total_amount;
                  $retail_col_vat+=$value->tax_amount;
                }
            @endphp
            <td>{{ $retailer_qty }}</td>
            <td>{{ $retailer_amount }}</td>
            <td>{{ $retailer_vat }}</td>
            {{-- boothman --}}
            @php
                $boothmandata=$data[$productid]['boothman'];
                $boothman_qty=0;
                $boothman_amount=0;
                $boothman_vat=0;
                foreach ($boothmandata as $value) {
                  $boothman_qty+=$value->quantity;
                  $boothman_amount+=$value->total_amount;
                  $boothman_vat+=$value->tax_amount;

                  $boothman_col_amount+=$value->total_amount;
                  $boothman_col_vat+=$value->tax_amount;
                }
            @endphp
            <td>{{ $boothman_qty }}</td>
            <td>{{ $boothman_amount }}</td>
            <td>{{ $boothman_vat }}</td>
            {{-- direct_customer --}}
            @php
                $direct_customerdata=$data[$productid]['direct_customer'];
                $direct_customer_qty=0;
                $direct_customer_amount=0;
                $direct_customer_vat=0;
                foreach ($direct_customerdata as $value) {
                  $direct_customer_qty+=$value->quantity;
                  $direct_customer_amount+=$value->total_amount;
                  $direct_customer_vat+=$value->tax_amount;

                  $dc_col_amount+=$value->total_amount;
                  $dc_col_vat+=$value->tax_amount;
                }
            @endphp
            <td>{{ $direct_customer_qty }}</td>
            <td>{{ $direct_customer_amount }}</td>
            <td>{{ $direct_customer_vat }}</td>
            {{-- staff --}}
            @php
                $staffdata=$data[$productid]['staff'];
                $staff_qty=0;
                $staff_amount=0;
                $staff_vat=0;
                foreach ($staffdata as $value) {
                  $staff_qty+=$value->quantity;
                  $staff_amount+=$value->total_amount;
                  $staff_vat+=$value->tax_amount;

                  $staff_col_amount+=$value->total_amount;
                  $staff_col_vat+=$value->tax_amount;
                }
            @endphp
            <td>{{ $staff_qty }}</td>
            <td>{{ $staff_amount }}</td>
            <td>{{ $staff_vat }}</td>
            <td></td>
            <td>{{ $total -($dist_qty + $retailer_qty + $boothman_qty + $direct_customer_qty + $staff_qty)}}</td>
            <td>{{ $dist_qty + $retailer_qty + $boothman_qty + $direct_customer_qty + $staff_qty}}</td>
            <td>{{ $dist_amount + $retailer_amount + $boothman_amount + $direct_customer_amount + $staff_amount}}</td>
            <td>{{ $dist_vat + $retailer_vat + $boothman_vat + $direct_customer_vat + $staff_vat}}</td>

        </tr>
    @endforeach
    <tr>
        <th colspan="6">Total</th>
        <th>{{ $dist_col_amount }}</th>
        <th>{{ $dist_col_vat }}</th>
        <th></th>
        <th>{{ $retail_col_amount }}</th>
        <th>{{ $retail_col_vat }}</th>
        <th></th>
        <th>{{ $boothman_col_amount }}</th>
        <th>{{ $boothman_col_vat }}</th>
        <th></th>
        <th>{{ $dc_col_amount }}</th>
        <th>{{ $dc_col_vat }}</th>
        <th></th>
        <th>{{ $staff_col_amount }}</th>
        <th>{{ $staff_col_vat }}</th>
        <th></th>
        <th></th>
        <th></th>
        <th>{{ $dist_col_amount + $retail_col_amount +  $boothman_col_amount + $dc_col_amount + $staff_col_amount }}</th>
        <th>{{ $dist_col_vat + $retail_col_vat + $boothman_col_vat + $dc_col_vat +$staff_col_vat }}</th>
    </tr>

    </tbody>
</table>
