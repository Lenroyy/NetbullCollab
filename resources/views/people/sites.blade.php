@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        
        <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        <link href="{{ asset('css/dots.css') }}" rel="stylesheet">
        

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Sites</h4>
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
                            <h3 class="box-title">Sites</h3>
                        </div>
                        @if(in_array("sites:add", $standardDisplay['permissions']))
                            <div class="col-md-6">
                                <a class="pull-right btn btn-info waves-effect waves-light" href="/editSite/0">
                                <!-- <a class="pull-right btn btn-info-testing waves-effect waves-light" href="/editSite/0"> -->

                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    New Site
                                </a>
                            </div>
                        @endif
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
                        <table id="accountTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>State</th>
                                    <th>Owner</th>
                                    <th>Hygienist</th>
                                    <th>Contact name</th>
                                    <th>Contact phone</th>
                                    <th>Participation</th>
                                    @if($standardDisplay['site'] == 0)
                                        <th>&nbsp;</th>
                                    @endif
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sites as $site)
                                    <tr @if($site->relinquish_id > 0) style="cursor: pointer;"  @else style="cursor: pointer;"  @endif>    
                                        <td onClick="window.location.href='/editSite/{{ $site->id }}'">{{ $site->name }}</td>
                                        <td onClick="window.location.href='/editSite/{{ $site->id }}'">{{ $site->address }}</td>
                                        <td onClick="window.location.href='/editSite/{{ $site->id }}'">{{ $site->state }}</td>
                                        <td onClick="window.location.href='/editSite/{{ $site->id }}'">@if($site->builder_id > 0){{ $site->Builder->name }}@endif</td>
                                        <td onClick="window.location.href='/editSite/{{ $site->id }}'">@if($site->hygenist_id > 0){{ $site->Hygenist->name }}@endif</td>
                                        <td onClick="window.location.href='/editSite/{{ $site->id }}'">@if($site->primary_contact_id > 0){{ $site->Contact->name }}@endif</td>
                                        <td onClick="window.location.href='/editSite/{{ $site->id }}'">@if($site->primary_contact_id > 0){{ $site->Contact->phone }}@endif</td>
                                        @if($site->relinquish_id > 0)
                                            <td>
                                                @if($activeMembership->organisation_id == $site->relinquish_id OR $standardDisplay['profile']->super_user == 1)
                                                    <a href="/acceptSite/{{ $site->id }}" class="btn btn-warning">Accept site</a>
                                                @else
                                                    Waiting on accepteance of site.
                                                @endif
                                            </td>
                                        @else
                                            <td onClick="window.location.href='/editSite/{{ $site->id }}'">
                                                @foreach($sitesParticipation as $sp)
                                                    @if($sp['site'] == $site->id)
                                                        @if($sp['participation']['totalParticipation'] < 20)
                                                            <span class="smallRedDot">&nbsp;</span>
                                                        @elseif($sp['participation']['totalParticipation'] > 20 && $sp['participation']['totalParticipation'] < 59.9)
                                                            <span class="smallOrangeDot">&nbsp;</span>
                                                        @else
                                                            <span class="smallGreenDot">&nbsp;</span>
                                                        @endif
                                                        <?php
                                                            break;
                                                        ?>
                                                    @endif
                                                @endforeach
                                            
                                            </td>
                                        @endif
                                        @if($standardDisplay['site'] == 0)
                                            <td><a class="btn btn-primary" href="/site/logon/{{ $site->id }}">Sign in</a></td>
                                        @endif
                                        
                                        <td>
                                            <div class="btn-group m-r-10">
                                                <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info dropdown-toggle waves-effect waves-light" type="button">Options <span class="caret"></span></button>
                                                <ul role="menu" class="dropdown-menu">
                                                    @if(in_array("sites:edit", $standardDisplay['permissions']))
                                                        <li><a href="/editSite/{{ $site->id }}">Edit</a></li>
                                                    @endif
                                                    @if(in_array("sites:edit", $standardDisplay['permissions']))
                                                        <li><a href="/completeSite/{{ $site->id }}">Mark complete</a></li>
                                                    @endif
                                                    @if(in_array("sites:add", $standardDisplay['permissions']))
                                                        <li><a href="/copySite/{{ $site->id }}">Copy</a></li>
                                                    @endif
                                                    @if(in_array("sites:relinquish", $standardDisplay['permissions']))
                                                        <li><a href="#" data-toggle="modal" data-target="#relinquishModal{{ $site->id }}">Relinquish</a></li>
                                                    @endif
                                                    @if(in_array("sites:merge", $standardDisplay['permissions']))
                                                        <li><a href="#" data-toggle="modal" data-target="#mergeModal{{ $site->id }}">Merge</a></li>
                                                    @endif                                                    
                                                    @if(in_array("sites:delete", $standardDisplay['permissions']))
                                                        <li><a href="/archiveSite/{{ $site->id }}">Archive</a></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    @foreach($pendingSites as $site)
                                        <tr @if($site->relinquish_id > 0) style="cursor: pointer;"  @else style="cursor: pointer;"  @endif>    
                                            <td onClick="window.location.href='/editSite/{{ $site->id }}'">{{ $site->name }}</td>
                                            <td onClick="window.location.href='/editSite/{{ $site->id }}'">{{ $site->address }}</td>
                                            <td onClick="window.location.href='/editSite/{{ $site->id }}'">@if($site->builder_id > 0){{ $site->Builder->name }}@endif</td>
                                            <td onClick="window.location.href='/editSite/{{ $site->id }}'">@if($site->hygenist_id > 0){{ $site->Hygenist->name }}@endif</td>
                                            <td onClick="window.location.href='/editSite/{{ $site->id }}'">@if($site->primary_contact_id > 0){{ $site->Contact->name }}@endif</td>
                                            <td onClick="window.location.href='/editSite/{{ $site->id }}'">@if($site->primary_contact_id > 0){{ $site->Contact->phone }}@endif</td>
                                            @if($site->relinquish_id > 0)
                                                <td>
                                                    @if($activeMembership->organisation_id == $site->relinquish_id OR $standardDisplay['profile']->super_user == 1)
                                                        <a href="/acceptSite/{{ $site->id }}" class="btn btn-warning">Accept site</a>
                                                    @else
                                                        Waiting on accepteance of site.
                                                    @endif
                                                </td>
                                            @else
                                                <td onClick="window.location.href='/editSite/{{ $site->id }}'">{{ $site->status }}</td>
                                            @endif
                                            @if($standardDisplay['site'] == 0)
                                                <td><a class="btn btn-primary" href="/site/logon/{{ $site->id }}">Sign in</a></td>
                                            @endif
                                            
                                            <td>
                                                <div class="btn-group m-r-10">
                                                    <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info dropdown-toggle waves-effect waves-light" type="button">Options <span class="caret"></span></button>
                                                    <ul role="menu" class="dropdown-menu">
                                                        @if(in_array("sites:edit", $standardDisplay['permissions']))
                                                            <li><a href="/editSite/{{ $site->id }}">Edit</a></li>
                                                        @endif
                                                        @if(in_array("sites:edit", $standardDisplay['permissions']))
                                                            <li><a href="/completeSite/{{ $site->id }}">Mark complete</a></li>
                                                        @endif
                                                        @if(in_array("sites:add", $standardDisplay['permissions']))
                                                            <li><a href="/copySite/{{ $site->id }}">Copy</a></li>
                                                        @endif
                                                        @if(in_array("sites:relinquish", $standardDisplay['permissions']))
                                                            <li><a href="#" data-toggle="modal" data-target="#relinquishModal{{ $site->id }}">Relinquish</a></li>
                                                        @endif
                                                        @if(in_array("sites:merge", $standardDisplay['permissions']))
                                                            <li><a href="/mergeSite/{{ $site->id }}">Merge</a></li>
                                                        @endif                                                    
                                                        @if(in_array("sites:delete", $standardDisplay['permissions']))
                                                            <li><a href="/archiveSite/{{ $site->id }}">Archive</a></li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                        &nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->

        <!-- Modals for relinquishing sites-->
        @foreach($sites as $site)
            <form class="form-horizontal form-material" action="/relinquishSite" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="site" value="{{ $site->id }}">
                <div class="modal fade" id="relinquishModal{{ $site->id }}" tabindex="-1" role="dialog" aria-labelledby="relinquishModal{{ $site->id }}" style="display: none;">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content modal-lg">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                <h4 class="modal-title" id="exampleModalLabel1">Relinquish site</h4> 
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="builder" class="control-label">Builder to relinquish site to</label>
                                            <select class="form-control form-control-line" name="builder">
                                                <option>Select</option>
                                                @foreach($builders as $builder)
                                                    <option value="{{ $builder['builder']->id }}">{{ $builder['builder']->name }}</option>
                                                @endforeach
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
            </form>
        @endforeach
        <!-- /relinqusihing modals -->

        <!-- Modals for Merging sites-->
        @foreach($sites as $site)
            <form class="form-horizontal form-material" action="/mergeSite" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="site" value="{{ $site->id }}">
                <div class="modal fade" id="mergeModal{{ $site->id }}" tabindex="-1" role="dialog" aria-labelledby="mergeModal{{ $site->id }}" style="display: none;">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content modal-lg">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                <h4 class="modal-title" id="exampleModalLabel1">Merge site <small>(this site will be merged into the selected site)</small></h4> 
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="plan-name" class="control-label">Site</label>
                                            <select class="form-control form-control-line" name="mergeTo">
                                                <option>Select</option>
                                                @foreach($sites as $site)
                                                    <option value="{{ $site->id }}">{{ $site->name }}</option>
                                                @endforeach
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
            </form>
        @endforeach
        <!-- /relinqusihing modals -->

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