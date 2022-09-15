@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Control type</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>
        <form class="form-horizontal form-material" action="/setup/controlType" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="controlType" value="{{ $controlType->id }}">
            <div class="row">
                <div class="col-md-12">
                    <span class="pull-right">
                        <input type="submit" id="submit-all" name="submit" value="Save" class="btn btn-primary">
                        &nbsp;
                        <a class="btn btn-info" href="/setup/controlTypes">Cancel</a>
                    </span>
                </div>
            </div>
            <br>
                
            <!-- .row -->
            <div class="row">
                <div class="col-md-4 col-xs-12">
                    <div class="white-box">
                        <div class="user-bg"><img width="100%" alt="user" src="@if(isset($controlType->image)) /storage/<?= $controlType->image; ?> @else /assets/images/logo/X.png @endif">
                            <div class="overlay-box">
                                <div class="user-content">
                                    <a href="javascript:void(0)"><img src="@if(isset($controlType->image)) /storage/<?= $controlType->image; ?> @else /assets/images/logo/X.png @endif" class="thumb-lg img-circle" alt="img"></a>
                                    <h4 class="text-white">@if(isset($controlType->name)){{ $controlType->name }}@endif</h4>
                                    <h5 class="text-white">@if(isset($controlType->manufacturer)){{ $controlType->manufacturer }}@endif</h5> </div>
                            </div>
                        </div>
                    </div>
                </div>
            



                    
                <div class="col-md-8 col-xs-12">
                    <div class="white-box">
                        <ul class="nav nav-tabs tabs customtab">
                            <li class="active tab">
                                <a href="#profile" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs">Profile</span> </a>
                            </li>
                            @if($controlType->id > 0)
                                <li class="tab">
                                    <a href="#fields" data-toggle="tab"> <span class="visible-xs"><i class="ti-harddrive"></i></span> <span class="hidden-xs">Fields </span> </a>
                                </li>
                                <li class="tab">
                                    <a href="#billing" data-toggle="tab"> <span class="visible-xs"><i class="ti-money"></i></span> <span class="hidden-xs">Billing </span> </a>
                                </li> 
                                <li class="tab">
                                    <a href="#units" data-toggle="tab"> <span class="visible-xs"><i class="ti-truck"></i></span> <span class="hidden-xs">Units </span> </a>
                                </li>
                                <li class="tab">
                                    <a href="#videos" data-toggle="tab"> <span class="visible-xs"><i class="ti-video-clapper"></i></span> <span class="hidden-xs">Instructional videos </span> </a>
                                </li>
                                <li class="tab">
                                    <a href="#attachments" data-toggle="tab"> <span class="visible-xs"><i class="ti-folder"></i></span> <span class="hidden-xs">Attachments @if(count($files) > 0)<span class="badge"><?= count($files); ?></span>@endif</span> </a>
                                </li>
                                <li class="tab">
                                    <a href="#logs" data-toggle="tab"> <span class="visible-xs"><i class="ti-calendar"></i></span> <span class="hidden-xs">Logs</span> </a>
                                </li>
                            @endif
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="profile">
                                <br>&nbsp;
                                <div class="form-group">
                                    <label class="col-md-12">Name</label>
                                    <div class="col-md-12">
                                        <input type="text" id="name" name="name" value="{{ $controlType->name }}" placeholder="Control type name" class="form-control form-control-line"> 
                                    </div>    
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Control type group</label>
                                    <div class="col-md-12">
                                        <select class="form-control form-control-line" id="typeGroup" name="typeGroup">
                                            <option value="0">None</option>
                                            @foreach($typeGroups as $tg)
                                                <option value="{{$tg->id}}" @if($tg->id == $controlType->control_type_group) selected @endif>{{ $tg->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>    
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Manufacturer</label>
                                    <div class="col-md-12">
                                        <input type="text" id="manufacturer" name="manufacturer" placeholder="Control type manufacturer"  value="{{ $controlType->manufacturer }}" class="form-control form-control-line"> 
                                    </div>    
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">simPRO Asset Type ID</label>
                                    <div class="col-md-12">
                                        <input type="text" id="simpro_asset_type_id" value="{{ $controlType->simpro_asset_type_id_1 }}" name="simpro_asset_type_id" placeholder="simPRO Asset Type ID" class="form-control form-control-line"> 
                                    </div>    
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Map shape</label>
                                    <div class="col-md-12">
                                        <select class="form-control form-control-line" name="shape">
                                            @foreach($shapes as $shape)
                                                <option @if($shape->shape == $controlType->shape) selected @endif value="{{ $shape->shape }}">{{ $shape->shape }}</option>
                                            @endforeach
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

                            <div class="tab-pane" id="fields">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="col-md-6">
                                                <label>New field name</label>
                                                <input type="text" id="name" name="newFieldName" placeholder="name of field" class="form-control form-control-line"> 
                                            </div>    
                                            <div class="col-md-6">
                                                <input type="submit" name="submit" value="Add field" class="btn btn-primary">
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
                                        <h3>Fields</h3>
                                        <hr>
                                    </div>
                                </div>    
                                @foreach($typeFields as $field)
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <div class="col-md-10">
                                                    <input type="hidden" name="fieldIDs[]" value="{{ $field->id }}"> 
                                                    <input type="text" name="fields[]" placeholder="Field name" value="{{ $field->name }}" class="form-control form-control-line"> 
                                                </div>
                                                <div class="col-md-2 pull-right">
                                                    <a href="/deleteField/{{ $field->id }}" class="pull-right">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="tab-pane" id="units">
                                <ul class="nav nav-tabs tabs customtab">
                                    <li class="active tab">
                                        <a href="#deployed" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="ti-export"></i></span> <span class="hidden-xs">Deployed</span> </a>
                                    </li>
                                    <li class="tab">
                                        <a href="#available" data-toggle="tab"> <span class="visible-xs"><i class="ti-import"></i></span> <span class="hidden-xs">Available </span> </a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane active" id="deployed">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="pull-right"><a class="btn btn-primary" data-toggle="modal" data-target="#newUnitModal" data-whatever="@mdo">Add unit</a></span>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="deployed">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            Serial number
                                                        </th>
                                                        <th>
                                                            Site
                                                        </th>
                                                        @foreach($controls['fields'] as $field)
                                                            <th>
                                                                {{ $field }}
                                                            </th>
                                                        @endforeach
                                                        <th>
                                                            &nbsp;
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($controls['controls'] as $c)
                                                        @if($c['control']->deployed > 0)
                                                            <tr style="cursor: pointer">
                                                                <td onClick="window.location.href='/editControl/{{ $c['control']->id }}'">
                                                                    <p class="text-muted">{{ $c['control']->serial }}</p>
                                                                </td>
                                                                <td onClick="window.location.href='/editControl/{{ $c['control']->id }}'">
                                                                    <p class="text-muted">{{ $c['control']->Site->name }}</p>
                                                                </td>
                                                                @foreach($c['fieldValues'] as $value)
                                                                    <td onClick="window.location.href='/editControl/{{ $c['control']->id }}'">
                                                                        {{ $value['value'] }}
                                                                    </td>
                                                                @endforeach
                                                                <td class="pull-right">
                                                                    <div class="btn-group m-r-10">
                                                                        <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info dropdown-toggle waves-effect waves-light" type="button">Options <span class="caret"></span></button>
                                                                        <ul role="menu" class="dropdown-menu">
                                                                            <li><a href="/editControl/{{ $c['control']->id }}">Edit</a></li>
                                                                            <li><a href="/removeControl/{{ $c['control']->id }}">Remove from site</a></li>
                                                                            <li><a href="/archiveControl/{{ $c['control']->id }}">Archive</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <br><br><br><br><br>
                                        </div>
                                    </div>
                                    
                                    <div class="tab-pane" id="available">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="pull-right"><a class="btn btn-primary" data-toggle="modal" data-target="#newUnitModal" data-whatever="@mdo">Add unit</a></span>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="Current requests">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            Serial number
                                                        </th>
                                                        @foreach($controls['fields'] as $field)
                                                            <th>
                                                                {{ $field }}
                                                            </th>
                                                        @endforeach
                                                        <th>
                                                            &nbsp;
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($controls['controls'] as $c)
                                                        @if($c['control']->deployed == 0)
                                                            <tr style="cursor: pointer">
                                                                <td onClick="window.location.href='/editControl/{{ $c['control']->id }}'">
                                                                    <p class="text-muted">{{ $c['control']->serial }}</p>
                                                                </td>
                                                                @foreach($c['fieldValues'] as $value)
                                                                    <td onClick="window.location.href='/editControl/{{ $c['control']->id }}'">
                                                                        {{ $value['value'] }}
                                                                    </td>
                                                                @endforeach
                                                                <td class="pull-right">
                                                                    <div class="btn-group m-r-10">
                                                                        <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info dropdown-toggle waves-effect waves-light" type="button">Options <span class="caret"></span></button>
                                                                        <ul role="menu" class="dropdown-menu">
                                                                            <li><a href="/editControl/{{ $c['control']->id }}">Edit</a></li>
                                                                            <li><a href="#" data-toggle="modal" data-target="#moveUnitModal{{ $c['control']->id }}" data-whatever="@mdo">Move to site</a></li>
                                                                            <li><a href="/archiveControl/{{ $c['control']->id }}">Archive</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </td>
                                                                    <p class="text-muted">
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <br><br><br><br><br>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="videos">
                                <br>
                                <div class="form-group">
                                    <label class="col-md-12">Instructional videos</label>
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span class="pull-right"><a class="btn btn-primary" data-toggle="modal" data-target="#newVideoModal" data-whatever="@mdo">Add video</a></span>
                                            </div>
                                        </div>
                                    </div>    
                                </div>
                                <div class="form-group">
                                    <label class="col-md-12">Videos</label>
                                    <div class="col-md-12">
                                        <table class="table table-responsive table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>&nbsp;</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($controlType->Videos as $video)
                                                    <tr>
                                                        <td data-toggle="modal" data-target="#editVideoModal{{ $video->id }}" style="cursor: pointer">{{ $video->type }}</td>
                                                        <td><a href="/deleteVideo/{{ $video->id }}">delete</a></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>    
                                </div>



                            </div>

                            <div class="tab-pane" id="billing">
                                <ul class="nav nav-tabs tabs customtab">
                                    <li class="active tab">
                                        <a href="#amounts" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="ti-money"></i></span> <span class="hidden-xs">Billing</span> </a>
                                    </li>
                                    <li class="tab">
                                        <a href="#costCenters" data-toggle="tab"> <span class="visible-xs"><i class="ti-layout-tab"></i></span> <span class="hidden-xs">Cost centers </span> </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="amounts">
                                        <div class="form-group">
                                            <label class="col-md-12">Internal lease amount</label>
                                            <div class="col-md-12">
                                                <input type="number" step="0.01" name="internalLease" value="{{ $controlType->billing_amount }}" placeholder="Control internal billing amount" class="form-control form-control-line"> 
                                            </div>    
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-12">External lease amount</label>
                                            <div class="col-md-12">
                                                <input type="number" step="0.01" name="externalLease" value="{{ $controlType->external_billing_amount }}" placeholder="Control external billing amount" class="form-control form-control-line"> 
                                            </div>    
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-12">Monitoring only amount</label>
                                            <div class="col-md-12">
                                                <input type="number" step="0.01" name="monitoringAmount" value="{{ $controlType->monitoring_only_billing_amount }}" placeholder="Control monitoring only billing amount" class="form-control form-control-line"> 
                                            </div>    
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-12">Sale amount</label>
                                            <div class="col-md-12">
                                                <input type="number" step="0.01" name="saleAmount" value="{{ $controlType->sale_amount }}" placeholder="Control sale amount" class="form-control form-control-line"> 
                                            </div>    
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-12">Frequency</label>
                                            <div class="col-md-12">
                                                <select name="billingFrequency" class="form-control form-control-line"> 
                                                    <option value="daily" @if($controlType->billing_frequency == "daily") selected @endif>Daily</option>
                                                    <option value="weekly" @if($controlType->billing_frequency == "weekly") selected @endif>Weekly</option>
                                                    <option value="monthly" @if($controlType->billing_frequency == "monthly") selected @endif>Monthly</option>
                                                </select>
                                            </div>    
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="costCenters">
                                        <div class="form-group">
                                            <label class="col-md-12">Internal lease cost center</label>
                                            <div class="col-md-12">
                                                <select name="internal_lease_cost_center_id" class="form-control form-control-line"> 
                                                    <option value="0">--- Please select---</option>
                                                    @foreach($costCenters as $costCenter)
                                                        {{-- <option value="{{ $costCenter->cost_center_id }}" @if($controlType->internal_lease_cost_center_id) @if($controlType->internal_lease_cost_center_id == $costCenter->cost_center_id) selected @endif @elseif($simPROSettings) @if($simPROSettings->internal_lease == $costCenter->cost_center_id) selected @endif @endif> --}}
                                                        <option value="{{ $costCenter->cost_center_id }}" @if($controlType->internal_lease_cost_center_id) @if($controlType->internal_lease_cost_center_id == $costCenter->cost_center_id) selected @endif @endif>
                                                            @if($costCenter->company_id == 2)
                                                                Nextrack ::
                                                            @else
                                                                Defender ::
                                                            @endif
                                                            {{ $costCenter->cost_center_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>    
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-12">External lease cost center</label>
                                            <div class="col-md-12">
                                                <select name="external_lease_cost_center_id" class="form-control form-control-line"> 
                                                    <option value="0">--- Please select---</option>
                                                    @foreach($costCenters as $costCenter)
                                                        <option value="{{ $costCenter->cost_center_id }}" @if($controlType->external_lease_cost_center_id) @if($controlType->external_lease_cost_center_id == $costCenter->cost_center_id) selected @endif @elseif($simPROSettings) @if($simPROSettings->external_lease == $costCenter->cost_center_id) selected @endif @endif>
                                                            @if($costCenter->company_id == 2)
                                                                Nextrack ::
                                                            @else
                                                                Defender ::
                                                            @endif
                                                            {{ $costCenter->cost_center_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>    
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-12">Monitoring only cost center</label>
                                            <div class="col-md-12">
                                                <select name="monitoring_only_cost_center_id" class="form-control form-control-line"> 
                                                    <option value="0">--- Please select---</option>
                                                    @foreach($costCenters as $costCenter)
                                                        <option value="{{ $costCenter->cost_center_id }}" @if($controlType->monitoring_only_cost_center_id) @if($controlType->monitoring_only_cost_center_id == $costCenter->cost_center_id) selected @endif @elseif($simPROSettings) @if($simPROSettings->monitoring_only == $costCenter->cost_center_id) selected @endif @endif>
                                                            @if($costCenter->company_id == 2)
                                                                Nextrack ::
                                                            @else
                                                                Defender ::
                                                            @endif
                                                            {{ $costCenter->cost_center_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>    
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-12">Sale cost center</label>
                                            <div class="col-md-12">
                                                <select name="sale_cost_center_id" class="form-control form-control-line"> 
                                                    <option value="0">--- Please select---</option>
                                                    @foreach($costCenters as $costCenter)
                                                        <option value="{{ $costCenter->cost_center_id }}" @if($controlType->sale_cost_center_id) @if($controlType->sale_cost_center_id == $costCenter->cost_center_id) selected @endif @endif>
                                                            @if($costCenter->company_id == 2)
                                                                Nextrack ::
                                                            @else
                                                                Defender ::
                                                            @endif
                                                            {{ $costCenter->cost_center_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>    
                                        </div>
                                    </div>
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

    <!-- New Video -->

        <form action="/editVideo" method="POST">
            @csrf
            <input type="hidden" name="video" value="0">
            <input type="hidden" name="controlType" value="{{ $controlType->id }}">
            <div class="modal fade" id="newVideoModal" tabindex="-1" role="dialog" aria-labelledby="newVideoModal" style="display: none;">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="exampleModalLabel1">Video</h4> </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="plan-name" class="control-label">Type</label>
                                    <select name="type" class="form-control form-control-line" >
                                        <option value="setup">Setup</option>
                                        <option value="usage">Usage</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="plan-name" class="control-label">Embed video code</label>
                                    <textarea name="code" class="form-control form-control-line" cols="50" rows="6"></textarea>
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

        @foreach($controlType->Videos as $video)
            <form action="/editVideo" method="POST">
                @csrf
                <input type="hidden" name="video" value="{{ $video->id }}">
                <input type="hidden" name="controlType" value="{{ $controlType->id }}">
                <div class="modal fade" id="editVideoModal{{ $video->id }}" tabindex="-1" role="dialog" aria-labelledby="editVideoModal{{ $video->id }}" style="display: none;">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                <h4 class="modal-title" id="exampleModalLabel1">Video</h4> </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="plan-name" class="control-label">Type</label>
                                        <select name="type" class="form-control form-control-line" >
                                            <option value="setup" @if($video->type == "setup") selected @endif>Setup</option>
                                            <option value="usage" @if($video->type == "usage") selected @endif>Usage</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="plan-name" class="control-label">Embedd video code</label>
                                        <textarea name="code" class="form-control form-control-line" cols="50" rows="6">{{ $video->code }}</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1">&nbsp;</div>
                                        <div class="col-md-10">
                                            {!! $video->code !!}
                                        </div>
                                    <div class="col-md-1">&nbsp;</div>
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

    <!-- End New Video -->

    <!-- New Unit -->
        <form action="/editControl" method="POST">
            @csrf
            <input type="hidden" name="control" value="0">
            <input type="hidden" name="controlType" value="{{ $controlType->id }}">
            <div class="modal fade" id="newUnitModal" tabindex="-1" role="dialog" aria-labelledby="newUnitModal" style="display: none;">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="exampleModalLabel1">New {{ $controlType->name }}</h4> </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="plan-name" class="control-label">Serial number</label>
                                    <input type="text" name="serial" class="form-control form-control-line"> 
                                </div>
                                <div class="form-group">
                                    <label for="plan-name" class="control-label">Commission date</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="datepicker-autoclose" placeholder="dd-mm-yyyy" name="commissionDate"><span class="input-group-addon"><i class="icon-calender"></i></span> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plan-name" class="control-label">simPRO Asset ID</label>
                                    <input type="text" name="simproAssetID" class="form-control form-control-line"> 
                                </div>
                                @foreach($typeFields as $field)
                                    <input type="hidden" name="fieldID[]" value="{{ $field->id }}">
                                    <div class="form-group">
                                        <label for="plan-name" class="control-label">{{ $field->name }}</label>
                                        <input type="text" name="fieldValue[]" class="form-control form-control-line"> 
                                    </div>
                                @endforeach
                            </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type="submit" name="submit" value="Save" class="btn btn-primary">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <!-- /New Unit modal -->

    <!-- Move unit modal -->
        @foreach($controls['controls'] as $c)
            <form action="/moveControl/{{ $c['control']->id }}" method="POST">
                @csrf
                <div class="modal fade" id="moveUnitModal{{ $c['control']->id }}" tabindex="-1" role="dialog" aria-labelledby="moveUnitModal{{ $c['control']->id }}" style="display: none;">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                <h4 class="modal-title" id="exampleModalLabel1">Move to site</h4> </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="plan-name" class="control-label">Select site</label>
                                        <select name="site" class="form-control form-control-line"> 
                                            @foreach($sites as $site)
                                                <option value="{{ $site->id }}">{{ $site->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            
                            <div class="modal-footer">
                                <input type="hidden" name="originator" value="controlType">
                                <input type="hidden" name="moduleID" value="{{ $controlType->id }}">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <input type="submit" name="submit" value="Move" class="btn btn-primary">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endforeach
    <!-- End move unit modal -->


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

            });

            function request(value)
            {
                alert(value);
            }

            jQuery('#datepicker-autoclose').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });
                    
        </script>

    @endsection