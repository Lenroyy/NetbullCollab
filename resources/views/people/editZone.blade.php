@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/bower_components/gridster/css/jquery.gridster.css') }}"> 
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/bower_components/gridster/css/jquery.dsmorse-gridster.min.css') }}"> 
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/bower_components/gridster/css/gridster.custom.css') }}"> 
        <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/highcharts/css/highcharts.css') }}" charster="utf-8"></script>
        <link href="{{ asset('css/dots.css') }}" rel="stylesheet">
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Zone :: {{ $zone->name }} <small>(on site {{ $zone->Site->name }})</small></h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>
        <form class="form-horizontal form-material" action="/saveZone/{{ $zone->id }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <span class="pull-right">
                        <input type="submit" id="submit-all" name="submit" value="Save" class="btn btn-primary">
                        &nbsp;
                        <a class="btn btn-info" href="/editSite/{{ $zone->site_id }}">Cancel</a>
                    </span>
                </div>
            </div>
            <br>
                    
            <!-- .row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <ul class="nav nav-tabs tabs customtab">
                            <li class="active tab">
                                <a href="#dashboard" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs">Dashboard</span> </a>
                            </li>
                            
                            <li class="tab">
                                <a href="#profile" data-toggle="tab"> <span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs">Details</span> </a>
                            </li>
                            <li class="tab">
                                <a href="#hazards" data-toggle="tab"> <span class="visible-xs"><i class="fa fa-bolt"></i></span> <span class="hidden-xs">Hazards</span> </a>
                            </li>
                            <li class="tab">
                                <a href="#permits" data-toggle="tab"> <span class="visible-xs"><i class="ti-pulse"></i></span> <span class="hidden-xs">Entry requirements </span> </a>
                            </li> 
                            <li class="tab">
                                <a href="#assets" data-toggle="tab"> <span class="visible-xs"><i class="ti-truck"></i></span> <span class="hidden-xs">Controls </span> </a>
                            </li>
                            <li class="tab">
                                <a href="#attachments" data-toggle="tab"> <span class="visible-xs"><i class="ti-folder"></i></span> <span class="hidden-xs">Attachments @if(count($files) > 0)<span class="badge">{{ count($files) }}</span>@endif</span> </a>
                            </li>
                            <li class="tab">
                                <a href="#history" data-toggle="tab"> <span class="visible-xs"><i class="ti-notepad"></i></span> <span class="hidden-xs">Activity </span> </a>
                            </li>
                            <!--
                            <li class="tab">
                                <a href="#visits" data-toggle="tab"> <span class="visible-xs"><i class="ti-pulse"></i></span> <span class="hidden-xs">Visits</span> </a>
                            </li>
                            -->
                            <li class="tab">
                                <a href="#logs" data-toggle="tab"> <span class="visible-xs"><i class="ti-calendar"></i></span> <span class="hidden-xs">Logs</span> </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="dashboard">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="btn-group pull-right">
                                            <div class="pull-right">
                                                <button type="button" class="btn btn-primary btn-sm light-widget-button" data-toggle="modal" data-target="#widgets"><i class="fa fa-plus"></i> Widget</button>&nbsp;&nbsp;&nbsp;
                                                <button type="button" class="btn btn-success btn-sm js-seralize"><i class="ft-save white"></i> Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                        
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
                            </div>
                            
                            <div class="tab-pane" id="profile">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-md-12">Name</label>
                                            <div class="col-md-12">
                                                <input type="text" id="name" name="name" placeholder="Zone name"value="{{ $zone->name }}" class="form-control form-control-line"> 
                                            </div>    
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-12">Map</label>
                                            <div class="col-md-12">
                                                {{ $zone->Sites_Map->name }}
                                            </div>    
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="text-align: center;">
                                        <label>QR code for zone</label>
                                        <br><br>
                                        <a href="/printableQRCode/{{ $zone->id }}" target="_blank">{!! QrCode::generate($qrAddress); !!}</a>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="history">
                                <ul class="nav nav-tabs tabs customtab">
                                    <li class="active tab">
                                        <a href="#ass" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="ti-flag-alt"></i></span> <span class="hidden-xs">Assessments / SWMS</span> </a>
                                    </li>
                                    <li class="tab">
                                        <a href="#te" data-toggle="tab"> <span class="visible-xs"><i class="ti-flag"></i></span> <span class="hidden-xs">Time entries </span> </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="ass">
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="assessmentsTable">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            ID
                                                        </th>
                                                        <th>
                                                            Date
                                                        </th>
                                                        <th>
                                                            Worker
                                                        </th>
                                                        <th>
                                                            Associated with
                                                        </th>
                                                        <th>
                                                            Assessment
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($assessments as $ass)
                                                        <tr style="cursor: pointer" onClick="window.location.href='/logActivity/{{ $ass->History_Assessment->history_id }}'">
                                                            <td>
                                                                {{ $ass->id }}
                                                            </td>
                                                            <td>
                                                                <p class="text-muted">
                                                                    {{ $ass->created_at->format('d-m-Y') }}
                                                                </p>
                                                            </td>
                                                            <td>
                                                                <p class="text-muted">
                                                                    @if(is_object($ass->Profile))
                                                                        {{ $ass->Profile->name }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                    
                                                                </p>
                                                            </td>
                                                            <td>
                                                                <p class="text-muted">
                                                                    @if(is_object($ass->Organisation))
                                                                        {{ $ass->Organisation->name }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </p>
                                                            </td>
                                                            <td>
                                                                <p class="text-muted">
                                                                    @if(is_object($ass->Assessment))
                                                                        {{ $ass->Assessment->name }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                    
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="te">
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="timeEntryTable">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            ID
                                                        </th>
                                                        <th>
                                                            Date
                                                        </th>
                                                        <th>
                                                            Worker
                                                        </th>
                                                        <th>
                                                            Associated with
                                                        </th>
                                                        <th>
                                                            Start
                                                        </th>
                                                        <th>
                                                            Finish
                                                        </th>
                                                        <th>
                                                            Hours
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($timeEntries as $time)
                                                        <tr style="cursor: pointer" onClick="window.location.href='/logActivity/{{ $time->history_id }}'">
                                                            <td>
                                                                {{ $time->id }}
                                                            </td>
                                                            <td>
                                                                <p class="text-muted">
                                                                    {{ $time->date->format('d-m-Y') }}
                                                                </p>
                                                            </td>
                                                            <td>
                                                                <p class="text-muted">
                                                                    @if(is_object($time->Profile))
                                                                        {{ $time->Profile->name }}
                                                                    @endif
                                                                </p>
                                                            </td>
                                                            <td>
                                                                <p class="text-muted">
                                                                    @if(is_object($time->Organisation))
                                                                        {{ $time->Organisation->name }}
                                                                    @endif
                                                                </p>
                                                            </td>
                                                            <td>
                                                                <p class="text-muted">
                                                                    {{ $time->start }}
                                                                </p?>
                                                            </td>
                                                            <td>
                                                                <p class="text-muted">
                                                                    {{ $time->finish }}
                                                                </p?>
                                                            </td>
                                                            <td>
                                                                <p class="text-muted">
                                                                    {{ $time->calcHours() }}
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="assets">
                                @foreach($controls as $controlType)
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4>{{ $controlType['type']->name }}</h4>
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        &nbsp;
                                                    </th>
                                                    <th>Serial</th>
                                                    @foreach($controlType['fields'] as $field)
                                                    <th>
                                                        {{ $field }}
                                                    </th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($controlType['controls'] as $control)
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" value="{{ $control['control']->id }}" name="controlsToTransfer[]">
                                                        </td>
                                                        <td @if($standardDisplay['profile']->super_user == 1) style="cursor: pointer" onClick="window.location.href='/editControl/{{ $control['control']->id }}'" @endif>{{ $control['control']->serial }}</td>
                                                        @foreach($control['fieldValues'] as $value)
                                                            <td @if($standardDisplay['profile']->super_user == 1) style="cursor: pointer" onClick="window.location.href='/editControl/{{ $control['control']->id }}'" @endif>
                                                                <p class="text-muted">
                                                                    {{ $value['value'] }}
                                                                </p>
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <hr>
                                        ^ <a href="#" onClick="populateControls()"  data-toggle="modal" data-target="#transferControl" data-whatever="@mdo">Transfer controls</a>
                                    </div>
                                @endforeach
                            </div>

                            <div class="tab-pane" id="workers">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="historyTable">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Name
                                                </th>
                                                <th>
                                                    Permit status
                                                </th>
                                                <th>
                                                    Associated with
                                                </th>
                                                <th>
                                                    Total hours
                                                </th>
                                                <th>
                                                    Total activities
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr style="cursor: pointer" onClick="window.location.href='/editProfile/0'">
                                                <td>
                                                    <p class="text-muted">Paul Brennan</p>
                                                </td>
                                                <td title="medical">
                                                    <p class="text-muted">1 problem</p>
                                                </td>
                                                <td>
                                                    <p class="text-muted">J&D Contracting</p>
                                                </td>
                                                <td>
                                                    <p class="text-muted">823</p>
                                                </td>
                                                <td>
                                                    <p class="text-muted">56</p>
                                                </td>
                                            </tr>
                                            <tr style="cursor: pointer" onClick="window.location.href='/editProfile/0'">
                                                <td>
                                                    <p class="text-muted">Curtis Thomson</p>
                                                </td>
                                                <td>
                                                    <p class="text-muted">ok</p>
                                                </td>
                                                <td>
                                                    <p class="text-muted">J&D Contracting</p>
                                                </td>
                                                <td>
                                                    <p class="text-muted">823</p>
                                                </td>
                                                <td>
                                                    <p class="text-muted">56</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="permits">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-md-12">Add Entry requirement</label>
                                            <div class="col-md-6">
                                                <select class="form-control form-control-line form-control-select" name="permit" id="permit">
                                                    <option value="0">Select</option>
                                                    @foreach($allPermits as $p)
                                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <select class="form-control form-control-line form-control-select" name="permit_mandatory" id="permit_mandatory" onChange="addRequirement(this.value, document.getElementById('permit').value)">
                                                    <option value="0">Required/Recommend</option>    
                                                    <option value="1">Recommended</option>
                                                    <option value="2">Required</option>
                                                </select>
                                            </div>
                                        </div>
                                    <div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            Name
                                                        </th>
                                                        <th>
                                                            Type
                                                        </th>
                                                        <th>
                                                            Status
                                                        </th>
                                                        <th>
                                                            &nbsp;
                                                        </th>
                                                        <th>
                                                            &nbsp;
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="permitsTable">
                                                    @foreach($permits as $per)
                                                        <tr>
                                                            <td>
                                                                <p class="text-muted">{{ $per['name'] }}</p>
                                                            </td>
                                                            <td>
                                                                <p class="text-muted">{{ $per['type'] }}</p>
                                                            </td>
                                                            @if($per['count'] == 0)
                                                                <td>
                                                                    <p class="text-muted">ok</p>
                                                                </td>
                                                            @else
                                                                <td title="{{ $per['people'] }}">
                                                                    <p class="text-muted">{{ $per['count'] }} problem
                                                                    @if($per['count'] > 1)s @endif</p>
                                                                </td>
                                                            @endif
                                                            <td>
                                                                <p class="text-muted">
                                                                    <select class="form-control form-control-line" name="requirements[{{ $per['id'] }}]">
                                                                        <option @if($per['mandatory'] == 2) selected @endif value="2">Required</option>
                                                                        <option @if($per['mandatory'] == 1) selected @endif value="1">Recommended</option>
                                                                    </select>
                                                                </p>
                                                            </td>
                                                            <td>
                                                                <a href="/removePermit/zone/{{ $per['pid'] }}">Remove</a>
                                                            </td>
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

                            <div class="tab-pane" id="hazards">
                                <div class="row">
                                    <div class="col-md-12">
                                        <span class="pull-right">
                                            <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#newHazard" data-whatever="@mdo">New hazard</a>
                                        </span>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Hazard
                                                </th>
                                                <th>
                                                    &nbsp;
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($zone->Hazards as $hazard)
                                                <tr style="cursor: pointer" onClick="window.location.href='/sites/hazards/{{ $hazard->id }}'">
                                                    <td>
                                                        <p class="text-muted">{{ $hazard->Hazard->name }}</p>
                                                    </td>
                                                    <td>
                                                        <p class="text-muted"><a href="/removeHazard/{{ $hazard->id }}">Remove</a></p>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="visits">
                                <div class="table-responsive">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Visit reason</label>
                                            <input type="text" class="form-control form-control-name" placeholder="reason for the visit">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Visit date</label>
                                            <input type="text" class="form-control form-control-name" id="datepicker-visit-date">
                                        </div>
                                        <div class="col-md-6">
                                            <span class="pull-right">
                                                <input type="submit" class="btn btn-primary" value="Add visit">
                                            </span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            &nbsp;<br>&nbsp;
                                        </div>
                                    </div>
                                    <table class="table table-hover" id="hazardTable">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Visit
                                                </th>
                                                <th>
                                                    Date
                                                </th>
                                                <!--
                                                <th>
                                                    Likely exposures
                                                </th>
                                                -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <p class="text-muted">Reaon for the visit</p>
                                                </td>
                                                <td>
                                                    <p class="text-muted">
                                                        31-01-2021
                                                    </p>
                                                </td>
                                                <!--
                                                <td>
                                                    <p class="text-muted">
                                                        Silica: 0.0001
                                                    </p>
                                                </td>
                                                -->
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p class="text-muted">Another reaon for the visit</p>
                                                </td>
                                                <td>
                                                    <p class="text-muted">
                                                        30-04-2021
                                                    </p>
                                                </td>
                                                <!--
                                                <td>
                                                    <p class="text-muted">
                                                        Silica: 0.0001
                                                    </p>
                                                </td>
                                                -->
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p class="text-muted">Yet another reaon for the visit</p>
                                                </td>
                                                <td>
                                                    <p class="text-muted">
                                                        31-07-2021
                                                    </p>
                                                </td>
                                                <!--
                                                <td>
                                                    <p class="text-muted">
                                                        Silica: 0.0001
                                                    </p>
                                                    <p class="text-muted">
                                                        Noise: 3db
                                                    </p>
                                                </td>
                                                -->
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="tab-pane" id="attachments">
                                @include('components.attachment') 
                            </div>

                            <div class="tab-pane" id="logs">
                                @include('components.log') 
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    <!-- /.row -->

    @include('components.widgetModal') 


    <!--Modal QR Box-->
    
    <div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModal" style="display: none;">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="exampleModalLabel1">Scan to start activity in Zone {{ $zone->name }}</h4> </div>
                <div class="modal-body">
                    <div style="text-align: center; height: 700px; line-height: 250px;">
                        <br>&nbsp;{!! QrCode::generate('$qrAddress'); !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!--/Modal plans Box-->


    <!--Hazards Box-->
    <form class="form-horizontal form-material" action="/addHazard/{{ $zone->id }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal fade" id="newHazard" tabindex="-1" role="dialog" aria-labelledby="newHazard" style="display: none;">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-lg">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="exampleModalLabel1">New hazard</h4> </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="plan-name" class="control-label">Hazard</label>
                                    <select class="form-control form-control-line" name="newHazard">
                                        @foreach($allHazards as $h)
                                            <option value="{{ $h->id }}">{{ $h->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <input type="submit" name="submit" value="Save" class="btn btn-primary">
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!--/Modal controls Box-->

    <!--Transfer control Box-->
        
    <form class="form-horizontal form-material" action="/transferControlsOnSite" method="POST" enctype="multipart/form-data">
        @csrf
        <div id="transferringControls"></div>
            <input type="hidden" name="zone" value="{{ $zone->id }}">
            <input type="hidden" name="referrer" value="zone">
            <input type="hidden" name="site" value="{{ $zone->Site->id }}">
            <div class="modal fade" id="transferControl" tabindex="-1" role="dialog" aria-labelledby="transferControl" style="display: none;">
                <div class="modal-dialog" role="document">
                    <div class="modal-content modal-lg">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="exampleModalLabel1">Transfer controls</h4> 
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="plan-name" class="control-label">Zone</label>
                                        <select class="form-control form-control-line" onChange="checkHazards(this.value)" name="toZone">
                                            <option>Select</option>
                                            @foreach($zones as $thisZone)
                                                <option value="{{ $thisZone['id'] }}">{{ $thisZone['zone'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="plan-name" class="control-label">Hazard</label>
                                        <select class="form-control form-control-line" name="toHazard" id="toHazard">
                                            <option value="0">None</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    &nbsp;
                                </div>
                            </div>
                        </div>
                    
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type="submit" name="submit" value="Save" class="btn btn-primary">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!--/Modal transfer control Box-->

            





    <!-- JS DEPENDENCIES -->
    @include('components.footer') 
    <!-- END JS DEPENDENCIES -->
                
    <!-- Page specific Javascript -->
    <script src="{{ asset('js/jasny-bootstrap.js') }}"></script>
    <script src="{{ asset('assets/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    <!-- <script src="{{ asset('assets/dropzone-master/dist/dropzone.js') }}"></script> -->
    <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.collision.js') }}" charster="utf-8"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.draggable.js') }}" charster="utf-8"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.coords.js') }}" charster="utf-8"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.dsmorse-gridster.js') }}" charster="utf-8"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.dsmorse-gridster.with-extras.js') }}" charster="utf-8"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.gridster.js') }}" charster="utf-8"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.gridster.js') }}" charster="utf-8"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/highcharts/highcharts.js') }}" charster="utf-8"></script>
    
    @include('js.dashboard') 


    <script>
        $(document).ready(function() {
            $('#historyTable').DataTable({
                "displayLength": 100,
            });

            $('#logTable').DataTable({
                    "displayLength": 100,
                });

        });

        function request(value)
        {
            alert(value);
        }

        function addRequirement(option, permit)
        {
            innerHTML = ""
            jQuery.getJSON('/addZoneRequirement/{{ $zone->id }}/' + permit + '/' + option, function (details) {
                $.each(details, function (d, detail) {
                    console.log(detail)
                    innerHTML += "<tr>"
                        innerHTML += "<td>"
                            innerHTML += "<p class=\"text-muted\">" + detail.name + "</p>"
                        innerHTML += "</td>"
                        innerHTML += "<td>"
                            innerHTML += "<p class=\"text-muted\">" + detail.type + "</p>"
                        innerHTML += "</td>"
                        if(detail.count == 0)
                        {
                            innerHTML += "<td>"
                                innerHTML += "<p class=\"text-muted\">ok</p>"
                            innerHTML += "</td>"
                        }
                        else
                        {
                            innerHTML += "<td title=\"" + detail.people + "\">"
                                innerHTML += "<p class=\"text-muted\">" + detail.count + " problem"
                                if(detail.count > 1)
                                {
                                    innerHTML += "s"
                                }
                            innerHTML += "</p>"
                            innerHTML += "</td>"
                        }
                        innerHTML += "<td>"
                            innerHTML += "<p class=\"text-muted\">"
                                innerHTML += "<select class=\"form-control form-control-line\" name=\"requirements[" + detail.id + "]\">"
                                    innerHTML += "<option"
                                    if(detail.mandatory == 2)
                                    {
                                        innerHTML += " selected "
                                    }
                                    innerHTML += "value=\"2\">Required</option>"
                                    innerHTML += "<option"
                                    if(detail.mandatory == 1)
                                    {
                                        innerHTML += " selected "
                                    }
                                    innerHTML += "value=\"1\">Recommended</option>"
                                innerHTML += "</select>"
                            innerHTML += "</p>"
                        innerHTML += "</td>"
                    innerHTML += "</tr>"
                });

                document.getElementById("permitsTable").innerHTML = innerHTML
            });
            document.getElementById("permit_mandatory").value = 0
            document.getElementById("permit").value = 0
            return 1;
        }

        function openContact(value)
        {
            alert("opening")
        }

        function checkHazards(zone)
        {
            innerHTML = "<option value=\"0\">None</option>"

            jQuery.getJSON('/checkZoneHazards/' + zone, function (details) {
                $.each(details, function (d, detail) {
                    console.log(detail)

                    innerHTML += "<option value=\"" + detail.id + "\">"
                        innerHTML += detail.name
                    innerHTML += "</option>"
                    
                });

                document.getElementById("toHazard").innerHTML = innerHTML

            });
        }

        function populateControls()
        {
            controls = document.getElementsByName("controlsToTransfer[]");
            input = ""

            $.each(controls, function (d, detail) {
                if(detail.checked == true)
                {
                    input += "<input type=\"hidden\" name=\"controlsSelected[]\" value=\"" + detail.value + "\">"
                }
            });

            document.getElementById("transferringControls").innerHTML = input
        }
                
    </script>

@endsection