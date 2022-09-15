@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/bower_components/gridster/css/jquery.gridster.css') }}"> 
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/bower_components/gridster/css/jquery.dsmorse-gridster.min.css') }}"> 
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/bower_components/gridster/css/gridster.custom.css') }}"> 
        <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/highcharts/css/highcharts.css') }}" charster="utf-8"></script>
        <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        <link href="{{ asset('css/dots.css') }}" rel="stylesheet">
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Dashboard</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>
                
        <div class="row">
            <div class="col-md-12">
                <div class="btn-group pull-right">
                    <div class="pull-right">
                        <button class="btn btn-primary btn-sm light-widget-button" data-toggle="modal" data-target="#widgets"><i class="fa fa-plus  "></i> Widget</button>&nbsp;&nbsp;&nbsp;
                        <button class="btn btn-success btn-sm js-seralize"><i class="ft-save white"></i> Save</button>
                    </div>
                </div>
            </div>
        </div>
        <br>

        @include('components.widgetModal') 
                
        <!-- .row -->
        <div class="row">
            <div class="col-md-12">
                <!-- Gridster Dashboard -->
                <section>
                    <div class="gridster">
                        <ul style="list-style: none;" id="grd"></ul> 
                    </div>
                </section>
                <!-- END Gridster Dashboard --> 
            </div>
        </div>
        <!-- /.row -->

        <!-- JS DEPENDENCIES -->
        @include('components.footer') 
        <!-- END JS DEPENDENCIES -->
                    
        <!-- Page specific Javascript -->
        <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.collision.js') }}" charster="utf-8"></script>
        <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.draggable.js') }}" charster="utf-8"></script>
        <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.coords.js') }}" charster="utf-8"></script>
        <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.dsmorse-gridster.js') }}" charster="utf-8"></script>
        <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.dsmorse-gridster.with-extras.js') }}" charster="utf-8"></script>
        <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.gridster.js') }}" charster="utf-8"></script>
        <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.gridster.js') }}" charster="utf-8"></script>
        <script src="{{ asset('assets/plugins/bower_components/toast-master/js/jquery.toast.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/highcharts/highcharts.js') }}" charster="utf-8"></script>

        @include('js.dashboard') 

        <script>
        $(document).ready(function() {
            if("{{ $alert }}")
            {
                $.toast({
                    heading: 'Success',
                    text: '{{ $alert }}',
                    position: 'top-right',
                    loaderBg:'#181a35;',
                    icon: 'info',
                    hideAfter: 3000, 
                    stack: 6
                });
            }
        });
                
        </script>

    @endsection