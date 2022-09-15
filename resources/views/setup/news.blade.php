@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Setup news</h4>
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
                            <h3 class="box-title">News</h3>
                        </div>
                        <div class="col-md-6">
                            <span class="pull-right"><a class="btn btn-primary" data-toggle="modal" data-target="#newsModal" data-whatever="@mdo">Add news</a></span>
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
                    <div class="table-responsive">
                        <table id="samplesTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>&nbsp;</th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($news as $n)
                                    <tr style="cursor: pointer;">    
                                        <td data-toggle="modal" data-target="#newsModal{{ $n->id }}" data-whatever="@mdo">
                                            <img src="/storage/{{ $n->image }}" style="height: 50px;">
                                        </td>
                                        <td data-toggle="modal" data-target="#newsModal{{ $n->id }}" data-whatever="@mdo">
                                            {{ $n->name }}
                                        </td>
                                        <td data-toggle="modal" data-target="#newsModal{{ $n->id }}" data-whatever="@mdo">
                                            {{ $n->status }}
                                        </td>
                                        <td class="pull-right">
                                            <div class="btn-group m-r-10">
                                                <button aria-expanded="false" data-toggle="dropdown" class="btn btn-info dropdown-toggle waves-effect waves-light" type="button">Options <span class="caret"></span></button>
                                                <ul role="menu" class="dropdown-menu">
                                                    <li><a href="#" data-target="#newsModal{{ $n->id }}" data-whatever="@mdo">Edit</a></li>
                                                    <li><a href="/setup/news/status/{{ $n->id }}">Make headline</a></li>
                                                    <li><a href="/setup/news/archive/{{ $n->id }}">Archive</a></li>
                                                </ul>
                                            </div>
                                        </td>
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




    <!--New Training Modal-->
        <form action="/setup/news" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="news" value="0">    
            <div class="modal fade" id="newsModal" tabindex="-1" role="dialog" aria-labelledby="newsModal" style="display: none;">
                <div class="modal-dialog" role="document">
                    <div class="modal-content modal-lg">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="exampleModalLabel1">New news</h4> </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-md-12">Title</label>
                                            <div class="col-md-12">
                                                <input type="text" id="name" name="name" placeholder="News article headline" class="form-control form-control-line"> 
                                            </div>    
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="col-sm-12">Image</label>
                                            <div class="col-sm-12">
                                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                                    <div class="form-control" data-trigger="fileinput"> 
                                                        <i class="glyphicon glyphicon-file fileinput-exists"></i> 
                                                        <span class="fileinput-filename"></span>
                                                    </div> 
                                                    <span class="input-group-addon btn btn-default btn-file">
                                                        <span class="fileinput-new">Select file</span> 
                                                        <span class="fileinput-exists">Change</span>
                                                        <input type="file" id="files" name="image">
                                                    </span> 
                                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-9">
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                    <label>Description</label>
                                        <textarea class="form-control form-control-line" name="body" cols="30" rows="5"></textarea>
                                    </div>
                                </div>
                            </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <input type="submit" name="submit" value="Save" class="btn btn-primary">
                        </div>
                        
                    </div>
                </div>
            </div>
        </form>
    <!--/New Training Modal-->

    <!--Existing Training Modal-->
        @foreach($news as $n)
            <form action="/setup/news" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="news" value="{{ $n->id }}">    
                <div class="modal fade" id="newsModal{{ $n->id }}" tabindex="-1" role="dialog" aria-labelledby="newsModal{{ $n->id }}" style="display: none;">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content modal-lg">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                <h4 class="modal-title" id="exampleModalLabel1">New news</h4> </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-md-12">Title</label>
                                                <div class="col-md-12">
                                                    <input type="text" id="name" name="name" value="{{ $n->name }}" placeholder="News article headline" class="form-control form-control-line"> 
                                                </div>    
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="col-sm-12">Image</label>
                                                <div class="col-sm-12">
                                                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                                        <div class="form-control" data-trigger="fileinput"> 
                                                            <i class="glyphicon glyphicon-file fileinput-exists"></i> 
                                                            <span class="fileinput-filename"></span>
                                                        </div> 
                                                        <span class="input-group-addon btn btn-default btn-file">
                                                            <span class="fileinput-new">Select file</span> 
                                                            <span class="fileinput-exists">Change</span>
                                                            <input type="file" id="files" name="image">
                                                        </span> 
                                                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-9">
                                            &nbsp;
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                        <label>Description</label>
                                            <textarea class="form-control form-control-line" name="body" cols="30" rows="5">{{ $n->body }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <input type="submit" name="submit" value="Save" class="btn btn-primary">
                            </div>
                            
                        </div>
                    </div>
                </div>
            </form>
        @endforeach
    <!--/existing Training Modal-->
    

        <!-- JS DEPENDENCIES -->
        @include('components.footer') 
        <!-- END JS DEPENDENCIES -->
                    
        <!-- Page specific Javascript -->
        <script src="{{ asset('assets/plugins/bower_components/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/bower_components/toast-master/js/jquery.toast.js') }}"></script>

        <script>
        $(document).ready(function() {
            $('#samplesTable').DataTable({
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