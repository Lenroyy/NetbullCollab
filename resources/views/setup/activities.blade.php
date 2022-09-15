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
                <h4 class="page-title">Setup activities</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div> 
        
        <!-- .row -->
        <form action="/setup/activities" method="post">
            @csrf
            <input type="hidden" name="activity" value="0">
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">Activites</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-md-12">Add activity</label>
                                    <div class="col-md-12">
                                        <input type="text" id="name" name="name" placeholder="Activity name" class="form-control form-control-line"> 
                                    </div>    
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-md-12">Relevant trades</label>
                                    <div class="col-md-12">
                                        <select class="select2 m-b-10 select2-multiple" multiple="multiple" data-placeholder="Choose" name="trades[]">
                                            @foreach($trades as $trade)
                                                <option value="{{ $trade->id }}">{{ $trade->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>    
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-md-12">Permits required</label>
                                    <div class="col-md-12">
                                        <select class="select2 m-b-10 select2-multiple" multiple="multiple" data-placeholder="Choose" name="permits[]">
                                            @foreach($permits as $permit)
                                                <option value="{{ $permit->id }}">{{ $permit->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>    
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button class="pull-right btn btn-info waves-effect waves-light" href="#">
                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    Add activity
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activities as $a)
                                    <tr style="cursor: pointer;">    
                                        <td data-toggle="modal" data-target="#activityModal{{ $a['activity']->id }}" data-whatever="@mdo">
                                            {{ $a['activity']->name }}
                                        </td>
                                        <td>
                                            <a href="/setup/activities/archive/{{ $a['activity']->id }}">Archive</a>
                                        </td>
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

    <!--Existing Permit Modal-->
        @foreach($activities as $act)
            <form action="/setup/activities" method="post">
                @csrf
                <input type="hidden" name="activity" value="{{ $act['activity']->id }}">    
                <div class="modal fade" id="activityModal{{ $act['activity']->id }}" tabindex="-1" role="dialog" aria-labelledby="activityModal{{ $act['activity']->id }}" style="display: none;">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content modal-lg">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                <h4 class="modal-title" id="exampleModalLabel1">Update activity</h4> </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="col-md-12">Update activity</label>
                                                <div class="col-md-12">
                                                    <input type="text" id="name" value="{{ $act['activity']->name }}" name="name" placeholder="Activity name" class="form-control form-control-line"> 
                                                </div>    
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Trades</label>
                                                <select class="select2 m-b-10 select2-multiple" multiple="multiple" data-placeholder="Choose" name="trades[]">
                                                    @foreach($trades as $t)
                                                        <option value="{{ $t->id }}" @if(in_array($t->id, $act['trades'])) selected @endif>{{ $t->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Permits</label>
                                                <select class="select2 m-b-10 select2-multiple" multiple="multiple" data-placeholder="Choose" name="permits[]">
                                                    @foreach($permits as $p)
                                                        <option value="{{ $p->id }}" @if(in_array($p->id, $act['permits'])) selected @endif>{{ $p->name }}</option>
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
    <!--/existing Permit Modal-->

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