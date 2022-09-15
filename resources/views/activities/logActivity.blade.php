@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('css/dots.css') }}" rel="stylesheet">
        
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Log new activity</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>

        <form class="form-horizontal form-material" action="/saveActivity/1" method="POST" enctype="multipart/form-data">
        @csrf
            <input type="hidden" name="loggedActivity" id="loggedActivity" value="{{ $history->id }}">
            <div class="row">
                <div class="col-md-12">
                    <span class="pull-right"><input type="submit" class="btn btn-primary" value="Finish" id="finishActivity"></span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <br>
                </div>
            </div>
            
            <!-- .row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <ul class="nav nav-tabs tabs customtab">
                            <li class="active tab">
                                <a href="#details" data-toggle="tab" aria-expanded="true"> <span class="visible-xs"><i class="fa-tasks"></i></span> <span class="hidden-xs">Details</span> </a>
                            </li>
                            @if($history->id > 0)
                                <li class="tab">
                                    <a href="#controls" data-toggle="tab"> <span class="visible-xs"><i class="ti-truck"></i></span> <span class="hidden-xs">Controls</span> </a>
                                </li>
                            @endif
                            <li class="tab">
                                <a href="#attachments" data-toggle="tab"> <span class="visible-xs"><i class="ti-folder"></i></span> <span class="hidden-xs">Attachments @if(count($files) > 0)<span class="">{{ count($files) }}</span>@endif</span> </a>
                            </li>
                            <li class="tab">
                                <a href="#logs" data-toggle="tab"> <span class="visible-xs"><i class="ti-calendar"></i></span> <span class="hidden-xs">Logs</span> </a>
                            </li>
                        </ul>
                        <div class="tab-content">

                            <div class="tab-pane active" id="details">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3>Details</h3>
                                    </div>
                                    <div class="col-md-6">
                                        @if($history->id > 0)
                                            @if($worstReading == "ok")
                                                <span class="pull-right"><span class="largeGreenDot">Ok</span></span>
                                            @elseif($worstReading == "not ok")
                                                <span class="pull-right"><span class="largeRedDot">Not Ok</span></span>
                                            @else
                                                <span class="pull-right"><span class="largeOrangeDot">Monitor</span></span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Site</label>
                                        @if($history->id > 0)
                                            <input type="hidden" id="sites" name="site" value="{{ $history->site_id }}">
                                            {{ $history->Site->name }}
                                        @else
                                            <select class="form-control form-control-line" onChange="getAreas(this.value)" name="site" id="sites">
                                                <option value="0">Please Select</option>
                                                @foreach($sites as $site)
                                                    <option @if($site->id == $history->site_id) selected @endif value="{{ $site->id }}">{{ $site->name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <label>Zone</label>
                                        @if($history->id > 0)
                                            <input type="hidden" id="zones" name="zone" value="{{ $history->zone_id }}">
                                            {{ $history->Zone->name }}
                                        @else
                                            <select class="form-control form-control-line" id="zones" name="zone">
                                                @foreach($zones as $zone)
                                                    <option @if($zone['id'] == $history->zone_id) selected @endif value="{{ $zone['id'] }}">{{ $zone['map'] }} :: {{ $zone['zone'] }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                    <div class="col-md-4">
                                        <label>Activity</label>
                                        @if($history->activity_id > 0)
                                            <input type="hidden" name="activity" value="{{ $history->activity_id }}">
                                            {{ $history->Activity->name }}
                                        @else
                                            <select class="form-control form-control-line" onChange="showAssessments(this.value)" name="activity" id="activities" required>
                                                <option value="0">None</option>
                                                @foreach($activities as $activity)
                                                    <option @if($activity->id == $history->activity_id) selected @endif value="{{ $activity->id }}">{{ $activity->name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <br><br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3>Required Assessments / SWMS</h3>
                                        <hr>
                                        <div class="table-responsive">
                                            <table id="accountTable" class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Assessment</th>
                                                        <th>Status</th>
                                                        <th>Score</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="assessments">
                                                    @if(!empty($assessments))
                                                        @foreach($assessments->assessments as $ass)
                                                            <tr style="cursor: pointer;" onClick="window.location.href='/siteVisit/swms/{{ $ass->id }}'">
                                                                <td>{{ $ass->name }}</td>
                                                                <td>{{ $ass->status }}</td>
                                                                <td>{{ $ass->score }}</td> 
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <br><br>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3>Times</h3>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Time start</label><br>
                                        <div class="col-md-5">
                                            <select class="form-control form-control-line" name="startHour">
                                                <option value="00" @if($startHour == "00") selected @endif>0</option>
                                                <option value="01" @if($startHour == "01") selected @endif>1</option>
                                                <option value="02" @if($startHour == "02") selected @endif>2</option>
                                                <option value="03" @if($startHour == "03") selected @endif>3</option>
                                                <option value="04" @if($startHour == "04") selected @endif>4</option>
                                                <option value="05" @if($startHour == "05") selected @endif>5</option>
                                                <option value="06" @if($startHour == "06") selected @endif>6</option>
                                                <option value="07" @if($startHour == "07") selected @endif>7</option>
                                                <option value="08" @if($startHour == "08") selected @endif>8</option>
                                                <option value="09" @if($startHour == "09") selected @endif>9</option>
                                                <option value="10" @if($startHour == "10") selected @endif>10</option>
                                                <option value="11" @if($startHour == "11") selected @endif>11</option>
                                                <option value="12" @if($startHour == "12") selected @endif>12</option>
                                                <option value="13" @if($startHour == "13") selected @endif>13</option>
                                                <option value="14" @if($startHour == "14") selected @endif>14</option>
                                                <option value="15" @if($startHour == "15") selected @endif>15</option>
                                                <option value="16" @if($startHour == "16") selected @endif>16</option>
                                                <option value="17" @if($startHour == "17") selected @endif>17</option>
                                                <option value="18" @if($startHour == "18") selected @endif>18</option>
                                                <option value="19" @if($startHour == "19") selected @endif>19</option>
                                                <option value="20" @if($startHour == "20") selected @endif>20</option>
                                                <option value="21" @if($startHour == "21") selected @endif>21</option>
                                                <option value="22" @if($startHour == "22") selected @endif>22</option>
                                                <option value="23" @if($startHour == "23") selected @endif>23</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            :
                                        </div>
                                        <div class="col-md-5">
                                            <select class="form-control form-control-line" name="startMinute">
                                                <option value="00" @if($startMinute == "00") selected @endif>00</option>
                                                <option value="05" @if($startMinute == "05") selected @endif>05</option>
                                                <option value="10" @if($startMinute == "10") selected @endif>10</option>
                                                <option value="15" @if($startMinute == "15") selected @endif>15</option>
                                                <option value="20" @if($startMinute == "20") selected @endif>20</option>
                                                <option value="25" @if($startMinute == "25") selected @endif>25</option>
                                                <option value="30" @if($startMinute == "30") selected @endif>30</option>
                                                <option value="35" @if($startMinute == "35") selected @endif>35</option>
                                                <option value="40" @if($startMinute == "40") selected @endif>40</option>
                                                <option value="45" @if($startMinute == "45") selected @endif>45</option>
                                                <option value="50" @if($startMinute == "50") selected @endif>50</option>
                                                <option value="55" @if($startMinute == "55") selected @endif>55</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Time end</label><br>
                                        <div class="col-md-5">
                                            <select class="form-control form-control-line" name="endHour" id="endHour">
                                                <option value="00" @if($endHour == "00") selected @endif>0</option>
                                                <option value="01" @if($endHour == "01") selected @endif>1</option>
                                                <option value="02" @if($endHour == "02") selected @endif>2</option>
                                                <option value="03" @if($endHour == "03") selected @endif>3</option>
                                                <option value="04" @if($endHour == "04") selected @endif>4</option>
                                                <option value="05" @if($endHour == "05") selected @endif>5</option>
                                                <option value="06" @if($endHour == "06") selected @endif>6</option>
                                                <option value="07" @if($endHour == "07") selected @endif>7</option>
                                                <option value="08" @if($endHour == "08") selected @endif>8</option>
                                                <option value="09" @if($endHour == "09") selected @endif>9</option>
                                                <option value="10" @if($endHour == "10") selected @endif>10</option>
                                                <option value="11" @if($endHour == "11") selected @endif>11</option>
                                                <option value="12" @if($endHour == "12") selected @endif>12</option>
                                                <option value="13" @if($endHour == "13") selected @endif>13</option>
                                                <option value="14" @if($endHour == "14") selected @endif>14</option>
                                                <option value="15" @if($endHour == "15") selected @endif>15</option>
                                                <option value="16" @if($endHour == "16") selected @endif>16</option>
                                                <option value="17" @if($endHour == "17") selected @endif>17</option>
                                                <option value="18" @if($endHour == "18") selected @endif>18</option>
                                                <option value="19" @if($endHour == "19") selected @endif>19</option>
                                                <option value="20" @if($endHour == "20") selected @endif>20</option>
                                                <option value="21" @if($endHour == "21") selected @endif>21</option>
                                                <option value="22" @if($endHour == "22") selected @endif>22</option>
                                                <option value="23" @if($endHour == "23") selected @endif>23</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            :
                                        </div>
                                        <div class="col-md-5">
                                            <select class="form-control form-control-line" name="endMinute" id="endMinute">
                                                <option value="00" @if($endMinute == "00") selected @endif>00</option>
                                                <option value="05" @if($endMinute == "05") selected @endif>05</option>
                                                <option value="10" @if($endMinute == "10") selected @endif>10</option>
                                                <option value="15" @if($endMinute == "15") selected @endif>15</option>
                                                <option value="20" @if($endMinute == "20") selected @endif>20</option>
                                                <option value="25" @if($endMinute == "25") selected @endif>25</option>
                                                <option value="30" @if($endMinute == "30") selected @endif>30</option>
                                                <option value="35" @if($endMinute == "35") selected @endif>35</option>
                                                <option value="40" @if($endMinute == "40") selected @endif>40</option>
                                                <option value="45" @if($endMinute == "45") selected @endif>45</option>
                                                <option value="50" @if($endMinute == "50") selected @endif>50</option>
                                                <option value="55" @if($endMinute == "55") selected @endif>55</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="controls">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3>Controls and readings</h3>
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    @foreach($readings as $controlType)
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h4>{{ $controlType['type']->name }}</h4>
                                                <hr>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped">
                                                <thead>
                                                    <tr>
                                                        @foreach($controlType['fields'] as $field)
                                                        <th>
                                                            {{ $field }}
                                                        </th>
                                                        @endforeach
                                                        <th>Outcome</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($controlType['controls'] as $control)
                                                        <tr style="cursor: pointer">
                                                            @foreach($control['fieldValues'] as $value)
                                                                <td>
                                                                    <p class="text-muted">
                                                                        {{ $value['value'] }}
                                                                    </p>
                                                                </td>
                                                            @endforeach
                                                            <td>
                                                                @if($control['outcome'] == "ok")
                                                                    <span class="smallGreenDot">&nbsp;</span> Ok
                                                                @elseif($control['outcome'] == "not ok")
                                                                    <span class="smallRedDot">&nbsp;</span> Not Ok
                                                                @else
                                                                    <span class="smallOrangeDot">&nbsp;</span> Monitor
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="{{ count($control['fieldValues'])-3 }}">&nbsp;</th>
                                                            <th>Reading Type</th>
                                                            <th>Reading</th>
                                                            <th>Outcome</th>
                                                        </tr>       
                                                        @foreach($control['readings'] as $reading)
                                                            <tr>
                                                                <td colspan="{{ count($control['fieldValues'])-3 }}">&nbsp;</td>
                                                                <td>
                                                                    {{-- {{ $reading->ReadingType->name }} --}}
                                                                    {{ isset($reading->ReadingType['name']) }}
                                                                </td>
                                                                <td>
                                                                    {{ $reading->reading }}
                                                                </td>
                                                                <td>
                                                                    @if($reading->outcome == "ok")
                                                                        <span class="xsmallGreenDot">&nbsp;</span> 
                                                                    @elseif($reading->outcome == "not ok")
                                                                        <span class="xsmallRedDot">&nbsp;</span> 
                                                                    @else
                                                                        <span class="xsmallOrangeDot">&nbsp;</span> 
                                                                    @endif
                                                                    {{ $reading->outcome }}
                                                                </td>
                                                            </tr>
                                                        @endforeach       
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" id="checkButton" data-toggle="modal" data-target="#checkingModal"></button>
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

        <div class="modal fade" id="checkingModal" tabindex="-1" role="dialog" aria-labelledby="checkingModal" style="display: none;">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-lg">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h4 class="modal-title" id="exampleModalLabel1">Just checking in</h4> </div>
                    <div class="modal-body">
                        <div style="text-align: center; height: 250px; line-height: 45px;">
                            <br>&nbsp;
                            <h1>Hi {{ $standardDisplay['profile']->name }}</h1>
                            <p>Just checking you're still there and working on this activity.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
            $(document).ready(function() {
                keepAlive();

                if({{ $history->site_id }} > 0)
                {
                    getAreas({{ $history->site_id }});
                }
            });

            function getAreas(site)
            {
                innerHTML = "<option value=\"0\">None</option>"

                jQuery.getJSON('/getSiteZonesJson/' + site, function (details) {
                    $.each(details, function (d, detail) {
                        console.log(detail)

                        innerHTML += "<option value=\"" + detail.id + "\">"
                            innerHTML += detail.map + " : " + detail.zone
                        innerHTML += "</option>"
                        
                    });

                    document.getElementById("zones").innerHTML = innerHTML
                });
            }

            function showAssessments(activity)
            {

                var site = document.getElementById("sites").value
                var zone = document.getElementById("zones").value
                var loggedActivity = document.getElementById("loggedActivity").value

                innerHTML = ""
                
                jQuery.getJSON('/getActivityAssessments/' + activity + '/' + loggedActivity + '/' + site + '/' + zone, function (details) {
                    console.log(details)
                    $.each(details.assessments, function (d, detail) {
                        console.log(detail)

                        innerHTML += "<tr style=\"cursor: pointer;\" onClick=\"window.location.href='/siteVisit/swms/" + detail.id + "'\">"
                            innerHTML += "<td>" + detail.name + "</td>"
                            innerHTML += "<td>" + detail.status + "</td>"
                            innerHTML += "<td>" + detail.score + "</td>"
                        innerHTML += "</tr>"
                    });
                    document.getElementById("loggedActivity").value = details.id 
                    document.getElementById("assessments").innerHTML = innerHTML
                });
                //document.getElementById("activities").disabled = true
            }

            function keepAlive()
            {
                now = Math.floor(Date.now() / 1000)
                check = now + 3600
                //check = now + 10
                round = 0;

                const interval = setInterval(function() {
                   now = Math.floor(Date.now() / 1000)
                   var d = new Date();
                   var h = d.getHours();
                   var m = d.getMinutes();


                    if("{{ $endHour }}" == "23" && "{{ $endMinute }}" == "59")
                    {
                        //this history hasn't yet been submitted - update the finish time automatically 
                        m = Math.ceil(m/5)*5;
                        document.getElementById("endHour").value = h;
                        document.getElementById("endMinute").value = m;
                    }

                   if(now > check)
                   {
                       if(round == 1)
                       {
                            document.getElementById("finishActivity").click();
                       }
                       else
                       {
                          document.getElementById("checkButton").click();
                          check = now + 3600
                          //check = now + 10
                          round = round + 1;
                       }
                       
                   }
                   else
                   {
                       console.log("checking - all good now is " + now + " waiting for time " + check + " before next step.")
                   }
                }, 30000);
            }

        </script>

    @endsection