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
                                            <input type="file" name="csv"> 
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
        <script>
            function nextStep(value)
            {
                instructions = "<br>&nbsp;<br>&nbsp;<br>"
                instructions += "<strong>Make sure your CSV is formatted in the following way</strong><br>First record of the CSV should be a header record<br>"

                if(value == "0")
                {
                    instructions += "Column 1 = name<br>"
                    instructions += "Column 2 = email address<br>"
                    instructions += "Column 3 = mobile number<br>"
                    instructions += "Column 4 = Street address<br>"
                }
                else if(value == "1")
                {
                    instructions += "Column 1 = site name<br>"
                    instructions += "Column 2 = street address<br>"
                    instructions += "Column 3 = city<br>"
                    instructions += "Column 4 = state<br>"
                    instructions += "Column 5 = postcode<br>"
                }
                else if(value == "2")
                {
                    instructions += "Column 1 = name<br>"
                    instructions += "Column 2 = email address<br>"
                    instructions += "Column 3 = mobile number<br>"
                    instructions += "Column 4 = Street address<br>"
                }
                else if(value == "3")
                {
                    instructions += "Column 1 = name<br>"
                    instructions += "Column 2 = email address<br>"
                    instructions += "Column 3 = mobile number<br>"
                    instructions += "Column 4 = Street address<br>"
                }
                else if(value == "4")
                {
                    instructions += "Column 1 = name<br>"
                    instructions += "Column 2 = email address<br>"
                    instructions += "Column 3 = mobile number<br>"
                    instructions += "Column 4 = Street address<br>"
                }
                else if(value == "5")
                {
                    instructions += "Column 1 = type<br>"
                    instructions += "Column 2 = serial number<br>"
                    instructions += "Onwards each column should be in order and match the field on the asset type"
                }
                else
                {
                    instructions = ""
                }
                document.getElementById("instructions").innerHTML = instructions
            }
        </script>

    @endsection