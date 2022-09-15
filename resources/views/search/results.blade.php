@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Search results</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>
                
        <!-- .results -->
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    @foreach($results as $result)
                        <h3 class="box-title">{{ $result['type'] }}</h3>
                        <div class="table-responsive">
                            <table id="accountTable" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($result['results'] as $record)
                                        <tr style="cursor: pointer;">    
                                            <td onClick="window.location.href='{{ $result['baseURL'] }}/{{ $record['id'] }}{{ $result['urlTail'] }}'">{{ $record['name'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            &nbsp;<br>&nbsp;<br>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- /.results -->

        <!-- JS DEPENDENCIES -->
        @include('components.footer') 
        <!-- END JS DEPENDENCIES -->
                    
        <!-- Page specific Javascript -->

    @endsection