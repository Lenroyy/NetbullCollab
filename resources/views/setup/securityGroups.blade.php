@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Security Groups</h4>
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
                    <ul class="nav nav-tabs tabs customtab">
                        <li class="active tab">
                            <a href="#system" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs">System groups</span> </a>
                        </li>
                        <li class="tab">
                            <a href="#builder" data-toggle="tab"> <span class="visible-xs"><i data-icon="U" class="linea-icon linea-basic"></i></span> <span class="hidden-xs">Builder roles </span> </a>
                        </li>                        
                        <li class="tab">
                            <a href="#contractor" data-toggle="tab"> <span class="visible-xs"><i data-icon="?" class="linea-icon linea-basic"></i></span> <span class="hidden-xs">Contractor roles </span> </a>
                        </li>
                        <li class="tab">
                            <a href="#hygenist" data-toggle="tab"> <span class="visible-xs"><i data-icon="?" class="linea-icon linea-basic"></i></span> <span class="hidden-xs">Hygienist roles </span> </a>
                        </li>
                    </ul>
                    <div class="tab-content">

                        <div class="tab-pane active" id="system">
                            <span class="pull-right"><a class="btn btn-primary" href="/setup/securityGroup/system/0">New Group</a></span>
                            <div class="table-responsive">
                                <table class="table table-hover" id="systemTable">
                                    <thead>
                                        <tr>
                                            <th>
                                                Name
                                            </th>                                                                              
                                            <th>
                                                &nbsp;
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($groups as $gp)
                                            @if($gp->type == "system")
                                                <tr style="cursor: pointer">
                                                    <td onClick="window.location.href='/setup/securityGroup/system/{{ $gp->id }}'">
                                                        <p class="text-muted">{{ $gp->name }}</p>
                                                    </td>
                                                    <td>
                                                        <p class="text-muted"><a href="/setup/archive/securityGroup/{{ $gp->id }}">Archive</a></p>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="builder">
                            <span class="pull-right"><a class="btn btn-primary" href="/setup/securityGroup/builder/0">New Group</a></span>
                            <div class="table-responsive">
                                <table class="table table-hover" id="builderTable">
                                    <thead>
                                        <tr>
                                            <th>
                                                Name
                                            </th>                                                                              
                                            <th>
                                                Billable
                                            </th>                                                                              
                                            <th>
                                                &nbsp;
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($groups as $gp)
                                            @if($gp->type == "builder")
                                                <tr style="cursor: pointer">
                                                    <td onClick="window.location.href='/setup/securityGroup/builder/{{ $gp->id }}'">
                                                        <p class="text-muted">{{ $gp->name }}</p>
                                                    </td>
                                                    <td>
                                                        @if($gp->billable == 1)
                                                            <i class="fa fa-check-circle"></i>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <p class="text-muted"><a href="/setup/archive/securityGroup/{{ $gp->id }}">Archive</a></p>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="contractor">
                            <span class="pull-right"><a class="btn btn-primary" href="/setup/securityGroup/contractor/0">New Group</a></span>
                            <div class="table-responsive">
                                <table class="table table-hover" id="contractorTable">
                                    <thead>
                                        <tr>
                                            <th>
                                                Name
                                            </th>        
                                            <th>
                                                Billable
                                            </th>                                                                         
                                            <th>
                                                &nbsp;
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($groups as $gp)
                                            @if($gp->type == "contractor")
                                                <tr style="cursor: pointer">
                                                    <td onClick="window.location.href='/setup/securityGroup/contractor/{{ $gp->id }}'">
                                                        <p class="text-muted">{{ $gp->name }}</p>
                                                    </td>
                                                    <td>
                                                        @if($gp->billable == 1)
                                                            <i class="fa fa-check-circle"></i>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <p class="text-muted"><a href="/setup/archive/securityGroup/{{ $gp->id }}">Archive</a></p>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="hygenist">
                            <span class="pull-right"><a class="btn btn-primary" href="/setup/securityGroup/hygenist/0">New Group</a></span>
                            <div class="table-responsive">
                                <table class="table table-hover" id="hygenistTable">
                                    <thead>
                                        <tr>
                                            <th>
                                                Name
                                            </th>       
                                            <th>
                                                Billable
                                            </th>                                                                          
                                            <th>
                                                &nbsp;
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($groups as $gp)
                                            @if($gp->type == "hygenist")
                                                <tr style="cursor: pointer">
                                                    <td onClick="window.location.href='/setup/securityGroup/hygenist/{{ $gp->id }}'">
                                                        <p class="text-muted">{{ $gp->name }}</p>
                                                    </td>
                                                    <td>
                                                        @if($gp->billable == 1)
                                                            <i class="fa fa-check-circle"></i>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <p class="text-muted"><a href="/setup/archive/securityGroup/{{ $gp->id }}">Archive</a></p>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- /.row -->


    

        <!-- JS DEPENDENCIES -->
        @include('components.footer') 
        <!-- END JS DEPENDENCIES -->
                    
        <!-- Page specific Javascript -->
        <script src="{{ asset('js/jasny-bootstrap.js') }}"></script>
        <!-- <script src="{{ asset('assets/dropzone-master/dist/dropzone.js') }}"></script> -->


    @endsection