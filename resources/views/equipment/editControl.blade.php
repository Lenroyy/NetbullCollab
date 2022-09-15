@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/bower_components/jquery-asColorPicker-master/css/asColorPicker.css') }}" rel="stylesheet">
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Control</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>
        <form class="form-horizontal form-material" action="/editControl" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="control" value="{{ $control->id }}">
            <div class="row">
                <div class="col-md-12">
                    <span class="pull-right">
                        <input type="submit" id="submit-all" name="submit" value="Save" class="btn btn-primary">
                        &nbsp;
                        <a class="btn btn-info" href="/controls">Cancel</a>
                        &nbsp;
                        <button type="button" class="btn btn-warning" id="jobButton" onClick="logJob()">Log job in simPRO</button>
                    </span>
                </div>
            </div>
            <br>
                    
            <!-- .row -->
            <div class="row">
                <div class="col-md-4 col-xs-12">
                    <div class="white-box">
                        <div class="user-bg"><img width="100%" alt="user" src="@if(isset($control->Controls_Type->image)) /storage/<?= $control->Controls_Type->image; ?> @else /assets/images/logo/X.png @endif">
                            <div class="overlay-box">
                                <div class="user-content">
                                    <a href="javascript:void(0)"><img src="@if(isset($control->Controls_Type->image)) /storage/<?= $control->Controls_Type->image; ?> @else /assets/images/logo/X.png @endif" class="thumb-lg img-circle" alt="img"></a>
                                    <h4 class="text-white">{{ $control->Controls_Type->name }}</h4>
                                    <h5 class="text-white">@if(isset($control->Controls_Type->manufacturer)){{ $control->Controls_Type->name }}@endif</h5> </div>
                            </div>
                        </div>
                    </div>
                </div> 
            



                    
            <div class="col-md-8 col-xs-12">
                <div class="white-box">
                    <ul class="nav nav-tabs tabs customtab">
                        <li class="active tab">
                            <a href="#profile" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs">Profile</span> </a>
                        </li>
                        <li class="tab">
                            <a href="#billing" data-toggle="tab"> <span class="visible-xs"><i class="ti-money"></i></span> <span class="hidden-xs">Billing </span> </a>
                        </li>
                        <li class="tab">
                            <a href="#monitors" data-toggle="tab"> <span class="visible-xs"><i class="ti-pulse"></i></span> <span class="hidden-xs">Monitors </span> </a>
                        </li>
                        <li class="tab">
                            <a href="#activities" data-toggle="tab"> <span class="visible-xs"><i class="icon-notebook"></i></span> <span class="hidden-xs">Activities </span> </a>
                        </li>
                        <li class="tab">
                            <a href="#transfers" data-toggle="tab"> <span class="visible-xs"><i class="ti-truck"></i></span> <span class="hidden-xs">Transfer history </span> </a>
                        </li>
                        <li class="tab">
                            <a href="#videos" data-toggle="tab"> <span class="visible-xs"><i class="ti-video-clapper"></i></span> <span class="hidden-xs">Instructional videos @if(count($control->Controls_Type->Videos) > 0)<span class="badge"><?= count($control->Controls_Type->Videos); ?></span>@endif</span> </a>
                        </li>
                        <li class="tab">
                            <a href="#attachments" data-toggle="tab"> <span class="visible-xs"><i class="ti-folder"></i></span> <span class="hidden-xs">Attachments @if(count($files) > 0)<span class="badge"><?= count($files); ?></span>@endif</span> </a>
                        </li>
                        <li class="tab">
                            <a href="#logs" data-toggle="tab"> <span class="visible-xs"><i class="ti-calendar"></i></span> <span class="hidden-xs">Logs</span> </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        
                        <div class="tab-pane active" id="profile">
                            <br>&nbsp;
                            <div class="form-group">
                                <label class="col-md-12">Serial number</label>
                                <div class="col-md-12">
                                    <input type="text" id="serial" name="serial" placeholder="Control serial number" value="{{ $control->serial }}" class="form-control form-control-line"> 
                                </div>    
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Current location</label>
                                <div class="col-md-12">
                                    {{ $currentLocation }}
                                </div>    
                            </div>
                            @foreach($fields as $field)
                                <input type="hidden" name="fieldID[]" value="{{ $field->control_field_id }}">
                                <div class="form-group">
                                    <label class="col-md-12">{{ $field->Controls_Type_Field->name }}</label>
                                    <div class="col-md-12">
                                        <input type="text" name="fieldValue[]" placeholder="Field value" value="{{ $field->value }}" class="form-control form-control-line"> 
                                    </div>    
                                </div>
                            @endforeach
                            <div class="form-group">
                                <label class="col-md-12">simPRO Asset ID</label>
                                <div class="col-md-12">
                                    <input type="text" name="simproAssetID" placeholder="simPRO Asset ID" value="{{ $control->simpro_asset_id }}" class="form-control form-control-line"> 
                                </div>    
                            </div>
                            <div class="form-group">
                                <label for="plan-name" class="control-label">Commission date</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="datepicker-autoclose" placeholder="dd-mm-yyyy" value="{{ $control->commission_date->format('d-m-Y') }}" name="commissionDate"><span class="input-group-addon"><i class="icon-calender"></i></span> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="plan colour" class="control-label">Colour</label>
                                <div class="example">
                                    <input type="text" class="colorpicker form-control" name="colour" value="{{ $control->colour }}" /> 
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="monitors">
                            <div class="row">
                                <div class="col-md-6">
                                    &nbsp;
                                </div>
                                <div class="col-md-6">
                                    Add monitor
                                    <span class="pull-right">
                                        <select onChange="sendMonitor(this.value)" class="form-control form-control-line">
                                            <option value="0">Select</option>
                                            @foreach($spareMonitors as $spare)
                                                <option value="{{ $spare->id }}">{{ $spare->thingsboard_id }} :: {{ $spare->type }} :: {{ $spare->name }}</option>
                                            @endforeach
                                        </select>
                                    </span>
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
                                        <table class="table table-hover" id="monitorTable">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        Sensor ID
                                                    </th>
                                                    <th>
                                                        Sensor name
                                                    </th>
                                                    <th>
                                                        Sensor type
                                                    </th>
                                                    <th>
                                                        &nbsp;
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="monitorsBody">
                                                @foreach($monitors as $monitor)
                                                    <tr>
                                                        <td>
                                                            {{ $monitor->thingsboard_id }}
                                                        </td>
                                                        <td>
                                                            {{ $monitor->name }}
                                                        </td>
                                                        <td>
                                                            {{ $monitor->type }}
                                                        </td>
                                                        <td>
                                                            <a href="/removeMonitor/{{ $monitor->id }}">Remove</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="activities">
                            <div class="table-responsive">
                                <table class="table table-hover" id="historyTable">
                                    <thead>
                                        <tr>
                                            <th>
                                                Date
                                            </th>
                                            <th>
                                                Activity type
                                            </th>
                                            <th>
                                                Site
                                            </th>
                                            <th>
                                                Zone
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activities as $act)
                                            <tr @if(is_object($act->Site)) style="cursor: pointer" onClick="window.location.href='/logActivity/{{ $act->history_id }}'" @endif>
                                                <td>
                                                    <p class="text-muted">{{ $act->created_at->format('d-m-Y') }}</p>
                                                </td>
                                                <td>
                                                    @if(is_object($act->History))
                                                        @if(is_object($act->History->Activity))
                                                            <p class="text-muted">{{ $act->History->Activity->name }}</p>
                                                        @else
                                                            -
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(is_object($act->Site))
                                                        <p class="text-muted">{{ $act->Site->name }}</p>                                                       
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(is_object($act->Zone))
                                                        <p class="text-muted">{{ $act->Zone->name }}</p>
                                                    @else
                                                        -
                                                    @endif
                                                    
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="transfers">
                            <div class="table-responsive">
                                <table class="table table-hover" id="deployed">
                                    <thead>
                                        <tr>
                                            <th>
                                                Date
                                            </th>
                                            <th>
                                                From
                                            </th>
                                            <th>
                                                To
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transfers as $transfer)
                                            <tr style="cursor: pointer" onClick="window.location.href='/siteHistory/0'">
                                                <td>
                                                    <p class="text-muted">{{ $transfer->created_at->format('d-m-Y H:i') }}</p>
                                                </td>
                                                <td>
                                                    <p class="text-muted">
                                                        @if($transfer->from_site_id == 0)
                                                            From : Storage
                                                        @elseif($transfer->from_site_id != $transfer->to_site_id)
                                                            From site: {{ $transfer->From_Site->name }}
                                                        @endif

                                                        @if($transfer->from_map_id != $transfer->to_map_id)
                                                            <br>From site map : 
                                                            @if($transfer->from_map_id == 0)
                                                                None
                                                            @else
                                                                {{ $transfer->From_Map->name }}
                                                            @endif
                                                        @endif

                                                        @if($transfer->from_zone_id != $transfer->to_zone_id)
                                                            <br>From map zone : 
                                                            @if($transfer->from_zone_id == 0)
                                                                None
                                                            @else
                                                                From map zone : {{ $transfer->From_Zone->name }}
                                                            @endif
                                                        @endif
                                                    </p>
                                                </td>
                                                <td>
                                                    <p class="text-muted">
                                                        @if($transfer->to_site_id == 0)
                                                            To : Storage
                                                        @elseif($transfer->from_site_id != $transfer->to_site_id)
                                                            To Site: {{ $transfer->To_Site->name }}
                                                        @endif

                                                        @if($transfer->from_map_id != $transfer->to_map_id)
                                                            <br>To site map : 
                                                            @if($transfer->to_map_id == 0)
                                                                None
                                                            @else
                                                                {{ $transfer->To_Map->name }}
                                                            @endif
                                                        @endif

                                                        @if($transfer->from_zone_id != $transfer->to_zone_id)
                                                            <br>To map zone : 
                                                            @if($transfer->to_zone_id == 0)
                                                                None
                                                            @else
                                                                To map zone: {{ $transfer->To_Zone->name }}
                                                            @endif
                                                        @endif
                                                    </p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="billing">
                            <br>
                            <div class="form-group">
                                <label class="col-md-12">Billing</label>
                                <div class="col-md-12">
                                    <select class="form-control form-control-line" id="billingValue" name="billing" onChange="checkBilling(this.value)">
                                        <option value="yes" @if($control->billing == "yes") selected @endif>Yes</option>
                                        <option value="monitoring" @if($control->billing == "monitoring") selected @endif>Monitoring only</option>
                                        <option value="initial" @if($control->billing == "initial") selected @endif>Initial site monitoring</option>
                                        <option value="no" @if($control->billing == "no") selected @endif>No billing</option>
                                    </select>
                                </div>    
                            </div>
                            <div id="billingDetails" style="visibility: hidden;">
                                <div class="form-group">
                                    <label class="col-md-12">Billing amount</label>
                                    <div class="col-md-12">
                                        <input type="number" step="0.01" name="billingAmount" value="{{ $control->billing_amount }}" placeholder="Control billing amount" class="form-control form-control-line"> 
                                    </div>    
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Frequency</label>
                                    <div class="col-md-12">
                                        <select name="billingFrequency" class="form-control form-control-line"> 
                                            <option value="daily" @if($control->billing_frequency == "daily") selected @endif>Daily</option>
                                            <option value="weekly" @if($control->billing_frequency == "weekly") selected @endif>Weekly</option>
                                            <option value="monthly" @if($control->billing_frequency == "monthly") selected @endif>Monthly</option>
                                        </select>
                                    </div>    
                                </div>
                                <div id="billingCommencement" style="visibility: hidden;">
                                    <div class="col-md-3">
                                        <label>Date billing commencement</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control input-datepicker" id="datepicker-billing" placeholder="dd-mm-yyyy" name="commencementDate" @if(isset($control->billing_commencement)) value="{{ $control->billing_commencement->format('d-m-Y') }}" @endif><span class="input-group-addon"><i class="icon-calender"></i></span> 
                                        </div>    
                                    </div>
                                </div>
                            </div>
                            <br>&nbsp;
                            <br>&nbsp;
                            <br>&nbsp;
                            <br>&nbsp;
                            <br>&nbsp;
                        </div>

                        <div class="tab-pane" id="videos">
                            <br>
                            <div class="form-group">
                                <label class="col-md-12">Instructional videos</label>    
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <table class="table table-responsive table-hover">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($control->Controls_Type->Videos as $video)
                                                <tr>
                                                    <td data-toggle="modal" data-target="#editVideoModal{{ $video->id }}" style="cursor: pointer">{{ $video->type }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
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

    <!-- Videos -->
    @foreach($control->Controls_Type->Videos as $video)
        <div class="modal fade" id="editVideoModal{{ $video->id }}" tabindex="-1" role="dialog" aria-labelledby="editVideoModal{{ $video->id }}" style="display: none;">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="exampleModalLabel1">Video</h4> </div>
                        <div class="row">
                            <div class="col-md-1">&nbsp;</div>
                                <div class="col-md-10">
                                    {!! $video->code !!}
                                </div>
                            <div class="col-md-1">&nbsp;</div>
                        </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        
    @endforeach
    <!-- /Videos -->


    <!-- JS DEPENDENCIES -->
    @include('components.footer') 
    <!-- END JS DEPENDENCIES -->
                
    <!-- Page specific Javascript -->
    <script src="{{ asset('js/jasny-bootstrap.js') }}"></script>
    <script src="{{ asset('assets/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asColor.js') }}"></script>
    <script src="{{ asset('assets/plugins/bower_components/jquery-asColorPicker-master/libs/jquery-asGradient.js') }}"></script>
    <script src="{{ asset('assets/plugins/bower_components/jquery-asColorPicker-master/dist/jquery-asColorPicker.min.js') }}"></script>



    <script>
        $(document).ready(function() {
            $('#historyTable').DataTable({
                "displayLength": 100,
            });
            
            billing = document.getElementById("billingValue").value;
            
            checkBilling(billing)
        });

        function sendMonitor(value)
        {
            innerHTML = ""
            
            jQuery.getJSON('/addMonitorToControl/' + value + '/' + {{ $control->id }}, function (details) {
                $.each(details, function (d, detail) {
                    console.log(detail)

                    innerHTML += "<tr>"
                        innerHTML += "<td>"
                            innerHTML += detail.thingsboard_id
                        innerHTML += "</td>"
                        innerHTML += "<td>"
                            innerHTML += detail.name
                        innerHTML += "</td>"
                        innerHTML += "<td>"
                            innerHTML += detail.type
                        innerHTML += "</td>"
                        innerHTML += "<td>"
                            innerHTML += "<a href=\"/removeMonitor/" + detail.id + "\">Remove</a>"
                        innerHTML += "</td>"
                    innerHTML += "</tr>"
                });
                document.getElementById("monitorsBody").innerHTML = innerHTML
            });
        }

        function logJob()
        {
            document.getElementById("jobButton").disabled = true;
            document.getElementById("jobButton").class = "btn btn-disabled";

            jQuery.getJSON('/logJob/' + {{ $control->id }}, function (details) {
                console.log(details) 
                alert('Job ' + details.job.one + ' created');
            });
            
        }

        function checkBilling(value)
        {
            console.log(value)
            
            if(value == "yes")
            {
                document.getElementById("billingDetails").style.visibility = "visible";
                document.getElementById("billingCommencement").style.visibility = "hidden";
            }
            else if(value == "monitoring")
            {
                document.getElementById("billingDetails").style.visibility = "visible";
                document.getElementById("billingCommencement").style.visibility = "hidden";
            }
            else if(value == "initial")
            {
                document.getElementById("billingDetails").style.visibility = "visible";
                document.getElementById("billingCommencement").style.visibility = "visible";
            }
            else if(value == "no")
            {
                document.getElementById("billingDetails").style.visibility = "hidden";
                document.getElementById("billingCommencement").style.visibility = "hidden";
            }
            
        }

        jQuery('#datepicker-autoclose').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'dd-mm-yyyy'
        });

        jQuery('#datepicker-billing').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'dd-mm-yyyy'
        });

        $(".colorpicker").asColorPicker();

                
    </script>

    @endsection