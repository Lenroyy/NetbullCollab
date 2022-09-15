@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        <link href="{{ asset('css/dots.css') }}" rel="stylesheet">
        

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Exposures for {{ $person->name }}</h4>
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
                            <h3 class="box-title">Exposures for the past 7 days</h3>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Reading Type</label>
                                    {{ $type }}
                                </div>
                                <div class="col-md-6">
                                    @if($outcome == "ok")
                                        <span class="largeGreenDot">Ok</span>
                                    @elseif($outcome == "not ok")
                                        <span class="largeRedDot">Not ok</span>
                                    @elseif($outcome == "unknown")
                                        <span class="largeOrangeDot">Unknown</span>
                                    @else
                                        <span class="largeOrangeDot">Monitor</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    &nbsp;
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="graph"></div>
                                </div>
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
        <script src="{{ asset('js/highcharts.js') }}"></script>

        <script>
            $(function () {
                var options = {
                    chart: {
                        renderTo: 'graph',
                        zoomType: 'x',
                        type: 'column',
                    },
                    title: {
                        text: 'Daily exposures'
                    },
                    yAxis: {
                        title: {
                            text: 'Total exposure'
                        },
                    },


                    labels: {
                        formatter: function () {
                            return this.value; // clean, unformatted number for year
                        }
                    },
                    xAxis:{
                        type: 'datetime'
                    },
                    plotOptions: {
                        area: {
                            fillColor: {
                                linearGradient: {
                                    x1: 0,
                                    y1: 0,
                                    x2: 0,
                                    y2: 1
                                },
                                stops: [
                                    [0, Highcharts.getOptions().colors[0]],
                                    [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                                ]
                            },
                            marker: {
                                radius: 2
                            },
                            lineWidth: 1,
                            states: {
                                hover: {
                                    lineWidth: 1
                                }
                            },
                            threshold: null
                        }
                    },
                    series: [],
                    credits: {
                        enabled: false
                    }
                };
                
                newseries = {};
                newseries.type = 'column';
                newseries.name = '{{ $type }}';
                data = [];
                @foreach($exposures as $exposure)
                    values = []
                    values.push({{ $exposure['timestamp']*1000 }})
                    values.push({{ $exposure['total'] }})
                    data.push(values)
                @endforeach
                newseries.data = data;
                options.series.push(newseries);       
                
            
                var chart = new Highcharts.Chart(options);
            });
                
        </script>

    @endsection