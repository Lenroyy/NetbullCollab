@extends('components.loginHeader')

@section('content')

        <!-- Page specific CSS -->
                
        <!-- .row -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        Hi {{ $name }},

                        <p>{!! $content !!}</p>

                        <p>Regards,<br><br></p>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->

        <!-- JS DEPENDENCIES -->
        @include('components.footer')
        <!-- END JS DEPENDENCIES -->
                    
        <!-- Page specific Javascript -->

    @endsection

