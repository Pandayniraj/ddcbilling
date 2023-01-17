<table style="width:100%; text-align:left; font-family:Gotham, 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 10px;">

    <thead>
        <tr>
            <td colspan="8">
                <img src="{{env('APP_URL')}}/{{ '/org/'.$organization->logo }}" alt="" class="img-responsive" style="width:200px;">
                <br/>
                <h1 style="font-size:30px; margin-top:10px; margin-bottom:0px; font-weight: bold; color: #00aef0;">Leads Report</h1>
                <p style="margin-top: 0; margin-bottom: 15px; padding-top: 0;">Between: {{ $start_date ." - ".$end_date}} </p>
            </td>
        </tr>
        <tr style="background-color: navy;color:white">
            <th style="text-align: left;">Date</th>
            <th style="text-align: left;">Sales Rep</th>
            <th style="text-align: left;">Name</th>
            <th style="text-align: left;">Contacts</th>
            <th style="text-align: left;">Address</th>
            <th style="text-align: left;">Description</th>
            <th style="text-align: left;">Product</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $dk => $dv)
        <tr style="background-color: #f9f9f9; border:1px solid #ccc;">
            <td width="50%" style="text-align:left;">{{ $dv->created_at }}</td>
            <td width="50%" style="text-align:left;">{{ TaskHelper::getUserData($dv->user_id)->username }}</td>
            <td width="50%" style="text-align:left;">{{ $dv->title.' '.$dv->name }}</td>
            <td width="50%" style="text-align:left;">{{ mb_substr($dv->mob_phone,0,17)}} , 
                {{ mb_substr($dv->home_phone,0,17) }}</td>
            <td width="50%" style="text-align:left;">{{ $dv->address_line_1 }}</td>
            <td width="50%" style="text-align:left;">{{ $dv->description }} - {{ $dv->email}}</td>
            <td width="50%" style="text-align:left;">{{ $dv->product->name }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<hr>
<p style="text-align: center;">downloaded from Meronetwork CRM</p>