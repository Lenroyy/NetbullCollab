@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('assets/plugins/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/plugins/bower_components/datatables/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('css/dots.css') }}" rel="stylesheet">
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Reports - Exposures</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>
                
        <!-- .row -->
        <form action="/reports/exposure" method="post">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <ul class="nav nav-tabs tabs customtab">
                            <li class="active tab">
                                <a href="#filters" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="fa-filter"></i></span> <span class="hidden-xs">Filters</span> </a>
                            </li>
                            
                            <li class="tab">
                                <a href="#ppe" data-toggle="tab"> <span class="visible-xs"><i class="fa-eye"></i></span> <span class="hidden-xs">PPE Settings</span> </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="filters">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="box-title">Filters</h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Site</label>
                                        <select class="form-control form-control-line" name="site" onChange="getSensors(this.value)">
                                            <option value="0">---Please Select---</option>
                                            @foreach($sites as $ste)
                                                <option value="{{ $ste->id }}" @if($ste->id == $site) selected @endif>{{ $ste->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Sensor</label>
                                        <select class="form-control form-control-line" name="sensor" onChange="getTypes(this.value)" id="sensor">
                                            <option value="0" @if(!is_object($sensor)) selected @endif>Select</option>
                                            @foreach($sensors as $s)
                                                <option value="{{ $s->id }}" @if(is_object($sensor)) @if($sensor->id == $s->id) selected @endif @endif>{{ $s->name }} :: {{ $s->type }} :: {{ $s->thingsboard_id }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Reading</label>
                                        <select class="form-control form-control-line" name="reading" id="reading">
                                            @if(is_object($reading))
                                                <option value="{{ $reading->id }}">{{ $reading->name }}</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Date range</label> <small> - {{ $dateRange }}</small>
                                        <div class="input-group">
                                        <input type="text" class="form-control input-daterange-datepicker" id="datepicker-autoclose" placeholder="dd-mm-yyyy - dd-mm-yyyy" name="search_date" @if(isset($widgetRange)) value="{{ $widgetRange }}" @endif><span class="input-group-addon"><i class="icon-calender"></i></span> 
                                        </div>    
                                    </div>
                                    <div class="col-md-3">
                                        <label>Exposure Limit</label>
                                        <input type="number" step="1" value="50" class="form-control form-control-line" name="limit">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Chemical weight</label>
                                        <input type="number" step="1" value="50" class="form-control form-control-line" name="weight">
                                    </div>
                                    <div class="col-md-3">
                                        <label>Interval</label>
                                        <select name="interval" class="form-control form-control-line">
                                            <option @if($interval == "2") selected @endif value="2">2 Minutes</option>
                                            <option @if($interval == "5") selected @endif value="5">5 Minutes</option>
                                            <option @if($interval == "60") selected @endif value="60">1 Hour</option>
                                            <option @if($interval == "120") selected @endif value="120">2 Hours</option>
                                            <option @if($interval == "480") selected @endif value="480">8 Hours</option>
                                            <option @if($interval == "1440") selected @endif value="1440">1 Day</option>
                                            <option @if($interval == "10080") selected @endif value="10080">7 Days</option>
                                            <option @if($interval == "302400") selected @endif value="302400">30 Days</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="ppe">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <label>Filter / half facepiece</label>
                                            <input type="number" step="1" value="10" class="form-control form-control-line" name="ppe1">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <label>Filter in full facepiece</label>
                                            <input type="number" step="1" value="50" class="form-control form-control-line" name="ppe2">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <label>Full facepiece with airhose</label>
                                            <input type="number" step="1" value="100" class="form-control form-control-line" name="ppe3">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <label>Full facepiece with airhose & blower</label>
                                            <input type="number" step="1" value="101" class="form-control form-control-line" name="ppe4">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                        <div class="row">
                            <div class="col-md-12">
                                <br><hr><br>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <span class="pull-right"><input type="submit" value="Filter" class="btn btn-primary"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- /.row -->

        <!-- .row -->
        @if($results != NULL)
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="box-title">Data from {{ $sensor->name }} :: {{ $sensor->type }}</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="exposureTable" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" style="vertical-align: middle;">Timestamp</th>
                                                <th rowspan="2" style="vertical-align: middle;">{{ $reading->name }}</th>
                                                <th rowspan="2" style="vertical-align: middle;">Likely exposure</th>
                                                <th rowspan="2" style="vertical-align: middle;">OEL</th>
                                                <th rowspan="2" style="vertical-align: middle;">Base Exposure Outcome</th>
                                                <th colspan="4" style="text-align: center;">PPE in operation</th>
                                            </tr>
                                            <tr>
                                                <th>Filter / half facepiece</th>
                                                <th>Filter in full facepiece</th>
                                                <th>Full facepiece with airhose</th>
                                                <th>Full facepiece with airhose & blower</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($results as $result)
                                                <tr>
                                                    <td>{{ $result['timestamp'] }}</td>
                                                    <td>{{ round($result['reading'], 4) }}</td>
                                                    <td>{{ round($result['likely'], 4) }}</td>
                                                    <td>{{ round($result['average'],4) }}</td>
                                                    <td style="text-align: center;">
                                                        @if($result['baseOutcome'] == "green")
                                                            <span class="smallGreenDot">&nbsp;</span> Ok
                                                        @else
                                                            <span class="smallOrangeDot">&nbsp;</span> Check PPE
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($result['ppe1'] == 8)
                                                            <span class="smallGreenDot">&nbsp;</span> Up to 8 hours
                                                        @else
                                                            <span class="smallOrangeDot">&nbsp;</span> {{ round($result['ppe1'], 2) }}
                                                        @endif    
                                                    </td>
                                                    <td>
                                                        @if($result['ppe2'] == 8)
                                                            <span class="smallGreenDot">&nbsp;</span> Up to 8 hours
                                                        @else
                                                            <span class="smallOrangeDot">&nbsp;</span> {{ round($result['ppe2'], 2) }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($result['ppe3'] == 8)
                                                            <span class="smallGreenDot">&nbsp;</span> Up to 8 hours
                                                        @else
                                                            <span class="smallOrangeDot">&nbsp;</span> {{ round($result['ppe3'], 2) }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($result['ppe4'] == 8)
                                                            <span class="smallGreenDot">&nbsp;</span> Up to 8 hours
                                                        @else
                                                            <span class="smallOrangeDot">&nbsp;</span> {{ round($result['ppe4'], 2) }}
                                                        @endif
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
        @endif

        <!-- JS DEPENDENCIES -->
        @include('components.footer') 
        <!-- END JS DEPENDENCIES -->
                    
        <!-- Page specific Javascript -->
        <script src="{{ asset('assets/plugins/bower_components/moment/moment.js') }}"></script>
        <script src="{{ asset('assets/plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
        <!--<script src="{{ asset('assets/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>-->
        <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

        <!-- start - This is for export functionality only -->
            <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
            
        <!-- end - This is for export functionality only -->

        <script>
            $(document).ready(function() {
                $('#exposureTable').DataTable({
                    "displayLength": 100,
                    dom: 'Bfrtip',
                    // buttons: [
                    //     'copy', 'csv', 'pdf'
                    buttons: [{
                        extend: 'csv',
                        text: 'CSV',
                        filename: 'Nextrack - Exposures - {{ $widgetRange }}',
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        filename: 'Nextrack - Exposures - {{ $widgetRange }}',
                    },
                        'copy'
                    ],
                });
            });

            jQuery('#datepicker-autoclose').daterangepicker({
                buttonClasses: ['btn', 'btn-sm'],
                applyClass: 'btn-danger',
                cancelClass: 'btn-inverse',
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });

            function getSensors(site)
            {
                innerHTML = "<option value=\"0\">---Please select---</option>"

                jQuery.getJSON('/getSiteSensors/' + site, function (details) {
                    $.each(details, function (d, detail) {
                        console.log(detail)

                        innerHTML += "<option value=\"" + detail.id + "\">"
                            innerHTML += detail.name + " :: " + detail.type + " :: " + detail.thingsboard_id
                        innerHTML += "</option>"
                        
                    });

                    document.getElementById("sensor").innerHTML = innerHTML

                });
            }

            function getTypes(device)
            {
                innerHTML = ""

                jQuery.getJSON('/getDeviceReadingTypes/' + device, function (details) {
                $.each(details, function (d, detail) {
                    console.log(detail)

                    innerHTML += "<option value=\"" + detail.id + "\">" + detail.name + "</option>"
                });

                document.getElementById("reading").innerHTML = innerHTML
            });
            }
        </script>

    @endsection
