@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->     
        <link href="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                {{-- <h4 class="page-title">{{ $site->name }} History on {{ $date }}</h4> --}}
                <h4 class="page-title">{{  isset($site['name']) ? count($site['name']) : 0 }} History on {{ $date }}</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div> 

        <!-- .row -->
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <ul class="nav nav-tabs tabs customtab">
                        <li class="active tab">
                            <a href="#people" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="fa-tasks"></i></span> <span class="hidden-xs">People</span> </a>
                        </li>
                        <li class="tab">
                            <a href="#swms" data-toggle="tab"> <span class="visible-xs"><i class="ti-notepad"></i></span> <span class="hidden-xs">SWMS <span class="badge">{{ count($assessments) }}</span></span> </a>
                        </li>
                        <li class="tab">
                            <a href="#assets" data-toggle="tab"> <span class="visible-xs"><i class="ti-truck"></i></span> <span class="hidden-xs">Controls <span class="badge">{{ count($controls) }}</span></span> </a>
                        </li>             
                    </ul>


                    <div class="tab-content">
                        <div class="tab-pane active" id="people">
                            <div class="table-responsive">
                                <table id="peopleTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Member of</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($people as $person)
                                        <tr @if($person['name'] == $person['rName']) style="cursor: pointer;" onClick="window.location.href='/editProfile/{{ $person['id'] }}'" @endif>    
                                            <td>{{ $person['name'] }}</td>
                                            <td>{{ $person['memberOf'] }}</td>
                                            <td>@if($person['name'] == $person['rName']) {{ $person['mobile'] }} @else - @endif</td>
                                            <td>@if($person['name'] == $person['rName']) {{ $person['email'] }} @else - @endif</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                        </div>
                    
                        <div class="tab-pane" id="swms">
                            <div class="table-responsive">
                                <table id="peopleTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>SWMS</th>
                                            <th>Person</th>
                                            <th>Member of</th>
                                            <th>Score</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assessments as $assessment)
                                            <tr style="cursor: pointer;" onClick="window.location.href='/siteVisit/swms/{{ $assessment['id'] }}'">    
                                                <td>{{ $assessment['assessment'] }}</td>
                                                <td>{{ $assessment['name'] }}</td>
                                                <td>{{ $assessment['memberOf'] }}</td>
                                                <td>{{ $assessment['score'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="assets">
                            <div class="table-responsive">
                                <table id="peopleTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Asset type</th>
                                            <th>Serial number</th>
                                            <th>Map</th>
                                            <th>Zone</th>
                                            <th>Person</th>
                                            <th>Member of</th>
                                            <th>Usage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($controls as $control)
                                            <tr>    
                                                <td>{{ $control['type'] }}</td>
                                                <td>{{ $control['serial'] }}</td>
                                                <td>{{ $control['map'] }}</td>
                                                <td>{{ $control['zone'] }}</td>
                                                <td>{{ $control['name'] }}</td>
                                                <td>{{ $control['memberOf'] }}</td>
                                                <td>{{ $control['time'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
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
        <script src="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
        <script>
            jQuery('#datepicker-start').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });

            jQuery('#datepicker-due').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });

            jQuery('#datepicker-completed').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });
        </script>

    @endsection