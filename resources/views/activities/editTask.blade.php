@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->     
        <link href="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Edit task</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div> 

        <!-- .row -->
        <form class="form-horizontal form-material" action="/saveTask/{{ $task->id }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <span class="pull-right">
                        <input type="submit" name="submit" value="Save" class="btn btn-primary"> &nbsp; 
                        <a class="btn btn-warning" href="/tasks">Cancel</a>
                    </span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    &nbsp;
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <ul class="nav nav-tabs tabs customtab">
                            <li class="active tab">
                                <a href="#details" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="fa-tasks"></i></span> <span class="hidden-xs">Details</span> </a>
                            </li>
                            <li class="tab">
                                <a href="#attachments" data-toggle="tab"> <span class="visible-xs"><i class="ti-folder"></i></span> <span class="hidden-xs">Attachments @if(count($files) > 0)<span class="badge">{{ count($files) }}</span>@endif</span> </a>
                            </li>
                            <li class="tab">
                                <a href="#logs" data-toggle="tab"> <span class="visible-xs"><i class="ti-calendar"></i></span> <span class="hidden-xs">Logs</span> </a>
                            </li>             
                        </ul>


                        <div class="tab-content">
                            <div class="tab-pane active" id="details">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-md-12">Subject</label>
                                            <div class="col-md-12">
                                                <input type="text" id="subject" name="subject" @if($task->id > 0) value="{{ $task->subject }}" @endif placeholder="Task subject" class="form-control form-control-line"> 
                                                <br><br>
                                            </div>    
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-md-12">Description</label>
                                            <div class="col-md-12">
                                                <textarea class="form-control form-control-line" cols="30" rows="10" name="description">{{ $task->description }}</textarea>
                                            </div>    
                                        </div>
                                    </div>    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-md-12">Notes</label>
                                            <div class="col-md-12">
                                                <textarea class="form-control form-control-line" cols="30" rows="10" name="notes">{{ $task->notes }}</textarea>
                                            </div>    
                                        </div>
                                    </div>    
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-md-12">Assigned to</label>
                                            <select class="form-control form-control-line" name="assignedTo">
                                                <option value="0">Unassigned</option>
                                                @foreach($people as $person)
                                                    <option value="{{ $person->id }}" @if($task->id > 0) @if($person->id == $task->assigned_id) selected @endif @endif>{{ $person->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-md-12">Status</label>
                                            <select class="form-control form-control-line" name="status">
                                                <option value="pending" @if($task->id > 0) @if($task->status == "pending") selected @endif @endif>Pending</option>
                                                <option value="progress" @if($task->id > 0) @if($task->status == "progress") selected @endif @endif>In Progress</option>
                                                <option value="complete" @if($task->id > 0) @if($task->status == "complete") selected @endif @endif>Complete</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-md-12">Priority</label>
                                            <select class="form-control form-control-line" name="priority">
                                                <option value="low" @if($task->id > 0) @if($task->priority == "low") selected @endif @endif>Low</option>
                                                <option value="medium" @if($task->id > 0) @if($task->priority == "medium") selected @endif @endif>Medium</option>
                                                <option value="high" @if($task->id > 0) @if($task->priority == "high") selected @endif @endif>High</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-md-12">Progress %</label>
                                            <input type="numbe" step="1" id="progress" name="progress" placeholder="% of completion" class="form-control form-control-line" @if($task->id > 0) value="{{ $task->progress }}" @endif> 
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-md-12">Start date</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="datepicker-start" placeholder="dd-mm-yyyy" name="start_date" @if(!empty($task->start_date)) value="{{ $task->start_date->format('d-m-Y') }}" @endif><span class="input-group-addon"><i class="icon-calender"></i></span> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-md-12">Due date</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="datepicker-due" placeholder="dd-mm-yyyy" name="due_date"  @if(!empty($task->due_date)) value="{{ $task->due_date->format('d-m-Y') }}" @endif><span class="input-group-addon"><i class="icon-calender"></i></span> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-md-12">Completed date</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="datepicker-completed" placeholder="dd-mm-yyyy" name="completed_date" @if(!empty($task->completed_date)) value="{{ $task->completed_date->format('d-m-Y') }}" @endif><span class="input-group-addon"><i class="icon-calender"></i></span> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-md-12">Site</label>
                                            <select class="form-control form-control-line" name="site">
                                                <option value="0">None</option>
                                                @foreach($sites as $site)
                                                    <option value="{{ $site->id }}" @if($task->id > 0) @if($site->id == $task->site_id) selected @endif @endif>{{ $site->name }}</option>
                                                @endforeach
                                            </select>
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

        <!-- JS DEPENDENCIES -->
        @include('components.footer') 
        <!-- END JS DEPENDENCIES -->
                    
        <!-- Page specific Javascript -->       
        <script src="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
        <script>
            jQuery('#datepicker-start').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });

            jQuery('#datepicker-due').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });

            jQuery('#datepicker-completed').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });
        </script>

    @endsection