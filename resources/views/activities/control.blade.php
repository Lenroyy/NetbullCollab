@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->     
        <link href="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Site asset history</h4>
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
                            <a href="#details" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="fa-notepad"></i></span> <span class="hidden-xs">Details</span> </a>
                        </li>
                        <li class="tab">
                            <a href="#usage" data-toggle="tab"> <span class="visible-xs"><i class="ti-notepad"></i></span> <span class="hidden-xs">Usage @if(count($usage) > 0)<span class="badge">{{ count($usage) }}</span>@endif</span> </a>
                        </li>  
                        <li class="tab">
                            <a href="#zones" data-toggle="tab"> <span class="visible-xs"><i class="fa-map-marker"></i></span> <span class="hidden-xs">Transfers</span> </a>
                        </li>  
                    </ul>


                    <div class="tab-content">
                        <div class="tab-pane active" id="details">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Type</label><br>
                                    {{ $control->Controls_Type->name }}
                                </div>
                                <div class="col-md-4">
                                    <label>Manufacturer</label><br>
                                    {{ $control->Controls_Type->manufacturer }}
                                </div>
                                <div class="col-md-4">
                                    <label>Serial number</label><br>
                                    {{ $control->serial }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    &nbsp;
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Date arrived</label><br>
                                    @if(is_object($movements['arrival']))
                                        {{ $movements['arrival']->created_at->format('d-m-Y') }}
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <label>Date removed</label><br>
                                    @if(is_object($movements['removal']))
                                        {{ $movements['removal']->created_at->format('d-m-Y') }}
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    &nbsp;
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    &nbsp;
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    &nbsp;
                                </div>
                            </div>
                            @foreach($fields as $field)
                                <div class="row">
                                    <div class="col-md-2">
                                        <label>{{ $field['name'] }}</label>
                                    </div>
                                    <div class="col-md-2">
                                        {{ $field['value'] }}
                                    </div>
                                </div>
                            @endforeach
                            
                        </div>
                    
                        <div class="tab-pane" id="usage">
                            <div class="table-responsive">
                                <table id="peopleTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Map</th>
                                            <th>Zone</th>
                                            <th>Person</th>
                                            <th>Member of</th>
                                            <th>Usage</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($usage as $use)
                                            <tr>    
                                                <td>{{ $use['date'] }}</td>
                                                <td>{{ $use['map'] }}</td>
                                                <td>{{ $use['zone'] }}</td>
                                                <td>{{ $use['name'] }}</td>
                                                <td>{{ $use['memberOf'] }}</td>
                                                <td>{{ round($use['time'], 2) }} hrs</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="zones">
                            <div class="table-responsive">
                                <table id="peopleTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Transferred from</th>
                                            <th>Transferred to</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>    
                                            <td>{{ $movements['arrival']->created_at->format('d-m-Y') }}</td>
                                            <td>
                                                @if($movements['arrival']->from_site_id == 0)
                                                    Storage
                                                @else
                                                    {{ $movements['arrival']->From_Site->name }}
                                                @endif
                                            </td>
                                            {{-- <td>{{ $movements['arrival']->To_Site->name }}</td> --}}
                                        </tr>
                                        @foreach($movements['movements'] as $move)
                                            <tr>    
                                                <td>{{ $move->created_at->format('d-m-Y') }}</td>
                                                <td>
                                                    @if($move->from_map_id > 0)
                                                        Map :: {{ $move->From_Map->name }} <br>
                                                    @endif
                                                    @if($move->from_zone_id > 0)
                                                        Zone :: {{ $move->From_Zone->name }} <br>
                                                    @endif
                                                    @if($move->from_hazard_id > 0)
                                                        Hazard :: {{ $move->From_Hazard->Hazard->name }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($move->to_map_id > 0)
                                                        Map :: {{ $move->To_Map->name }} <br>
                                                    @endif
                                                    @if($move->to_zone_id > 0)
                                                        Zone :: {{ $move->To_Zone->name }} <br>
                                                    @endif
                                                    @if($move->to_hazard_id > 0)
                                                        Hazard :: {{ $move->To_Hazard->Hazard->name }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if(is_object($movements['removal']))
                                            <tr>    
                                                <td>{{ $movements['removal']->created_at->format('d-m-Y') }}</td>
                                                <td>
                                                    {{-- @if($move->from_map_id > 0)
                                                        Map :: {{ $move->From_Map->name }} <br>
                                                    @endif
                                                    @if($move->from_zone_id > 0)
                                                        Zone :: {{ $move->From_Zone->name }} <br>
                                                    @endif
                                                    @if($move->from_hazard_id > 0)
                                                        Hazard :: {{ $move->From_Hazard->Hazard->name }}
                                                    @endif --}}
                                                </td>
                                                <td>
                                                    @if($movements['removal']->to_site_id == 0)
                                                        Storage
                                                    @else
                                                        {{ $movements['removal']->to_Site->name }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
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