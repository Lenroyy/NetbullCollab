@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Integration</h4>
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
                    <h3 class="box-title">Communicating with integrated system</h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Task</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="taskTable">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->

        <!-- JS DEPENDENCIES -->
        @include('components.footer') 
        <!-- END JS DEPENDENCIES -->
                    
        <!-- Page specific Javascript -->
        <script>
            $(document).ready(function() {
                table = document.getElementById("taskTable")
                @foreach($tasks as $task)
                    @if($task == "fetchMonitors")
                        startHTML = "<tr><td>Retrieving sensors from thingsboard</td><td id=\"waiting\"><img src=\"/assets/images/stationary/spinnerBlob.gif\" width=\"80px\"></td>"
                        table.innerHTML = startHTML
                        message = ""
                        jQuery.getJSON('/retrieveMonitors', function (details) {
                            $.each(details, function (d, detail) {
                                if(d == "devices")
                                {
                                    message += "Retrieved " + detail + " devices.<br>"
                                }

                                if(d == "created")
                                {
                                    message += "Created " + detail + " new devices.<br>"
                                }

                                if(d == "updated")
                                {
                                    message += "Updated " + detail + " devices.<br>"
                                    updateStatus(message)
                                }
                            });
                        });
                    @endif
                @endforeach
            });

            function sleep (time) {
                return new Promise((resolve) => setTimeout(resolve, time));
            }

            function updateStatus(message)
            {
                sleep(2000).then(() => {
                    table = document.getElementById("taskTable")
                    innerHTML = "<tr><td>Completed</td><td id=\"waiting\">" + message + "</td>"
                    table.innerHTML = innerHTML
                });
            }
        </script>

    @endsection