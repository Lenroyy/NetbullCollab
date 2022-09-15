@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Marketplace</h4>
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
                            <h3 class="box-title">Marketplace</h3>
                        </div>
                        <div class="col-md-6">
                            &nbsp;
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
                            <a href="#closed" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="fa fa-cab"></i></span> <span class="hidden-xs">All </span> </a>
                            
                        </li>
                        <li class="tab">
                            <a href="#open" data-toggle="tab"> <span class="visible-xs"><i class="fa fa-shopping-cart"></i></span> <span class="hidden-xs">Purchased </span> </a>
                        </li>                        
                    </ul>


                    <div class="tab-content">
                        <div class="tab-pane" id="open">
                            <div class="table-responsive">
                                <table id="openTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Price</th>
                                            <th>Link</th>
                                            <th>Provider</th>
                                            <th>Status</th>
                                            <th>Type</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subscribedTraining as $t)
                                            <tr style="cursor: pointer;">    
                                                <td onClick="window.open('http://{{ $t->TrainingHygenist->link }}')">{{ $t->Training->name }}</td>
                                                <td onClick="window.open('http://{{ $t->TrainingHygenist->link }}')">${{ $t->price }}</td>
                                                <td onClick="window.open('http://{{ $t->TrainingHygenist->link }}')">{{ $t->TrainingHygenist->link }}</td>
                                                <td onClick="window.open('http://{{ $t->TrainingHygenist->link }}')">{{ $t->Hygenist->name }}</td>
                                                <td onClick="window.open('http://{{ $t->TrainingHygenist->link }}')">{{ $t->status }}</td>
                                                <td onClick="window.open('http://{{ $t->TrainingHygenist->link }}')">
                                                    @if(is_object($t->Training->Training_Types))
                                                        {{ $t->Training->Training_Types->name }}
                                                    @else
                                                        Not set
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="/training/join/{{ $t->id }}">Details</a>
                                                    @if($t->status == "pending")
                                                         | <a href="/cancelService/{{ $t->id }}">Cancel order</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    
                        <div class="tab-pane active" id="closed">
                            <div class="table-responsive">
                                <table id="openTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Price</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($training as $t)
                                            <tr style="cursor: pointer;" onClick="window.location.href='/buyTraining/{{ $t['training']->id }}'">    
                                                <td>{{ $t['training']->name }}</td>
                                                <td>${{ $t['training']->price }}</td>
                                                <td>
                                                    @if(is_object($t['training']->Training_Types))
                                                        {{ $t['training']->Training_Types->name }}
                                                    @else
                                                        Not set
                                                    @endif
                                                </td>
                                                <td>{{ substr($t['training']->description, 0, 30) }}</td>
                                            </tr>
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