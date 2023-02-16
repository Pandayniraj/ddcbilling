<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Receipt</title>

    <!-- Normalize or reset CSS with your favorite library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

    <!-- Load paper.css for happy printing -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

    <!-- Set page size here: A5, A4 or A3 -->
    <!-- Set also "landscape" if you need -->
    <style>@page { size: A5 landscape }</style>

    <!-- Custom styles for this document -->
    <link href='https://fonts.googleapis.com/css?family=Roboto:700' rel='stylesheet' type='text/css'>
    <style>
        body   { font-family: serif }
        h1     { font-family: 'Roboto', cursive; font-size: 40pt; line-height: 18mm}
        h2, h3 { font-family: 'Roboto', cursive; font-size: 14pt; line-height: 7mm }
        h4     { font-size: 22pt; line-height: 10mm }
        h2 + p { font-size: 18pt; line-height: 5mm }
        h3 + p { font-size: 14pt; line-height: 5mm }
        li     { font-size: 11pt; line-height: 4mm }

        h1      { margin: 0 }
        h1 + ul { margin: 2mm 0 5mm }
        h2, h3  { margin: 0 3mm 3mm 0; float: left }
        h2 + p,
        h3 + p  { margin: 0 0 3mm 50mm }
        h4      { margin: 2mm 0 0 50mm; border-bottom: 2px solid black }
        h4 + ul { margin: 5mm 0 0 50mm }
        article { border: 4px double black; padding: 5mm 10mm; border-radius: 3mm }
    </style>
</head>

<!-- Set "A5", "A4" or "A3" for class name -->
<!-- Set also "landscape" if you need -->
<body class="A5 landscape">

<!-- Each sheet element should have the class "sheet" -->
<!-- "padding-**mm" is optional: you can set 10, 15, 20 or 25 -->
<section class="sheet padding-20mm">
    <!-- @dump($organization) -->
    <div class="reg-div">
        <p>Vat No. {{$organization->vat_id}}</p>
    </div>
    <h5 style="text-align: center;">{{ $organization->organization_name}}</h5>
    <div style="text-align: center;">
        <div class="address">{{$organization->address}}, {{$organization->websites}}</div>
        <div class="email">{{$organization->email}}, {{$organization->phone}}</div>
        <div class="title">Receipt</div>
    </div>
    <div>Receipt No.: {{$payment_detail->id}}</div>
    <div style="text-align: right">Date: {{date('Y-m-d')}}</div>


    <article>
        <h2>Received from:</h2>
        <p>{{$payment_detail->invoice->client->name??''}}</p>

        <h3>For:</h3>
        <p>Invoice #{{$payment_detail->invoice->id}}</p>

        <h4 >Rs. {{$payment_detail->amount}} /-</h4>
        <ul>
            <li>Tax: included</li>
            <li>Paid by: {{$payment_detail->paidBy->name}}</li>
            <li>No. {{$payment_detail->reference_no}}</li>
        </ul>
        <?php
        function getPaisa($number)
        {
            $no = round($number);
            $decimal = round($number - ($no = floor($number)), 2) * 100;
            $words = array(
                0 => '',
                1 => 'One',
                2 => 'Two',
                3 => 'Three',
                4 => 'Four',
                5 => 'Five',
                6 => 'Six',
                7 => 'Seven',
                8 => 'Eight',
                9 => 'Nine',
                10 => 'Ten',
                11 => 'Eleven',
                12 => 'Twelve',
                13 => 'Thirteen',
                14 => 'Fourteen',
                15 => 'Fifteen',
                16 => 'Sixteen',
                17 => 'Seventeen',
                18 => 'Eighteen',
                19 => 'Nineteen',
                20 => 'Twenty',
                30 => 'Thirty',
                40 => 'Forty',
                50 => 'Fifty',
                60 => 'Sixty',
                70 => 'Seventy',
                80 => 'Eighty',
                90 => 'Ninety');
            $paise = ($decimal) ? ' and ' . ($words[$decimal - $decimal % 10]) . " " . ($words[$decimal % 10]) . ' Paisa' : '';
            return $paise;
        }

        function numberFomatter($number)
        {
            $constnum = $number;
            $no = floor($number);
            $point = round($number - $no, 2) * 100;
            $hundred = null;
            $digits_1 = strlen($no);
            $i = 0;
            $str = array();
            $words = array('0' => '', '1' => 'one',
                '2' => 'two',
                '3' => 'three',
                '4' => 'four', '5' => 'five', '6' => 'six',
                '7' => 'seven', '8' => 'eight', '9' => 'nine',
                '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
                '13' => 'thirteen', '14' => 'fourteen',
                '15' => 'fifteen', '16' => 'sixteen',
                '17' => 'seventeen',
                '18' => 'eighteen',
                '19' => 'nineteen',
                '20' => 'twenty',
                '30' => 'thirty',
                '40' => 'forty',
                '50' => 'fifty',
                '60' => 'sixty',
                '70' => 'seventy',
                '80' => 'eighty',
                '90' => 'ninety');
            $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
            while ($i < $digits_1) {
                $divider = ($i == 2) ? 10 : 100;
                $number = floor($no % $divider);
                $no = floor($no / $divider);
                $i += ($divider == 10) ? 1 : 2;
                if ($number) {
                    $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                    $hundred = ($counter == 1 && $str[0]) ? '' : null;
                    $str [] = ($number < 21) ? $words[$number] .
                        " " . $digits[$counter] . $plural . " " . $hundred
                        :
                        $words[floor($number / 10) * 10]
                        . " " . $words[$number % 10] . " "
                        . $digits[$counter] . $plural . " " . $hundred;
                } else $str[] = null;
            }

            $str = array_reverse($str);
            $result = implode('', $str);
            $points = getPaisa($constnum);
            return $result . ' Rupees' . $points;
        }
        ?>
        <?php
        // $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
        ?>
        <h2>In Words:</h2>
        <p style="text-transform: capitalize;">{{numberFomatter($payment_detail->amount)}}</p>

        <p style="text-align: right;">....................</p>
        <p style="text-align: right;">Received By</p>
    </article>
</section>

<script type="text/javascript">
    window.print();
    window.onafterprint = () => window.close();
</script>
</body>

</html>
