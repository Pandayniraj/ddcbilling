<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<style type="text/css">
    .footer {
        text-align: left;
        position: relative;
        margin: 2px auto;
        padding: 0;
        width: 100%;
        height: auto;
        border-collapse: collapse;
        text-align: center;
    }


table{
    font-size: 14px !important;
    padding-top: 2px !important; /*cancels out browser's default cell padding*/
    padding-bottom: 2px !important; /*cancels out browser's default cell padding*/
}

td{

  padding-top: 2px !important; /*cancels out browser's default cell padding*/
  padding-bottom: 2px !important; /*cancels out browser's default cell padding*/
}

th{
  border: 1px dotted #999 !important;
    padding-top: 2px !important; /*cancels out browser's default cell padding*/
  padding-bottom: 2px !important; /*cancels out browser's default cell padding*/
}


 @page {
    size: auto;
    margin: 0;
  }

  body{
    padding-left: 1.3cm;
    padding-right: 1.3cm;
    padding-top: 1.3cm;
  }

</style>
<body>
    <table>
        <tr>
            <td style="">
            <img style="" src="{{ public_path('org/'.Auth::user()->organization->logo) }}">
            </td>
            <th style="text-align: center; font-size: 22px;font-weight: bold">
                <h2>RECEIPT</h2>
            </th>
        </tr>
        <tr>
            <td rowspan="3" style="width: 200px;padding-right: 90px">
            <div style="font-size:16px;font-weight:bold">{{ \Auth::user()->organization->organization_name }} </div>
                        <div>{{ \Auth::user()->organization->address }}</div>
                        <div>Phone: {{ \Auth::user()->organization->phone }}</div>
                Tax ID: {{ \Auth::user()->organization->vat_id }}
                
                <strong>{{ $income->customers->name}}</strong><br>
                {{ $income->customers->physical_address}}<br>
            </td>
            <td valign="top">
                <table class="table table-striped table-responsive" style="padding-right: 50px">
                    <tr>
                        <td style="white-space: nowrap;">Date: </td>
                        <td>{{date('d M Y',strtotime($income->date_received))}} / {{ TaskHelper::getNepaliDate($income->date_received) }}

                        </td>
                    </tr>
                    <tr>
                        <td style="white-space: nowrap;">Reference No: </td>
                        <td>{{ $income->reference_no }}</td>
                    </tr>
                    <tr>
                        <td valign="top" style="white-space: nowrap;">Receipt Method: </td>
                        <td>{{ ucfirst($income->received_via) }}</td>
                    </tr>
                    <tr>
                        <td valign="top" style="white-space: nowrap;">Receipt #: </td>
                        <td>00{{\FinanceHelper::getAccountingPrefix('INCOME_PRE')}}{{ $income->id }}</td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>


    <table class="footer" cellspacing="0" style="">
        <tr>
            <th style="background-color: #f5f5f5; padding: 10px; border: 1px dotted black;text-align: left;text-align: left;text-indent: 3px" colspan="5">Description</th>
            <td style="padding: 10px; border: 1px dotted black;text-align: left;" colspan="5">{{ $income->description }}</td>
        </tr>
        <tr>
            <th style="background-color: #f5f5f5; padding: 10px; border: 1px dotted black;text-align: left;text-indent: 3px" colspan="5">Received Amount</th>
            <td style="padding: 10px; border: 1px dotted black;text-align: left;text-indent: 3px" colspan="5"><strong>{{ env('APP_CURRENCY') }} {{ number_format($income->amount,2) }}</strong></td>
        </tr>
    </table>
    
            In Words:{{\FinanceHelper::numberRupeeFomatter($income->amount)}}
   <br/><br/>.........................
   <br/>
    Sincerely
    <p> {{ \Auth::user()->organization->organization_name }}</p>
    Printed by: {{\Auth::user()->username}}<br/>
                    Printed Time: {{ date("F j, Y, g:i a") }} <br/>
                     <p> If you have any questions or concerns, please contact us. Thank You</p>
</body>
</html>
