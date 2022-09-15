@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Site History</h4>
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
                            <a href="#visits" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="fa-flag-o"></i></span> <span class="hidden-xs"> Visits</span> </a>
                        </li>
                        <li class="tab">
                            <a href="#people" data-toggle="tab"> <span class="visible-xs"><i class="fa-male"></i></span> <span class="hidden-xs"> People </span> </a>
                        </li>                        
                        <li class="tab">
                            <a href="#swms" data-toggle="tab"> <span class="visible-xs"><i class="ti-notepad"></i></span> <span class="hidden-xs"> Activities </span> </a>
                        </li>                        
                        <li class="tab">
                            <a href="#assets" data-toggle="tab"> <span class="visible-xs"><i class="ti-truck"></i></span> <span class="hidden-xs"> Controls </span> </a>
                        </li>                        
                    </ul>


                    <div class="tab-content">
                        <div class="tab-pane active" id="visits">
                            <div class="table-responsive">
                                <table id="visitsTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th>Date</th>
                                            <th>Activities</th>
                                            <th>SWMS</th>
                                            <th>People</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($visits as $v)
                                            <tr style="cursor: pointer;" onClick="window.location.href='/siteVisit/visit/{{ $site }}/{{ $v['date'] }}'">    
                                                <td>{{ $v['id'] }}</td>
                                                <td>{{ $v['date'] }}</td>
                                                <td>{{ $v['activities'] }}</td>
                                                <td>{{ $v['assessments'] }}</td>
                                                <td>{{ $v['people'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    
                        <div class="tab-pane" id="people">
                            <div class="table-responsive">
                                <table id="peopleTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Member of</th>
                                            <th>Email</th>
                                            <th>Mobile</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($people as $p)
                                            <tr style="cursor: pointer;" onClick="window.location.href='/siteVisit/person/{{ $p['id'] }}/{{ $site }}'">    
                                                <td >{{ $p['name'] }}</td>
                                                <td >{{ $p['memberOf'] }}</td>
                                                <td >@if($p['name'] == $p['rName']){{ $p['email'] }}@else - @endif</td>
                                                <td >@if($p['name'] == $p['rName']){{ $p['mobile'] }}@else - @endif</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="swms">
                            <div class="table-responsive">
                                <table id="swmsTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th>Date</th>
                                            <th>Activity</th>
                                            <th>Person</th>
                                            <th>Map</th>
                                            <th>Zone</th>
                                            <th>Assessments</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($swms as $ass)
                                            <tr style="cursor: pointer;" onClick="window.location.href='/logActivity/{{ $ass['id'] }}'">    
                                                <td>{{ $ass['id'] }}</td>
                                                <td>{{ $ass['date'] }}</td>
                                                <td>{{ $ass['activity'] }}</td>
                                                <td>{{ $ass['person'] }}</td>
                                                <td>{{ $ass['map'] }}</td>
                                                <td>{{ $ass['zone'] }}</td>
                                                <td>{{ $ass['assessments'] }}</td>
                                                <td>{{ $ass['time'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="assets">
                            <div class="table-responsive">
                                <table id="assetsTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Serial number</th>
                                            <th>Date arrived</th>
                                            <th>Date removed</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($controls as $control)
                                            <tr style="cursor: pointer;" onClick="window.location.href='/siteVisit/asset/{{ $control['id'] }}/{{ $site }}'">    
                                                <td >{{ $control['type'] }}</td>
                                                <td >{{ $control['serial'] }}</td>
                                                <td >{{ $control['arrived'] }}</td>
                                                <td >{{ $control['removed'] }}</td>
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
        <script src="{{ asset('assets/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/bower_components/toast-master/js/jquery.toast.js') }}"></script>

        <script>
        $(document).ready(function() {
            $('#visitsTable').DataTable({
                "displayLength": 100,
            });

            $('#peopleTable').DataTable({
                "displayLength": 100,
            });

            $('#swmsTable').DataTable({
                "displayLength": 100,
            });

            $('#assetsTable').DataTable({
                "displayLength": 100,
            });

        });
                
        </script>

    @endsection