@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Assets</h4>
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
                        <div class="col-md-12">
                            @foreach($controls as $controlType)
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="box-title">{{ $controlType['type']->name }}</h3>
                                        <hr>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>
                                                    Map
                                                </th>
                                                <th>
                                                    Zone
                                                </th>
                                                @foreach($controlType['fields'] as $field)
                                                <th>
                                                    {{ $field }}
                                                </th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($controlType['controls'] as $control)
                                                <tr @if(in_array("controls:edit", $standardDisplay['permissions']) OR $standardDisplay['profile']->super_user == 1) style="cursor: pointer" onClick="window.location.href='/editControl/{{ $control['control']->id }}'" @endif>
                                                    <td>{{ $control['map'] }}</td>
                                                    <td>{{ $control['zone'] }}</td>
                                                    @foreach($control['fieldValues'] as $value)
                                                        <td>
                                                            <p class="text-muted">
                                                                {{ $value['value'] }}
                                                            </p>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <hr>
                                    <!--^ <a href="#" onClick="populateControls()"  data-toggle="modal" data-target="#transferControl" data-whatever="@mdo">Transfer controls</a>-->
                                </div>
                            @endforeach
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