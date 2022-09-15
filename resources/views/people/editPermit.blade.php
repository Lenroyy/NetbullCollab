@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet" type="text/css" />
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">User</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>
        <form class="form-horizontal form-material" action="/savePermit/{{ $permit->id }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="profile" value="{{ $profile->id }}">
            <div class="row">
                <div class="col-md-12">
                    <span class="pull-right">
                        <input type="submit" id="submit-all" name="submit" value="Save" class="btn btn-primary">
                        &nbsp;
                        <a class="btn btn-info" href="/users">Cancel</a>
                    </span>
                </div>
            </div>
            <br>
                    
            <!-- .row -->
            <div class="row">             
                <div class="col-md-12 col-xs-12">
                    <div class="white-box">
                        <ul class="nav nav-tabs tabs customtab">
                            <li class="active tab">
                                <a href="#profile" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="ti-user"></i></span> <span class="hidden-xs">Details</span> </a>
                            </li>
                            <li class="tab">
                                <a href="#attachments" data-toggle="tab"> <span class="visible-xs"><i class="ti-folder"></i></span> <span class="hidden-xs">Attachments @if(count($files) > 0)<span class="badge">{{ count($files) }}</span>@endif</span> </a>
                            </li>
                            <li class="tab">
                                <a href="#logs" data-toggle="tab"> <span class="visible-xs"><i class="ti-calendar"></i></span> <span class="hidden-xs">Logs</span> </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="profile">
                                <br>&nbsp;
                                <input type="hidden" name="profile" value="{{ $profile->id }}">
                                <div class="form-group">
                                    <label for="plan-name" class="control-label">Permit type</label>
                                    <select name="permitType" class="form-control form-control-line" onChange="showTraining(this.value)"> 
                                        <option>Select to add permit</option>
                                        @foreach($allPermits as $p)
                                            <option value="{{ $p->id }}" @if($p->id == $permit->permits_id) selected @endif>{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="plan-name" class="control-label">Required training</label>
                                    <div id="showTrainingDiv">
                                        @foreach($requiredTraining as $training)
                                            <br><a href="/training/{{ $training->id }}">{{ $training->Training->name }}</a>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plan-name" class="control-label">Reference number</label>
                                    <input type="text" id="plan-name" name="reference" value="{{ $permit->reference }}" class="form-control form-control-line"> 
                                </div>
                                <div class="form-group">
                                    <label for="plan-name" class="control-label">Effective date</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="datepicker-effective" placeholder="dd-mm-yyyy" name="effectiveDate" value="@if(!empty($permit->effective_date)){{ $permit->effective_date->format('d-m-Y') }}@endif"><span class="input-group-addon"><i class="icon-calender"></i></span> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="plan-name" class="control-label">Expiry date</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="datepicker-expiry" placeholder="dd-mm-yyyy" name="expiryDate" value="@if(!empty($permit->expiry_date)){{ $permit->expiry_date->format('d-m-Y') }}@endif"><span class="input-group-addon"><i class="icon-calender"></i></span> 
                                    </div>
                                </div>
                                @if($forProfile->type == "user")
                                    @if($standardDisplay['profile']->super_user == 1 OR $standardDisplay['profile']->id != $permit->profiles_id)
                                        <div class="form-group">
                                            <label for="status" class="control-label">Status</label>
                                            <select name="status" class="form-control form-control-line">
                                                <option value="pending approval" @if($permit->status == "pending approval") selected @endif>Pending approval</option>
                                                <option value="approved" @if($permit->status == "approved") selected @endif>Approved</option>
                                                <option value="declined" @if($permit->status == "declined") selected @endif>Declined</option>
                                            </select>
                                        </div>
                                    @else
                                        <input type="hidden" name="status" value="{{ $permit->status }}">
                                    @endif
                                @endif
                                @if($forProfile->type == "contractor")
                                    @if($standardDisplay['profile']->super_user == 1 OR $isBuilder == 1)
                                        <div class="form-group">
                                            <label for="status" class="control-label">Status</label>
                                            <select name="status" class="form-control form-control-line">
                                                <option value="pending approval" @if($permit->status == "pending approval") selected @endif>Pending approval</option>
                                                <option value="approved" @if($permit->status == "approved") selected @endif>Approved</option>
                                                <option value="declined" @if($permit->status == "declined") selected @endif>Declined</option>
                                            </select>
                                        </div>
                                    @else
                                        <input type="hidden" name="status" value="{{ $permit->status }}">
                                    @endif
                                @endif
                            </div>

                            <div class="tab-pane" id="attachments">
                                @include('components.attachment') 
                            </div>

                            <div class="tab-pane" id="logs">
                                @include('components.log') 
                            </div>

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
        <script src="{{ asset('js/jasny-bootstrap.js') }}"></script>        
        <script src="{{ asset('assets/plugins/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>


        <script>
            function showTraining(value)
            {
                var div = document.getElementById("showTrainingDiv")

                innerHTML = ""
                jQuery.getJSON('/getPermitTraining/' + value, function (details) {
                    $.each(details, function (d, detail) {
                        innerHTML += "<br><a href=\"/training/" + detail.trainings_id + "\">" + detail.trainings_name + "</a>"
                    });
                    div.innerHTML = innerHTML
                });

            }

            jQuery('#datepicker-effective').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });

            jQuery('#datepicker-expiry').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'dd-mm-yyyy'
            });
                    
        </script>

    @endsection