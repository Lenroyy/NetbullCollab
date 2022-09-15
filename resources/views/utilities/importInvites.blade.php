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
        <form action="/utilities/import/invites/process" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="plan-name" class="control-label">From organisation</label>
                                    <select name="requestor" class="form-control form-control-line"> 
                                        @foreach($memberships as $member)
                                            <option value="{{ $member->Organisation->id }}">{{ $member->Organisation->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-12">CSV File</label>
                                    <div class="col-sm-12">
                                        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                            <div class="form-control" data-trigger="fileinput"> 
                                                <i class="glyphicon glyphicon-file fileinput-exists"></i> 
                                                <span class="fileinput-filename"></span>
                                            </div> 
                                            <span class="input-group-addon btn btn-default btn-file">
                                                <span class="fileinput-new">Select file</span> 
                                                <span class="fileinput-exists">Change</span>
                                                <input type="file" name="fileinput"> 
                                            </span> 
                                            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a> 
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <span class="pull-right">
                                            <input type="submit" class="btn btn-primary" value="import">
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div id="instructions">
                                Ensure that a CSV that is uploaded has two columns.<br>  
                                Column A = Name of person/organisation that is being invited.<br>
                                Column B = Email address to send the invitation to.<br><br>
                                If the person / organisation is already using Nextrack they will get an membership request and an email.  Otherwise they will get an email asking them to join nextrack and your organisation.
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

    @endsection