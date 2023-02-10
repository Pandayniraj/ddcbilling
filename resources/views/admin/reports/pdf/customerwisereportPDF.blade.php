<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Sales Report</title>
    <style>
        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
        }

        .row .col {
            margin: 0px !important;
            padding: 0px !important;
        }
    </style>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>

<body>
<p>{{ $organization->organization_name }}</p>
<p>{{ $outletname->name}}</p>
<h3>Transaction Report From {{ $nepalistartdate }} to {{ $nepalienddate }}</h3>
<div class="row">
    <table>
        <thead>
        <tr>
            <th>S.N</th>
            <th>Customer</th>
            <th>DR Amount</th>
            <th>DR Vat</th>
            <th>DR Total</th>
            <th>CR Amount</th>
            <th>Period Balance</th>
            <th>Overall Balance</th>
        </tr>

        </thead>
        <tbody>

        @foreach ($clients as $client)
            <tr>
                <td>{{$loop->index+1}}</td>
                <td>{{$client->name}}</td>
                @php
                    $client_id=$client->id;
                    $client_invoice=\App\Models\Invoice::where('client_id',$client_id)
                    ->where('bill_date','>=',$startdate)
                    ->where('bill_date','<=',$enddate)
                    ->pluck('id');
                    $temp=\App\Models\InvoicePayment::whereIn('invoice_id',$client_invoice)->select(DB::raw('SUM(amount) as cr_amount'))->first();
                    $period_cr_amount=$temp->cr_amount??0;

                    $overall_dr=\App\Models\Invoice::
                    select('client_id',DB::raw('SUM(total_amount) as dr_total'))
                    ->where('client_id',$client_id)
                    ->first();
                    $overall_paid=\App\Models\Invoicepayment::whereIn('invoice_id',\App\Models\Invoice::where('client_id',$client_id)->pluck('id'))->select(DB::raw('SUM(amount) as cr_amount'))->first();
                    // dd($overall_detail,$overall_paid);
                    $dr_amount=(double)$detail_transaction[$client_id][0]->dr_total - $detail_transaction[$client_id][0]->dr_vat;

                @endphp
                <td>{{$dr_amount}}</td>
                <td>{{(double)$detail_transaction[$client_id][0]->dr_vat??0}}</td>
                <td>{{(double)$detail_transaction[$client_id][0]->dr_total??0}}</td>


                <td>{{$period_cr_amount}}</td>
                <td>{{(double)$detail_transaction[$client_id][0]->dr_total??0 - (double)$period_cr_amount}}</td>
                <td>{{(double)$overall_dr->dr_total - (double)$overall_paid->cr_amount}}</td>
            </tr>

        @endforeach


        </tbody>
    </table>
</div>


<div class="row">
    <div class="col-xs-3" style="padding-top:20px">Submitted By</div>
    <div class="col-xs-3" style="padding-top:20px">Marketing Officer</div>
    <div class="col-xs-3" style="padding-top:20px">Project Manager</div>
</div>
<div class="row">
    <div class="col-xs-3" style="padding-top:40px">_______________</div>
    <div class="col-xs-3" style="padding-top:40px">_______________</div>
    <div class="col-xs-3" style="padding-top:40px">_______________</div>
</div>
</body>

</html>
