@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Entry logged</h4>
            </div>
            <!-- /.page title -->
        </div>
                
        <!-- .row -->
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title">You have logged entry</h3><br><br>Site :: {{ $site->name }}<br>Zone :: {{ $zone->name }}</h3>
                </div>
            </div>
        </div>
        <!-- /.row -->

        <!-- JS DEPENDENCIES -->
        @include('components.footer') 
        <!-- END JS DEPENDENCIES -->
                    
        <!-- Page specific Javascript -->

    @endsection