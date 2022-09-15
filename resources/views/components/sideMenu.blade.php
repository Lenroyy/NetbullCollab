@extends('components.topMenu')

@section('sideMenu')

<!-- Left navbar-header -->
<div class="navbar-default sidebar" role="navigation">
    <br>
            <div class="sidebar-nav navbar-collapse slimscrollsidebar">
                
                <ul class="nav" id="side-menu">
                    <li class="sidebar-search hidden-sm hidden-md hidden-lg">
                        <!-- input-group -->
                        <div class="input-group custom-search-form">
                            <input type="text" class="form-control" placeholder="Search..."> <span class="input-group-btn">
                            <button class="btn btn-default" type="button"> <i class="fa fa-search"></i> </button>
                            </span> 
                        </div>
                        <!-- /input-group -->
                    </li>
                    @if(in_array("sites:view", $standardDisplay['permissions']) OR in_array("sites:view-all", $standardDisplay['permissions'])) 
                        <li><a href="/sites"><i class="ti-location-pin"></i><span class="hide-menu"> &nbsp; Sites</span></a></li>
                    @endif
                    <li><a href="#" class="waves-effect"><i class="icon-user"></i> <span class="hide-menu"> &nbsp; People <span class="fa arrow"></span></span></a>
                        <ul class="nav nav-second-level">
                            @if(in_array("users:view", $standardDisplay['permissions'])) 
                                <li><a href="/users">Users</a></li>
                            @endif
                            @if(in_array("builders:view", $standardDisplay['permissions'])) 
                                <li><a href="/builders">Builders</a></li>
                            @endif
                            @if(in_array("contractors:view", $standardDisplay['permissions'])) 
                                <li><a href="/contractors">Contractors</a></li>
                            @endif
                            @if(in_array("hygenists:view", $standardDisplay['permissions'])) 
                                <li><a href="/hygenists">Hygienists</a></li>
                            @endif
                            @if(in_array("providers:view", $standardDisplay['permissions'])) 
                                <li><a href="/servicePartners">Service providers</a></li>
                            @endif
                        </ul>
                    </li>
                    <li> <a href="#" class="waves-effect"><i class="icon-screen-tablet"></i> <span class="hide-menu"> &nbsp; Equipment<span class="fa arrow"></span></span></a>
                        <ul class="nav nav-second-level">
                            @if(in_array("controls:view", $standardDisplay['permissions'])) 
                                <li><a href="/controls">Controls</a></li>
                            @endif
                            @if(in_array("controls:order", $standardDisplay['permissions'])) 
                                <li><a href="/orderControl/0">Request controls</a></li>
                            @endif
                        </ul>
                    </li>
                    <li> <a href="#" class="waves-effect"><i class="icon-notebook"></i> <span class="hide-menu"> &nbsp; Activities<span class="fa arrow"></span></span></a>
                        <ul class="nav nav-second-level">
                            @if(in_array("activity:log", $standardDisplay['permissions'])) 
                                <li><a href="/logActivity/0">New activity</a></li>
                            @endif
                            <li><a href="/exposures">View exposures</a></li>
                            @if(in_array("history:view", $standardDisplay['permissions'])) 
                                <li><a href="/siteHistory">Site history</a></li>
                            @endif
                            @if(in_array("tasks:view", $standardDisplay['permissions'])) 
                                <li><a href="/tasks">Tasks</a></li>
                            @endif
                        </ul>
                    </li>
                    @if(in_array("training:view", $standardDisplay['permissions'])) 
                        <li><a href="/training"><i class="icon-graduation"></i><span class="hide-menu"> &nbsp; Marketplace</span></a></li>
                    @endif
                    @if(in_array("reports:view", $standardDisplay['permissions'])) 
                    <li> 
                        <a href="#" class="waves-effect"><i class="icon-book-open"></i> <span class="hide-menu"> &nbsp; Reports<span class="fa arrow"></span></span></a>
                        <ul class="nav nav-second-level">
                            <!--<li><a href="/reports/individualExposures">Individual exposures</a></li>
                            <li><a href="/reports/individualExposures">Zones exposures</a></li>-->
                            <li><a href="/reports/activities">Activities</a></li>
                            <li><a href="/reports/logs">Team logs</a></li>
                            <li><a href="/reports/controlUsage">Control usage</a></li>
                            <li><a href="/reports/participation">Participation</a></li>
                            <li><a href="/reports/exposure">Exposures</a></li>
                            @if($standardDisplay['profile']->super_user == 1)
                                <li><a href="/reports/billing">Billing report</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    <li> <a href="#" class="waves-effect"><i class="icon-wrench"></i> <span class="hide-menu"> &nbsp; Utilities<span class="fa arrow"></span></span></a>
                        <ul class="nav nav-second-level">
                            <!--<li><a href="/api">API</a></li>-->
                            {{-- <li><a href="https://nextrack.freshdesk.com/support/home" target="_blank">Knowledge base</a></li> --}}
                            @if(in_array("import:view", $standardDisplay['permissions']))                                     
                                <li> <a href="javascript:void(0)" class="waves-effect">Import <span class="fa arrow"></span></a>
                                    <ul class="nav nav-third-level">
                                        <li><a href="/utilities/import/invites">Import invites</a></li> 
                                    </ul>
                                </li>
                            @endif
                            @if(in_array("export:view", $standardDisplay['permissions'])) 
                                <!--<li><a href="/export">Export</a></li>-->
                            @endif
                            @if(in_array("account:view", $standardDisplay['permissions'])) 
                                <li><a href="/account">Account</a></li>
                            @endif
                        </ul>
                    </li>
                    <li> <a href="#" class="waves-effect"><i class="fa-solid fa-question"></></i> <span class="hide-menu"> &nbsp; Help<span class="fa arrow"></span></span></a>
                        <ul class="nav nav-second-level">
                            <li><a href="https://nextrack.tribalhabits.com/accounts/sign_in#/" target="_blank" >Access the Academy</a></li>
                            <li><a href="https://nextrack.freshdesk.com/support/solutions" target="_blank">Search the Knowledge Base</a></li>
                            <li><a href="https://nextrack-helpdesk.atlassian.net/servicedesk/customer/portal/1/group/1/create/1" target="_blank">Submit a Support ticket</a></li>
                          
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Left navbar-header end -->


        <!-- Page Content -->
        <div id="page-wrapper">
            <div class="container-fluid">

            @yield('content')

            <!-- .right-sidebar -->
            <div class="right-sidebar" style="overflow: auto;">
                <div class="slimscrollright">
                    <div class="rpanel-title"> Setup <span><i class="ti-close right-side-toggle"></i></span> </div>
                    <div class="r-panel-body">
                        <ul class="nav" id="side-menu">
                            @if(in_array("trades:setup", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user ==1) 
                                <li><a href="/setup/trades/0" class="waves-effect"><i class="ti-ruler-pencil"></i> <span class="hide-menu"> &nbsp; Trades</span></a></li>
                            @endif

                            @if(in_array("activities:setup", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user ==1)
                                <li><a href="/setup/activities" class="waves-effect"><i class="icon-notebook"></i> <span class="hide-menu"> &nbsp; Activities</span></a></li>
                            @endif
                            
                            @if(in_array("swms:setup", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user ==1)
                                <li><a href="/setup/assessments" class="waves-effect"><i class="ti-notepad"></i> <span class="hide-menu"> &nbsp; Assessments</span></a></li>
                            @endif
                            
                            @if(in_array("hazards:setup", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user ==1)
                                <li><a href="/setup/hazards/0" class="waves-effect"><i class="fa fa-bolt"></i> <span class="hide-menu"> &nbsp; Hazards</span></a></li>
                            @endif
                            
                            @if(in_array("samples:setup", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user ==1)
                                <li><a href="/setup/samples/0" class="waves-effect"><i class="ti-envelope"></i> <span class="hide-menu"> &nbsp; Samples</span></a></li>
                            @endif
                            
                            @if(in_array("controls:setup", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user ==1)
                                <li><a href="/setup/controlTypes" class="waves-effect"><i class="ti-truck"></i> <span class="hide-menu"> &nbsp; Controls</span></a></li>
                            @endif

                            @if(in_array("controls:setup", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user ==1)
                                <li><a href="/setup/controlGroups/0" class="waves-effect"><i class="ti-bag"></i> <span class="hide-menu"> &nbsp; Control type groups</span></a></li>
                            @endif
                            
                            @if(in_array("iot:setup", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user ==1)
                                <li><a href="/setup/rules" class="waves-effect"><i class="ti-link"></i> <span class="hide-menu"> &nbsp; Reading rules</span></a></li>
                            @endif

                            @if(in_array("exposures:setup", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user ==1) 
                                <li><a href="/setup/exposures/0" class="waves-effect"><i class="ti-target"></i> <span class="hide-menu"> &nbsp; Exposures</span></a></li>
                            @endif
                            
                            @if(in_array("training:setup", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user ==1)
                                <li><a href="/setup/training" class="waves-effect"><i class="icon-graduation"></i> <span class="hide-menu"> &nbsp; Services</span></a></li>
                            @endif

                            @if(in_array("training:setup", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user ==1)
                                <li><a href="/setup/trainingTypes/0" class="waves-effect"><i class="fa fa-cutlery"></i> <span class="hide-menu"> &nbsp; Service types</span></a></li>
                            @endif
                            
                            @if(in_array("permits:setup", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user ==1)
                                <li><a href="/setup/permits" class="waves-effect"><i class="ti-pulse"></i> <span class="hide-menu"> &nbsp; Entry requirements</span></a></li>
                            @endif
                            
                            @if(in_array("secGroups:setup", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user ==1)
                                <li><a href="/setup/securityGroups" class="waves-effect"><i class="ti-lock"></i> <span class="hide-menu"> &nbsp; Security Groups</span></a></li>
                            @endif
                            
                            @if(in_array("news:setup", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user ==1)
                                <li><a href="/setup/news" class="waves-effect"><i class="ti-agenda"></i> <span class="hide-menu"> &nbsp; News</span></a></li>
                            @endif
                            
                            @if(in_array("iot:setup", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user ==1)
                                <li><a href="/setup/integrations" class="waves-effect"><i class="ti-signal"></i> <span class="hide-menu"> &nbsp; Integrations</span></a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /.right-sidebar -->
@endsection