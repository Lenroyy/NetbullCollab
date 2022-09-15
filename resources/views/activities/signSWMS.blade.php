@extends('components.sideMenu')

    @section('content')

    <!-- Page specific CSS -->     
    <!--<link href="{{ asset('assets/plugins/bower_components/signature_pad/docs/css/signature-pad.css') }}" rel="stylesheet" type="text/css" />-->
    <!-- Page Content -->
    <div class="row bg-title">
        <!-- .page title -->
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">SWMS Signature</h4>
        </div>
        <!-- /.page title -->
        <!-- .breadcrumb -->
            {!! $breadcrumbs !!}
        <!-- /.breadcrumb -->
    </div> 

    <!-- .row -->
    <form class="form-horizontal form-material" action="/postSignature/{{ $assessment->id }}" method="POST" id="signatureForm">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    Signature:
                    <div id="signature-pad" class="signature-pad">
                        <div class="signature-pad--body">
                            <canvas style="background: white;" height="250" width="500"></canvas>
                        </div>
                        <div class="signature-pad--footer">
                            <div class="signature-pad--actions">
                                <div>
                                    <button type="button" class="btn btn-danger" data-action="clear">Clear</button>
                                    <button type="button" class="btn btn-warning" data-action="undo">Undo</button>
                                    <span class="pull-right">
                                        <button type="button" class="btn btn-primary" data-action="save-png" id="savePNGButton">Finish</button>
                                        <input type="hidden" name="thisOne" id="thisOne" value="boo">
                                    </span>
                                </div>
                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- JS DEPENDENCIES -->
    @include('components.footer') 
    <!-- END JS DEPENDENCIES -->
                
    <!-- Page specific Javascript -->   
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@3.0.0-beta.3/dist/signature_pad.min.js"></script>
    
    <script>
        var wrapper = document.getElementById("signature-pad");
        var clearButton = wrapper.querySelector("[data-action=clear]");
        var undoButton = wrapper.querySelector("[data-action=undo]");
        var savePNGButton = wrapper.querySelector("[data-action=save-png]");
        var canvas = wrapper.querySelector("canvas");
        var signaturePad = new SignaturePad(canvas, {
        // It's Necessary to use an opaque color when saving image as JPEG;
        // this option can be omitted if only saving as PNG or SVG
        backgroundColor: 'rgb(255, 255, 255)'
        });

        // Adjust canvas coordinate space taking into account pixel ratio,
        // to make it look crisp on mobile devices.
        // This also causes canvas to be cleared.
        
        /*
        function resizeCanvas() {
            // When zoomed out to less than 100%, for some very strange reason,
            // some browsers report devicePixelRatio as less than 1
            // and only part of the canvas is cleared then.
            var ratio =  Math.max(window.devicePixelRatio || 1, 1);

            // This part causes the canvas to be cleared
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);

            // This library does not listen for canvas changes, so after the canvas is automatically
            // cleared by the browser, SignaturePad#isEmpty might still return false, even though the
            // canvas looks empty, because the internal data of this library wasn't cleared. To make sure
            // that the state of this library is consistent with visual state of the canvas, you
            // have to clear it manually.
            signaturePad.clear();
        }

        // On mobile devices it might make more sense to listen to orientation change,
        // rather than window resize events.
        window.onresize = resizeCanvas;
        //resizeCanvas();
        */
        
        function download(dataURL, filename) 
        {
            if (navigator.userAgent.indexOf("Safari") > -1 && navigator.userAgent.indexOf("Chrome") === -1) 
            {
                window.open(dataURL);
            } 
            else 
            {
                var blob = dataURLToBlob(dataURL);
                var url = window.URL.createObjectURL(blob);

                var a = document.createElement("a");
                a.style = "display: none";
                a.href = url;
                a.download = filename;

                document.body.appendChild(a);
                a.click();

                window.URL.revokeObjectURL(url);
            }
        }

        // One could simply use Canvas#toBlob method instead, but it's just to show
        // that it can be done using result of SignaturePad#toDataURL.
        function dataURLToBlob(dataURL) {
        // Code taken from https://github.com/ebidel/filer.js
        var parts = dataURL.split(';base64,');
        var contentType = parts[0].split(":")[1];
        var raw = window.atob(parts[1]);
        var rawLength = raw.length;
        var uInt8Array = new Uint8Array(rawLength);

        for (var i = 0; i < rawLength; ++i) {
            uInt8Array[i] = raw.charCodeAt(i);
        }

        return new Blob([uInt8Array], { type: contentType });
        }

        clearButton.addEventListener("click", function (event) {
            signaturePad.clear();
        });

        undoButton.addEventListener("click", function (event) {
            var data = signaturePad.toData();

            if (data) 
            {
                data.pop(); // remove the last dot or line
                signaturePad.fromData(data);
            }
        });



        savePNGButton.addEventListener("click", function (event) {
        if (signaturePad.isEmpty()) {
            alert("Please provide a signature first.");
        } else {
            var dataURL = signaturePad.toDataURL();
            document.getElementById("thisOne").value = dataURL
            document.getElementById("signatureForm").submit(); 
        }
        });


        var canvas = document.querySelector("canvas");

        var signaturePad = new SignaturePad(canvas);       

        /*
        function resizeCanvas() {
            var ratio =  Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear(); // otherwise isEmpty() might return incorrect value
        }

        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();
        */
    </script>

@endsection