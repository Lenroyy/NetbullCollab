@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Setup service types</h4>
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
                            <h3 class="box-title">Service types</h3>
                        </div>
                    </div>
                    <form method="post" action="/setup/trainingTypes">
                        @csrf
                        <input type="hidden" name="type" @if(isset($type->id)) value="{{ $type->id }}" @else value="0" @endif>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="col-md-12">@if(isset($type->id)) Edit service type @else Add service type @endif</label>
                                    <div class="col-md-12">
                                        <input type="text" id="name" name="name" placeholder="Service type name" @if(isset($type->id)) value="{{ $type->name }}" @endif class="form-control form-control-line"> 
                                    </div>    
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="pull-right btn btn-info waves-effect waves-light">
                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    @if(isset($type->id)) Edit service type @else Add service type @endif
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
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($types as $t)
                                <tr style="cursor: pointer;" onClick="window.location.href='/setup/trainingTypes/{{ $t->id }}'">    
                                    <td>{{ $t->name }}</td>
                                    <td><a href="/setup/trainingTypes/archive/{{ $t->id }}">Archive</a></td>
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