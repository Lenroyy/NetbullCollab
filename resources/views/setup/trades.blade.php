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
                <h4 class="page-title">Setup trades</h4>
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
                            <h3 class="box-title">Trades</h3>
                        </div>
                    </div>
                    <form method="post" action="/setup/trades">
                        @csrf
                        <input type="hidden" name="trade" @if(isset($trade->id)) value="{{ $trade->id }}" @else value="0" @endif>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-md-12">@if(isset($trade->id)) Edit trade @else Add trade @endif</label>
                                    <div class="col-md-12">
                                        <input type="text" id="name" name="name" placeholder="Trade name" @if(isset($trade->id)) value="{{ $trade->name }}" @endif class="form-control form-control-line"> 
                                    </div>    
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-md-12">Hazards</label>
                                    <div class="col-md-12">
                                        <select class="select2 m-b-10 select2-multiple" multiple="multiple" data-placeholder="Choose" name="hazards[]">
                                            @foreach($allHazards as $hazard)
                                                <option value="{{ $hazard->id }}" @if(in_array($hazard->id, $hazards)) selected @endif>{{ $hazard->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>    
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="col-md-12">Estimated daily hazards</label>
                                    <div class="col-md-12">
                                        <input type="number" step="1" class="form-control form-control-line" placeholder="Number of hazards estimated per day" name="est_hazards" @if(isset($trade->id)) value="{{ $trade->est_hazards }}" @endif>
                                    </div>    
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button class="pull-right btn btn-info waves-effect waves-light">
                                    <span class="btn-label">
                                        <i class="fa fa-plus"></i>
                                    </span>
                                    @if(isset($trade->id)) Edit trade @else Add trade @endif
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
                                    <th>Members</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($trades as $t)
                                <tr style="cursor: pointer;" onClick="window.location.href='/setup/trades/{{ $t->id }}'">    
                                    <td>{{ $t->name }}</td>
                                    <td>{{ $t->qty }}</td>
                                    <td><a href="/setup/trades/archive/{{ $t->id }}">Archive</a></td>
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