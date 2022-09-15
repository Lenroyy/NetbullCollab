@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Rules</h4>
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
                            <h3 class="box-title">Rules</h3>
                        </div>
                        <div class="col-md-6">
                            <a class="pull-right btn btn-info waves-effect waves-light" href="/setup/rules/0">
                                <span class="btn-label">
                                    <i class="fa fa-plus"></i>
                                </span>
                                New rule
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
                    <div class="table-responsive">
                        <table id="accountTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Reading Type</th>
                                    <th>Order</th>
                                    <th>Outcome</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rules as $rule)
                                    <tr style="cursor: pointer;">    
                                        <td onClick="window.location.href='/setup/rules/{{ $rule->id }}'">{{ $rule->name }}</td>
                                        <td onClick="window.location.href='/setup/rules/{{ $rule->id }}'">@if(!empty($rule->Reading_Type->name)){{ $rule->Reading_Type->name }}@else - @endif</td>
                                        <td onClick="window.location.href='/setup/rules/{{ $rule->id }}'">{{ $rule->order }}</td>
                                        <td onClick="window.location.href='/setup/rules/{{ $rule->id }}'">{{ $rule->outcome }}</td>
                                        <td><a href="/setup/rules/archive/{{ $rule->id }}">Archive</a></td>
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