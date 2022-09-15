@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Import data</h4>
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
                            <div class="form-group">
                                <label for="plan-name" class="control-label">Data type</label>
                                <select name="permitType" class="form-control form-control-line" onChange="nextStep(this.value)"> 
                                    <option value="n/a">Please select</option>
                                    <option value="0">Users</option>
                                    <option value="1">Sites</option>
                                    <option value="2">Contractors</option>
                                    <option value="3">Builders</option>
                                    <option value="4">Hygenists</option>
                                    <option value="5">Assets</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    &nbsp;
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <span class="pull-right">
                                        <input type="submit" class="btn btn-primary" value="export">
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div id="instructions"></div>
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