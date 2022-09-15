@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Integrations</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>

        <form action="/setup/integrations" method="POST">
            @csrf
            <!-- .row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <h3 class="box-title">Integrations setup</h3>
                    </div>
                </div>
            </div>
            <span class="pull-right"><input type="submit" name="submit" value="Save" class="btn btn-success"></span><br>&nbsp;
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <ul class="nav nav-tabs tabs customtab">
                            <li class="active tab">
                                <a href="#integrations" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="ti-link"></i></span> <span class="hidden-xs">Integrations</span> </a>
                            </li>
                            
                            <li class="tab">
                                <a href="#monitors" data-toggle="tab"> <span class="visible-xs"><i class="ti-pulse"></i></span> <span class="hidden-xs">Thingsboard monitors </span> </a>
                            </li>                    

                            <li class="tab">
                                <a href="#costCenters" data-toggle="tab"> <span class="visible-xs"><i class="ti-money"></i></span> <span class="hidden-xs">Cost Centers </span> </a>
                            </li>   
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="integrations">
                                <h3 class="box-title">API integrations</h3>
                                <div class="table-responsive"> 
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Application Name</th>
                                                <th>Base URL</th>
                                                <th>Username</th>
                                                <th>Password</th>
                                                <th>Current Token</th>
                                                <th>Token TTL</th>
                                                <th>Current Refresh Token</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($integrations as $integration)
                                                <input type="hidden" name="integrationID[]" value="{{ $integration->id }}">
                                                <tr>
                                                    <td>{{ $integration->application_name }}</td>
                                                    <td><input type="text" name="baseURL[]" class="form-control form-control-line" value="{{ $integration->base_url }}"></td>
                                                    <td><input type="text" name="username[]" class="form-control form-control-line" value="{{ $integration->username }}"></td>
                                                    <td><input type="text" name="password[]" class="form-control form-control-line" value="{{ $integration->password }}"></td>
                                                    <td>{{ substr($integration->token, 0, 15) }}...</td>
                                                    <td>{{ substr($integration->token_ttl, 0, 15) }}...</td>
                                                    <td>{{ substr($integration->refresh_token, 0, 15) }}...</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane" id="monitors">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3 class="box-title">Monitors</h3>
                                    </div>
                                    <div class="col-md-6 pull-right">
                                        <a href="/setup/refreshMonitors" class="btn btn-primary pull-right">Fetch new monitors</a>
                                    </div>
                                </div>
                                <div class="table-responsive"> 
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Name</th>
                                                <th>Thingsboard ID</th>
                                                <th>Attached to</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($monitors as $monitor)
                                                <tr>
                                                    <td>{{ $monitor->type }}</td>
                                                    <td>{{ $monitor->name }}</td>
                                                    <td>{{ $monitor->thingsboard_id }}</td>
                                                    <td>
                                                        @if($monitor->control_id > 0)
                                                            <a href="/editControl/{{ $monitor->control_id }}">{{ $monitor->Control->Controls_Type->name }}</a>
                                                        @else
                                                            <a href="/setup/controlTypes">None</a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group m-r-10">
                                                            <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info dropdown-toggle waves-effect waves-light" type="button"><span class="caret"></span></button>
                                                            <ul role="menu" class="dropdown-menu">
                                                                <li>
                                                                    <a href="/setup/monitor/{{ $monitor->id }}">Configure</a>
                                                                </li>
                                                                <li>
                                                                    <a href="/archiveMonitor/{{ $monitor->id }}">Archive</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                    <br><br><br><br>
                                </div>
                            </div>
                            <div class="tab-pane" id="costCenters">
                                <h3 class="box-title">Cost Centers</h3>
                                <div class="row">
                                    <div class="col-md-12">
                                        <span class="pull-right">
                                            <button type="button" class="btn btn-primary" onClick="runRetrieve()">Update Cost Centers</button>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                        <label class="col-md-12">Subscriptions</label>
                                            <select name="subscriptions" class="form-contol form-control-line">
                                                <option value="0">---Please select---</option>
                                                @foreach($costCenters as $costCenter)
                                                    <option value="{{ $costCenter->cost_center_id }}" @if($simPROSettings) @if($simPROSettings->subscriptions == $costCenter->cost_center_id) selected @endif @endif>
                                                        @if($costCenter->company_id == 2)
                                                            Nextrack :: 
                                                        @elseif($costCenter->company_id == 3)
                                                            Defender :: 
                                                        @else
                                                            Unknown ::
                                                        @endif
                                                        {{ $costCenter->cost_center_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-md-12">Marketplace</label>
                                            <div class="col-md-12">
                                                <select name="marketplace" class="form-contol form-control-line">
                                                    <option value="0">---Please select---</option>
                                                    @foreach($costCenters as $costCenter)
                                                        <option value="{{ $costCenter->cost_center_id }}" @if($simPROSettings) @if(!empty($simPROSettings->marketplace)) @if($simPROSettings->marketplace == $costCenter->cost_center_id) selected @endif @endif @endif>
                                                            @if($costCenter->company_id == 2)
                                                                Nextrack :: 
                                                            @elseif($costCenter->company_id == 3)
                                                                Defender :: 
                                                            @else
                                                                Unknown ::
                                                            @endif
                                                            {{ $costCenter->cost_center_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>    
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-md-12">Lease and Monitoring</label>
                                            <div class="col-md-12">
                                                <select name="external_lease" class="form-contol form-control-line">
                                                    <option value="0">---Please select---</option>
                                                    @foreach($costCenters as $costCenter)
                                                        <option value="{{ $costCenter->cost_center_id }}" @if($simPROSettings) @if($simPROSettings->external_lease == $costCenter->cost_center_id) selected @endif @endif>
                                                            @if($costCenter->company_id == 2)
                                                                Nextrack :: 
                                                            @elseif($costCenter->company_id == 3)
                                                                Defender :: 
                                                            @else
                                                                Unknown ::
                                                            @endif
                                                            {{ $costCenter->cost_center_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>    
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-md-12">Monitoring only</label>
                                            <div class="col-md-12">
                                                <select name="monitoring_only" class="form-contol form-control-line">
                                                    <option value="0">---Please select---</option>
                                                    @foreach($costCenters as $costCenter)
                                                        <option value="{{ $costCenter->cost_center_id }}" @if($simPROSettings) @if($simPROSettings->monitoring_only == $costCenter->cost_center_id) selected @endif @endif>
                                                            @if($costCenter->company_id == 2)
                                                                Nextrack :: 
                                                            @elseif($costCenter->company_id == 3)
                                                                Defender :: 
                                                            @else
                                                                Unknown ::
                                                            @endif{{ $costCenter->cost_center_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>    
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>simPRO ID</th>
                                                <th>Company</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($costCenters as $costCenter)
                                                <tr>
                                                    <td>{{ $costCenter->cost_center_name }}</td>
                                                    <td>{{ $costCenter->cost_center_id }}</td>
                                                    <td>
                                                        @if($costCenter->company_id == 2)
                                                            Nextrack
                                                        @elseif($costCenter->company_id == 3)
                                                            Defender
                                                        @else
                                                            Unknown
                                                        @endif    
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- /.row -->

        <!-- JS DEPENDENCIES -->
        @include('components.footer') 
        <!-- END JS DEPENDENCIES -->
                    
        <!-- Page specific Javascript -->
        <script>
            function runRetrieve()
            {
                jQuery.getJSON('/updateCostCenters', function (details) {
                    
                    alert(details + " Cost Centers Processed.  Refresh page to see outcome.")    

                });
            }
        </script>

    @endsection