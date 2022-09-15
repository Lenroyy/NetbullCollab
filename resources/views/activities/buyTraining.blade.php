@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Buy service</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>
                
        <!-- .row -->
        @if($isHygenist > 0 OR $standardDisplay['profile']->super_user == 1)
        <ul class="nav nav-tabs tabs customtab">
            <li class="active tab">
                <a href="#details" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="fa fa-tasks"></i></span> <span class="hidden-xs">Details</span> </a>
            </li>
            <li class="tab">
                <a href="#members" data-toggle="tab"> <span class="visible-xs"><i class="fa fa-dollar"></i></span> <span class="hidden-xs">Your member pricing <span class="badge"> {{ count($myMembers) }}</span></span> </a>
            </li>           
            <li class="tab">
                <a href="#orders" data-toggle="tab"> <span class="visible-xs"><i class="fa fa-shopping-basket"></i></span> <span class="hidden-xs">Ordered courses</span> </a>
            </li>           
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="details">
            @endif
                <form class="form-horizontal form-material" action="/buyTraining/{{ $training->id }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="white-box">
                        <h3 class="box-title">Confirm service and provider</h3>
                        <hr><br>
                        <h4>{{ $training->name }}</h4>
                        <br><br>
                        <label>Description</label><br>
                        {{ $training->description }}
                        <br><br>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>&nbsp;</th>
                                        <th>Service Provider</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($hygenists as $h)
                                        <tr>
                                            <td>
                                                <input type="radio" name="provider" value="{{ $h['hygenist_id'] }}">
                                            </td>
                                            <td>
                                                @if($h['special'] == 1)
                                                    <strong>
                                                        {{ $h['name'] }}
                                                    </strong>
                                                @else
                                                    {{ $h['name'] }}
                                                @endif

                                            </td>
                                            <td>
                                                @if($h['special'] == 1)
                                                    <strong>
                                                        ${{ $h['price'] }}
                                                    </strong>
                                                @else
                                                    ${{ $h['price'] }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <br><br>
                        <div class="row">
                            <div class="col-md-3">
                                <label>Order Number</label>
                                <input type="text" name="order" placeholder="Your order number here" required class="form-control form-control-line">
                                <br>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <input type="submit" class="btn btn-primary" value="Confirm">
                            </div>
                        </div>
                    </div>
                </form>
                <!-- /.row -->
                @if($isHygenist > 0 OR $standardDisplay['profile']->super_user == 1)        
                    </div>
                    <div class="tab-pane" id="members">
                        <div class="white-box">
                            <form class="form-horizontal form-material" action="/updateMemberPricing/{{ $training->id }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Active Provider</label>
                                        <input type="checkbox" name="activeProvider" @if($isActive == 1) checked @endif>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Member</th>
                                                        <th>Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($myMembers as $member)
                                                        <tr>
                                                            <td>
                                                                {{ $member->Member->name }}
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" name="price[]" value="{{ $member->price }}">
                                                                <input type="hidden" name="memberID[]" value="{{ $member->id }}">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <input type="submit" name="submit" value="Update Member Prices" class="btn btn-primary">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="tab-pane" id="orders">
                        <div class="white-box">
                            <form class="form-horizontal form-material" action="/updateTrainingOrders/{{ $training->id }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Paid</th>
                                                <th>Person</th>
                                                <th>Organisation</th>
                                                <th>Price</th>
                                                <th>Order No</th>
                                                <th>Status</th>
                                                <th>Instructions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($myOrders as $order)
                                                <tr>
                                                    <input type="hidden" name="orderID[]" value="{{ $order['order']->id }}">
                                                    <td>
                                                        <input type="checkbox" name="paid[]" @if($order['order']->paid == 1) checked @endif>
                                                    </td>
                                                    <td>
                                                        {{ $order['name'] }}
                                                    </td>
                                                    <td>
                                                        @if($order['order']->active_organisation_id > 0){{ $order['order']->ActiveOrganisation->name }}@endif
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" name="price[]" value="{{ $order['order']->price }}" class="form-control form-control-line">
                                                    </td>
                                                    <td>
                                                        {{ $order['order']->order_no }}
                                                    </td>
                                                    <td>
                                                        <select name="status[]" class="form-control form-control-line">
                                                            <option value="pending" @if($order['order']->status == "pending") selected @endif>Pending</option>
                                                            <option value="progress" @if($order['order']->status == "progress") selected @endif>In Progress</option>
                                                            <option value="completed" @if($order['order']->status == "completed") selected @endif>Completed</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <textarea class="form-control form-control-line" name="instructions[]">{{ $order['order']->instructions }}</textarea>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <input type="submit" name="submit" value="Update details" class="btn btn-primary">
                            </form>
                        </div>
                    </div>
                @endif

        <!-- JS DEPENDENCIES -->
        @include('components.footer') 
        <!-- END JS DEPENDENCIES -->
                    
        <!-- Page specific Javascript -->

    @endsection