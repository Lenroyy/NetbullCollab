@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/plugins/bower_components/custom-select/custom-select.css') }}" rel="stylesheet" type="text/css" />        

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Setup exposures</h4>
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
                            <h3 class="box-title">Exposures</h3>
                        </div>
                    </div>
                    <form method="post" action="/setup/exposures">
                        @csrf
                        <input type="hidden" name="exposure" @if(isset($exposure->id)) value="{{ $exposure->id }}" @else value="0" @endif>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="col-md-12">@if(isset($exposure->id)) Edit exposure @else Add exposure @endif</label>
                                    <div class="col-md-12">
                                        <input type="text" id="name" name="name" required placeholder="Exposure name" @if(isset($exposure->id)) value="{{ $exposure->name }}" @endif class="form-control form-control-line"> 
                                    </div>    
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="col-md-12">Reading type</label>
                                    <div class="col-md-12">
                                        <select class="form-control form-control-line" name="readingType">
                                            @foreach($readingTypes as $type)
                                                <option value="{{ $type->id }}" @if(isset($exposure->id)) @if($type->id == $exposure->reading_type_id) selected @endif @endif>{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>    
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="col-md-12">Time period</label>
                                    <div class="col-md-12">
                                        <select class="form-control form-control-line" name="timePeriod">
                                            <!-- <option value="hour" @if(isset($exposure->id)) @if($exposure->time_period == "hour") selected @endif @endif>Per hour</option> -->
                                            <option value="day" @if(isset($exposure->id)) @if($exposure->time_period == "day") selected @endif @endif>Per day</option>
                                            <option value="week" @if(isset($exposure->id)) @if($exposure->time_period == "week") selected @endif @endif>Per week</option>
                                        </select>
                                    </div>    
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="col-md-12">Level</label>
                                    <div class="col-md-12">
                                        <input type="text" name="level" @if(isset($exposure->id)) value="{{ $exposure->level }}" @endif class="form-control form-control-line">
                                    </div>    
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <button class="pull-right btn btn-info waves-effect waves-light">
                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    @if(isset($exposure->id)) Edit exposure @else Add exposure @endif
                                </button>
                            </div>
                        </div>
                    </form>
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
                                    <th>Reading type</th>
                                    <th>Time period</th>
                                    <th>Level</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($exposures as $e)
                                <tr style="cursor: pointer;">    
                                    <td onClick="window.location.href='/setup/exposures/{{ $e->id }}'">{{ $e->name }}</td>
                                    <td onClick="window.location.href='/setup/exposures/{{ $e->id }}'">{{ $e->ReadingType->name }}</td>
                                    <td onClick="window.location.href='/setup/exposures/{{ $e->id }}'">{{ $e->time_period }}</td>
                                    <td onClick="window.location.href='/setup/exposures/{{ $e->id }}'">{{ $e->level }}</td>
                                    <td><a href="/setup/exposures/archive/{{ $e->id }}">Archive</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        &nbsp;<br>&nbsp;<br>
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
        <script src="{{ asset('assets/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>

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

            $(".select2").select2();

        });
                
        </script>

    @endsection