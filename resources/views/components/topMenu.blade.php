@extends('components.header')

@section('topMenu')
        <!-- Top Navigation -->
        <nav class="navbar navbar-default navbar-static-top m-b-0">
            <div class="navbar-header"> 
                <a class="navbar-toggle hidden-sm hidden-md hidden-lg " href="javascript:void(0)" data-toggle="collapse" data-target=".navbar-collapse">
                    <i class="ti-menu"></i>
                </a>
                <div class="top-left-part">
                    <a class="logo" href="/">
                        <span class="hidden-xs"><!--This is dark logo text-->
                            @if($standardDisplay['profile']->theme == "dark")
                                <img src="{{ asset('assets/images/logo/nextrack-inverted.png') }}" style="width: 100%" alt="home" class="dark-logo" />
                            @else
                                <img src="{{ asset('assets/images/logo/nextrack-inverted.png') }}" style="width: 100%" alt="home" class="light-logo" />
                            @endif
                        </span>
                    </a>
                </div>
            <ul class="nav navbar-top-links navbar-left hidden-xs">
                <li>
                    <a href="javascript:void(0)" class="open-close hidden-xs waves-effect waves-light"><i class="icon-arrow-left-circle ti-menu"></i></a>
                </li>
                <li>
                    <form role="search" class="app-search hidden-xs" action="/search" method="POST">
                        @csrf
                        <input type="text" name="search" placeholder="Search..." class="form-control"> 
                        <a href=""><i class="fa fa-search"></i></a> 
                    </form>
                </li>
            </ul>
            <ul class="nav navbar-top-links navbar-right pull-right">
                <li class="dropdown"> 
                    <a class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" href="#"><i class="icon-note"></i>
                        @if(count($standardDisplay['tasks']) > 0)
                            <div class="notify"><span class="heartbit"></span><span class="point"></span></div>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-tasks animated flipInY">
                        @foreach($standardDisplay['tasks'] as $task)
                            <li>
                                <a href="/editTask/{{ $task->id }}">
                                    <div>
                                        <p> <strong>{{ $task->subject }}</strong> <span class="pull-right text-muted">{{ $task->progress }}% Complete</span> </p>
                                        <div class="progress progress-striped active">
                                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{{ $task->progress }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $task->progress }}%"> <span class="sr-only">{{ $task->progress }}% Complete ({{ $task->status }})</span> </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="divider"></li>
                        @endforeach
                        <li>
                            <a class="text-center" href="/tasks"> <strong>See All Tasks</strong> <i class="fa fa-angle-right"></i> </a>
                        </li>
                    </ul>
                    <!-- /.dropdown-tasks -->
                </li>
                <!-- /.dropdown -->
                



                
                <!-- .Megamenu -->
                <li class="mega-dropdown"> <a class="dropdown-toggle waves-effect waves-light" data-toggle="dropdown" href="#"><span class="hidden-xs">Menu</span> <i class="icon-options-vertical"></i></a>
                    <ul class="dropdown-menu mega-dropdown-menu animated bounceInDown">
                        <li class="col-sm-3">
                            <ul>
                                <li class="dropdown-header">News</li>
                                <li>
                                    <img src="@if(!empty($standardDisplay['news']->image))/storage/{{ $standardDisplay['news']->image }} @else{{ asset('/images/logo/nextrack-inverted.png') }}@endif" style="width: 85%;"><br><br>
                                    <strong>{{ $standardDisplay['news']->name }}</strong><br><br>
                                    {{ $standardDisplay['news']->body }}
                                </li>
                            </ul>
                        </li>
                        <li class="col-sm-3">
                            <ul>
                                <li class="dropdown-header">People</li>
                                @if(in_array("sites:view", $standardDisplay['permissions'])) 
                                    <li><a href="/sites">Sites</a></li>
                                @endif
                                @if(in_array("contractors:view", $standardDisplay['permissions'])) 
                                    <li><a href="/contractors">Contractors</a></li>
                                @endif
                                @if(in_array("builders:view", $standardDisplay['permissions'])) 
                                    <li><a href="/builders">Builders</a></li>
                                @endif
                                @if(in_array("users:view", $standardDisplay['permissions'])) 
                                    <li><a href="/users">Users</a></li>
                                @endif
                                @if(in_array("hygenists:view", $standardDisplay['permissions']))
                                    <li><a href="/hygenists">Hygienists</a></li>
                                @endif
                                @if(in_array("providers:view", $standardDisplay['permissions']))
                                    <li><a href="/servicePartners">Service providers</a></li>
                                @endif
                            </ul>
                        </li>
                        <li class="col-sm-3">
                            <ul>
                                <li class="dropdown-header">Equipment</li>
                                @if(in_array("controls:view", $standardDisplay['permissions'])) 
                                    <li><a href="/controls">Controls</a></li>
                                @endif
                            </ul>
                        </li>
                        <li class="col-sm-3">
                            <ul>
                                <li class="dropdown-header">Activities</li>
                                @if(in_array("activity:log", $standardDisplay['permissions'])) 
                                    <a href="/logActivity/0" class="btn btn-primary"><i class="fa fa-plus"></i> New activity</a>
                                @endif
                                @if(in_array("history:view", $standardDisplay['permissions'])) 
                                    <li><a href="/siteHistory">Site history</a></li>
                                @endif
                                @if(in_array("tasks:view", $standardDisplay['permissions'])) 
                                    <li><a href="/tasks">Tasks</a></li>
                                @endif
                            </ul>
                        </li>
                    </ul>
                </li>
                @if(in_array("activity:log", $standardDisplay['permissions']))
                    <li>    
                        @if(is_object($standardDisplay['entry']))
                            <a href="/qrActivity/{{ $standardDisplay['entry']->site_id }}/{{ $standardDisplay['entry']->zone_id }}" class="btn btn-danger"><i class="fa fa-minus"></i> Exit zone</a>
                        @else
                            <a href="/logActivity/0" class="btn btn-primary"><i class="fa fa-plus"></i> Activity</a>
                        @endif
                    </li>
                @endif
                @if($standardDisplay['site'] > 0)
                    <li>
                        <a href="#" onClick="siteSignOff()" class="btn btn-info"><i class="fa fa-minus"></i> Sign out</a>
                    </li>
                @endif
                <!-- /.Megamenu -->
                <script>
                    function siteSignOff()
                    {
                        var r = confirm("Are you sure you're ready to sign off from site.");
                        if(r == true)
                        {
                            location.href="/site/logoff"
                        }
                    }
                </script>
                
                <!-- User profile menu -->
                <!-- .user dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#">
                        <img src="@if(!empty($standardDisplay['profile']->logo))/storage/{{ $standardDisplay['profile']->logo }} @else{{ asset('assets/images/users/user.jpg') }}@endif" alt="user-img" width="36" class="img-circle">
                        <b class="hidden-xs">
                            {{ $standardDisplay['profile']->name }}
                        </b> 
                    </a>
                    <ul class="dropdown-menu dropdown-user animated flipInY">
                        <li><a href="/editProfile/{{ $standardDisplay['profile']->id }}"><i class="ti-user"></i> My Profile</a></li>
                        {{-- <li><a href="/account"><i class="ti-credit-card"></i> Account</a></li> --}}
                        <li role="separator" class="divider"></li>
                        <li>
                            <form method="POST" action="/logout">
                                @csrf
                                <a href="/logout" onclick="event.preventDefault(); this.closest('form').submit();"> &nbsp; &nbsp; &nbsp; <i class="fa fa-power-off"></i> Logout</a>
                            </form>
                        </li>
                    </ul>
                    <!-- /.user dropdown-user -->
                </li>
                <!-- /.user dropdown -->


                
                
                <!-- Right menu -->
                @if( $standardDisplay['profile']->super_user ==1) 
                <li class="right-side-toggle"> <a class="waves-effect waves-light" href="javascript:void(0)"><i class="ti-settings"></i></a>
                </li>
                 @endif
                {{-- <li class="right-side-toggle"> <a class="waves-effect waves-light" href="javascript:void(0)"><i class="ti-settings"></i></a>
                </li> --}}
                <!-- /.dropdown -->
            </ul>
        </div>
        <!-- /.navbar-header -->
        <!-- /.navbar-top-links -->
        <!-- /.navbar-static-side -->
    </nav>
    <!-- End Top Navigation -->

    @yield('sideMenu')

@endsection