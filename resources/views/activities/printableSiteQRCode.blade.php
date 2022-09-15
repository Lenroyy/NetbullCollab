<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ asset('css/light/style.css') }}" rel="stylesheet">
    <title>Printable Site QR Code</title>
</head>
<body>
    {{-- <div style="text-align: center;"> --}}
    <div  class="printable_qr_code">
        <b>
            SCAN QR CODE TO LOG ONTO :: {{ $site->name }}
            <br><br><br><br>
            <div class="Qr_code_size">
                {!! QrCode::generate($qrAddress); !!}
            </div>
        </b>
    </div>
</body>
</html>
