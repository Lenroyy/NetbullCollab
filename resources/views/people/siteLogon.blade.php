@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Site sign in</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>
                
        <!-- .row -->
        @if(is_object($logon))
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <h3 class="box-title">You've signed in</h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        You have signed in to {{ $site->name }}, at {{ date('d-m-Y H:i', $logon->time_in) }}.<br><br>
                        If you have been directed to this page from logging into a zone, you will need to log into the zone again as you weren't already signed into the site.<br><br>
                        @if($member == "not ok")
                            Please make sure you join the organisation you work with.  You will be allowed to sign into sites for up to 3 days.<br><br>
                            Please send a request to join the organisation you are working for with their code, then get them to accept the request.<br>
                            Alternatively they can request you to join using your code (found on your profile under the memberships tab) and you can accept the request.<br><br>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <h3 class="box-title">Sign in denied</h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        You are not allowed to sign into site {{ $site->name }}, because its been over 3 days now and you haven't joined an organisation.<br><br>
                        Please send a request to join the organisation you are working for with their code, then get them to accept the request.<br><br>
                        Alternatively they can request you to join using your code (found on your profile under the memberships tab) and you can accept the request.<br><br>
                    </div>
                </div>
            </div>
        @endif
        <!-- /.row -->

        <!-- JS DEPENDENCIES -->
        @include('components.footer') 
        <!-- END JS DEPENDENCIES -->
                    
        <!-- Page specific Javascript -->

    @endsection