@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Request</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>
                
        <!-- .row -->
        <div class="row">
            <div class="col-md-12">
                <span class="pull-right"><a class="btn btn-warning" href="/training"><< Back</a></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                &nbsp;
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <h3 class="box-title">Join {{ $training->Training->name }}</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                &nbsp;
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Provider</label><br>
                            {{ $training->Hygenist->name }}
                        </div>
                        <div class="col-md-6">
                            <label>Link</label><br>
                            {{ $training->TrainingHygenist->link }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            &nbsp;
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label>Joining instructions</label><br>
                            {{ $training->instructions }}
                        </div>
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