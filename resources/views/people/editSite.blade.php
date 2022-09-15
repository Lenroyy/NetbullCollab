@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/bower_components/gridster/css/jquery.gridster.css') }}"> 
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/bower_components/gridster/css/jquery.dsmorse-gridster.min.css') }}"> 
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/bower_components/gridster/css/gridster.custom.css') }}"> 
        <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/highcharts/css/highcharts.css') }}" charster="utf-8"></script>
        <link href="{{ asset('css/dots.css') }}" rel="stylesheet">
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Site : {{ $site->name }}</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>
        <form class="form-horizontal form-material" action="/saveSite/{{ $site->id }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-12">
                <span class="pull-right">
                    <input type="submit" id="submit-all" name="submit" value="Save" class="btn btn-primary">
                    &nbsp;
                    <a class="btn btn-info" href="/sites">Cancel</a>
                </span>
            </div>
        </div>
        <br>
        <input type="hidden" value="{{ $site->id }}" name="siteParticipationValue">
                
        <!-- .row -->
        <div class="row">
            <div class="col-md-3 col-xs-12">
                <div class="white-box">
                    <div class="user-bg"><img width="100%" alt="user" src="@if(!empty($site->image))/storage/{{ $site->image }} @else{{ asset('assets/images/sites/logo.png') }}@endif">
                        <div class="overlay-box">
                            <div class="user-content">
                                <a href="javascript:void(0)"><img src="@if(!empty($site->image))/storage/{{ $site->image }} @else{{ asset('assets/images/sites/logo.png') }}@endif" class="thumb-lg img-circle" alt="img"></a>
                                <h4 class="text-white">{{ $site->name }}</h4>
                                <h5 class="text-white">{{ $site->address }}</h5> </div>
                        </div>
                    </div>
                    <div class="user-btm-box">
                        <div class="col-md-4 col-sm-4 text-center">
                            <p class="text-purple text-muted"><i class="fa fa-circle-o"></i> Builder</p>
                            <h3>
                                {{ $headings[0] }}
                            </h3>
                        </div> 
                        <div class="col-md-4 col-sm-4 text-center">
                            <p class="text-purple text-muted"><i class="fa fa-unlock"></i> Status</p>
                            <h3>{{ $headings[1] }}</h3>
                        </div>
                        <div class="col-md-4 col-sm-4 text-center">
                            <p class="text-purple text-muted"><i class="fa fa-comments-o"></i> Total hours</p>
                            <h3><?php echo number_format($headings[2], 0, ".", ","); ?></h3> 
                        </div>
                    </div>
                </div>
            </div> 
            



                    
            <div class="col-md-9 col-xs-12">
                <div class="white-box">
                    <ul class="nav nav-tabs tabs customtab">
                        <li @if($site->id > 0) class="active tab" @else class="tab" @endif>
                            <a href="#dashboard" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="ti-dashboard"></i></span> <span class="hidden-xs">Dashboard</span> </a>
                        </li>
                        <li @if($site->id == 0) class="active tab" @else class="tab" @endif>
                            <a href="#profile" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs">Profile</span> </a>
                        </li>
                        @if($site->id > 0)
                        <li class="tab">
                            <a href="#plan" data-toggle="tab"> <span class="visible-xs"><i class="ti-map-alt"></i></span> <span class="hidden-xs">Site maps and zones</span> </a>
                        </li>
                        <li class="tab">
                            <a href="#permits" data-toggle="tab"> <span class="visible-xs"><i class="ti-pulse"></i></span> <span class="hidden-xs">Entry requirements </span> </a>
                        </li>
                        <li class="tab">
                            <a href="#assets" data-toggle="tab"> <span class="visible-xs"><i class="ti-truck"></i></span> <span class="hidden-xs">Controls </span> </a>
                        </li>
                        <li class="tab">
                            <a href="#workers" data-toggle="tab"> <span class="visible-xs"><i class="ti-flag-alt"></i></span> <span class="hidden-xs">Workers </span> </a>
                        </li>
                        <li class="tab">
                            <a href="#tasks" data-toggle="tab"> <span class="visible-xs"><i class="ti-flag-alt"></i></span> <span class="hidden-xs">Tasks </span> </a>
                        </li>
                        <li class="tab">
                            <a href="#attachments" data-toggle="tab"> <span class="visible-xs"><i class="ti-folder"></i></span> <span class="hidden-xs">Attachments @if(count($files) > 0)<span class="badge">{{ count($files) }}</span>@endif</span> </a>
                        </li>
                        <li class="tab">
                            <a href="#history" data-toggle="tab"> <span class="visible-xs"><i class="ti-notepad"></i></span> <span class="hidden-xs">Activity </span> </a>
                        </li>                        
                        <li class="tab">
                            <a href="#logs" data-toggle="tab"> <span class="visible-xs"><i class="ti-calendar"></i></span> <span class="hidden-xs">Logs</span> </a>
                        </li>
                        @endif
                    </ul>
                    <div class="tab-content">

                        <div @if($site->id > 0) class="tab-pane active" @else class="tab-pane" @endif id="dashboard">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="btn-group pull-right">
                                        <div class="pull-right">
                                            <button type="button" class="btn btn-primary btn-sm light-widget-button" data-toggle="modal" data-target="#widgets"><i class="fa fa-plus"></i> Widget</button>&nbsp;&nbsp;&nbsp;
                                            <button type="button" class="btn btn-success btn-sm js-seralize"><i class="ft-save white"></i> Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br>

                                    
                            <!-- .row -->
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Gridster Dashboard -->
                                    <section>
                                        <div class="gridster">
                                            <ul style="list-style: none;" id="grd"></ul>
                                        </div>
                                    </section>
                                    <!-- END Gridster Dashboard --> 
                                </div>
                            </div>
                            <!-- /.row -->
                        </div>

                        <div @if($site->id == 0) class="tab-pane active" @else class="tab-pane" @endif id="profile">
                            <br>&nbsp;
                            <div class="form-group">
                                <label class="col-md-12">Name</label>
                                <div class="col-md-12">
                                    <input type="text" id="name" name="name" placeholder="Site name" value="@if($site->name != 'New'){{ $site->name }}@endif" class="form-control form-control-line"> 
                                </div>    
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Managing Hygienist</label>
                                <div class="col-md-12">
                                    <select class="form-control form-control-line" name="hygenist">
                                        <option value="">Choose site hygienist</option>
                                        @foreach($hygenists as $hygenist)
                                            <option value="{{ $hygenist->id }}" @if($hygenist->id == $site->hygenist_id) selected @endif>{{ $hygenist->name }}</option>
                                        @endforeach
                                    </select>
                                </div>    
                            </div>
                            @if($standardDisplay['profile']->super_user == 1)
                                <div class="form-group">
                                    <label class="col-md-12">simPRO ID</label>
                                    <div class="col-md-12">
                                        <input type="text" id="simpro_id" name="simpro_id" placeholder="simPRO ID" value="{{ $site->simpro_site_id_1 }}" class="form-control form-control-line"> 
                                    </div>    
                                </div>
                            @else
                                <input type="hidden" name="simpro_id" value="{{ $site->simpro_site_id_1 }}">
                            @endif
                            <div class="form-group">
                                <label class="col-md-12">Address</label>
                                <div class="col-md-12">
                                <input type="text" id="address" name="address" placeholder="Address" value="{{ $site->address }}" class="form-control form-control-line"> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">City</label>
                                <div class="col-md-12">
                                <input type="text" id="city" name="city" placeholder="City" value="{{ $site->city }}" class="form-control form-control-line"> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">State</label>
                                <div class="col-md-12">
                                <input type="text" id="state" name="state" placeholder="State" value="{{ $site->state }}" class="form-control form-control-line"> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Postcode</label>
                                <div class="col-md-12">
                                <input type="text" id="postcode" name="postcode" placeholder="Postcode" value="{{ $site->postcode }}" class="form-control form-control-line"> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Country</label>
                                <div class="col-md-12">
                                <input type="text" id="country" name="country" placeholder="Country" value="{{ $site->country }}" class="form-control form-control-line"> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Primary contact</label>
                                <div class="col-md-9">
                                    <select class="form-control form-control-line" id="pContact" name="contact">
                                        @foreach($contacts as $contact)
                                            <option @if($contact->user_id == $site->primary_contact_id) selected @endif value="{{ $contact->user_id }}">{{ $contact->Profile->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if(in_array("users:edit", $standardDisplay['permissions']))
                                    <div class="col-md-3">
                                        <i onClick="openContact(document.getElementById('pContact').value)" class="icon-magnifier" style="cursor: pointer;"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Phone</label>
                                <div class="col-md-12">
                                    <input type="text" name="phone" placeholder="Phone number" value="{{ $site->phone }}" class="form-control form-control-line"> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Mobile</label>
                                <div class="col-md-12">
                                    <input type="text" name="mobile" placeholder="Mobile phone number" value="{{ $site->mobile }}" class="form-control form-control-line">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">Zone QR Code function</label>
                                <div class="col-md-9">
                                    <select class="form-control form-control-line" id="qrCodeFunction" name="qrCodeFunction">
                                        <option value="0" @if($site->zone_qr_code_function == 0) selected @endif>Start activity</option>
                                        <option value="1" @if($site->zone_qr_code_function == 1) selected @endif>Entry visits</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-12">Image</label>
                                <div class="col-sm-12">
                                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                        <div class="form-control" data-trigger="fileinput"> 
                                            <i class="glyphicon glyphicon-file fileinput-exists"></i> 
                                            <span class="fileinput-filename"></span>
                                        </div> 
                                        <span class="input-group-addon btn btn-default btn-file">
                                            <span class="fileinput-new">Select file</span> 
                                            <span class="fileinput-exists">Change</span>
                                            <input type="file" name="image"> 
                                        </span> 
                                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a> 
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="history">
                            <ul class="nav nav-tabs tabs customtab">
                                <li class="active tab">
                                    <a href="#ass" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="ti-flag-alt"></i></span> <span class="hidden-xs">Assessments / SWMS</span> </a>
                                </li>
                                <li class="tab">
                                    <a href="#te" data-toggle="tab"> <span class="visible-xs"><i class="ti-flag"></i></span> <span class="hidden-xs">Time entries </span> </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="ass">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="assessmentsTable">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        ID
                                                    </th>
                                                    <th>
                                                        Date
                                                    </th>
                                                    <th>
                                                        Worker
                                                    </th>
                                                    <th>
                                                        Associated with
                                                    </th>
                                                    <th>
                                                        Assessment
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($assessments as $ass)
                                                    <tr @if(is_object($ass->History_Assessment)) style="cursor: pointer" onClick="window.location.href='/logActivity/{{ $ass->History_Assessment->history_id }}'" @endif>
                                                        <td>
                                                            <p class="text-muted">
                                                                {{ $ass->id }}
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">
                                                                @if(!empty($ass->created_at))
                                                                    {{ $ass->created_at->format('d-m-Y') }}
                                                                @else
                                                                    - 
                                                                @endif
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">
                                                                @if(is_object($ass->Profile))
                                                                    {{ $ass->Profile->name }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">
                                                                @if(is_object($ass->Organisation))
                                                                    {{ $ass->Organisation->name }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">
                                                                @if(is_object($ass->Assessment))
                                                                    {{ $ass->Assessment->name }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </p>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="te">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="timeEntryTable">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        ID
                                                    </th>
                                                    <th>
                                                        Date
                                                    </th>
                                                    <th>
                                                        Worker
                                                    </th>
                                                    <th>
                                                        Associated with
                                                    </th>
                                                    <th>
                                                        Start
                                                    </th>
                                                    <th>
                                                        Finish
                                                    </th>
                                                    <th>
                                                        Hours
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($timeEntries as $time)
                                                    <tr style="cursor: pointer" onClick="window.location.href='/logActivity/{{ $time->history_id }}'">
                                                        <td>
                                                            {{ $time->id }}
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">
                                                                {{ $time->date->format('d-m-Y') }}
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">
                                                                @if(is_object($time->Profile))
                                                                    {{ $time->Profile->name }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">
                                                                @if(is_object($time->Organisation))
                                                                    {{ $time->Organisation->name }}
                                                                @else
                                                                    -
                                                                @endif
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">
                                                                {{ $time->start }}
                                                            </p?>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">
                                                                {{ $time->finish }}
                                                            </p?>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">
                                                                {{ $time->calcHours() }}
                                                            </p>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="workers">
                            <ul class="nav nav-tabs tabs customtab">
                                <li class="active tab">
                                    <a href="#contractors" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="ti-hammer"></i></span> <span class="hidden-xs">Contractors</span> </a>
                                </li>
                                <li class="tab">
                                    <a href="#individuals" data-toggle="tab"> <span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs">Individuals </span> </a>
                                </li>
                                <li class="tab">
                                    <a href="#signin" data-toggle="tab"> <span class="visible-xs"><i class="ti-marker"></i></span> <span class="hidden-xs">Sign ins </span> </a>
                                </li>  
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="contractors">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-md-12">Add Contractor</label>
                                                <div class="col-md-12">
                                                    <select class="form-control form-control-line" id="newContractor" onChange="addContractorToSite(this.value)">
                                                        <option>Select</option>
                                                        @foreach($allContractors as $ac)
                                                            <option value="{{ $ac->id }}">{{ $ac->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>    
                                            </div>
                                                <div class="table-responsive">
                                                <table class="table table-hover" id="workerTable">
                                                    <thead>
                                                        <tr>
                                                            <th>
                                                                Contractor
                                                            </th>
                                                            <th>
                                                                Workers
                                                            </th>
                                                            <th>
                                                                Trades
                                                            </th>
                                                            <th>
                                                                Phone
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="workerContractorsTable">
                                                        @foreach($contractors as $contractor)
                                                            <tr>
                                                                @if(is_object($contractor->Profile))
                                                                    <td>{{ $contractor->Profile->name }}</td>
                                                                    <td>{{ count($contractor->Profile->Members) }}</td>
                                                                    <td>
                                                                        @foreach($contractor->Profile->Profiles_Trade as $profileTrade)
                                                                            {{ $profileTrade->Trade->name }}, 
                                                                        @endforeach
                                                                    </td>
                                                                    <td>{{ $contractor->Profile->phone }}</td>
                                                                    <td><a href="/removeSiteWorker/{{ $site->id }}/{{ $contractor->profile_id }}">Remove</a></td>
                                                                @endif
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane" id="individuals">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="workerTable">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        Name
                                                    </th>
                                                    <th>
                                                        Permit status
                                                    </th>
                                                    <th>
                                                        Associated with
                                                    </th>
                                                    <th>
                                                        Total hours
                                                    </th>
                                                    <th>
                                                        Total activities
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($workers as $worker)
                                                    <tr style="cursor: pointer" onClick="window.location.href='/editProfile/{{ $worker['worker']->id }}'">
                                                        <td>
                                                            <p class="text-muted">
                                                                @if($worker['same'] == 1)
                                                                {{ $worker['worker']->name }}
                                                                @else
                                                                    {{ $worker['worker']->member_hash }}
                                                                @endif
                                                            </p>
                                                        </td>
                                                        <td @if(count($worker['issues']) > 0) title="@foreach($worker['issues'] as $issue) {{ $issue }} @endforeach" @endif>
                                                            <p class="text-muted">
                                                            @if(count($worker['issues']) > 0)
                                                                <?= count($worker['issues']); ?> problem(s)
                                                            @else
                                                                ok
                                                            @endif
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">
                                                                {{ $worker['organisation'] }}
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">
                                                                {{ round($worker['hours'], 2) }}
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">
                                                                {{ $worker['activities'] }}
                                                            </p>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="signin">
                                    <div class="row">
                                        <div class="col-md-12 pull-right">
                                            <a href="/printableSiteQRCode/{{ $site->id }}" target="_blank" class="btn btn-primary pull-right">Site sign on QR code</a><br><br>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="signinTable">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        Date
                                                    </th>
                                                    <th>
                                                        Name
                                                    </th>
                                                    <th>
                                                        Member of
                                                    </th>
                                                    <th>
                                                        Trade(s)
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
                                                @foreach($logons as $logon)
                                                    <tr>
                                                        <td>{{ $logon['logon']->date->Format('d-m-Y') }}</td>
                                                        <td>{{ $logon['name'] }}</td>
                                                        <td>
                                                            @if(is_object($logon['member'])) 
                                                                {{ $logon['member']->Organisation->name }} 
                                                            @else 
                                                                unknown 
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @foreach($logon['logon']->Profile->Profiles_Trade as $pt)
                                                                {{ $pt->Trade->name }}, 
                                                            @endforeach
                                                        </td>
                                                        <td>{{ date('H:i', $logon['logon']->time_in) }}</td>
                                                        <td>
                                                            @if(!empty($logon['logon']->time_out))
                                                                {{ date('H:i', $logon['logon']->time_out) }}
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

                        <div class="tab-pane" id="plan">
                            <ul class="nav nav-tabs tabs customtab">
                                <li class="active tab">
                                    <a href="#map" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs">Site maps</span> </a>
                                </li>
                                <li class="tab">
                                    <a href="#zones" data-toggle="tab"> <span class="visible-xs"><i class="ti-location-arrow"></i></span> <span class="hidden-xs">Zones </span> </a>
                                </li>                        
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="map">
                                    <div class="row pull-right">
                                        <div class="col-md-6">
                                            <input type="text" name="newMap" id="newMap" class="form-control form-control-line" placeholder="New map name">
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-primary" onClick="addMap(document.getElementById('newMap').value)">Add map</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            &nbsp;<br>&nbsp;
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        Name
                                                    </th>
                                                    <th>
                                                        Zones
                                                    </th>
                                                    <th>
                                                        Controls
                                                    </th>
                                                    <th>
                                                        &nbsp;
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="mapsTable">
                                                @foreach($maps as $map)
                                                    <tr>
                                                        <td style="cursor: pointer" data-toggle="modal" data-target="#mapModal{{ $map['map']->id }}" data-whatever="@mdo">
                                                            <p class="text-muted">{{ $map['map']->name }}</p>
                                                        </td>
                                                        <td style="cursor: pointer" data-toggle="modal" data-target="#mapModal{{ $map['map']->id }}" data-whatever="@mdo">
                                                            <p class="text-muted">{{ $map['zones'] }}</p>
                                                        </td>
                                                        <td style="cursor: pointer" data-toggle="modal" data-target="#mapModal{{ $map['map']->id }}" data-whatever="@mdo">
                                                            <p class="text-muted">{{ $map['controls'] }}</p>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group m-r-10">
                                                                <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info dropdown-toggle waves-effect waves-light" type="button">Options <span class="caret"></span></button>
                                                                <ul role="menu" class="dropdown-menu">
                                                                    @if(in_array("sites:delete", $standardDisplay['permissions']))
                                                                        <li><a href="/deleteMap/{{ $map['map']->id }}">Remove</a></li>
                                                                    @endif
                                                                    @if(in_array("sites:edit", $standardDisplay['permissions']))
                                                                        <li><a href="#" data-toggle="modal" data-target="#transferModal{{ $map['map']->id }}">Transfer</a></li>
                                                                    @endif
                                                                </ul>
                                                            </div>
                                                            <p class="text-muted"></p>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="zones">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Zone name</label>
                                            <input type="text" id="newZone" class="form-control form-control-line" placeholder="Name of the zone">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Map name</label>
                                            <select class="form-control form-control-line" name="selectMaps" id="selectMaps">
                                                <option>Select</option>
                                                @foreach($maps as $map)
                                                    <option value="{{ $map['map']->id }}">{{ $map['map']->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>&nbsp;</label>
                                            <br>
                                            <button type="button" class="btn btn-primary" onClick="addZone()">Add zone</a>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            &nbsp;<br>&nbsp;
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        Name
                                                    </th>
                                                    <th>
                                                        Map
                                                    </th>
                                                    <th>
                                                        Controls
                                                    </th>
                                                    <th>&nbsp;</th>
                                                </tr>
                                            </thead>
                                            <tbody id="zonesTable">
                                                @foreach($zones as $zone)
                                                    <tr style="cursor: pointer">
                                                        <td onClick="window.location.href='/sites/zone/{{ $zone['id'] }}'">
                                                            <p class="text-muted">{{ $zone['zone'] }}</p>
                                                        </td>
                                                        <td onClick="window.location.href='/sites/zone/{{ $zone['id'] }}'">
                                                            <p class="text-muted">{{ $zone['map'] }}</p>
                                                        </td>
                                                        <td onClick="window.location.href='/sites/zone/{{ $zone['id'] }}'">
                                                            <p class="text-muted">{{ $zone['controls'] }}</p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted"><a href="/deleteZone/{{ $zone['id'] }}">Remove</a></p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted"><a href="/printableQRCode/{{ $zone['id'] }}" target="_blank">Get QR Code</a></p>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="assets">
                            <div class="row">
                                <div class="col-md-12">
                                    <span class="pull-right">
                                        @if(in_array("controls:order", $standardDisplay['permissions']))
                                            <button type="button" data-toggle="modal" data-target="#newOrder" data-whatever="@mdo" class="btn btn-info"><i class="fa fa-plus"></i> Order controls</button>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            @foreach($controls as $controlType)
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>{{ $controlType['type']->name }}</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>
                                                    &nbsp;
                                                </th>
                                                @foreach($controlType['fields'] as $field)
                                                <th>
                                                    {{ $field }}
                                                </th>
                                                @endforeach
                                                <th>
                                                    Zone
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($controlType['controls'] as $control)
                                                <tr style="cursor: pointer">
                                                    <td>
                                                        <input type="checkbox" value="{{ $control['control']->id }}" name="controlsToTransfer[]">
                                                    </td>
                                                    @foreach($control['fieldValues'] as $value)
                                                        <td @if($standardDisplay['profile']->super_user == 1) onClick="window.locatio.href='/editControl/{{ $control['control']->id }}'" @endif>
                                                            <p class="text-muted">
                                                                {{ $value['value'] }}
                                                            </p>
                                                        </td>
                                                    @endforeach
                                                    <td>
                                                        {{ $control['zone'] }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <hr>
                                    ^ <a href="#" onClick="populateControls()"  data-toggle="modal" data-target="#transferControl" data-whatever="@mdo">Transfer controls</a> | 
                                    <a href="#" onClick="populateRemoveControls()"  data-toggle="modal" data-target="#removeControl" data-whatever="@mdo">Request control removal</a>
                                </div>
                            @endforeach
                        </div>
                        

                        <div class="tab-pane" id="permits">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-md-12">Add requirement</label>
                                        <div class="col-md-6">
                                            <select class="form-control form-control-line form-control-select" name="permit" id="permit">
                                                <option value="0">Select</option>
                                                @foreach($allPermits as $p)
                                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <select class="form-control form-control-line form-control-select" name="permit_mandatory" id="permit_mandatory" onChange="addRequirement(this.value, document.getElementById('permit').value)">
                                                <option value="0">Select</option>    
                                                <option value="1">Recommended</option>
                                                <option value="2">Required</option>
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
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        Name
                                                    </th>
                                                    <th>
                                                        Type
                                                    </th>
                                                    <th>
                                                        Status
                                                    </th>
                                                    <th>
                                                        &nbsp;
                                                    </th>
                                                    <th>
                                                        &nbsp;
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="permitsTable">
                                                @foreach($permits as $per)
                                                    <tr>
                                                        <td>
                                                            <p class="text-muted">{{ $per['name'] }}</p>
                                                        </td>
                                                        <td>
                                                            <p class="text-muted">{{ $per['type'] }}</p>
                                                        </td>
                                                        @if($per['count'] == 0)
                                                            <td>
                                                                <p class="text-muted">ok</p>
                                                            </td>
                                                        @else
                                                            <td title="{{ $per['people'] }}">
                                                                <p class="text-muted">{{ $per['count'] }} problem
                                                                @if($per['count'] > 1)s @endif</p>
                                                            </td>
                                                        @endif
                                                        <td>
                                                            <p class="text-muted">
                                                                <select class="form-control form-control-line" name="requirements[{{ $per['id'] }}]">
                                                                    <option @if($per['mandatory'] == 2) selected @endif value="2">Required</option>
                                                                    <option @if($per['mandatory'] == 1) selected @endif value="1">Recommended</option>
                                                                </select>
                                                            </p>
                                                        </td>
                                                        <td>
                                                            <a href="/removePermit/site/{{ $per['pid'] }}">Remove</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            </div></div>
                        </div>
                        
                        <div class="tab-pane" id="tasks">
                            <div class="row">
                                <div class="col-md-12">
                                    <span class="pull-right"><a href="/editTask/0" class="btn btn-primary">New task</a></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <h3>Tasks</h3>
                                        <table class="table table-striped table-hover" id="taskTable">
                                            <thead>
                                                <tr>
                                                    <th>Subject</th>
                                                    <th>Assigned</th>
                                                    <th>Status</th>
                                                    <th>Priority</th>
                                                    <th>Progress</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($tasks as $task)
                                                    <tr style="cursor: pointer;" onClick="window.location.href='/editTask/{{ $task['id'] }}'">
                                                        <td>{{ $task['subject'] }}</td>
                                                        <td>{{ $task['assigned'] }}</td>
                                                        <td>{{ $task['status'] }}</td>
                                                        <td>{{ $task['priority'] }}</td>
                                                        <td>{{ $task['progress'] }}%</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                        <div class="tab-pane" id="attachments">
                            @include('components.attachment') 
                        </div>

                        <div class="tab-pane" id="reports">
                            <table>
                                {{-- <thead>
                                    <tr>
                                        <th>header 1</th>
                                        <th>header 1</th>
                                        <th>header 1</th>
                                        <th>header 1</th>
                                        <th>header 1</th>
                                    </tr>    
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Activities</td>
                                        @foreach($site->Sites_Reports as $report)
                                            @if($report->report_name == "activities")
                                                <td>
                                                    <input type="hidden" name="name[]" value="activities">
                                                    <select>
                                                        <option value="daily" @if($report->frequency == "daily") selected @endif>Daily</option>
                                                        <option value="weekly" @if($report->frequency == "weekly") selected @endif>Weekly</option>
                                                        <option value="monthly" @if($report->frequency == "monthly") selected @endif>Monthly</option>
                                                        <option value="yearly" @if($report->frequency == "yearly") selected @endif>Yearly</option>   
                                                    </select>                                             
                                                </td>
                                            @endif
                                        @endforeach
                                    </tr>
                                </tbody> --}}
                                {{-- Commented the above code because I think was working on a new feature and left it right there --}}
                            </table>
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



    <!--Modal plans Box-->
    @foreach($maps as $map)
        <form class="form-horizontal form-material" action="/saveMap/{{ $map['map']->id }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal fade" id="mapModal{{ $map['map']->id }}" tabindex="-1" role="dialog" aria-labelledby="mapModal{{ $map['map']->id }}" style="display: none;">
                <div class="modal-dialog" role="document">
                    <div class="modal-content modal-lg">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="exampleModalLabel1">{{ $map['map']->name }}</h4> </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="plan-name" class="control-label">Name</label>
                                <input type="text" id="plan-name" name="mapName" placeholder="Name of map" value="{{ $map['map']->name }}" class="form-control form-control-line"> 
                            </div>
                            <div class="form-group">
                                <label class="col-sm-12">Map image</label>
                                <div class="col-sm-12">
                                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                        <div class="form-control" data-trigger="fileinput"> 
                                            <i class="glyphicon glyphicon-file fileinput-exists"></i> 
                                            <span class="fileinput-filename"></span>
                                        </div> 
                                        <span class="input-group-addon btn btn-default btn-file">
                                            <span class="fileinput-new">Select file</span> 
                                            <span class="fileinput-exists">Change</span>
                                            <input type="file" name="mapImage"> 
                                        </span> 
                                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a> 
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    &nbsp;
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-9">
                                    @if(!empty($map['map']->image))
                                        <div id="floorPlan{{ $map['map']->id }}" style="width: 95%; z-index: 0;"></div>
                                    @else
                                        <h2>Please upload an image of the floor plan / map</h2>
                                    @endif
                                </div>
                                <div class="col-md-3">
                                    <h4>Controls</h4>
                                    <div class="table-responsive" style=" z-index: 999;">
                                        @foreach($map['controlsArray'] as $controlType)
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h4>{{ $controlType['type']->name }} &nbsp; @if(!empty($controlType['type']->shape))<img alt="shape" src="/assets/images/shapes/{{ $controlType['type']->shape }}.png">@endif</h4>
                                                    <hr>
                                                </div>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>&nbsp;</th>
                                                            @foreach($controlType['fields'] as $field)
                                                            <th>
                                                                {{ $field }}
                                                            </th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($controlType['controls'] as $control)
                                                            <tr style="cursor: pointer" onClick="highlightControl({{ $control['control']->id }})">
                                                                <td style="background: {{ $control['control']->colour }}">&nbsp;</td>
                                                                @foreach($control['fieldValues'] as $value)
                                                                    <td>
                                                                        <p class="text-muted">
                                                                            {{ $value['value'] }}
                                                                        </p>
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endforeach
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
    @endforeach

    <!--/Modal plans Box-->


    <!--Controls Box-->
    
    <div class="modal fade" id="newControl" tabindex="-1" role="dialog" aria-labelledby="newControl" style="display: none;">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="exampleModalLabel1">New control</h4> </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="plan-name" class="control-label">Hazard</label>
                                <input type="text" id="plan-name" name="planNname" placeholder="Name of plan" class="form-control form-control-line"> 
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="plan-name" class="control-label">Controls</label>
                                <input type="text" id="plan-name" name="planNname" placeholder="Name of plan" class="form-control form-control-line"> 
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="plan-name" class="control-label">Monitors</label>
                                <input type="text" id="plan-name" name="planNname" placeholder="Name of plan" class="form-control form-control-line"> 
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            &nbsp;
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="plan-name" class="control-label">Risk assessment score</label>
                                <input type="text" id="plan-name" name="planNname" placeholder="Name of plan" class="form-control form-control-line"> 
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="plan-name" class="control-label">Plan</label>
                                <textarea class="form-control form-control-line">Do things</textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="plan-name" class="control-label">Scope brief</label>
                                <textarea class="form-control form-control-line">Do things</textarea>
                            </div>
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

    <!--/Modal controls Box-->

    <!--Transfer control Box-->
        
    <form class="form-horizontal form-material" action="/transferControlsOnSite" method="POST" enctype="multipart/form-data">
        @csrf
        <div id="transferringControls"></div>
            <input type="hidden" name="site" value="{{ $site->id }}">
            <input type="hidden" name="referrer" value="site">
            <div class="modal fade" id="transferControl" tabindex="-1" role="dialog" aria-labelledby="transferControl" style="display: none;">
                <div class="modal-dialog" role="document">
                    <div class="modal-content modal-lg">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="exampleModalLabel1">Transfer controls</h4> 
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="plan-name" class="control-label">Zone</label>
                                        <select class="form-control form-control-line" onChange="checkHazards(this.value)" name="toZone">
                                            <option>Select</option>
                                            @foreach($zones as $zone)
                                                <option value="{{ $zone['id'] }}">{{ $zone['zone'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="plan-name" class="control-label">Hazard</label>
                                        <select class="form-control form-control-line" name="toHazard" id="toHazard">
                                            <option value="0">None</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    &nbsp;
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
        </div>
    </form>

    <!--/Modal transfer control Box-->

    <!--Control removal Box-->
        
    <form class="form-horizontal form-material" action="/removeControlsFromSite" method="POST" enctype="multipart/form-data">
        @csrf
            <div id="removingControls"></div>
            <input type="hidden" name="site" value="{{ $site->id }}">
            <input type="hidden" name="referrer" value="site">
            <div class="modal fade" id="removeControl" tabindex="-1" role="dialog" aria-labelledby="removeControl" style="display: none;">
                <div class="modal-dialog" role="document">
                    <div class="modal-content modal-lg">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="exampleModalLabel1">Remove controls</h4> 
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="plan-name" class="control-label">Notes</label>
                                        <textarea class="form-control form-control-line" name="notes"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    &nbsp;
                                </div>
                            </div>
                        </div>
                    
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type="submit" name="submit" value="Confirm" class="btn btn-primary">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!--/Modal control removal Box-->

    <!-- Controls order box-->
    <form class="form-horizontal form-material" action="/saveOrder/site" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="orderID" value="0">
        <div class="modal fade" id="newOrder" tabindex="-1" role="dialog" aria-labelledby="newOrder" style="display: none;">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-lg">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="exampleModalLabel1">Order controls</h4> 
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="white-box">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-md-12">Control type</label>
                                                <div class="col-md-12">
                                                    <select class="form-control form-control-line" name="controlType">
                                                        <option>Please select</option>
                                                        @foreach($controlOrderTypes as $type)
                                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>    
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-md-12">Quantity</label>
                                                <div class="col-md-12">
                                                    <input type="text" id="quantity" name="quantity" placeholder="Quantity required of this control type" required value="1" class="form-control form-control-line"> 
                                                </div>    
                                            </div>
                                        </div>
                                    </div>
                                    @if($standardDisplay['profile']->super_user == 1)
                                        <div class="row">
                                            <div class="col-md-12">
                                                <br>
                                                <div class="form-group">
                                                    <label class="col-md-12">simPRO ID</label>
                                                    <div class="col-md-12">
                                                        <input type="text" id="simproID" name="simproID" placeholder="simPRO ID (if sent)" class="form-control form-control-line"> 
                                                    </div>    
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <input type="hidden" name="site" value="{{ $site->id }}">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <br>
                                            <div class="form-group">
                                                <label class="col-md-12">Date required</label>
                                                <div class="col-md-12">
                                                    <input type="text" id="date" name="date" placeholder="dd-mm-yyyy" class="form-control form-control-line"> 
                                                </div>    
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <br>
                                            <div class="form-group">
                                                <label class="col-md-12">Order number</label>
                                                <div class="col-md-12">
                                                    <input type="text" id="orderNo" name="orderNo" placeholder="Your company order number" class="form-control form-control-line"> 
                                                </div>    
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <br>
                                            <div class="form-group">
                                                <label class="col-md-12">Notes</label>
                                                <div class="col-md-12">
                                                    <textarea name="notes" class="form-control form-control-line"></textarea>
                                                </div>    
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <br>
                                            <span class="pull-right"><input type="submit" name="submit" value="Order" class="btn btn-primary"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- /Controls order box -->

    <!--Transfer map Box-->
    @foreach($maps as $map)
        <form class="form-horizontal form-material" action="/transferMap" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="map" value="{{ $map['map']->id }}">
            <div class="modal fade" id="transferModal{{ $map['map']->id }}" tabindex="-1" role="dialog" aria-labelledby="transferModal{{ $map['map']->id }}" style="display: none;">
                <div class="modal-dialog" role="document">
                    <div class="modal-content modal-lg">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="exampleModalLabel1">Transfer map</h4> 
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="plan-name" class="control-label">Site to move to</label>
                                        <select class="form-control form-control-line" name="toSite">
                                            <option>Select</option>
                                            @foreach($sites as $ste)
                                                <option value="{{ $ste->id }}">{{ $ste->name }}</option>
                                            @endforeach
                                        </select>
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
    @endforeach

    <!--/Modal transfer control Box-->
            
    @include('components.widgetModal') 

    <!-- JS DEPENDENCIES -->
    @include('components.footer') 
    <!-- END JS DEPENDENCIES -->
                
    <!-- Page specific Javascript -->
    <script src="{{ asset('js/jasny-bootstrap.js') }}"></script>
    <script src="{{ asset('assets/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
    <!-- <script src="{{ asset('assets/dropzone-master/dist/dropzone.js') }}"></script> -->
    <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.collision.js') }}" charster="utf-8"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.draggable.js') }}" charster="utf-8"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.coords.js') }}" charster="utf-8"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.dsmorse-gridster.js') }}" charster="utf-8"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.dsmorse-gridster.with-extras.js') }}" charster="utf-8"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.gridster.js') }}" charster="utf-8"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/gridster/js/jquery.gridster.js') }}" charster="utf-8"></script>
    <script src="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/highcharts/highcharts.js') }}" charster="utf-8"></script>
    
    <!-- <script src="https://d3js.org/d3.v4.min.js"></script>
    <script src="https://giottojs.org/d3-canvas-transition/0.3.6/d3-canvas-transition.js"></script>  -->

    <script src="https://unpkg.com/konva@7.2.3/konva.min.js"></script>
    
    

    <script>
        $(document).ready(function() {
            $('#timeEntryTable').DataTable({
                "displayLength": 100,
            });

            $('#assessmentsTable').DataTable({
                "displayLength": 100,
            });

            $('#signinTable').DataTable({
                "displayLength": 100,
            });

            $('#logTable').DataTable({
                "displayLength": 100,
            });

            $('#taskTable').DataTable({
                "displayLength": 100,
            });

            jQuery('#date').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });

        });

        function openContact(value)
        {
            window.location.href="/editProfile/" + value
        }

        function addRequirement(mandatory, permit)
        {
            innerHTML = ""
            jQuery.getJSON('/addSiteRequirement/' + permit + '/' + mandatory + '/' + {{ $site->id }}, function (details) {
                $.each(details, function (d, detail) {
                    console.log(detail)
                    innerHTML += "<tr>"
                        innerHTML += "<td>"
                            innerHTML += "<p class=\"text-muted\">" + detail.name + "</p>"
                        innerHTML += "</td>"
                        innerHTML += "<td>"
                            innerHTML += "<p class=\"text-muted\">" + detail.type + "</p>"
                        innerHTML += "</td>"
                        if(detail.count == 0)
                        {
                            innerHTML += "<td>"
                                innerHTML += "<p class=\"text-muted\">ok</p>"
                            innerHTML += "</td>"
                        }
                        else
                        {
                            innerHTML += "<td title=\"" + detail.people + "\">"
                                innerHTML += "<p class=\"text-muted\">" + detail.count + " problem"
                                if(detail.count > 1)
                                {
                                    innerHTML += "s"
                                }
                            innerHTML += "</p>"
                            innerHTML += "</td>"
                        }
                        innerHTML += "<td>"
                            innerHTML += "<p class=\"text-muted\">"
                                if(detail.mandatory == 2)
                                {
                                    innerHTML += "Required"
                                }
                                else
                                {
                                    innerHTML += "Recommended"
                                }
                            innerHTML += "</p>"
                        innerHTML += "</td>"
                    innerHTML += "</tr>"
                });

                document.getElementById("permitsTable").innerHTML = innerHTML
            });
            document.getElementById("permit_mandatory").value = 0
            document.getElementById("permit").value = 0
            return 1;
        }

        function addMap(value)
        {
            innerHTML = ""
            jQuery.getJSON('/addSiteMap/' + {{ $site->id }} + '/' + value, function (details) {
                $.each(details, function (d, detail) {
                    console.log(detail.map.id)
                    
                    innerHTML += "<tr>"
                        innerHTML += "<td style=\"cursor: pointer\" data-toggle=\"modal\" data-target=\"#mapModal" + detail.map.id + "\">"
                            innerHTML += "<p class=\"text-muted\">" + detail.map.name + "</p>"
                        innerHTML += "</td>"
                        innerHTML += "<td style=\"cursor: pointer\" data-toggle=\"modal\" data-target=\"#mapModal" + detail.map.id + "\">"
                            innerHTML += "<p class=\"text-mutedz\">" + detail.zones + "</p>"
                        innerHTML += "</td>"
                        innerHTML += "<td style=\"cursor: pointer\" data-toggle=\"modal\" data-target=\"#mapModal" + detail.map.id + "\">"
                            innerHTML += "<p class=\"text-muted\">" + detail.controls + "</p>"
                        innerHTML += "</td>"
                        innerHTML += "<td>"
                            innerHTML += "<p class=\"text-muted\"><a href=\"/deleteMap/"+ detail.map.id +"\">Remove</a></p>"
                        innerHTML += "</td>"
                    innerHTML += "</tr>"
                });

                document.getElementById("mapsTable").innerHTML = innerHTML

                innerHTML = ""
                $.each(details, function (d, detail) {
                    innerHTML += "<option value=\"" + detail.map.id + "\">" + detail.map.name + "</option>"
                });

                document.getElementById("selectMaps").innerHTML = innerHTML

                document.getElementById("newMap").value = ""
            });
        }

        function addZone()
        {
            innerHTML = ""
            var zone = document.getElementById("newZone").value
            var map = document.getElementById("selectMaps").value
            console.log("Map is " + map)

            jQuery.getJSON('/addSiteZone/' + {{ $site->id }} + '/' + zone + '/' + map, function (details) {
                $.each(details, function (d, detail) {
                    console.log(detail)

                    innerHTML += "<tr style=\"cursor: pointer\">"
                        innerHTML += "<td onClick=\"window.location.href='/sites/zone/" + detail.id + "'\">"
                            innerHTML += "<p class=\"text-muted\">" + detail.zone + "</p>"
                        innerHTML += "</td>"
                        innerHTML += "<td onClick=\"window.location.href='/sites/zone/" + detail.id + "'\">"
                            innerHTML += "<p class=\"text-mutedz\">" + detail.map + "</p>"
                        innerHTML += "</td>"
                        innerHTML += "<td onClick=\"window.location.href='/sites/zone/" + detail.id + "'\">"
                            innerHTML += "<p class=\"text-muted\">" + detail.controls + "</p>"
                        innerHTML += "</td>"
                        innerHTML += "<td>"
                            innerHTML += "<p class=\"text-muted\"><a href=\"/deleteZone/"+ detail.id +"\">Remove</a></p>"
                        innerHTML += "</td>"
                    innerHTML += "</tr>"
                });

                document.getElementById("zonesTable").innerHTML = innerHTML

                

                document.getElementById("newZone").value = ""
                document.getElementById("selectMaps").value = ""
            });
        }

        function addContractorToSite(value)
        {
            innerHTML = ""

            jQuery.getJSON('/addContractorToSite/' + {{ $site->id }} + '/' + value, function (details) {
                $.each(details, function (d, detail) {
                    console.log(detail)

                    innerHTML += "<tr>"
                        innerHTML += "<td>" + detail.name + "</td>"
                        innerHTML += "<td>" + detail.workers + "</td>"
                        innerHTML += "<td>" + detail.trades + "</td>"
                        innerHTML += "<td>" + detail.phone + "</td>"
                        innerHTML += "<td><a href=\"/removeSiteWorker/" + detail.id + "\">Remove</a></td>"
                    innerHTML += "</tr>"
                });

                document.getElementById("workerContractorsTable").innerHTML = innerHTML

            });
        }

        function checkHazards(zone)
        {
            innerHTML = "<option value=\"0\">None</option>"

            jQuery.getJSON('/checkZoneHazards/' + zone, function (details) {
                $.each(details, function (d, detail) {
                    console.log(detail)

                    innerHTML += "<option value=\"" + detail.id + "\">"
                        innerHTML += detail.name
                    innerHTML += "</option>"
                    
                });

                document.getElementById("toHazard").innerHTML = innerHTML

            });
        }

        function populateControls()
        {
            controls = document.getElementsByName("controlsToTransfer[]");
            input = ""

            $.each(controls, function (d, detail) {
                if(detail.checked == true)
                {
                    input += "<input type=\"hidden\" name=\"controlsSelected[]\" value=\"" + detail.value + "\">"
                }
            });

            document.getElementById("transferringControls").innerHTML = input
        }

        function populateRemoveControls()
        {
            controls = document.getElementsByName("controlsToTransfer[]");
            input = ""

            $.each(controls, function (d, detail) {
                if(detail.checked == true)
                {
                    input += "<input type=\"hidden\" name=\"controlsSelected[]\" value=\"" + detail.value + "\">"
                }
            });

            document.getElementById("removingControls").innerHTML = input
        }

        function highlightControl(value)
        {
            console.log(value)
        }

        <?php
        foreach($maps as $map)
        {
            ?>
            //var container = document.getElementById("mapModal{{ $map['map']->id }}")
            //var rect = container.getBoundingClientRect();


            width = $("#mapModal{{ $map['map']->id }}").width();
            width = width*0.63
            
            console.log("on map {{ $map['map']->id }} the canvas width is " + width)

            if(width > "{{ $map['map']->width }}")
            {
                widthDifference = width / "{{ $map['map']->width }}"
                newWidth = "{{ $map['map']->width }}" * widthDifference
                newHeight = "{{ $map['map']->height }}" * widthDifference
            }
            else
            {
                widthDifference = "{{ $map['map']->width }}" / width
                newWidth = "{{ $map['map']->width }}" / widthDifference
                newHeight = "{{ $map['map']->height }}" / widthDifference
                console.log("on map {{ $map['map']->id }} the width difference is " + widthDifference)
            }
            
            console.log("on map {{ $map['map']->id }} the original width is {{ $map['map']->width }}, the new height is " + newWidth)
            
            $("#floorPlan{{ $map['map']->id }}").css('width', newWidth);
            $("#floorPlan{{ $map['map']->id }}").css('height', newHeight);
            $("#floorPlan{{ $map['map']->id }}").css('background-image', 'url(/storage/{{ $map['map']->image }})');
            $("#floorPlan{{ $map['map']->id }}").css('background-repeat', 'no-repeat');
            $("#floorPlan{{ $map['map']->id }}").css('background-size', newWidth + 'px ' + newHeight + 'px');

            <?php
        }
        ?>
                                    
            
    </script>

    @include('js.dashboard') 
    @include('js.canvas') 

@endsection