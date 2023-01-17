@extends('layouts.reportmaster')
@section('content')

      <table >
        <thead>
          <tr>
          <th class="no">id</th>
          <th class="no">Bill No.</th>
          <th class="no">Bill Date</th>
          <th class="no">Client</th>
          <th class="no">Non Taxable Amount</th>
          <th class="no">Taxable Amount</th>
          <th class="no">Tax Amount</th>
          <th class="no">Total</th>
          <th class="no">Paid Amount</th> 
          <th class="no">Pay Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $n= 0;
          ?>
          @foreach($orders as $odk => $o)
          <tr>
              <td>{!! $o->id !!}</td>
              <td>{!! $o->bill_no !!}</td>
              <td> {{ $o->bill_date }}</td>

              <td > {{ $o->client->name }}  </td>
              <td>{!! number_format($o->non_taxable_amount,2) !!}</td>
              <td>{!! number_format($o->taxable_amount,2) !!}</td>
              <td>{!! number_format($o->tax_amount,2) !!}</td>
              <td>{!! number_format($o->total,2) !!}</td>
               <?php
                $paid_amount= \TaskHelper::getPurchasePaymentAmount($o->id);
               ?>
              <td>{!! number_format($paid_amount,2) !!}</td>

              <td>{{$o->payment_status}}</td>
          </tr>
         @endforeach
          
        </tbody> 
       
      </table>
@endsection