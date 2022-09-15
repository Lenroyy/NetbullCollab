@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Sites</h4>
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
                            <h3 class="box-title">Sites</h3>
                            <div class="table-responsive">
                                <table id="accountTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Address</th>
                                            <th>City</th>
                                            <th>State</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sites as $site)
                                            <tr style="cursor: pointer;" onClick="window.location.href='/siteHistory/{{ $site->id }}'">
                                                <td>{{ $site->name }}</td>
                                                <td>{{ $site->address }}</td>
                                                <td>{{ $site->city }}</td>
                                                <td>{{ $site->state }}</td>
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

            if("{{ $alert }}")
            {
                $.toast({
                    heading: 'Success',
                    text: '{{ $alert }}',
                    position: 'top-right',
                    loaderBg:'#181a35;',
                    icon: 'info',
                    hideAfter: 3000, 
                    stack: 6
                });
            }

        });
                
        </script>

    @endsection