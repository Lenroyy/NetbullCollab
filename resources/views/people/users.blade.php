@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Users</h4>
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
                            <h3 class="box-title">User profiles</h3>
                        </div>
                        <div class="col-md-6">
                            @if($standardDisplay['profile']->super_user == 1)
                                <a class="pull-right btn btn-info waves-effect waves-light" href="/editProfile/0">
                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    New User
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
                                    <th>Mobile</th>
                                    <th>Phone</th>
                                    <th>Member hash</th>
                                    <th>&nbsp;</th> 
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($profiles as $profile)
                                    <tr style="cursor: pointer;">    
                                        <td><img src="@if(!empty($profile->logo))/storage/{{ $profile->logo }} @else{{ asset('assets/images/users/user.jpg') }}@endif" alt="user-img" width="36" class="img-circle"></td>
                                        <td @if($standardDisplay['profile']->id == $profile->id OR in_array("users:edit", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user == 1) onClick="window.location.href='/editProfile/{{ $profile->id }}'" @endif>{{ $profile->name }}</td>
                                        <td @if($standardDisplay['profile']->id == $profile->id OR in_array("users:edit", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user == 1) onClick="window.location.href='/editProfile/{{ $profile->id }}'" @endif>{{ $profile->email }}</td>
                                        <td @if($standardDisplay['profile']->id == $profile->id OR in_array("users:edit", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user == 1) onClick="window.location.href='/editProfile/{{ $profile->id }}'" @endif>{{ $profile->mobile }}</td>
                                        <td @if($standardDisplay['profile']->id == $profile->id OR in_array("users:edit", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user == 1) onClick="window.location.href='/editProfile/{{ $profile->id }}'" @endif>{{ $profile->phone }}</td>
                                        <td @if($standardDisplay['profile']->id == $profile->id OR in_array("users:edit", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user == 1) onClick="window.location.href='/editProfile/{{ $profile->id }}'" @endif>{{ $profile->member_hash }}</td>
                                        <td>
                                            <div class="btn-group m-r-10">
                                                <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info dropdown-toggle waves-effect waves-light" type="button">Options <span class="caret"></span></button>
                                                <ul role="menu" class="dropdown-menu">
                                                    <li>
                                                        @if($standardDisplay['profile']->id == $profile->id OR in_array("users:edit", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user == 1)
                                                            <a href="/editProfile/{{ $profile->id }}">Edit</a>
                                                        @endif
                                                    </li>
                                                    <li>
                                                        @if($standardDisplay['profile']->id == $profile->id OR in_array("users:delete", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user == 1)
                                                            <a onclick="return confirm('Are you sure you want to Archive? This action can not be undone!')" href="/archiveProfile/{{ $profile->id }}">Archive</a>
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