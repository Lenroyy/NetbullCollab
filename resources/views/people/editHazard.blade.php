@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/bower_components/custom-select/custom-select.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('css/dots.css') }}" rel="stylesheet">
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Hazard :: {{ $hazard->Hazard->name }}<br><small>(in zone {{ $hazard->Zone->name }} on site {{ $hazard->Zone->Site->name }})</small></h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>
        <form class="form-horizontal form-material" action="/saveHazard/{{ $hazard->id }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <span class="pull-right">
                    <input type="submit" id="submit-all" name="submit" value="Save" class="btn btn-primary" name="submitted">
                    &nbsp;
                    <a class="btn btn-info" href="/sites/zone/{{ $hazard->zone_id }}">Cancel</a>
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
                            <a href="#profile" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs">Details</span> </a>
                        </li>
                        <li class="tab">
                            <a href="#controls" data-toggle="tab"> <span class="visible-xs"><i class="ti-truck"></i></span> <span class="hidden-xs">Controls </span> </a>
                        </li>
                        <li class="tab">
                            <a href="#plan" data-toggle="tab"> <span class="visible-xs"><i class="ti-help-alt"></i></span> <span class="hidden-xs">Plan</span> </a>
                        </li>
                        <li class="tab">
                            <a href="#monitors" data-toggle="tab"> <span class="visible-xs"><i class="ti-stamp"></i></span> <span class="hidden-xs">Monitors & samples</span> </a>
                        </li>
                        <li class="tab">
                            <a href="#attachments" data-toggle="tab"> <span class="visible-xs"><i class="ti-folder"></i></span> <span class="hidden-xs">Attachments @if(count($files) > 0)<span class="badge">{{ count($files) }}</span>@endif</span> </a>
                        </li>
                        <li class="tab">
                            <a href="#logs" data-toggle="tab"> <span class="visible-xs"><i class="ti-calendar"></i></span> <span class="hidden-xs">Logs</span> </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="profile">
                            <div class="row">          
                                <div class="col-md-3">
                                    <label class="col-md-12">Map name</label>
                                </div>
                                <div class="col-md-3">
                                    {{ $hazard->Zone->Sites_Map->name }}
                                </div>
                                <div class="col-md-3">
                                    <label class="col-md-12">Zone</label>
                                </div>
                                <div class="col-md-3">
                                    {{ $hazard->Zone->name }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    &nbsp;
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-responsive table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>Trades</th>
                                                <th>Activities</th>
                                                <th>Assessments</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($assessments as $assessment)
                                                <tr>
                                                    <td>{{ $assessment['trade'] }}</td>
                                                    <td>{{ $assessment['activity'] }}</td>
                                                    <td>{{ $assessment['assessment'] }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="plan">
                            <div class="row">
                                <div class="col-md-1">
                                    <label>Add step</label>
                                </div>
                                <div class="col-md-10">
                                    <input type="text" name="step" class="form-control form-control-name">
                                </div>
                                <div class="col-md-1">
                                    <span class="pull-right">
                                        <input type="submit" class="btn btn-primary" name="submitted" value="Add step">
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    &nbsp;<br>&nbsp;
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="hazardTable">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        Plan step
                                                    </th>
                                                    <th>
                                                        &nbsp;
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($hazard->Steps as $step)
                                                    <tr>
                                                        <td>
                                                            <p class="text-muted">{{ $step->step }}</p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted"><a href="/removeStep/{{ $step->id }}">Remove</a></p>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    &nbsp;<br>&nbsp;
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <label>Plan summary</label>
                                    <textarea class="form-control form-control-line" cols="30" rows="10" name="plan">{{ $hazard->plan }}</textarea>
                                </div>
                            </div>
                            
                        </div>

                        <div class="tab-pane" id="controls">
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
                                                @foreach($controlType['fields'] as $field)
                                                <th>
                                                    {{ $field }}
                                                </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($controlType['controls'] as $control)
                                                <tr style="cursor: pointer">
                                                    <td>
                                                        <input type="checkbox" value="{{ $control['control']->id }}" name="controlsToTransfer[]">
                                                    </td>
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

                        <div class="tab-pane" id="monitors">
                            <ul class="nav nav-tabs tabs customtab">
                                <li class="active tab">
                                    <a href="#controlSamples" data-toggle="tab"> <span class="visible-xs"><i class="ti-envelope"></i></span> <span class="hidden-xs">Samples</span> </a>
                                </li>

                                <li class="tab">
                                    <a href="#controlMonitors" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="ti-dashboard"></i></span> <span class="hidden-xs">Monitors</span> </a>
                                </li>
                                
                            </ul>
                            <div class="tab-content">

                                <div class="tab-pane" id="controlMonitors">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="hazardTable">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        Control
                                                    </th>
                                                    <th>
                                                        Sensor
                                                    </th>
                                                    <th>
                                                        ID
                                                    </th>
                                                    <th>
                                                        Reading type
                                                    </th>
                                                    <th>
                                                        Latest reading
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($monitors as $monitor)
                                                    <tr>
                                                        <td>{{ $monitor['control']->Controls_Type->name }}</td>
                                                        <td>{{ $monitor['device']->name }}</td>
                                                        <td>{{ $monitor['device']->thingsboard_id }}</td>
                                                        <td>&nbsp;</td>
                                                        <td>&nbsp;</td>
                                                    </tr>
                                                    @foreach($monitor['readings'] as $reading)
                                                        <tr>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>&nbsp;</td>
                                                            <td>{{ $reading['readingType'] }}</td>
                                                            <td>{{ $reading['reading'] }}</td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane active" id="controlSamples">
                                    <div class="table-responsive">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>Sample</label>
                                                <select class="form-control form-control-line" id="sampleType">
                                                    <option>Select</option>
                                                    @foreach($sampleTypes as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Sample date</label>
                                                <input type="text" class="form-control form-control-name" id="datepicker-sample-date">
                                            </div>
                                            <div class="col-md-3">
                                                <label>Sample result</label>
                                                <input type="text" class="form-control form-control-name" id="result">
                                            </div>
                                            <div class="col-md-3">
                                                <span class="pull-right">
                                                    <button type="button" class="btn btn-primary" onClick="addSample()">Add sample</button>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                &nbsp;<br>&nbsp;
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>
                                                                    Sample
                                                                </th>
                                                                <th>
                                                                    Timestamp
                                                                </th>
                                                                <th>
                                                                    Result
                                                                </th>
                                                                <th>
                                                                    &nbsp;
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="sampleResults">
                                                            @foreach($samples as $sample)
                                                            <tr>
                                                                <td>
                                                                    <p class="text-muted">{{ $sample->Sample->name }}</p>
                                                                </td>
                                                                <td>
                                                                    <p class="text-muted">
                                                                        {{ $sample->date->format('d-m-Y') }}
                                                                    </p>
                                                                </td>
                                                                <td>
                                                                    <p class="text-muted">
                                                                        {{ $sample->result }} {{ $sample->Sample->measurement }}
                                                                    </p>
                                                                </td>
                                                                <td>
                                                                    <p class="text-muted">
                                                                        <a href="/deleteSample/{{ $sample->id }}">Remove</a>
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
                                </div>

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
        </form>
    </div>
    <!-- /.row -->


    <!--Transfer control Box-->
        
    <form class="form-horizontal form-material" action="/transferControlsOnSite" method="POST" enctype="multipart/form-data">
        @csrf
        <div id="transferringControls"></div>
            <input type="hidden" name="hazard" value="{{ $hazard->id }}">
            <input type="hidden" name="referrer" value="hazard">
            <input type="hidden" name="site" value="{{ $hazard->Zone->Site->id }}">
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
    <script src="{{ asset('assets/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
    <!-- <script src="{{ asset('assets/dropzone-master/dist/dropzone.js') }}"></script> -->
    <script src="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>


    <script>
        $(document).ready(function() {
            $('#historyTable').DataTable({
                "displayLength": 100,
            });

            $('#logTable').DataTable({
                    "displayLength": 100,
                });

        });

        $(".select2").select2();

        jQuery('#datepicker-visit-date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'dd-mm-yyyy'
        });

        jQuery('#datepicker-sample-date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'dd-mm-yyyy'
        });

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

        function addSample()
        {
            type = document.getElementById("sampleType")
            date = document.getElementById("datepicker-sample-date")
            result = document.getElementById("result")

            innerHTML = ""

            jQuery.getJSON('/addHazardSample/' + {{ $hazard->id }} + '/' + type.value + '/' + date.value + '/' + result.value, function (details) {
                $.each(details, function (d, detail) {
                    console.log(detail)

                    innerHTML += "<tr>"
                        innerHTML += "<td>"
                            innerHTML += "<p class=\"text-muted\">" + detail.name + "</p>"
                        innerHTML += "</td>"
                        innerHTML += "<td>"
                            innerHTML += "<p class=\"text-muted\">"
                                innerHTML += detail.date
                            innerHTML += "</p>"
                        innerHTML += "</td>"
                        innerHTML += "<td>"
                            innerHTML += "<p class=\"text-muted\">"
                                innerHTML += detail.result
                                if(detail.measurement != null)
                                {
                                    innerHTML += " " + detail.measurement
                                }
                            innerHTML += "</p>"
                        innerHTML += "</td>"
                        innerHTML += "<td>"
                            innerHTML += "<p class=\"text-muted\">"
                                innerHTML += "<a href=\"/deleteSample/" + detail.id + "\">Remove</a>"
                            innerHTML += "</p>"
                        innerHTML += "</td>"
                    innerHTML += "</tr>"
                    
                });

                document.getElementById("sampleResults").innerHTML = innerHTML
                type.value = 0
                date.value = ""
                result.value = ""

            });
        }
                
    </script>

@endsection