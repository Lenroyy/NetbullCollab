@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        <link href="{{ asset('assets/plugins/bower_components/custom-select/custom-select.css') }}" rel="stylesheet" type="text/css" />
        <script src="{{ asset('assets/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
        

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Setup entry requirements</h4>
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
                            <h3 class="box-title">Entry requirements</h3>
                        </div>
                    </div>
                    <form action="/setup/permits" method="POST">
                        @csrf
                        <input type="hidden" name="permit" value="0">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-md-12">Add requirement</label>
                                    <div class="col-md-12">
                                        <input type="text" id="name" name="name" placeholder="Permit name" class="form-control form-control-line"> 
                                    </div>    
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Requirement type</label>
                                    <select class="form-control form-control-line" name="type">
                                        @foreach($types as $t)
                                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Required training</label>
                                    <select class="select2 m-b-10 select2-multiple" multiple="multiple" data-placeholder="Choose" name="trainings[]">
                                        @foreach($trainings as $t)
                                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button class="pull-right btn btn-info waves-effect waves-light" href="#">
                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    Add requirement
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
                                    <th>Type</th>
                                    <th>Members</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permits as $permit)
                                    <tr style="cursor: pointer;">    
                                        <td data-toggle="modal" data-target="#permitModal{{ $permit['permit']->id }}" data-whatever="@mdo">
                                            {{ $permit['permit']->name }}
                                        </td>
                                        <td data-toggle="modal" data-target="#permitModal{{ $permit['permit']->id }}" data-whatever="@mdo">
                                            {{ $permit['permit']->Permits_Type->name }}
                                        </td>
                                        <td data-toggle="modal" data-target="#permitModal{{ $permit['permit']->id }}" data-whatever="@mdo">
                                            {{ $permit['permit']->qty }}
                                        </td>
                                        <td>
                                            <a href="/setup/permits/archive/{{ $permit['permit']->id }}">Archive</a>
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
        @foreach($permits as $permit)
            <form action="/setup/permits" method="post">
                @csrf
                <input type="hidden" name="permit" value="{{ $permit['permit']->id }}">    
                <div class="modal fade" id="permitModal{{ $permit['permit']->id }}" tabindex="-1" role="dialog" aria-labelledby="permitModal{{ $permit['permit']->id }}" style="display: none;">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content modal-lg">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                <h4 class="modal-title" id="exampleModalLabel1">Update permit</h4> </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="col-md-12">Permit name</label>
                                                <div class="col-md-12">
                                                <input type="text" id="name" name="name" placeholder="Permit name" value="{{ $permit['permit']->name }}" class="form-control form-control-line"> 
                                                </div>    
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Permit type</label>
                                                <select class="form-control form-control-line" name="type">
                                                    @foreach($types as $t)
                                                        <option value="{{ $t->id }}" @if($t->id == $permit['permit']->permits_types_id) selected @endif>{{ $t->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Required training</label>
                                                <select class="select2 m-b-10 select2-multiple" multiple="multiple" data-placeholder="Choose" name="trainings[]">
                                                    @foreach($trainings as $t)
                                                        <option value="{{ $t->id }}" @if(in_array($t->id, $permit['trainings'])) selected @endif>{{ $t->name }}</option>
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

            $(".select2").select2();

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