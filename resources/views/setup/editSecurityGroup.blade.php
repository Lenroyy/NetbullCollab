@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Edit security group</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>
                
        <!-- .row -->
        <form action="/setup/securityGroup/{{ $type }}/{{ $group->id }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <span class="pull-right">
                        <input type="submit" id="submit-all" name="submit" value="Save" class="btn btn-primary">
                        <a class="btn btn-info" href="/setup/securityGroups"> &nbsp;Cancel</a>
                    </span>
                </div>
            </div>
            <br>
                
            <!-- .row -->
            <div class="row white-box">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="security_group_name" class="control-label">Security group name:</label>
                        <input type="text" class="form-control" id="security_group_name" placeholder="Name of the security group" name="security_group_name" value="@if($group->name != "New"){{ $group->name }}@endif"> 
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="checkbox checkbox-primary">
                        <input id="billable" name="billable" <?php if($group->billable == 1){ echo " checked"; } ?> type="checkbox">
                        <label for="billable"> Billable security group </label>
                    </div>
                </div>
            </div>

            <!-- .row -->
        
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <ul class="nav nav-tabs tabs customtab">

                            <li class="active tab">
                                <a href="#people" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="fa fa-home"></i></span> <span class="hidden-xs">People</span> </a>
                            </li>

                            <li class="tab">
                                <a href="#equipment" data-toggle="tab"> <span class="visible-xs"><i class="fa fa-envelope-o"></i></span> <span class="hidden-xs">Equipment</span> </a>
                            </li>

                            <li class="tab">
                                <a href="#activities" data-toggle="tab"> <span class="visible-xs"><i class="fa fa-envelope-o"></i></span> <span class="hidden-xs">Activities</span> </a>
                            </li>

                            <li class="tab">
                                <a href="#utilities" data-toggle="tab"> <span class="visible-xs"><i class="fa fa-envelope-o"></i></span> <span class="hidden-xs">Utilites</span> </a>
                            </li>
                            <li class="tab">
                                <a href="#logs" data-toggle="tab"> <span class="visible-xs"><i class="ti-calendar"></i></span> <span class="hidden-xs">Logs</span> </a>
                            </li>

                            @if($standardDisplay['profile']->super_user == 1)

                                <li class="tab">
                                    <a href="#system" data-toggle="tab" > <span class="visible-xs"><i class="fa fa-envelope-o"></i></span> <span class="hidden-xs">System</span> </a>
                                </li>

                            @endif
                        </ul>
                        
                        <div class="tab-content">
                            <div class="tab-pane" id="system">

                                <div class="row">
                                    <div class="col-md-6">
                                        &nbsp;<br>&nbsp;<br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Trades</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="setup_trades" name="perms[setup][trades][setup]" <?php if(in_array("trades:setup", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="setup_trades"> Setup trades </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>
                                



                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Activities</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="setup_activities" name="perms[setup][activities][setup]" <?php if(in_array("activities:setup", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="setup_activities"> Setup activities </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>




                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>SWMS / Assessments</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="setup_swms" name="perms[setup][swms][setup]" <?php if(in_array("swms:setup", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="setup_swms"> Setup Assessments / SWMS </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Hazards</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="setup_swms" name="perms[setup][hazards][setup]" <?php if(in_array("hazards:setup", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="setup_swms"> Setup Hazards </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Samples</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="setup_swms" name="perms[setup][samples][setup]" <?php if(in_array("samples:setup", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="setup_swms"> Setup Samples </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Controls</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="setup_assets" name="perms[setup][controls][setup]" <?php if(in_array("controls:setup", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="setup_assets"> Setup Controls </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Training</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="setup_training" name="perms[setup][training][setup]" <?php if(in_array("training:setup", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="setup_training"> Setup Training </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Permits</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="setup_permits" name="perms[setup][permits][setup]" <?php if(in_array("permits:setup", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="setup_permits"> Setup permits </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Security groups</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="secGroups" name="perms[setup][secGroups][setup]" <?php if(in_array("secGroups:setup", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="secGroups"> Setup security groups </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>News</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="setup_news" name="perms[setup][news][setup]" <?php if(in_array("news:setup", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="setup_news"> Setup news </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Exposures</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="setup_news" name="perms[setup][exposures][setup]" <?php if(in_array("exposures:setup", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="setup_news"> Setup exposures </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Integrations</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="setup_iot" name="perms[setup][iot][setup]" <?php if(in_array("iot:setup", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="setup_iot"> Setup Integrations </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane active" id="people">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Users</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="view_users" name="perms[people][users][view]" <?php if(in_array("users:view", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="view_users"> View users </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="edit_users" name="perms[people][users][edit]" <?php if(in_array("users:edit", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="edit_users"> Edit users </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="add_users" name="perms[people][users][add]" <?php if(in_array("users:add", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="add_users"> Add users </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="delete_users" name="perms[people][users][delete]" <?php if(in_array("users:delete", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="delete_users"> Delete users </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-6">
                                        &nbsp;<br>&nbsp;<br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Sites</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="view_sites_all" name="perms[people][sites][view-all]" <?php if(in_array("sites:view-all", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="view_sites_all"> View all sites </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="view_sites" name="perms[people][sites][view]" <?php if(in_array("sites:view", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="view_sites"> View only my sites </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="add_sites" name="perms[people][sites][add]" <?php if(in_array("sites:add", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="add_sites"> Add sites </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="edit_sites" name="perms[people][sites][edit]" <?php if(in_array("sites:edit", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="edit_sites"> Edit sites </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="delete_sites" name="perms[people][sites][delete]" <?php if(in_array("sites:delete", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="delete_sites"> Delete sites </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="relinquish_sites" name="perms[people][sites][relinquish]" <?php if(in_array("sites:relinquish", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="relinquish_sites"> Relinquish sites </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="merge_sites" name="perms[people][sites][merge]" <?php if(in_array("sites:merge", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="merge_sites"> Merge sites </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            &nbsp;
                                        </div>
                                    </div>
                                </div>




                                <div class="row">
                                    <div class="col-md-6">
                                        &nbsp;<br>&nbsp;<br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Contractors</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="view_contractors" name="perms[people][contractors][view]" <?php if(in_array("contractors:view", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="view_contractors"> View contractors </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="edit_contractors" name="perms[people][contractors][edit]" <?php if(in_array("contractors:edit", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="edit_contractors"> Edit contractors </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="add_contractors" name="perms[people][contractors][add]" <?php if(in_array("contractors:add", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="add_contractors"> Add contractors </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="delete_contractors" name="perms[people][contractors][delete]" <?php if(in_array("contractors:delete", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="delete_contractors"> Delete contractors </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>




                                <div class="row">
                                    <div class="col-md-6">
                                        &nbsp;<br>&nbsp;<br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Builders</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="view_builders" name="perms[people][builders][view]" <?php if(in_array("builders:view", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="view_builders"> View builders </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="edit_builders" name="perms[people][builders][edit]" <?php if(in_array("builders:edit", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="edit_builders"> Edit builders </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="add_builders" name="perms[people][builders][add]" <?php if(in_array("builders:add", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="add_builders"> Add builders </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="delete_builders" name="perms[people][builders][delete]" <?php if(in_array("builders:delete", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="delete_builders"> Delete builders </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-6">
                                        &nbsp;<br>&nbsp;<br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Hygienists</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="view_hygenists" name="perms[people][hygenists][view]" <?php if(in_array("hygenists:view", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="view_hygenists"> View hygienists </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="edit_hygenists" name="perms[people][hygenists][edit]" <?php if(in_array("hygenists:edit", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="edit_hygenists"> Edit hygienists </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="add_hygenists" name="perms[people][hygenists][add]" <?php if(in_array("hygenists:add", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="add_hygenists"> Add hygienists </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="delete_hygenists" name="perms[people][hygenists][delete]" <?php if(in_array("hygenists:delete", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="delete_hygenists"> Delete hygienists </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-6">
                                        &nbsp;<br>&nbsp;<br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Service providers</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="view_providers" name="perms[people][providers][view]" <?php if(in_array("providers:view", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="view_providers"> View service providers </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="edit_providers" name="perms[people][providers][edit]" <?php if(in_array("providers:edit", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="edit_providers"> Edit service providers </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="add_providers" name="perms[people][providers][add]" <?php if(in_array("providers:add", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="add_providers"> Add service providers </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="delete_providers" name="perms[people][providers][delete]" <?php if(in_array("providers:delete", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="delete_providers"> Delete service providers </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>

                            <div class="tab-pane" id="equipment">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Controls</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="view_assets" name="perms[equipment][controls][view]" <?php if(in_array("controls:view", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="view_assets"> View controls </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="view_assets" name="perms[equipment][controls][edit]" <?php if(in_array("controls:edit", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="view_assets"> Edit controls </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="order_assets" name="perms[equipment][controls][order]" <?php if(in_array("controls:order", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="order_assets"> Order controls </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>

                            </div>

                            <div class="tab-pane" id="activities">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Site activity</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="log_activity" name="perms[activity][activity][log]" <?php if(in_array("activity:log", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="log_activity"> Log new activity</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Site history</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="view_history" name="perms[activity][history][view]" <?php if(in_array("history:view", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="view_history"> View history</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="edit_history" name="perms[activity][history][edit]" <?php if(in_array("history:edit", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="edit_history"> Edit history</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="add_history" name="perms[activity][history][add]" <?php if(in_array("history:add", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="add_history"> Add history</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="delete_history" name="perms[activity][history][delete]" <?php if(in_array("history:delete", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="delete_history"> Delete history</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-6">
                                        &nbsp;<br>&nbsp;<br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Assessments / SWMS</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="view_swms" name="perms[activity][swms][view]" <?php if(in_array("swms:view", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="view_swms"> View swms </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="edit_swms" name="perms[activity][swms][edit]" <?php if(in_array("swms:edit", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="edit_swms"> Edit swms </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="add_swms" name="perms[activity][swms][add]" <?php if(in_array("swms:add", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="add_swms"> Add swms </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="delete_swms" name="perms[activity][swms][delete]" <?php if(in_array("swms:delete", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="delete_swms"> Delete swms </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>




                                <div class="row">
                                    <div class="col-md-6">
                                        &nbsp;<br>&nbsp;<br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Tasks</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="view_tasks" name="perms[activity][tasks][view]" <?php if(in_array("tasks:view", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="view_tasks"> View tasks </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="edit_tasks" name="perms[activity][tasks][edit]" <?php if(in_array("tasks:edit", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="edit_tasks"> Edit tasks </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="add_tasks" name="perms[activity][tasks][add]" <?php if(in_array("tasks:add", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="add_tasks"> Add tasks </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="delete_tasks" name="perms[activity][tasks][delete]" <?php if(in_array("tasks:delete", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="delete_tasks"> Delete tasks </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="all_tasks" name="perms[activity][tasks][view-all]" <?php if(in_array("tasks:view-all", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="all_tasks"> View all organisations tasks </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-6">
                                        &nbsp;<br>&nbsp;<br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Training</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="view_training" name="perms[activity][training][view]" <?php if(in_array("training:view", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="view_training"> View training </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="edit_training" name="perms[activity][training][order]" <?php if(in_array("training:order", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="edit_training"> Order training </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="utilities">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Import</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="view_import" name="perms[utilities][import][view]" <?php if(in_array("import:view", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="view_import"> Run import </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-md-6">
                                        &nbsp;<br>&nbsp;<br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Export</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="view_export" name="perms[utilities][export][view]" <?php if(in_array("export:view", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="view_export"> Run export </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        &nbsp;
                                    </div>
                                </div>




                                <div class="row">
                                    <div class="col-md-6">
                                        &nbsp;<br>&nbsp;<br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Account</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="view_account" name="perms[utilities][account][view]" <?php if(in_array("account:view", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="view_account"> View account </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            &nbsp;
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-md-6">
                                        &nbsp;<br>&nbsp;<br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Reports</h4>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary">
                                                <input id="view_reports" name="perms[utilities][reports][view]" <?php if(in_array("reports:view", $sgSettings)){ echo " checked"; } ?> type="checkbox">
                                                <label for="view_reports"> View reports </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            &nbsp;
                                        </div>
                                    </div>
                                </div>

                                
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

    </form>
    <!-- /.row -->


    

        <!-- JS DEPENDENCIES -->
        @include('components.footer') 
        <!-- END JS DEPENDENCIES -->
                    
        <!-- Page specific Javascript -->
        <script src="{{ asset('js/jasny-bootstrap.js') }}"></script>

    @endsection