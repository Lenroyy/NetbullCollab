@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Setup services</h4>
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
                            <h3 class="box-title">Services</h3>
                        </div>
                        <div class="col-md-6">
                            <span class="pull-right"><a class="btn btn-primary" data-toggle="modal" data-target="#trainingModal" data-whatever="@mdo">Add service</a></span>
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
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Link</th>
                                    <th>Type</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($training as $t)
                                    <tr style="cursor: pointer;">    
                                        <td data-toggle="modal" data-target="#trainingModal{{ $t['training']->id }}" data-whatever="@mdo">
                                            {{ $t['training']->name }}
                                        </td>
                                        <td data-toggle="modal" data-target="#trainingModal{{ $t['training']->id }}" data-whatever="@mdo">
                                            ${{ $t['training']->price }}
                                        </td>
                                        <td data-toggle="modal" data-target="#trainingModal{{ $t['training']->id }}" data-whatever="@mdo">
                                            <a href="{{ $t['training']->link }}" target="_blank">{{ $t['training']->link }}</a>
                                        </td>
                                        <td data-toggle="modal" data-target="#trainingModal{{ $t['training']->id }}" data-whatever="@mdo">
                                            @if(is_object($t['training']->Training_Types)){{ $t['training']->Training_Types->name }} @else Not set @endif
                                        </td>
                                        <td>
                                            <a href="/setup/training/archive/{{ $t['training']->id }}">Archive</a>
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
        <form action="/setup/training" method="post">
            @csrf
            <input type="hidden" name="training" value="0">    
            <div class="modal fade" id="trainingModal" tabindex="-1" role="dialog" aria-labelledby="trainingModal" style="display: none;">
                <div class="modal-dialog" role="document">
                    <div class="modal-content modal-lg">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="exampleModalLabel1">New Service</h4> </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-md-12">Service name</label>
                                            <div class="col-md-12">
                                                <input type="text" id="name" name="name" placeholder="Service name" class="form-control form-control-line"> 
                                            </div>    
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-md-12">Default link</label>
                                            <div class="col-md-12">
                                                <input type="text" id="link" name="link" placeholder="Default link to use for providers" class="form-control form-control-line"> 
                                            </div>    
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-md-12">Default price</label>
                                            <div class="col-md-12">
                                                <input type="number" step="0.01" id="price" name="price" placeholder="Default price for the service" class="form-control form-control-line"> 
                                            </div>    
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="col-md-12">Service type</label>
                                            <div class="col-md-12">
                                                <select name="type" class="form-control form-control-line"> 
                                                    @foreach($trainingTypes as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                    @endforeach
                                                </select>
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
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Hygenist</th>
                                                    <th>Price</th>
                                                    <th>Link</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $x = 0;
                                                ?>
                                                @foreach($hygenists as $h)
                                                    <tr>
                                                        <td>
                                                            {{ $h->name }}
                                                            <input type="hidden" name="hygenists[<?= $x ?>][id]" value="{{ $h->id }}">
                                                        </td>
                                                        <td>
                                                            <input type="number" id="price" name="hygenists[<?= $x ?>][price]" step="0.01" class="form-control form-control-line">
                                                        </td>
                                                        <td>
                                                            <input type="text" name="hygenists[<?= $x ?>][link]" placeholder="Link to specific service details from provider" class="form-control form-control-line">
                                                        </td>
                                                    </tr>
                                                    <?php
                                                        $x++;
                                                    ?>
                                                @endforeach
                                            </tbody>
                                        </table>
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
                                        <textarea class="form-control form-control-line" name="description" cols="30" rows="5"></textarea>
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
        @foreach($training as $t)
            <form action="/setup/training" method="post">
                @csrf
                <input type="hidden" name="training" value="{{ $t['training']->id }}">    
                <div class="modal fade" id="trainingModal{{ $t['training']->id }}" tabindex="-1" role="dialog" aria-labelledby="trainingModal{{ $t['training']->id }}" style="display: none;">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content modal-lg">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                <h4 class="modal-title" id="exampleModalLabel1">Update service</h4> </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="col-md-12">Service name</label>
                                                <div class="col-md-12">
                                                    <input type="text" id="name" name="name" value="{{ $t['training']->name }}" placeholder="Service name" class="form-control form-control-line"> 
                                                </div>    
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="col-md-12">Default link</label>
                                                <div class="col-md-12">
                                                    <input type="text" id="link" value="{{ $t['training']->link }}" name="link" placeholder="Default link to use for providers" class="form-control form-control-line" onBlur="updateLinks(this.value, <?= $x ?>)"> 
                                                </div>    
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="col-md-12">Default price</label>
                                                <div class="col-md-12">
                                                    <input type="text" id="price" value="{{ $t['training']->price }}" name="price" placeholder="Default price for the service" class="form-control form-control-line" onBlur="updatePrice(this.value, <?= $x ?>))"> 
                                                </div>    
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="col-md-12">Service type</label>
                                                <div class="col-md-12">
                                                    <select name="type" class="form-control form-control-line"> 
                                                        @foreach($trainingTypes as $type)
                                                            <option value="{{ $type->id }}" @if($type->id == $t['training']->training_type_id) selected @endif>{{ $type->name }}</option>
                                                        @endforeach
                                                    </select>
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
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Provider</th>
                                                        <th>Price</th>
                                                        <th>Link</th>
                                                        <th>Active provider</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        $x = 0;
                                                    ?>
                                                    @foreach($t['hygenists'] as $h)
                                                        <tr>
                                                            <td>
                                                                @if(is_object($h->Profile))
                                                                    {{ $h->Profile->name }} 
                                                                @else
                                                                    -
                                                                @endif
                                                                <input type="hidden" name="hygenists[<?= $x ?>][id]" value="{{ $h->profile_id }}">
                                                            </td>
                                                            <td>
                                                                <input type="number" name="hygenists[<?= $x ?>][price]" step="0.01" class="form-control form-control-line" value="{{ $h->price }}">
                                                            </td>
                                                            <td>
                                                                <input type="text" name="hygenists[<?= $x ?>][link]" placeholder="Link to specific training from provider" class="form-control form-control-line" value="{{ $h->link }}">
                                                            </td>
                                                            <td>
                                                                <input type="checkbox" name="hygenists[<?= $x ?>][activeProvider]" @if($h->active_provider == "1") checked @endif>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                            $x++;
                                                        ?>
                                                    @endforeach
                                                </tbody>
                                            </table>
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
                                            <textarea class="form-control form-control-line" name="description" cols="30" rows="5">{{ $t['training']->description }}</textarea>
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