@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Configure device</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>
                
        <!-- .row -->
        <form action="/setup/device" method="POST">
            @csrf
            <input type="hidden" name="device" value="{{ $device->id }}">
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">Configure device - {{ $device->name }} :: {{ $device->thingsboard_id }}</h3>
                            </div>
                            <div class="col-md-6 pull-right">
                                <input type="submit" name="submit" value="Save" class="btn btn-primary pull-right">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Reading Type</th>
                                        <th>Calculation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($readingTypes as $readingType)
                                        <input type="hidden" name="readingTypeId[]" value="{{ $readingType->id }}">
                                        <tr>
                                            <td>
                                                {{ $readingType->ReadingType->name }}
                                            </td>
                                            <td>
                                                <select class="form-control form-control-line" name="calculation[]">
                                                    <option value="none" @if($readingType->calculation == "none") selected @endif>None</option>
                                                    <option value="average" @if($readingType->calculation == "average") selected @endif>Sum Average</option>
                                                    <option value="sum" @if($readingType->calculation == "sum") selected @endif>Sum</option>
                                                    <option value="highest" @if($readingType->calculation == "highest") selected @endif>Highest reading</option>
                                                    <option value="lowest" @if($readingType->calculation == "lowest") selected @endif>Lowest reading</option>
                                                    <option value="perHour" @if($readingType->calculation == "perHour") selected @endif>Average per hour</option>
                                                    <option value="perMinute" @if($readingType->calculation == "perMinute") selected @endif>Average per minute</option>
                                                    <option value="first" @if($readingType->calculation == "first") selected @endif>First reading</option>
                                                    <option value="last" @if($readingType->calculation == "last") selected @endif>Last reading</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- /.row -->

        <!-- JS DEPENDENCIES -->
        @include('components.footer') 
        <!-- END JS DEPENDENCIES -->
                    
        <!-- Page specific Javascript -->

    @endsection