@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Builders</h4>
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
                            <h3 class="box-title">Builder profiles</h3>
                        </div>
                        <div class="col-md-6">
                            @if($standardDisplay['profile']->super_user == 1)
                                <a class="pull-right btn btn-info waves-effect waves-light" href="/editBuilder/0">
                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    New Builder
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
                        <table id="accountTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Members</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($builders as $builder)
                                <tr style="cursor: pointer;">    
                                    <td @if(in_array("builders:edit", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user == 1) onClick="window.location.href='/editBuilder/{{ $builder['builder']->id }}'" @endif>
                                        {{ $builder['builder']->name }}
                                    </td>
                                    <td @if(in_array("builders:edit", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user == 1) onClick="window.location.href='/editBuilder/{{ $builder['builder']->id }}'" @endif>
                                        {{ $builder['builder']->email }}
                                    </td>
                                    <td @if(in_array("builders:edit", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user == 1) onClick="window.location.href='/editBuilder/{{ $builder['builder']->id }}'" @endif>
                                        {{ $builder['builder']->phone }}
                                    </td>
                                    <td @if(in_array("builders:edit", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user == 1) onClick="window.location.href='/editBuilder/{{ $builder['builder']->id }}'" @endif>
                                        {{ $builder['count'] }}
                                    </td>
                                    <td>
                                        <div class="btn-group m-r-10">
                                            <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info dropdown-toggle waves-effect waves-light" type="button">Options <span class="caret"></span></button>
                                            <ul role="menu" class="dropdown-menu">
                                                <li>
                                                    @if(in_array("builders:edit", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user == 1)
                                                        <a href="/editBuilder/{{ $builder['builder']->id }}">Edit</a>
                                                    @endif
                                                </li>
                                                <li>
                                                    @if(in_array("builders:delete", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user == 1)
                                                        <a onclick="return confirm('Are you sure you want to Archive? This action can not be undone!')" href="/archiveBuilder/{{ $builder['builder']->id }}">Archive</a>
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