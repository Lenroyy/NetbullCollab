@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('assets/plugins/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">
        
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Reports - Individual exposures</h4>
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
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="box-title">Filters</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label>Person</label>
                            <select class="form-control form-control-line">
                                <option>Paul Brennan</option>
                                <option>Curtis Thomson</option>
                                <option>Bob Dillan</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Site</label>
                            <select class="form-control form-control-line">
                                <option>Test site</option>
                                <option>Queenswharf</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Date range</label>
                            <div class="input-group">
                                <input type="text" class="form-control input-daterange-datepicker" id="datepicker-autoclose" placeholder="dd-mm-yyyy - dd-mm-yyyy" name="search_date" @if(isset($widgetRange)) value="{{ $widgetRange }}" @endif><span class="input-group-addon"><i class="icon-calender"></i></span> 
                            </div>    
                        </div>
                        <div class="col-md-3">
                            &nbsp;
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                            <div class="table-responsive">
                                <table id="accountTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>column 1</th>
                                            <th>column 2</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <tr style="cursor: pointer;">    
                                            <td onClick="window.location.href='/editProfile/0'">Curtis Thomson</td>
                                            <td onClick="window.location.href='/editProfile/0'">curtis.thomson@simprogroup.com</td>
                                        </tr>
                                        <tr style="cursor: pointer;">    
                                            <td onClick="window.location.href='/editProfile/0'">Paul Brennan</td>
                                            <td onClick="window.location.href='/editProfile/0'">paul.brennan@trieste.tech</td>
                                        </tr>
                                        <tr style="cursor: pointer;">    
                                            <td onClick="window.location.href='/editProfile/0'">Bob Dillan</td>
                                            <td onClick="window.location.href='/editProfile/0'">bob.dillan@trieste.tech</td>
                                        </tr>
                                    </tbody>
                                </table>
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
        <!-- start - This is for export functionality only -->
            <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
        <!-- end - This is for export functionality only -->

        <script>           
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