@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">@if($type == 0) Hygienist @else Service providers @endif</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>
        <form class="form-horizontal form-material" action="/saveHygenist/{{ $hygenist->id }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">
            <div class="row">
                <div class="col-md-12">
                    <span class="pull-right">
                        <input type="submit" id="submit-all" name="submit" value="Save" class="btn btn-primary">
                        &nbsp;
                        <a class="btn btn-info" href="/hygenists">Cancel</a>
                    </span>
                </div>
            </div>
            <br>
                    
            <!-- .row -->
            <div class="row">
                <div class="col-md-3 col-xs-12">
                    <div class="white-box">
                        <div class="user-bg"><img width="100%" alt="user" src="@if(!empty($hygenist->logo))/storage/{{ $hygenist->logo }} @else{{ asset('assets/images/hygenists/logo.png') }}@endif">
                            <div class="overlay-box">
                                <div class="user-content">
                                    <a href="javascript:void(0)"><img src="@if(!empty($hygenist->logo))/storage/{{ $hygenist->logo }} @else{{ asset('assets/images/hygenists/logo.png') }}@endif" class="thumb-lg img-circle" alt="img"></a>
                                    <h4 class="text-white">{{ $hygenist->name }}</h4>
                                    <h5 class="text-white">{{ $hygenist->email }}</h5> </div>
                                </div>
                            </div>
                            @if($type == 0)
                            <div class="user-btm-box">
                                <div class="col-md-4 col-sm-4 text-center">
                                    <p class="text-purple text-muted"><i class="ti-map"></i> Sites</p>
                                    <h3>
                                        {{ $headings[2] }}
                                    </h3>
                                </div> 
                                <div class="col-md-4 col-sm-4 text-center">
                                    <p class="text-purple text-muted"><i class="ti-location-arrow"></i> Zones</p>
                                    <h3>
                                        {{ $headings[0] }}
                                    </h3>
                                </div>
                                <div class="col-md-4 col-sm-4 text-center">
                                    <p class="text-purple text-muted"><i class="ti-truck"></i> Controls</p>
                                    <h3>
                                        {{ $headings[1] }}
                                    </h3> 
                                </div>
                            </div>
                            @else
                            <div class="user-btm-box">
                                <div class="col-md-4 col-sm-4 text-center">
                                    <p class="text-purple text-muted"><i class="ti-map"></i> Number of offerings</p>
                                    <h3>
                                        {{ $headings[2] }}
                                    </h3>
                                </div> 
                                <div class="col-md-4 col-sm-4 text-center">
                                    <p class="text-purple text-muted"><i class="ti-location-arrow"></i> No Sales</p>
                                    <h3>
                                        {{ $headings[0] }}
                                    </h3>
                                </div>
                                <div class="col-md-4 col-sm-4 text-center">
                                    <p class="text-purple text-muted"><i class="ti-truck"></i> Value sales</p>
                                    <h3>
                                        {{ $headings[1] }}
                                    </h3> 
                                </div>
                            </div>
                            @endif
                            <div id="container"></div>
                        </div>
                    </div> 
                



                        
                <div class="col-md-9 col-xs-12">
                    <div class="white-box">
                        <ul class="nav nav-tabs tabs customtab">
                            <li class="active tab">
                                <a href="#profile" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs">Profile</span> </a>
                            </li>
                            @if($type == 0)
                                <li class="tab">
                                    <a href="#memberships" data-toggle="tab"> <span class="visible-xs"><i class="ti-flag-alt"></i></span> <span class="hidden-xs">Memberships @if(count($memberships) > 0)<span class="badge">{{ count($memberships) }}</span>@endif</span> </a>
                                </li>
                                <li class="tab">
                                    <a href="#sites" data-toggle="tab"> <span class="visible-xs"><i class="ti-map"></i></span> <span class="hidden-xs">Sites @if(count($sites) > 0)<span class="badge">{{ count($sites) }}</span>@endif</span> </a>
                                </li>
                            @endif
                            <li class="tab">
                                <a href="#attachments" data-toggle="tab"> <span class="visible-xs"><i class="ti-folder"></i></span> <span class="hidden-xs">Attachments @if(count($files) > 0)<span class="badge">{{ count($files) }}</span>@endif</span> </a>
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
                                        <input type="text" id="name" name="name" placeholder="@if($type == 0) Hygienist @else Service providers @endif name" value="@if($hygenist->name != 'New'){{ $hygenist->name }}@endif" class="form-control form-control-line"> 
                                    </div>    
                                </div>
                                @if($standardDisplay['profile']->super_user == 1)
                                    <div class="form-group">
                                        <label class="col-md-12">simPRO ID</label>
                                        <div class="col-md-12">
                                            <input type="text" id="simpro_id" name="simpro_id" placeholder="simPRO ID" value="{{ $hygenist->simpro_id_1 }}" class="form-control form-control-line"> 
                                        </div>    
                                    </div>
                                @else
                                    <input type="hidden" name="simpro_id" value="{{ $hygenist->simpro_id_1 }}">
                                @endif
                                @if($type == 0)
                                    <div class="form-group">
                                        <label class="col-md-12">Primary contact</label>
                                        <div class="col-md-12">
                                            <select class="form-control form-control-line form-control-select" name="primaryContact" id="primaryContact">
                                                @foreach($memberships as $member)
                                                    @if($member->membership_status == "active" && $member->Profile->type == "user")
                                                        <option value="{{ $member->user_id }}" @if($hygenist->primary_contact == $member->user_id) selected @endif>{{ $member->Profile->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label class="col-md-12">Address</label>
                                    <div class="col-md-12">
                                        <input type="text" id="address" name="address" placeholder="Address" value="{{ $hygenist->address }}" class="form-control form-control-line"> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">City</label>
                                    <div class="col-md-12">
                                        <input type="text" id="city" name="city" placeholder="City" value="{{ $hygenist->city }}" class="form-control form-control-line"> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">State</label>
                                    <div class="col-md-12">
                                        <input type="text" id="state" name="state" placeholder="State" value="{{ $hygenist->state }}" class="form-control form-control-line"> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Postcode</label>
                                    <div class="col-md-12">
                                        <input type="text" id="postcode" name="postcode" placeholder="Postcode" value="{{ $hygenist->postcode }}" class="form-control form-control-line"> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Email</label>
                                    <div class="col-md-12">
                                        <input type="text" id="email" name="email" placeholder="Email address" value="{{ $hygenist->email }}" class="form-control form-control-line"> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Phone</label>
                                    <div class="col-md-12">
                                        <input type="text" name="phone" placeholder="Phone number" value="{{ $hygenist->phone }}" class="form-control form-control-line"> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Mobile</label>
                                    <div class="col-md-12">
                                        <input type="text" name="mobile" placeholder="Mobile phone number" value="{{ $hygenist->mobile }}" class="form-control form-control-line">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">ABN</label>
                                    <div class="col-md-12">
                                        <input type="text" name="tax_id" placeholder="ABN" value="{{ $hygenist->tax_id }}" class="form-control form-control-line"> 
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

                            <div class="tab-pane" id="memberships">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label class="col-md-12">Ask to join</label>
                                            <div class="col-md-6">
                                                <input id="requestMembership" class="form-control form-control-line" name="requestMembership" placeholder="ID of the user you want to join">
                                            </div>    
                                            <div class="col-md-6">
                                                <a onClick="request(document.getElementById('requestMembership').value)" class="btn btn-primary">Request</a>
                                            </div>
                                        </div>  
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">    
                                            <label class="col-md-12">Your ID</label>
                                            <div class="col-md-6">
                                                {{ $hygenist->member_hash }}
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
                                                    Type
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
                                                @if($member->membership_status == "organisation requested" OR $member->membership_status == "user requested")
                                                    <tr>
                                                        <td>
                                                            <p class="text-muted">{{ $member->Profile->name }}</p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">{{ $member->Profile->member_hash }}</p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">{{ $member->Profile->type }}</p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">
                                                            @if($member->membership_status == "organisation requested")
                                                                Outbound
                                                            @else
                                                                Inbound
                                                            @endif
                                                            </p>
                                                        </td>
                                                        <td>
                                                            @if($member->membership_status == "user requested")
                                                                <a href="/acceptMembership/{{ $member->id }}/accept/{{ $hygenist->id }}">Accept request</a>
                                                                | <a href="/acceptMembership/{{ $member->id }}/decline/{{ $hygenist->id }}">Decline request</a>
                                                            @elseif($member->membership_status == "active")
                                                                <p class="text-muted"><a href="/cancelMembership/{{ $member->id }}">Leave</a></p>
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
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3>Members</h3>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="Members">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Name
                                                </th>
                                                <th>
                                                    Role
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
                                            @foreach($memberships as $member)
                                                @if($member->membership_status == "active" OR $member->membership_status == "inactive")
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
                                                            <p class="text-muted">
                                                                {{ $member->Profile->type }}
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">@if(!empty($member->joined)){{ $member->joined->format('d-m-Y') }}@endif</p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">@if(!empty($member->exitted)){{ $member->exitted->format('d-m-Y') }}@endif</p>
                                                        </td>
                                                        @if($member->membership_status == "active")
                                                            <td>
                                                                <a href="/cancelMembership/{{ $member->id }}/{{ $hygenist->id }}">Cancel</a>
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
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

    <form class="form-horizontal form-material" action="/sendInvitation" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="profileID" value="{{ $hygenist->id }}">
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
        <!-- <script src="{{ asset('assets/dropzone-master/dist/dropzone.js') }}"></script> -->
        <script src="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>


        <script>
            $(document).ready(function() {
                $('#historyTable').DataTable({
                    "displayLength": 100,
                });

                $('#logTable').DataTable({
                    "displayLength": 100,
                });

            });

            function request(value)
            {
                jQuery.getJSON('/requestMembership/' + value + '/' + {{ $hygenist->id }}, function (details) {
                    alert(details.message);
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
                hash = "{{ $hygenist->member_hash }}"
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