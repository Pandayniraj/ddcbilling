

<table style="width:100%; text-align:center; padding: 30px 0; box-shadow: 0 1.2rem 1.8rem 0 rgba(0,0,0,0.24),0 1.7rem 5rem 0 rgba(0,0,0,0.19); -webkit-box-shadow: 0 1.2rem 1.8rem 0 rgba(0,0,0,0.24),0 1.7rem 5rem 0 rgba(0,0,0,0.19); font-family:Gotham, 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10px;">

    <thead>
        <tr>
            <td colspan="9" style="text-align: right;">Fiscal Year: {{$fiscal_year}}</td>
        </tr>
        <tr>
            <td colspan="9" style="text-align: right;">
              @if($months) Month: {{$months}} 
              @else
              Date: {{ date('dS M y', strtotime($startdate)) }} - {{ date('dS M y', strtotime($enddate)) }}
              @endif</td> 
        </tr>

        <tr>

        <th colspan="3" style="text-align: center; background-color: #eee" >Invoice</th>
        <th colspan="5"></th>
        <th colspan="2" style="text-align: center;  background-color: #eee">Taxable Purchase</th>
    
    </tr>
    <tr>
            <th>Date</th>
        <th>Bill No</th>
        <th>Supplier’s Name</th>
        <th>Supl. PAN Number</th>
        <th>Total Purchase</th>
        <th>Non Tax Purchase</th>
        <th>Exp. Purchase</th>
        <th>Discount</th> 
        <th>Amount</th>
        <th>Tax(Rs)</th>

    </tr>
</thead>
<tbody>
  <?php  
  $taxable_amount = 0;
  $tax_amount = 0;
  ?>
    @foreach($purchase_book as $pur_bks)
<tr>
      <td>{{ date('dS M y', strtotime($pur_bks->ord_date)) }}<br/>
                              <?php
                                        $temp_date = explode(" ",$pur_bks->ord_date );
                                        $temp_date1 = explode("-",$temp_date[0]);
                                        $cal = new \App\Helpers\NepaliCalendar();
                                        //nepali date
                                        $a = $temp_date1[0];
                                        $b = $temp_date1[1];
                                        $c = $temp_date1[2];
                                        $d = $cal->eng_to_nep($a,$b,$c);
                                    
                                         $nepali_date = $d['date'].' '.$d['nmonth'] .', '.$d['year'];
                                        ?>

                         <small> {!! $nepali_date !!}</small>

                        </td>
    <td>#{{$pur_bks->bill_no}} </td>
    <td>{{$pur_bks->client->name}}</td>
        <td>{{$pur_bks->client->vat}}</td>
    <td>{{$pur_bks->taxable_amount + $pur_bks->tax_amount}}</td>
    <td></td>
    <td></td>
    <td></td>
    <td>{{$pur_bks->taxable_amount}}</td>
    <?php  
      $taxable_amount = $taxable_amount + $pur_bks->taxable_amount;
      $tax_amount = $tax_amount +  $pur_bks->tax_amount;
    ?>
 <td>{{$pur_bks->tax_amount}}</td>
</tr>
@endforeach
<tr>
    <th>Total Amount</th>
    <td></td>
     <td></td>
      <td></td>
       <td></td>
        <td></td>
         <td></td>
          <td></td>
    <td>{{$taxable_amount}}</td>
    <td>{{$tax_amount}}</td>
    </tr>



    </tbody>
</table>

<hr>
<p style="text-align: center;">Sent from MEROCRM</p>