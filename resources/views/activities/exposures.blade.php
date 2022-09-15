@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        <link href="{{ asset('css/dots.css') }}" rel="stylesheet">
        

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Exposures</h4>
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
                            <h3 class="box-title">Exposures</h3>
                            <div class="table-responsive">
                                <table id="accountTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($exposuresArray as $exposure)
                                            <tr style="cursor: pointer;" onClick="window.location.href='/exposurePerson/{{ $exposure['profile']->id }}'">
                                                <td>{{ $exposure['profile']->name }}</td>
                                                <td>
                                                    @if($exposure['exposure'] == "ok")
                                                        <span class="smallGreenDot">&nbsp;</span> Ok
                                                    @elseif($exposure['exposure'] == "not ok")
                                                        <span class="smallRedDot">&nbsp;</span> Not Ok
                                                    @elseif($exposure['exposure'] == "unknown")
                                                        <span class="smallOrangeDot">&nbsp;</span> Unknown
                                                    @else
                                                        <span class="smallOrangeDot">&nbsp;</span> Monitor
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                &nbsp;<br>&nbsp;<br>
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
            $('#accountTable').DataTable({
                "displayLength": 100,
            });
        });
                
        </script>

    @endsection