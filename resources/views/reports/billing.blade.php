@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('assets/plugins/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">        
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Reports - Billing</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>
                
        <!-- .row -->
        <form action="/reports/billing" method="post">
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
                                <label>Date range</label><small> - {{ $dateRange }} </small>
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
                            <h3>Builders</h3>
                            <div class="table-responsive">
                                <table id="accountTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Customer</th>
                                            <th>No Users</th>
                                            <th>User Cost</th>
                                            <th>User Total</th>
                                            <th>No Sites</th>
                                            <th>Site Cost</th>
                                            <th>Site Total</th>
                                            <th>Total Controls</th>
                                            <th>Total Services</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($results['builders'] as $result)
                                            <tr>
                                                <td>{{ $result['profile']->name }}</td>
                                                <td>{{ $result['no_users'] }}</td>
                                                <td>${{ $result['user_cost'] }}</td>
                                                <td>${{ $result['user_cost'] * $result['no_users'] }}</td>
                                                <td>{{ $result['no_sites'] }}</td>
                                                <td>${{ $result['site_cost'] }}</td>
                                                <td>${{ $result['site_cost'] * $result['no_sites'] }}</td>
                                                <td>${{ $result['controlsTotal'] }}</td>
                                                <td>${{ $result['servicesTotal'] }}</td>
                                                <td>${{ $result['total'] }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>BREAKDOWN</strong></td>
                                                <td>CONTROLS</td>
                                                @if(!empty($result['controls']))
                                                    @foreach($result['controls'] as $control)                                                        
                                                        <td>{{ $control['type']->name }}</td>
                                                        <td>{{ $control['qty'] }}</td>
                                                        <td>${{ $control['typeTotal'] }}</td>
                                                    @endforeach
                                                @else
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                @endif
                                                <td>&nbsp;</td>
                                                <td>SERVICES</td>
                                                @if(!empty($result['services']))
                                                    @foreach($result['services'] as $service)                                                        
                                                        <td>{{ $service['service'] }}</td>
                                                        <td>{{ $service['qty'] }}</td>
                                                        <td>${{ $service['total'] }}</td>
                                                    @endforeach
                                                @else
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            <br><hr><br>
                            <h3>Contractors</h3>
                            <div class="table-responsive">
                                <table id="accountTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Customer</th>
                                            <th>No Users</th>
                                            <th>User Cost</th>
                                            <th>User Total</th>
                                            <th>No Sites</th>
                                            <th>Site Cost</th>
                                            <th>Site Total</th>
                                            <th>Total Controls</th>
                                            <th>Total Services</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($results['contractors'] as $result)
                                            <tr>
                                                <td>{{ $result['profile']->name }}</td>
                                                <td>{{ $result['no_users'] }}</td>
                                                <td>${{ $result['user_cost'] }}</td>
                                                <td>${{ $result['user_cost'] * $result['no_users'] }}</td>
                                                <td>{{ $result['no_sites'] }}</td>
                                                <td>${{ $result['site_cost'] }}</td>
                                                <td>${{ $result['site_cost'] * $result['no_sites'] }}</td>
                                                <td>${{ $result['controlsTotal'] }}</td>
                                                <td>${{ $result['servicesTotal'] }}</td>
                                                <td>${{ $result['total'] }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>BREAKDOWN</strong></td>
                                                <td>CONTROLS</td>
                                                @if(!empty($result['controls']))
                                                    @foreach($result['controls'] as $control)                                                        
                                                        <td>{{ $control['type']->name }}</td>
                                                        <td>{{ $control['qty'] }}</td>
                                                        <td>${{ $control['typeTotal'] }}</td>
                                                    @endforeach
                                                @else
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                @endif
                                                <td>&nbsp;</td>
                                                <td>SERVICES</td>
                                                @if(!empty($result['services']))
                                                    @foreach($result['services'] as $service)                                                        
                                                        <td>{{ $service['service'] }}</td>
                                                        <td>{{ $service['qty'] }}</td>
                                                        <td>${{ $service['total'] }}</td>
                                                    @endforeach
                                                @else
                                                    <td>-</td>
                                                    <td>-</td>
                                                    <td>-</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            <br><hr><br>
                            <h3>Hygienists</h3>
                            <div class="table-responsive">
                                <table id="billingTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Customer</th>
                                            <th>No Users</th>
                                            <th>User Cost</th>
                                            <th>User Total</th>
                                            <th>No Sites</th>
                                            <th>Site Cost</th>
                                            <th>Site Total</th>
                                            <th>Total Controls</th>
                                            <th>Total Services</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($results['hygenists'] as $result)
                                            <tr>
                                                <td>{{ $result['profile']->name }}</td>
                                                <td>{{ $result['no_users'] }}</td>
                                                <td>${{ $result['user_cost'] }}</td>
                                                <td>${{ $result['user_cost'] * $result['no_users'] }}</td>
                                                <td>{{ $result['no_sites'] }}</td>
                                                <td>${{ $result['site_cost'] }}</td>
                                                <td>${{ $result['site_cost'] * $result['no_sites'] }}</td>
                                                <td>${{ $result['controlsTotal'] }}</td>
                                                <td>${{ $result['servicesTotal'] }}</td>
                                                <td>${{ $result['total'] }}</td>
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

        <!-- JS DEPENDENCIES -->
        @include('components.footer') 
        <!-- END JS DEPENDENCIES -->
                    
        <!-- Page specific Javascript -->
        <script src="{{ asset('assets/plugins/bower_components/moment/moment.js') }}"></script>
        <script src="{{ asset('assets/plugins/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
        <script src="{{ asset('assets/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>

        <script>     
            $(document).ready(function() {
                $('#billingTable').DataTable({
                    "displayLength": 100,
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