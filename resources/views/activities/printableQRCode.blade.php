<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('css/light/style.css') }}" rel="stylesheet">
    <title>Printable QR Code</title>
</head>
<body>
    <div  class="printable_qr_code">
        <b>
            @if($site->zone_qr_code_function == 0)
                SCAN QR CODE TO START ACTIVITY IN :: 
            @else
                SCAN QR CODE TO LOG IN AND OUT OF ENTRY IN :: 
            @endif{{ $zone->name }}
            <br><br><br><br>
                {!! QrCode::generate($qrAddress); !!}
            <br><br><br><br>
            {{$qrAddress}}
        </b>
    </div>
    
</body>
</html>