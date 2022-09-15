@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->     
        <link href="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Site person history</h4>
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
                            <a href="#people" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="fa-notepad"></i></span> <span class="hidden-xs">Details</span> </a>
                        </li>
                        <li class="tab">
                            <a href="#swms" data-toggle="tab"> <span class="visible-xs"><i class="ti-notepad"></i></span> <span class="hidden-xs">Activities @if(count($activities) > 0)<span class="badge">{{ count($activities) }}</span>@endif</span> </a>
                        </li>
                        <li class="tab">
                            <a href="#assets" data-toggle="tab"> <span class="visible-xs"><i class="ti-truck"></i></span> <span class="hidden-xs">Controls @if(count($controls) > 0)<span class="badge">{{ count($controls) }}</span>@endif</span> </a>
                        </li>             
                    </ul>


                    <div class="tab-content">
                        <div class="tab-pane active" id="people">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Name</label>
                                    {{ $name }}
                                </div>
                                <div class="col-md-4">
                                    <label>Member of</label>
                                    {{ $membership->Organisation->name }}
                                </div>
                            </div>
                            
                        </div>
                    
                        <div class="tab-pane" id="swms">
                            <div class="table-responsive">
                                <table id="peopleTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Activity</th>
                                            <th>Map</th>
                                            <th>Zone</th>
                                            <th>Assessments</th>
                                            <th>Date</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activities as $activity)
                                        <tr style="cursor: pointer;" onClick="window.location.href='/logActivity/{{ $activity['history']->id }}'">    
                                            <td>{{ $activity['history']->Activity->name }}</td>
                                            <td>{{ $activity['history']->Zone->Sites_Map->name }}</td>
                                            <td>{{ $activity['history']->Zone->name }}</td>
                                            <td>{{ $activity['assessments'] }}</td>
                                            <td>{{ $activity['history']->created_at->format('d-m-Y') }}</td>
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
                                            <th>Date</th>
                                            <th>Asset type</th>
                                            <th>Serial number</th>
                                            <th>Map</th>
                                            <th>Zone</th>
                                            <th>Usage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($controls as $control)
                                            <tr>    
                                                <td>{{ $control['date'] }}</td>
                                                <td>{{ $control['type'] }}</td>
                                                <td>{{ $control['serial'] }}</td>
                                                <td>{{ $control['map'] }}</td>
                                                <td>{{ $control['zone'] }}</td>
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