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
                        Ooops!  Looks like you forgot to log off another site {{ $logOff->Site->name }}. What time did you leave?
                        <br>
                        <form method="post" action="/laterSiteLogoff">
                            @csrf
                            <input type="hidden" name="logOff" value="{{ $logOff->id }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="col-md-2">
                                        <select class="form-control form-control-line" name="hour">
                                            <option value="00">0</option>
                                            <option value="01">1</option>
                                            <option value="02">2</option>
                                            <option value="03">3</option>
                                            <option value="04">4</option>
                                            <option value="05">5</option>
                                            <option value="06">6</option>
                                            <option value="07">7</option>
                                            <option value="08">8</option>
                                            <option value="09">9</option>
                                            <option value="10">10</option>
                                            <option value="11">11</option>
                                            <option value="12">12</option>
                                            <option value="13">13</option>
                                            <option value="14">14</option>
                                            <option value="15">15</option>
                                            <option value="16" selected>16</option>
                                            <option value="17">17</option>
                                            <option value="18">18</option>
                                            <option value="19">19</option>
                                            <option value="20">20</option>
                                            <option value="21">21</option>
                                            <option value="22">22</option>
                                            <option value="23">23</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1">
                                        :
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-control form-control-line" name="minute">
                                            <option value="00" selected>00</option>
                                            <option value="05">05</option>
                                            <option value="10">10</option>
                                            <option value="15">15</option>
                                            <option value="20">20</option>
                                            <option value="25">25</option>
                                            <option value="30">30</option>
                                            <option value="35">35</option>
                                            <option value="40">40</option>
                                            <option value="45">45</option>
                                            <option value="50">50</option>
                                            <option value="55">55</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <input type="submit" class="btn btn-primary" value="Logoff {{ $logOff->Site->name }}">
                                </div>
                            </div>
                        </form>
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