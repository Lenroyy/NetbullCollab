@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('assets/plugins/bower_components/toast-master/css/jquery.toast.css') }}" rel="stylesheet">
        

                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Assessment / SWMS</h4>
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
                        <div class="col-md-3">
                            <label>Site</label><br>
                            {{ $history->Site->name }}
                        </div>
                        <div class="col-md-3">
                            <label>Area / Zone</label><br>
                            {{ $history->Zone->Sites_Map->name }} : {{ $history->Zone->name }}
                        </div>
                        <div class="col-md-3">
                            <label>Activity</label><br>
                            {{ $history->Activity->name }}
                        </div>
                        <div class="col-md-3">
                            <label>Assessment</label><br>
                            {{ $assessment->name }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="actionAssessment" id="actionAssessment" value="{{ $actionAssessment->id }}">
        <input type="hidden" name="assessmentQuestion" id="assessmentQuestion">
        <input type="hidden" name="question" id="question">
        <!-- /row -->

        
        <!-- .row -->
        <div class="row">
            <div class="col-md-12">
                <div class="white-box">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <h3 class="box-title">Assessment / SWMS</h3>
                                </div>
                                @if($historyAssessment->status == "Completed")
                                    <div class="col-md-4">
                                        <h3 class="box-title">Signature</h3>
                                    </div>
                                @endif
                            </div>
                            <div id="swms">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div id="currentQuestion"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <div id="currentAnswer">
                                            @if($historyAssessment->status == "Completed")
                                                <div style="background: white;">@if(!empty($historyAssessment->signature))<img src="{{ $historyAssessment->signature }}">@endif</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" class="pull-right">
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" class="pull-right">
                                        <span class="pull-right">
                                            <div id="buttons">
                                                <button type="button" class="btn btn-warning"><< back</button>
                                                &nbsp; &nbsp; &nbsp;
                                                <button type="button" class="btn btn-primary" value="" onClick="getNextQuestion()">next >></button>
                                            </div>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" class="pull-right">
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" class="pull-right">
                                        <hr>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" class="pull-right">
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="table-responsive">
                                        <table id="openTable" class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Question</th>
                                                    <th>Answer</th>
                                                    <th>Comments</th>
                                                </tr>
                                            </thead>
                                            <tbody id="questionsTable">
                                            </tbody>
                                        </table>
                                    </div>
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
        <script>
            $(document).ready(function() {
                jQuery.getJSON('/requestQuestion/' + {{ $actionAssessment->id }} + '/0/0/0', function (details) {

                    if(details.question == "finished")
                    {
                        document.getElementById("currentQuestion").innerHTML = "Assessment has been completed."
                        @if($historyAssessment->status == "Completed")
                            innerHTML = "<a class=\"btn btn-primary\" href=\"/logActivity/{{ $history->id }}\">Back</a>"
                        @else
                            innerHTML = "<a class=\"btn btn-primary\" href=\"/submitAssessment/{{ $actionAssessment->id }}\">Finish</a>"
                        @endif
                        document.getElementById("buttons").innerHTML = innerHTML
                        innerHTML = ""
                    }
                    else
                    {
                        if(details.question.answer_type == "options")
                        {
                            innerHTML = "<select name=\"answer\" id=\"answer\" class=\"form-control form-control-line\">"
                            $.each(details.options, function (d, detail) {
                                innerHTML += "<option value=\"" + detail.id + "\">" + detail.name + "</option>"
                            });
                            innerHTML += "</select>"
                        }
                        else
                        {
                            innerHTML = "<textarea id=\"answer\" name=\"answer\" cols=\"70\" rows=\"5\" class=\"form-control form-control-line\"></textarea>"
                        }
                        document.getElementById("currentQuestion").innerHTML = details.question.question
                        document.getElementById("currentAnswer").innerHTML = innerHTML
                        document.getElementById("assessmentQuestion").value = details.actionsQuestion.id
                        document.getElementById("question").value = details.question.id
                    }

                    populateQuestions({{ $actionAssessment->id, 0 }});

                });
            });

            function getNextQuestion()
            {
                var assessmentQuestion = document.getElementById("assessmentQuestion").value
                var question = document.getElementById("question").value
                var answer = document.getElementById("answer").value
                answer = answer.replace("/", "-");
                answer = answer.replace("\\", "-");
                answer = answer.replace("%", "-");
                answer = encodeURIComponent(answer)

                jQuery.getJSON('/requestQuestion/' + {{ $actionAssessment->id }} + '/' + assessmentQuestion + '/' + question + '/' + answer, function (details) {
                    
                    if(details.comments)
                    {
                        alert(details.comments)
                    }


                    if(details.question == "finished")
                    {
                        document.getElementById("currentQuestion").innerHTML = "Assessment has been completed."
                        document.getElementById("currentAnswer").innerHTML = ""
                        innerHTML = "<a class=\"btn btn-primary\" href=\"/submitAssessment/{{ $actionAssessment->id }}\">Finish</a>"
                        document.getElementById("buttons").innerHTML = innerHTML
                        innerHTML = ""

                        populateQuestions({{ $actionAssessment->id }}, 1);
                    }
                    else
                    {
                        if(details.question.answer_type == "options")
                        {
                            innerHTML = "<select name=\"answer\" id=\"answer\" class=\"form-control form-control-line\">"
                            $.each(details.options, function (d, detail) {
                                innerHTML += "<option value=\"" + detail.id + "\">" + detail.name + "</option>"
                            });
                            innerHTML += "</select>"
                        }
                        else
                        {
                            innerHTML = "<textarea id=\"answer\" name=\"answer\" cols=\"70\" rows=\"5\" class=\"form-control form-control-line\"></textarea>"
                        }
                        document.getElementById("assessmentQuestion").value = details.actionsQuestion.id
                        document.getElementById("question").value = details.question.id
                        document.getElementById("currentQuestion").innerHTML = details.question.question
                        document.getElementById("currentAnswer").innerHTML = innerHTML

                        populateQuestions({{ $actionAssessment->id }}, 0);
                    }
                });

            }

            function populateQuestions(actionAssessment, status)
            {
                jQuery.getJSON('/requestPreviousQuestions/' + {{ $actionAssessment->id }} + '/' + status, function (details) {
                    table = document.getElementById("questionsTable")

                    innerHTML = ""

                    $.each(details, function (d, detail) {
                        innerHTML += "<tr>"
                            innerHTML += "<td>" + detail.question + "</td>"
                            if(detail.answer)
                            {
                                innerHTML += "<td>" + detail.answer + "</td>"
                            }
                            else
                            {
                                innerHTML += "<td>&nbsp;</td>"
                            }
                            if(detail.comments)
                            {
                                innerHTML += "<td>" + detail.comments + "</td>"
                            }
                            else
                            {
                                innerHTML += "<td>&nbsp;</td>"
                            }
                        innerHTML += "</tr>"
                    });

                    table.innerHTML = innerHTML
                });
            }
        </script>

    @endsection