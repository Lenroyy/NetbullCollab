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
                <h4 class="page-title">Reports - Participation</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>
                
        <!-- .row -->
        <form action="/reports/participation" method="post">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 class="box-title">Filters</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label>Site</label>
                                <select class="form-control form-control-line" name="site">
                                    <option value="0" @if($site == "0") selected @endif>Select</option>
                                    @foreach($userSites as $us)
                                        <option value="{{ $us->id }}" @if($site == $us->id) selected @endif>{{ $us->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Date range</label> <small> - {{ $dateRange }}</small>
                                <div class="input-group">
                                <input type="text" class="form-control input-daterange-datepicker" id="datepicker-autoclose" placeholder="dd-mm-yyyy - dd-mm-yyyy" name="search_date" @if(isset($widgetRange)) value="{{ $widgetRange }}" @endif><span class="input-group-addon"><i class="icon-calender"></i></span> 
                                </div>    
                            </div>
                            <div class="col-md-3">
                                &nbsp;
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
                                <h3 class="box-title">Results</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="participationTable" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Trade</th>
                                                <th>Workers</th>
                                                <th>Est hazards</th>
                                                <th>Logged SWMS</th>
                                                <th>Activities</th>
                                                <th>Log ons</th>
                                                <th>Participation</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($results['results'] as $result)
                                                @if($result['workers'] > 0)
                                                    <tr>
                                                        <td>{{ $result['trade'] }}</td>
                                                        <td>{{ $result['workers'] }}</td>
                                                        <td>{{ $result['expectedSWMS'] }}</td>
                                                        <td>{{ $result['actualSMWS'] }}</td>
                                                        <td>{{ $result['activities'] }}</td>
                                                        <td>{{ $result['logons'] }}</td>
                                                        <td>{{ $result['participation'] }} %</td>
                                                        <td>
                                                            @if($result['participation'] < 20)
                                                                <span class="smallRedDot">&nbsp;</span>
                                                            @elseif($result['participation'] > 20 && $result['participation'] < 59.9)
                                                                <span class="smallOrangeDot">&nbsp;</span>
                                                            @else
                                                                <span class="smallGreenDot">&nbsp;</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td><strong>{{ $results['totalWorkers'] }}</strong></td>
                                                <td><strong>{{ $results['totalEstHazards'] }}</strong></td>
                                                <td><strong>{{ $results['totalActHazards'] }}</strong></td>
                                                <td><strong>{{ $results['totalActivities'] }}</strong></td>
                                                <td><strong>{{ $results['totalLogons'] }}</strong></td>
                                                <td><strong>{{ $results['totalParticipation'] }} %</strong></td>
                                                <td><strong>&nbsp;</strong></td>
                                            </tr>
                                        </tfoot>
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
        <script src="{{ asset('assets/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>

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
                $('#participationTable').DataTable({
                    "displayLength": 100,
                    dom: 'Bfrtip',
                    buttons: [{
                        extend: 'csv',
                        text: 'CSV',
                        filename: 'Nextrack - Participation - {{ $widgetRange }}',
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        filename: 'Nextrack - Participation - {{ $widgetRange }}',
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
        </script>

    @endsection
