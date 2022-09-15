@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">@if($type == 0) Hygienists @else Service providers @endif</h4>
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
                        <div class="col-md-6">
                            <h3 class="box-title">@if($type == 0)Hygienist @else Service provider @endif profiles</h3>
                        </div>
                        <div class="col-md-6">
                            @if($standardDisplay['profile']->super_user == 1)
                                <a class="pull-right btn btn-info waves-effect waves-light" href="/editHygenist/0/{{ $type }}"> 
                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    @if($type == 0)
                                        New Hygienist
                                    @else
                                        New Service provider
                                    @endif
                                </a>
                            @endif
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
                    <div class="table-responsive">
                        <table id="accountTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>&nbsp;</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($hygenists as $hygenist)
                                    <tr style="cursor: pointer;">    
                                        <td><img src="@if(!empty($hygenist->logo))/storage/{{ $hygenist->logo }} @else{{ asset('assets/images/hygenists/logo.png') }}@endif" alt="user-img" width="36" class="img-circle"></td>
                                        <td onClick="window.location.href='/editHygenist/{{ $hygenist->id }}/{{ $type }}'">{{ $hygenist->name }}</td>
                                        <td onClick="window.location.href='/editHygenist/{{ $hygenist->id }}/{{ $type }}'">{{ $hygenist->email }}</td>
                                        <td onClick="window.location.href='/editHygenist/{{ $hygenist->id }}/{{ $type }}'">{{ $hygenist->phone }}</td>
                                        <td>
                                            <div class="btn-group m-r-10">
                                                <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info dropdown-toggle waves-effect waves-light" type="button">Options <span class="caret"></span></button>
                                                <ul role="menu" class="dropdown-menu">
                                                    <li>
                                                        @if(in_array("hygenists:edit", $standardDisplay['permissions']))
                                                            <a href="/editHygenist/{{ $hygenist->id }}/{{ $type }}">Edit</a>
                                                        @endif
                                                    </li>
                                                    <li>
                                                        @if(in_array("hygenists:delete", $standardDisplay['permissions']))
                                                            <a onclick="return confirm('Are you sure you want to Archive? This action can not be undone!')" href="/archiveHygenist/{{ $hygenist->id }}">Archive</a>
                                                        @endif
                                                    </li>
                                                </ul>
                                            </div>
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

            $('#logTable').DataTable({
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