@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Account for {{ $organisation->name }}</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>

        <div class="row">
            <div class="col-md-12">
                <span class="pull-right">
                    <input type="submit" class="btn btn-primary" value="update">
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                &nbsp;
            </div>
        </div>

        <!-- .row -->
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table id="openTable" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Item</th>
                                            <th>Price</th>
                                            <th>Qty</th>
                                            <th>Total</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>    
                                            <td>License</td>
                                            <td>Site license</td>
                                            <td>${{ $licenses->site_cost }}</td>
                                            <td>
                                                {{ $licenses->no_sites }}
                                            </td>
                                            <td>
                                                $<?php echo $licenses->site_cost * $licenses->no_sites; ?>
                                            </td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>    
                                            <td>License</td>
                                            <td>User license</td>
                                            <td>${{ $licenses->user_cost }}</td>
                                            <td>
                                                {{ $licenses->no_users }}
                                            </td>
                                            <td>
                                                $<?php echo $licenses->user_cost * $licenses->no_users; ?>
                                            </td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        @foreach($controls as $control)
                                        <tr>    
                                            <td>Controls</td>
                                            <td>{{ $control['type']->name }}</td>
                                            <td>${{ $control['type']->billing_amount }} {{ $control['type']->billing_frequency }}</td>
                                            <td>{{ $control['qty'] }}</td>
                                            <td>
                                                $<?php 
                                                    if($control['type']->billing_amount > 0)
                                                    { 
                                                        echo $control['type']->billing_amount * $control['qty']; 
                                                    } 
                                                ?>
                                            </td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        @endforeach
                                        @foreach($services as $service)
                                        <tr>    
                                            <td>Services</td>
                                            <td>{{ $service['service'] }}</td>
                                            <td>${{ $service['cost'] }}</td>
                                            <td>{{ $service['qty'] }}</td>
                                            <td>${{ $service['total'] }}</td>
                                            <td>
                                                @foreach($service['names'] as $name)
                                                    {{ $name }}, 
                                                @endforeach
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td><strong>${{ $total }}</strong></td>
                                            <td>&nbsp;</td>
                                        </tr>
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

    @endsection