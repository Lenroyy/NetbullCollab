@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">User</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>
        <form class="form-horizontal form-material" action="/saveProfile/{{ $profile->id }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="{{ $profile->type }}">
            <div class="row">
                <div class="col-md-12">
                    <span class="pull-right">
                        <input type="submit" id="submit-all" name="submit" value="Save" class="btn btn-primary">
                        &nbsp;
                        <a class="btn btn-info" href="/users">Cancel</a>
                    </span>
                </div>
            </div>
            <br>
                    
            <!-- .row -->
            <div class="row">
                <div class="col-md-3 col-xs-12">
                    <div class="white-box">
                        <div class="user-bg"><img width="100%" alt="user" src="@if(!empty($profile->logo))/storage/{{ $profile->logo }} @else{{ asset('assets/images/users/user.jpg') }}@endif">
                            <div class="overlay-box">
                                <div class="user-content">
                                    <a href="javascript:void(0)"><img src="@if(!empty($profile->logo))/storage/{{ $profile->logo }} @else{{ asset('assets/images/users/user.jpg') }}@endif" class="thumb-lg img-circle" alt="img"></a>
                                    <h4 class="text-white">{{ $profile->name }}</h4>
                                    <h5 class="text-white">{{ $profile->email }}</h5>  
                                </div>
                            </div>
                        </div>
                        <div class="user-btm-box">
                            <div class="col-md-4 col-sm-4 text-center">
                                <p class="text-purple text-muted"><i class="fa fa-circle-o"></i> Last action</p>
                                <h3>
                                {{ $headings[0] }}
                            </div> 
                            <div class="col-md-4 col-sm-4 text-center">
                                <p class="text-purple text-muted"><i class="fa fa-unlock"></i> Security Group</p>
                                <h3>{{ $headings[1] }}</h3>
                            </div>
                            <div class="col-md-4 col-sm-4 text-center">
                                <p class="text-purple text-muted"><i class="fa fa-comments-o"></i> Activity</p>
                                <h3>{{ $headings[2] }}</h3> 
                            </div>
                            <div id="container"></div>
                        </div>
                    </div>
                </div> 
                



                        
                <div class="col-md-9 col-xs-12">
                    <div class="white-box">
                        <ul class="nav nav-tabs tabs customtab">
                            <li class="active tab">
                                <a href="#profile" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs">Profile</span> </a>
                            </li>
                            <li class="tab">
                                <a href="#memberships" data-toggle="tab"> <span class="visible-xs"><i class="ti-flag-alt"></i></span> <span class="hidden-xs">Memberships &nbsp @if(count($memberships) > 0)<span class="badge">{{ count($memberships) }}</span>@endif</span> </a>
                            </li>
                            <li class="tab">
                                <a href="#trades" data-toggle="tab"> <span class="visible-xs"><i class="ti-ruler-pencil"></i></span> <span class="hidden-xs">Trades &nbsp @if(count($trades) > 0)<span class="badge">{{ count($trades) }}</span>@endif</span> </a>
                            </li>
                            <li class="tab">
                                <a href="#permits" data-toggle="tab"> <span class="visible-xs"><i class="ti-pulse"></i></span> <span class="hidden-xs">Compliance &nbsp @if(count($permits) > 0)<span class="badge">{{ count($permits) }}</span>@endif</span> </a>
                            </li>
                            <li class="tab">
                                <a href="#attachments" data-toggle="tab"> <span class="visible-xs"><i class="ti-folder"></i></span> <span class="hidden-xs">Attachments &nbsp @if(count($files) > 0)<span class="badge">{{ count($files) }}</span>@endif</span> </a>
                            </li>
                            <li class="tab">
                                <a href="#history" data-toggle="tab"> <span class="visible-xs"><i class="ti-notepad"></i></span> <span class="hidden-xs">Activity </span> </a>
                            </li>                        
                            <li class="tab">
                                <a href="#logons" data-toggle="tab"> <span class="visible-xs"><i class="ti-marker"></i></span> <span class="hidden-xs">Sign-ins</span> </a>
                            </li>
                            <li class="tab">
                                <a href="#logs" data-toggle="tab"> <span class="visible-xs"><i class="ti-calendar"></i></span> <span class="hidden-xs">Logs</span> </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="profile">
                                <br>&nbsp;
                                <div class="form-group">
                                    <label class="col-md-12">Name</label>
                                    <div class="col-md-12">
                                        <input type="text" id="name" name="name" placeholder="Profile name" value="@if($profile->name != 'New'){{ $profile->name }}@endif" class="form-control form-control-line"> 
                                    </div>    
                                </div>
                                @if($standardDisplay['profile']->super_user == 1)
                                    <div class="form-group">
                                        <label class="col-md-12">simPRO ID</label>
                                        <div class="col-md-12">
                                            <input type="text" id="simpro_id" name="simpro_id" placeholder="simPRO ID" value="{{ $profile->simpro_id_1 }}" class="form-control form-control-line"> 
                                        </div>    
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12">Security Group</label>
                                        <div class="col-md-12">
                                            <select class="form-control form-control-line form-control-select" name="security_groups_id" id="security_groups_id">
                                                @foreach($securityGroups as $sg)
                                                    <option value="{{ $sg->id }}" @if($profile->security_group == $sg->id) selected @endif>{{ $sg->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @else
                                    <input type="hidden" name="security_groups_id" value="{{ $profile->security_group }}">
                                    <input type="hidden" name="simpro_id" value="{{ $profile->simpro_id_1 }}">
                                @endif
                                <div class="form-group">
                                    <label class="col-md-12">Email / Username</label>
                                    <div class="col-md-12">
                                        <input type="email" id="email" name="email" placeholder="Email address" value="{{ $profile->email }}" class="form-control form-control-line"> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Phone</label>
                                    <div class="col-md-12">
                                        <input type="text" name="phone" placeholder="Phone number" value="{{ $profile->phone }}" class="form-control form-control-line"> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Mobile</label>
                                    <div class="col-md-12">
                                        <input type="text" name="mobile" placeholder="Mobile phone number" value="{{ $profile->mobile }}" class="form-control form-control-line">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Theme</label>
                                    <div class="col-md-12">
                                        <select name="theme" class="form-control form-control-line">
                                            <option @if($profile->theme == "light") selected @endif value="light">Light</option>
                                            <option @if($profile->theme == "dark") selected @endif value="dark">Dark</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-12">Avatar</label>
                                    <div class="col-sm-12">
                                        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                            <div class="form-control" data-trigger="fileinput"> 
                                                <i class="glyphicon glyphicon-file fileinput-exists"></i> 
                                                <span class="fileinput-filename"></span>
                                            </div> 
                                            <span class="input-group-addon btn btn-default btn-file">
                                                <span class="fileinput-new">Select file</span> 
                                                <span class="fileinput-exists">Change</span>
                                                <input type="file" name="logo"> 
                                            </span> 
                                            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a> 
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="history">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="historyTable">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Date
                                                </th>
                                                <th>
                                                    Site
                                                </th>
                                                <th>
                                                    Action
                                                </th>
                                                <th>
                                                    Name
                                                </th>
                                                <th>
                                                    Start
                                                </th>
                                                <th>
                                                    Finish
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($history as $hi)
                                                <tr style="cursor: pointer" onClick="window.location.href='/siteHistory/{{ $hi->site_id }}'">
                                                    <td>
                                                        <p class="text-muted">@if(!empty($hi->created_at)){{ $hi->created_at->format('d-m-Y H:i') }}@endif</p>
                                                    </td>
                                                    <td>
                                                        @if(is_object($hi->Site))
                                                            <p class="text-muted">{{ $hi->Site->name }}</p>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <p class="text-muted">
                                                            @if(isset($hi->assessment_id))
                                                                Assessment / SWMS
                                                            @else
                                                                Time entry
                                                            @endif
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <p class="text-muted">
                                                            @if(isset($hi->assessment_id))
                                                                {{ $hi->Assessment->name }}
                                                            @else
                                                                -
                                                            @endif
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <p class="text-muted">
                                                            @if(isset($hi->assessment_id))
                                                                -
                                                            @else
                                                                {{ $hi->start }}
                                                            @endif
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <p class="text-muted">
                                                            @if(isset($hi->assessment_id))
                                                                -
                                                            @else
                                                                {{ $hi->finish }}
                                                            @endif
                                                        </p>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="memberships">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="col-md-12">Request to join</label>
                                            <div class="col-md-6">
                                                <input id="requestMembership" class="form-control form-control-line" name="requestMembership" placeholder="ID of the organisation you want to request to join">
                                            </div>    
                                            <div class="col-md-6">
                                                <a onClick="request(document.getElementById('requestMembership').value)" class="btn btn-primary" href="#">Request</a>
                                            </div>
                                        </div>  
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">    
                                            <label class="col-md-12">Your ID</label>
                                            {{ $profile->member_hash }}
                                            &nbsp; <i class="fa fa-copy" onClick="copyHash()" style="cursor: pointer;"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="membershipTable">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Name
                                                </th>
                                                <th>
                                                    Type
                                                </th>
                                                <th>
                                                    Role
                                                </th>
                                                <th>
                                                    Date joined
                                                </th>
                                                <th>
                                                    Date left
                                                </th>
                                                <th>
                                                    &nbsp;
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($memberships as $m)
                                                <tr>
                                                    <td>
                                                        <p class="text-muted">{{ $m->Organisation->name }}</p>
                                                    </td>
                                                    <td>
                                                        <p class="text-muted">{{ $m->Organisation->type }}</p>
                                                    </td>
                                                    <td>
                                                        <p class="text-muted">@if($m->security_group == 0) Worker @else {{ $m->Security_Group->name }} @endif</p>
                                                    </td>
                                                    <td>
                                                        <p class="text-muted">@if(!empty($m->joined)){{ $m->joined->format('d-m-Y') }}@endif</p>
                                                    </td>
                                                    <td>
                                                        <p class="text-muted">@if(!empty($m->exitted)){{ $m->exitted->format('d-m-Y') }}@endif</p>
                                                    </td>
                                                    <td>
                                                        <p class="text-muted">
                                                            @if($m->membership_status == "active")
                                                                <a href="/cancelMembership/{{ $m->id }}/{{ $profile->id }}">Leave</a>
                                                            @elseif($m->membership_status == "user requested")
                                                                Waiting for acceptance
                                                            @elseif($m->membership_status == "organisation requested")
                                                                <a href="/acceptMembership/{{ $m->id }}/accept/{{ $profile->id }}">Accept request</a>
                                                                | <a href="/acceptMembership/{{ $m->id }}/decline/{{ $profile->id }}">Decline request</a>
                                                            @else
                                                                Left membership
                                                            @endif
                                                        </p>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="permits">
                                <div class="row">
                                    <div class="col-md-12">
                                        <span class="pull-right"><a class="btn btn-primary" data-toggle="modal" data-target="#permitModal" data-whatever="@mdo">Add compliance</a></span>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="permitTable">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Date
                                                </th>
                                                <th>
                                                    Expiry date
                                                </th>
                                                <th>
                                                    Type
                                                </th>
                                                <th>
                                                    Reference
                                                </th>
                                                <th>
                                                    Applicable sites
                                                </th>
                                                <th>
                                                    &nbsp;
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($permits as $permit)
                                                <tr style="cursor: pointer" onClick="window.location.href='/editPermit/{{ $permit->id }}'">
                                                    <td>
                                                        <p class="text-muted">@if(!empty($permit->effective_date)){{ $permit->effective_date->format('d-m-Y') }}@endif</p>
                                                    </td>
                                                    <td>
                                                        <p class="text-muted">@if(!empty($permit->expiry_date)){{ $permit->expiry_date->format('d-m-Y') }}@endif</p>
                                                    </td>
                                                    <td>
                                                        <p class="text-muted">{{ $permit->Permit->name }}</p>
                                                    </td>
                                                    <td>
                                                        <p class="text-muted">{{ $permit->reference }}</p>
                                                    </td>
                                                    <td title="@foreach($permit->Permit->Permits_Site as $site) @if(is_object($site->Site)){{ $site->Site->name }}, @endif @endforeach">
                                                        <p class="text-muted">{{ count($permit->Permit->Permits_Site) }}</p>
                                                    </td>
                                                    <td>
                                                        @if($permit->status == "approved")
                                                            <i class="fa fa-check" title="Compliance status has been approved."></i>
                                                        @elseif($permit->status == "declined")
                                                            <i class="fa fa-ban" title="Compliance approval has been declined, problems need to be rectified and a request for it to be reassessed should be sought."></i>
                                                        @else
                                                            <i class="fa fa-warning" title="Compliance is pending approval which needs to be verified with another user that has access to your account."></i>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="trades">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-md-12">Add trade</label>
                                            <div class="col-md-6">
                                                <select class="form-control form-control-line form-control-select" name="permit" id="permit" onChange="addTrade(this.value)">
                                                    <option value="-1">Select to add</option>
                                                    @foreach($allTrades as $t)
                                                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    <div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="permitTable">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Trade
                                                </th>
                                                <th>
                                                    &nbsp;
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="profileTrades">
                                            @foreach($trades as $t)
                                                <tr>
                                                    <td>
                                                        <p class="text-muted">{{ $t->Trade->name }}</p>
                                                    </td>
                                                    <td>
                                                        <p class="text-muted">
                                                            <a href="/deleteTrade/{{ $t->id }}">Delete</a>
                                                        </p>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                </div></div>
                            </div>

                            <div class="tab-pane" id="logons">
                                <div class="table-responsive">
                                    <table class="table table-hover" id="signinTable">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Date
                                                </th>
                                                <th>
                                                    Site
                                                </th>
                                                <th>
                                                    Builder
                                                </th>
                                                <th>
                                                    Time in
                                                </th>
                                                <th>
                                                    Time Out
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($profile->Sites_Logon as $logon)
                                                <tr>
                                                    <td>{{ $logon->date->Format('d-m-Y') }}</td>
                                                    <td>{{ $logon->Site->name }}</td>
                                                    <td>{{ $logon->Site->Builder->name }}</td>
                                                    <td>{{ date('H:i', $logon->time_in) }}</td>
                                                    <td>
                                                        @if(!empty($logon->time_out))
                                                            {{ date('H:i', $logon->time_out) }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="attachments">
                                @include('components.attachment') 
                            </div>

                            <div class="tab-pane" id="logs">
                                @include('components.log') 
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- /.row -->


    <!--Permit Modal-->
    <form class="form-horizontal form-material" action="/savePermit/0" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="profile" value="{{ $profile->id }}">
        <div class="modal fade" id="permitModal" tabindex="-1" role="dialog" aria-labelledby="permitModal" style="display: none;">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-lg">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="exampleModalLabel1">New Compliance</h4> </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="plan-name" class="control-label">Compliance type</label>
                                <select name="permitType" class="form-control form-control-line" onChange="showTraining(this.value)"> 
                                    <option>Select to add compliance</option>
                                    @foreach($allPermits as $permit)
                                        <option value="{{ $permit->id }}">{{ $permit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="plan-name" class="control-label">Required training</label>
                                <div id="showTrainingDiv"></div>
                            </div>
                            <div class="form-group">
                                <label for="plan-name" class="control-label">Reference number</label>
                                <input type="text" id="plan-name" name="reference" class="form-control form-control-line"> 
                            </div>
                            <div class="form-group">
                                <label for="plan-name" class="control-label">Effective date</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="datepicker-effective" placeholder="dd-mm-yyyy" name="effectiveDate"><span class="input-group-addon"><i class="icon-calender"></i></span> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="plan-name" class="control-label">Expiry date</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="datepicker-expiry" placeholder="dd-mm-yyyy" name="expiryDate"><span class="input-group-addon"><i class="icon-calender"></i></span> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-12">Upload evidence <small> e.g. Photo, scanned certificate</small></label>
                                <div class="col-sm-12">
                                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                        <div class="form-control" data-trigger="fileinput"> 
                                            <i class="glyphicon glyphicon-file fileinput-exists"></i> 
                                            <span class="fileinput-filename"></span>
                                        </div> 
                                        <span class="input-group-addon btn btn-default btn-file">
                                            <span class="fileinput-new">Select file(s)</span> 
                                            <span class="fileinput-exists">Change</span>
                                            <input type="file" id="files" name="files[]" multiple>
                                        </span> 
                                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <input type="submit" name="submit" value="Save" class="btn btn-primary">
                    </div>
                    
                </div>
            </div>
        </div>
    </form>
    <!--/Permit Modal-->

        <!-- JS DEPENDENCIES -->
        @include('components.footer') 
        <!-- END JS DEPENDENCIES -->
                    
        <!-- Page specific Javascript -->
        <script src="{{ asset('js/jasny-bootstrap.js') }}"></script>
        <script src="{{ asset('assets/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
        <!-- <script src="{{ asset('assets/dropzone-master/dist/dropzone.js') }}"></script> -->
        <script src="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>


        <script>
            $(document).ready(function() {
                $('#historyTable').DataTable({
                    "displayLength": 100,
                });

                $('#signinTable').DataTable({
                    "displayLength": 100,
                });

                $('#logTable').DataTable({
                    "displayLength": 100,
                });

            });

            function request(value)
            {
                jQuery.getJSON('/requestMembership/' + value + '/' + {{ $profile->id }}, function (details) {
                    alert(details.message);
                });
            }

            function addTrade(value)
            {
                var div = document.getElementById("profileTrades")
                innerHTML = "";

                
                jQuery.getJSON('/addTrade/' + value + '/' + {{ $profile->id }}, function (details) {
                    $.each(details, function (d, detail) {
                        innerHTML += "<tr>"
                            innerHTML += "<td>"
                                innerHTML += "<p class=\"text-muted\">" + detail.name + "</p>"
                            innerHTML += "</td>"
                            innerHTML += "<td>"
                                innerHTML += "<p class=\"text-muted\">"
                                    innerHTML += "<a href=\"/deleteTrade/" + detail.id + "\">Delete</a>"
                                innerHTML += "</p>"
                            innerHTML += "</td>"
                        innerHTML += "</tr>"                                            
                    });
                    div.innerHTML = innerHTML
                });
            }

            function showTraining(value)
            {
                var div = document.getElementById("showTrainingDiv")

                innerHTML = ""
                jQuery.getJSON('/getPermitTraining/' + value, function (details) {
                    $.each(details, function (d, detail) {
                        innerHTML += "<br><a href=\"/training/" + detail.trainings_id + "\">" + detail.trainings_name + "</a>"
                    });
                    div.innerHTML = innerHTML
                });

            }

            jQuery('#datepicker-effective').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });

            jQuery('#datepicker-expiry').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });
            
            function copyHash()
            {
                hash = "{{ $profile->member_hash }}"
                container = document.getElementById("container")
                var textArea = document.createElement("textarea");
                textArea.value = hash
                container.appendChild(textArea);
                textArea.focus();
                textArea.select();

                try {
                    var successful = document.execCommand('copy');
                    var msg = successful ? 'successful' : 'unsuccessful';
                    console.log('Copying text command was ' + msg);
                } catch (err) {
                    console.log('Oops, unable to copy');
                }

                container.removeChild(textArea);
                alert('ID copied.')
            }
                    
        </script>

    @endsection