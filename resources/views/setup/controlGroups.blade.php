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
                <h4 class="page-title">Setup control type groups</h4>
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
                            <h3 class="box-title">Control type groups</h3>
                        </div>
                    </div>
                    <form method="post" action="/setup/controlGroups">
                        @csrf
                        <input type="hidden" name="group" @if(isset($group->id)) value="{{ $group->id }}" @else value="0" @endif>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="col-md-12">@if(isset($group->id)) Edit control type group @else Add control type group @endif</label>
                                    <div class="col-md-12">
                                        <input type="text" id="name" name="name" placeholder="Control type group name" @if(isset($group->id)) value="{{ $group->name }}" @endif class="form-control form-control-line"> 
                                    </div>    
                                </div>
                            </div>
                            <div class="col-md-4 pull-right">
                                <button class="pull-right btn btn-info waves-effect waves-light">
                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    @if(isset($group->id)) Edit group @else Add group @endif
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
                                    <th>Control type group name</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($groups as $g)
                                <tr style="cursor: pointer;" onClick="window.location.href='/setup/controlGroups/{{ $g->id }}'">    
                                    <td>{{ $g->name }}</td>
                                    <td><a href="/setup/controlGroups/archive/{{ $g->id }}">Archive</a></td>
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

        });
                
        </script>

    @endsection