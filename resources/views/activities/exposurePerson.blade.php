@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        <link href="{{ asset('css/dots.css') }}" rel="stylesheet">
        

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Exposures for {{ $person->name }}</h4>
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
                            <h3 class="box-title">Exposures for the past 7 days</h3>
                            <div class="table-responsive">
                                <table id="accountTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Reading Type</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($exposures as $exposure)
                                            <tr style="cursor: pointer;" onClick="window.location.href='/exposureDetail/{{ $person->id }}/{{ $exposure['type'] }}'">
                                                <td>{{ $exposure['type'] }}</td>
                                                <td>
                                                    @if($exposure['outcome'] == "ok")
                                                        <span class="smallGreenDot">&nbsp;</span> Ok
                                                    @elseif($exposure['outcome'] == "not ok")
                                                        <span class="smallRedDot">&nbsp;</span> Not Ok
                                                    @elseif($exposure['outcome'] == "unknown")
                                                        <span class="smallBlackDot">&nbsp;</span> Unknown
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