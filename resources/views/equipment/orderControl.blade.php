@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Request control</h4>
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
                    <ul class="nav nav-tabs tabs customtab">
                        <li class="active tab">
                            <a href="#profile" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="ti-notepad"></i></span> <span class="hidden-xs">Requests</span> </a>
                        </li>
                        <li class="tab">
                            <a href="#history" data-toggle="tab"> <span class="visible-xs"><i class="ti-folder"></i></span> <span class="hidden-xs">Past requests </span> </a>
                        </li>                        
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="profile">
                            <form class="form-horizontal form-material" action="/saveOrder/equipment" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="orderID" value="{{ $order->id }}">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="white-box">
                                            <h3 class="box-title">Request control</h3>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="col-md-12">Control type</label>
                                                        <div class="col-md-12">
                                                            <select class="form-control form-control-line" name="controlType">
                                                                <option>Please select</option>
                                                                @foreach($controlTypes as $type)
                                                                    <option value="{{ $type->id }}" @if($order->id > 0)@if($type->id == $order->control_type) selected @endif @endif>{{ $type->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>    
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="col-md-12">Quantity</label>
                                                        <div class="col-md-12">
                                                            <input type="text" id="quantity" name="quantity" placeholder="Quantity required of this control type" required value="@if($order->id > 0){{ $order->quantity }}@else 1 @endif" class="form-control form-control-line"> 
                                                        </div>    
                                                    </div>
                                                </div>
                                            </div>
                                            @if($standardDisplay['profile']->super_user == 1)
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <br>
                                                        <div class="form-group">
                                                            <label class="col-md-12">simPRO ID</label>
                                                            <div class="col-md-12">
                                                                <input type="text" id="simproID" name="simproID" placeholder="simPRO ID (if sent)" value="@if($order->id > 0){{ $order->simpro_id }}@endif" class="form-control form-control-line"> 
                                                            </div>    
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <br>
                                                    <div class="form-group">
                                                        <label class="col-md-12">Site</label>
                                                        <div class="col-md-12">
                                                            <select class="form-control form-control-line" name="site">
                                                                <option>Please select</option>
                                                                @foreach($sites as $site)
                                                                    <option value="{{ $site->id }}" @if($order->id > 0)@if($site->id == $order->site_id) selected @endif @endif>{{ $site->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>    
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <br>
                                                    <div class="form-group">
                                                        <label class="col-md-12">Date required</label>
                                                        <div class="col-md-12">
                                                            <input type="text" id="date" name="date" placeholder="dd-mm-yyyy" value="@if($order->id > 0 && !empty($order->date_due)){{ $order->date_due->format('d-m-Y') }}@endif" class="form-control form-control-line"> 
                                                        </div>    
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <br>
                                                    <div class="form-group">
                                                        <label class="col-md-12">Order number</label>
                                                        <div class="col-md-12">
                                                            <input type="text" id="orderNo" name="orderNo" placeholder="Your company order number" value="@if($order->id > 0){{ $order->order_no }}@endif" class="form-control form-control-line"> 
                                                        </div>    
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <br>
                                                    <div class="form-group">
                                                        <label class="col-md-12">Notes</label>
                                                        <div class="col-md-12">
                                                            <textarea name="notes" class="form-control form-control-line">@if($order->id > 0){{ $order->notes }}@endif</textarea>
                                                        </div>    
                                                    </div>
                                                </div>
                                            </div>
                                            @if($order->id == 0)
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <br>
                                                        <span class="pull-right">Prior to items being dispatched, a PO number will be required.</span>
                                                        <br>
                                                        <br>
                                                        <span class="pull-right"><input type="submit" name="submit" value="Request" class="btn btn-primary"></span>
                                                    </div>
                                                </div>
                                            @else
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <br>
                                                    To make changes to this order, please contact your Trieste account manager directly.
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>


                        <div class="tab-pane" id="history">
                            <div class="table-responsive">
                                <table class="table table-hover" id="ordersTable">
                                    <thead>
                                        <tr>
                                            <th>
                                                ID
                                            </th>
                                            <th>
                                                Order Date
                                            </th>
                                            <th>
                                                Control Type
                                            </th>
                                            <th>
                                                Quantity
                                            </th>
                                            <th>
                                                Ordered By
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orders as $o)
                                            <tr style="cursor: pointer" onClick="window.location.href='/orderControl/{{ $o->id }}'">
                                                <td>
                                                    <p class="text-muted">{{ $o->id }}</p>
                                                </td>
                                                <td>
                                                    <p class="text-muted">@if(!empty($o->created_at)){{ $o->created_at->format('d-m-Y H:i') }}@endif</p>
                                                </td>
                                                <td>
                                                    @if(is_object($o->Controls_Type))
                                                        {{ $o->Controls_Type->name }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <p class="text-muted">
                                                        {{ $o->quantity }}
                                                    </p>
                                                </td>
                                                <td>
                                                    <p class="text-muted">
                                                        {{ $o->User->name }}
                                                    </p>
                                                </td>
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
        <script src="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
        <script>
            $(document).ready(function() {
                jQuery('#date').datepicker({
                    autoclose: true,
                    todayHighlight: true,
                    format: 'dd-mm-yyyy'
                });

                $('#ordersTable').DataTable({
                    "displayLength": 100,
                });
            });
        </script>

    @endsection