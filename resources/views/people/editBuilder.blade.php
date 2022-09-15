@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Builder</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>
        <form class="form-horizontal form-material" action="/saveBuilder/{{ $builder->id }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <span class="pull-right">
                    <input type="submit" id="submit-all" name="submit" value="Save" class="btn btn-primary">
                    &nbsp;
                    <a class="btn btn-info" href="/builders">Cancel</a>
                </span>
            </div>
        </div>
        <br>
                
        <!-- .row -->
        <div class="row">
            <div class="col-md-3 col-xs-12">
                <div class="white-box">
                    <div class="user-bg"><img width="100%" alt="user" src="@if(!empty($builder->logo))/storage/{{ $builder->logo }} @else{{ asset('assets/images/builders/logo.png') }}@endif">
                        <div class="overlay-box">
                            <div class="user-content">
                                <a href="javascript:void(0)"><img src="@if(!empty($builder->logo))/storage/{{ $builder->logo }} @else{{ asset('assets/images/builders/logo.png') }}@endif" class="thumb-lg img-circle" alt="img"></a>
                                <h4 class="text-white">{{ $builder->name }}</h4>
                                <h5 class="text-white">{{ $builder->email }}</h5> </div>
                        </div>
                    </div>
                    <div class="user-btm-box">
                        <div class="col-md-4 col-sm-4 text-center">
                            <p class="text-purple text-muted"><i class="fa fa-circle-o"></i> No Sites</p>
                            <h3>
                            {{ $headings[0] }}
                            </h3>
                        </div> 
                        <div class="col-md-4 col-sm-4 text-center">
                            <p class="text-purple text-muted"><i class="fa fa-unlock"></i> No Contractors</p>
                            <h3>{{ $headings[1] }}</h3>
                        </div>
                        <div class="col-md-4 col-sm-4 text-center">
                            <p class="text-purple text-muted"><i class="fa fa-comments-o"></i> Members activity</p>
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
                            <a href="#sites" data-toggle="tab"> <span class="visible-xs"><i class="ti-map"></i></span> <span class="hidden-xs">Sites </span> </a>
                        </li>
                        <li class="tab">
                            <a href="#memberships" data-toggle="tab"> <span class="visible-xs"><i class="ti-flag-alt"></i></span> <span class="hidden-xs">Members @if(count($memberships) > 0)<span class="badge">{{ count($memberships) }}</span>@endif </span> </a>
                        </li>
                        <li class="tab">
                            <a href="#permits" data-toggle="tab"> <span class="visible-xs"><i class="ti-pulse"></i></span> <span class="hidden-xs">Entry requirement issues @if(count($problems) > 0)<span class="badge">{{ count($problems) }}</span>@endif</span> </a>
                        </li>
                        <li class="tab">
                            <a href="#attachments" data-toggle="tab"> <span class="visible-xs"><i class="ti-folder"></i></span> <span class="hidden-xs">Attachments @if(count($files) > 0)<span class="badge">{{ count($files) }}</span>@endif</span> </a>
                        </li>
                        @if($standardDisplay['profile']->super_user == 1)
                            <li class="tab">
                                <a href="#billing" data-toggle="tab"> <span class="visible-xs"><i class="ti-money"></i></span> <span class="hidden-xs">Billing</span> </a>
                            </li>
                        @endif
                        <li class="tab">
                            <a href="#history" data-toggle="tab"> <span class="visible-xs"><i class="ti-notepad"></i></span> <span class="hidden-xs">Activity </span> </a>
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
                                    <input type="text" id="name" name="name" placeholder="Builder name" value="@if($builder->name != 'New'){{ $builder->name }}@endif" class="form-control form-control-line"> 
                                </div>    
                            </div>
                            @if($standardDisplay['profile']->super_user == 1)
                                <div class="form-group">
                                    <label class="col-md-12">simPRO Customer ID</label>
                                    <div class="col-md-12">
                                        <input type="text" id="simproID" value="{{ $builder->simpro_id_1 }}" name="simproID" placeholder="simPRO ID" class="form-control form-control-line"> 
                                    </div>    
                                </div>
                            @else
                                <input type="hidden" id="simproID" name="simproID" value="{{ $builder->simpro_id_1 }}"> 
                            @endif
                            <div class="form-group">
                                <label class="col-md-12">Primary contact</label>
                                <div class="col-md-12">
                                    <select class="form-control form-control-line form-control-select" name="primaryContact" id="primaryContact">
                                        @foreach($memberships as $member)
                                            @if($member->membership_status == "active" && $member->Profile->type == "user")
                                                <option value="{{ $member->user_id }}" @if($builder->primary_contact == $member->user_id) selected @endif>{{ $member->Profile->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Email</label>
                                <div class="col-md-12">
                                    <input type="text" id="email" name="email" placeholder="Email address" value="{{ $builder->email }}" class="form-control form-control-line"> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Phone</label>
                                <div class="col-md-12">
                                    <input type="text" name="phone" placeholder="Phone number" value="{{ $builder->phone }}" class="form-control form-control-line"> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Address</label>
                                <div class="col-md-12">
                                    <input type="text" name="address" placeholder="Address" value="{{ $builder->address }}" class="form-control form-control-line"> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">City</label>
                                <div class="col-md-12">
                                    <input type="text" name="city" placeholder="City" value="{{ $builder->city }}" class="form-control form-control-line"> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">State / Province</label>
                                <div class="col-md-12">
                                    <input type="text" name="state" placeholder="State" value="{{ $builder->state }}" class="form-control form-control-line"> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Postcode</label>
                                <div class="col-md-12">
                                    <input type="text" name="postcode" placeholder="Postcode" value="{{ $builder->postcode }}" class="form-control form-control-line"> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Country</label>
                                <div class="col-md-12">
                                    <input type="text" name="country" placeholder="Country" value="{{ $builder->country }}" class="form-control form-control-line"> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">ABN</label>
                                <div class="col-md-12">
                                    <input type="text" name="tax_id" placeholder="ABN" value="{{ $builder->tax_id }}" class="form-control form-control-line"> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">URL</label>
                                <div class="col-md-12">
                                    <input type="text" name="tax_id" placeholder="ABN" value="{{  Request::url() }}" class="form-control form-control-line"> 
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-12">Logo</label>
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
                                                User / Contractor
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
                                            <tr style="cursor: pointer" onClick="window.location.href='/siteHistory/0'">
                                                <td>
                                                    <p class="text-muted">@if(!empty($hi->created_at)){{ $hi->created_at->format('d-m-Y H:i') }}@endif</p>
                                                </td>
                                                <td>
                                                    <p class="text-muted">{{ $hi->site->name }}</p>
                                                </td>
                                                <td>
                                                    @if(in_array($standardDisplay['profile']->id, $usersArray) OR $standardDisplay['profile']->super_user == 1)
                                                        <p class="text-muted">{{ $hi->Profile->name }}</p>
                                                    @else
                                                        <p class="text-muted">
                                                        {{ $hi->Organisation->name }} - {{ $hi->Profile->member_hash }}
                                                        </p>
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
                                        <label class="col-md-12">Ask to join</label>
                                        <div class="col-md-6">
                                            <input id="requestMembership" class="form-control form-control-line" name="requestMembership" placeholder="ID of the user or contractor you want to join">
                                        </div>
                                        <div class="col-md-6">
                                            <a onClick="request(document.getElementById('requestMembership').value)" class="btn btn-primary" href="#">Request</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">    
                                        <label class="col-md-12">Your ID</label>
                                        <div class="col-md-6">
                                            {{ $builder->member_hash }} 
                                            &nbsp; <i class="fa fa-copy" onClick="copyHash()" style="cursor: pointer;"></i>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#Invitation">Invitation</button>
                                        </div>
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
                                        <h3>Current requests</h3>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="Current requests">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Name
                                                </th>
                                                <th>
                                                    ID
                                                </th>
                                                <th>
                                                    Inbound / Outbound
                                                </th>
                                                <th>
                                                    &nbsp;
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($memberships as $member)
                                                @if($member->membership_status == "user requested" OR $member->membership_status == "organisation requested")
                                                    <tr>
                                                        <td>
                                                            <p class="text-muted">{{ $member->Profile->name }}</p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">{{ $member->Profile->member_hash }}</p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">
                                                                @if($member->membership_status == "user requested")
                                                                    Inbound
                                                                @else
                                                                    Outbound
                                                                @endif
                                                            </p>
                                                        </td>
                                                        <td>
                                                            @if($member->membership_status == "user requested")
                                                                <a href="/acceptMembership/{{ $member->id }}/accept/{{ $builder->id }}">Accept request</a>
                                                                | <a href="/acceptMembership/{{ $member->id }}/decline/{{ $builder->id }}">Decline request</a>
                                                            @else
                                                                Waiting
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            @foreach($membersOf as $member)
                                                @if($member->membership_status == "user requested" OR $member->membership_status == "organisation requested")
                                                    <tr>
                                                        <td>
                                                            <p class="text-muted">{{ $member->Organisation->name }}</p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">{{ $member->Organisation->member_hash }}</p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">
                                                                @if($member->membership_status == "organisation requested" && $member->organisation_id != $builder->id)
                                                                    Inbound
                                                                @else
                                                                    Outbound
                                                                @endif
                                                            </p>
                                                        </td>
                                                        <td>
                                                            @if($member->membership_status == "organisation requested" && $member->organisation_id != $builder->id)
                                                                <a href="/acceptMembership/{{ $member->id }}/accept/{{ $builder->id }}">Accept request</a>
                                                                | <a href="/acceptMembership/{{ $member->id }}/decline/{{ $builder->id }}">Decline request</a>
                                                            @else
                                                                Waiting
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        &nbsp;<br>&nbsp;
                                    </div>
                                </div>
                                
                                <ul class="nav nav-tabs tabs customtab">
                                    <li class="active tab">
                                        <a href="#mm" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="ti-flag-alt"></i></span> <span class="hidden-xs">Members</span> </a>
                                    </li>
                                    <li class="tab">
                                        <a href="#m" data-toggle="tab"> <span class="visible-xs"><i class="ti-flag"></i></span> <span class="hidden-xs">Memberships </span> </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="mm">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h3>Members</h3>
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
                                                    @foreach($memberships as $member)
                                                        @if($member->membership_status == "active")
                                                            <tr>
                                                                <td>
                                                                    <p class="text-muted">{{ $member->Profile->name }}</p>
                                                                </td>
                                                                <td>
                                                                    <p class="text-muted">
                                                                        <input type="hidden" name="memberID[]" value="{{ $member->id }}">
                                                                        <select name="securityGroups[]" class="form-control form-control-line">
                                                                            <option value="0">None</option>
                                                                            @foreach($securityGroups as $group)
                                                                                <option value="{{ $group->id }}" @if($group->id == $member->security_group) selected @endif>{{ $group->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </p>
                                                                </td>
                                                                <td>
                                                                    <p class="text-muted">@if(!empty($member->joined)){{ $member->joined->format('d-m-Y') }}@endif</p>
                                                                </td>
                                                                <td>
                                                                    <p class="text-muted">@if(!empty($member->exitted)){{ $member->exitted->format('d-m-Y') }}@endif</p>
                                                                </td>
                                                                <td>
                                                                    <p class="text-muted">
                                                                        @if($member->membership_status == "active")
                                                                            <a href="/cancelMembership/{{ $member->id }}/{{ $builder->id }}">Cancel</a>
                                                                        @elseif($member->membership_status == "user requested")
                                                                            Waiting for acceptance
                                                                        @elseif($member->membership_status == "organisation requested")
                                                                            <a href="/acceptMembership/{{ $member->id }}/accept/{{ $builder->id }}">Accept request</a>
                                                                            | <a href="/acceptMembership/{{ $member->id }}/decline/{{ $builder->id }}">Decline request</a>
                                                                        @else
                                                                            Left membership
                                                                        @endif
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                    </div>

                                    <div class="tab-pane" id="m">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h3>Memberships</h3>
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
                                                    @foreach($membersOf as $member)
                                                        @if($member->membership_status == "active")
                                                            <tr>
                                                                <td>
                                                                    <p class="text-muted">{{ $member->Organisation->name }}</p>
                                                                </td>
                                                                <td>
                                                                    <p class="text-muted">
                                                                        {{ $member->Organisation->type }}
                                                                    </p>
                                                                </td>
                                                                <td>
                                                                    <p class="text-muted">@if(!empty($member->joined)){{ $member->joined->format('d-m-Y') }}@endif</p>
                                                                </td>
                                                                <td>
                                                                    <p class="text-muted">@if(!empty($member->exitted)){{ $member->exitted->format('d-m-Y') }}@endif</p>
                                                                </td>
                                                                <td>
                                                                    <p class="text-muted">
                                                                        @if($member->membership_status == "active")
                                                                            <a href="/cancelMembership/{{ $member->id }}/{{ $builder->id }}">Leave</a>
                                                                        @elseif($member->membership_status == "user requested")
                                                                            Waiting for acceptance
                                                                        @elseif($member->membership_status == "organisation requested")
                                                                            <a href="/acceptMembership/{{ $member->id }}/accept/{{ $builder->id }}">Accept request</a>
                                                                            | <a href="/acceptMembership/{{ $member->id }}/decline/{{ $builder->id }}">Decline request</a>
                                                                        @else
                                                                            Left membership
                                                                        @endif
                                                                    </p>
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

                        <div class="tab-pane" id="sites">
                            <div class="table-responsive">
                                <table class="table table-hover" id="Current requests">
                                    <thead>
                                        <tr>
                                            <th>
                                                Name
                                            </th>
                                            <th>
                                                Status
                                            </th>
                                            <th>
                                                Address
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sites as $site)
                                            <tr style="cursor: pointer;" onClick="window.location.href='/editSite/{{ $site->id }}'">
                                                <td>
                                                    <p class="text-muted">{{ $site->name }}</p>
                                                </td>
                                                <td>
                                                    <p class="text-muted">{{ $site->status }}</p>
                                                </td>
                                                <td>
                                                    <p class="text-muted">{{ $site->address }}</p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="permits">
                            <div class="table-responsive">
                                <table class="table table-hover" id="permitsTable">
                                    <thead>
                                        <tr>
                                            <th>
                                                Name
                                            </th>
                                            <th>
                                                Worker
                                            </th>
                                            <th>
                                                Site
                                            </th>
                                            <th>
                                                Requirement
                                            </th>
                                            <th>
                                                Problem
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($problems as $problem)
                                            <tr>
                                                <td>
                                                    {{-- <p class="text-muted">{{ $problem['permit']->name }}</p> --}}
                                                    {{-- Name is throwing a non object error which means it is an array --}}
                                                    <p class="text-muted">{{ $problem['permit'] }}</p>
                                                </td>
                                                <td>
                                                    <p class="text-muted">{{ $problem['person'] }}</p>
                                                </td>
                                                <td>
                                                    <p class="text-muted">{{ $problem['site'] }}</p>
                                                </td>
                                                <td>
                                                    <p class="text-muted">
                                                        @if($problem['mandatory'] == 0)
                                                            Recommended
                                                        @else
                                                            Mandatory
                                                        @endif
                                                    </p>
                                                </td>
                                                <td>
                                                    {{ $problem['description'] }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane" id="billing">
                            <div class="form-group">
                                <label>Date billing commencement</label>
                                <div class="input-group">
                                    <input type="text" class="form-control input-datepicker" id="datepicker-billing" placeholder="dd-mm-yyyy" name="commencementDate" @if(isset($builder->billing_start)) value="{{ $builder->billing_start->format('d-m-Y') }}" @endif><span class="input-group-addon"><i class="icon-calender"></i></span> 
                                </div>    
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">User discount</label>
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <input type="number" step="1" id="user_discount" name="user_discount" placeholder="Discount per user" value="@if($builder->name != 'New'){{ $license->user_discount }}@endif" class="form-control form-control-line"><span class="input-group-addon"><i class="fa fa-percent"></i></span> 
                                    </div>
                                </div>    
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Hardware discount</label>
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <input type="number" step="1" id="hardware_discount" name="hardware_discount" placeholder="Discount on hardware" value="@if($builder->name != 'New'){{ $license->hardware_discount }}@endif" class="form-control form-control-line"> <span class="input-group-addon"><i class="fa fa-percent"></i></span> 
                                    </div>
                                </div>    
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Marketplace discount</label>
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <input type="number" step="1" id="marketplace_discount" name="marketplace_discount" placeholder="Discount on marketplace" value="@if($builder->name != 'New'){{ $license->marketplace_discount }}@endif" class="form-control form-control-line"> <span class="input-group-addon"><i class="fa fa-percent"></i></span> 
                                    </div>    
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Per site</label>
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <input type="number" step="0.01" id="billing_site" name="billing_site" placeholder="Builder per site per month cost" value="@if($builder->name != 'New'){{ $license->site_cost }}@endif" class="form-control form-control-line"> <span class="input-group-addon"><i class="fa fa-dollar"></i></span> 
                                    </div>  
                                </div>  
                            </div>
                            <br><br><hr><br>
                            <h3>Log</h3>
                            <table class="table table-responsive table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Changed by</th>
                                        <th>Change</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($builder->Licenses as $l)
                                        @if($l->changed)
                                            <tr>
                                                <td>{{ $l->created_at->format('d-m-Y') }}</td>
                                                <td>{{ $l->changed_by }}</td>
                                                <td>{{ $l->changed }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
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
        </form>
    </div>
    <!-- /.row -->

    <form class="form-horizontal form-material" action="/sendInvitation" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="profileID" value="{{ $builder->id }}">
        <div class="modal fade" id="Invitation" tabindex="-1" role="dialog" aria-labelledby="Invitation" style="display: none;">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-lg">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="exampleModalLabel1">Send invitation to join Nextrack</h4> 
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-md-12">Name</label>
                                            <div class="col-md-12">
                                                <input type="text" class="form-control form-control-line" placeholder="Name of person to invite" name="name" required>
                                            </div>    
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-12">Email address</label>
                                        <div class="col-md-12">
                                            <input type="text" class="form-control form-control-line" placeholder="Email address" name="email" required>
                                        </div>    
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <br>
                                        <div class="form-group">
                                            <label class="col-md-12">Message to add to email</label>
                                            <div class="col-md-12">
                                                <textarea name="notes" class="form-control form-control-line"></textarea>
                                            </div>    
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <br>
                                        <span class="pull-right"><input type="submit" name="submit" value="Send invitation" class="btn btn-primary"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

        <!-- JS DEPENDENCIES -->
        @include('components.footer') 
        <!-- END JS DEPENDENCIES -->
                    
        <!-- Page specific Javascript -->
        <script src="{{ asset('js/jasny-bootstrap.js') }}"></script>
        <script src="{{ asset('assets/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
        <!-- <script src="{{ asset('assets/dropzone-master/dist/dropzone.js') }}"></script> -->


        <script>
            $(document).ready(function() {
                $('#historyTable').DataTable({
                    "displayLength": 100,
                });
                
                $('#permitsTable').DataTable({
                    "displayLength": 100,
                });

                $('#logTable').DataTable({
                    "displayLength": 100,
                });

            });

            function request(value)
            {
                jQuery.getJSON('/requestMembership/' + value + '/' + {{ $builder->id }}, function (details) {
                    alert(details.message);
                });
            }

            function copyHash()
            {
                hash = "{{ $builder->member_hash }}"
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

            jQuery('#datepicker-billing').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });
                    
        </script>

    @endsection