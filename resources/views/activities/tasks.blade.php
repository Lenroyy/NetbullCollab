@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Tasks</h4>
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
                            <h3 class="box-title">Tasks</h3>
                        </div>
                        <div class="col-md-6">
                            <a class="pull-right btn btn-info waves-effect waves-light" href="/editTask/0">
                                <span class="btn-label">
                                    <i class="fa fa-plus"></i>
                                </span>
                                New task
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->

        <!-- .row -->
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <ul class="nav nav-tabs tabs customtab">
                        <li class="active tab">
                            <a href="#open" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="fa-folder-open"></i></span> <span class="hidden-xs">Open</span> </a>
                        </li>
                        <li class="tab">
                            <a href="#closed" data-toggle="tab"> <span class="visible-xs"><i class="fa-folder"></i></span> <span class="hidden-xs">Closed </span> </a>
                        </li>                        
                    </ul>


                    <div class="tab-content">
                        <div class="tab-pane active" id="open">
                            <div class="table-responsive">
                                <table id="openTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Subject</th>
                                            <th>Assigned to</th>
                                            <th>Start date</th>
                                            <th>Due date</th>
                                            <th>Status</th>
                                            <th>Progress</th>
                                            <th>Priority</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tasks as $task)
                                            @if($task->completed_date == NULL)
                                                <tr style="cursor: pointer;">    
                                                    <td onClick="window.location.href='/editTask/{{ $task->id }}'">{{ $task->id }}</td>
                                                    <td onClick="window.location.href='/editTask/{{ $task->id }}'">{{ $task->subject }}</td>
                                                    <td onClick="window.location.href='/editTask/{{ $task->id }}'">@if(is_object($task->Assigned)){{ $task->Assigned->name }}@else - @endif</td>
                                                    <td onClick="window.location.href='/editTask/{{ $task->id }}'">@if(!empty($task->start_date)){{ $task->start_date->format('d-m-Y') }}@else - @endif</td>
                                                    <td onClick="window.location.href='/editTask/{{ $task->id }}'">@if(!empty($task->due_date)){{ $task->due_date->format('d-m-Y') }}@else - @endif</td>
                                                    <td onClick="window.location.href='/editTask/{{ $task->id }}'">{{ $task->status }}</td>
                                                    <td onClick="window.location.href='/editTask/{{ $task->id }}'">{{ $task->progress }} %</td>
                                                    <td onClick="window.location.href='/editTask/{{ $task->id }}'">{{ $task->priority }}</td>
                                                    <td>
                                                        <div class="btn-group m-r-10">
                                                            <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info dropdown-toggle waves-effect waves-light" type="button">Options <span class="caret"></span></button>
                                                            <ul role="menu" class="dropdown-menu">
                                                                <li><a href="/editTask/{{ $task->id }}">Edit</a></li>
                                                                <li><a href="/completeTask/{{ $task->id }}">Mark as complete</a></li>
                                                                <li><a href="/deleteTask/{{ $task->id }}">Delete</a></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    
                        <div class="tab-pane" id="closed">
                            <div class="table-responsive">
                                <table id="closedTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Subject</th>
                                            <th>Assigned to</th>
                                            <th>Start date</th>
                                            <th>Due date</th>
                                            <th>Progress</th>
                                            <th>Priority</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tasks as $task)
                                            @if($task->completed_date != NULL)
                                                <tr style="cursor: pointer;">    
                                                    <td onClick="window.location.href='/editTask/{{ $task->id }}'">{{ $task->id }}</td>
                                                    <td onClick="window.location.href='/editTask/{{ $task->id }}'">{{ $task->subject }}</td>
                                                    <td onClick="window.location.href='/editTask/{{ $task->id }}'">@if(is_object($task->Assigned)){{ $task->Assigned->name }}@else - @endif</td>
                                                    <td onClick="window.location.href='/editTask/{{ $task->id }}'">@if(!empty($task->start_date)){{ $task->start_date->format('d-m-Y') }}@else - @endif</td>
                                                    <td onClick="window.location.href='/editTask/{{ $task->id }}'">@if(!empty($task->due_date)){{ $task->due_date->format('d-m-Y') }}@else - @endif</td>
                                                    <td onClick="window.location.href='/editTask/{{ $task->id }}'">{{ $task->progress }} %</td>
                                                    <td onClick="window.location.href='/editTask/{{ $task->id }}'">{{ $task->priority }}</td>
                                                    <td>
                                                        <div class="btn-group m-r-10">
                                                            <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info dropdown-toggle waves-effect waves-light" type="button">Options <span class="caret"></span></button>
                                                            <ul role="menu" class="dropdown-menu">
                                                                <li><a href="/editTask/{{ $task->id }}">Edit</a></li>
                                                                <li><a href="/deleteTask/{{ $task->id }}">Delete</a></li>
                                                            </ul>
                                                        </div>
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
        </div>
        <!-- /.row -->

        <!-- JS DEPENDENCIES -->
        @include('components.footer') 
        <!-- END JS DEPENDENCIES -->
                    
        <!-- Page specific Javascript -->
        <script src="{{ asset('assets/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/bower_components/toast-master/js/jquery.toast.js') }}"></script>

        <script>
        $(document).ready(function() {
            $('#openTable').DataTable({
                "displayLength": 100,
            });
            $('#closedTable').DataTable({
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