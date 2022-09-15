@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Edit Rule</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>
                
        <!-- .row -->
        <form class="form-horizontal form-material" action="/setup/rules" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="ruleID" value="{{ $rule->id }}">
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title">Edit rule</h3>
                            </div>
                            <div class="col-md-6 pull-right">
                                <input type="submit" name="submit" value="Save" class="btn btn-primary pull-right">
                            </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Name</label>
                                <input type="text" name="name" placeholder="Name of this rule" @if($rule->id > 0) value="{{ $rule->name }}" @endif class="form-control form-control-line">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <label>Order</label>
                                <input type="text" name="order" placeholder="Order to process this rule" @if($rule->id > 0) value="{{ $rule->order }}" @endif class="form-control form-control-line">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <label>Reading type</label>
                                <select type="text" name="readingType" class="form-control form-control-line">
                                    @foreach($readingTypes as $readingType)
                                        <option value="{{ $readingType->id }}" @if($readingType->id == $rule->reading_type_id) selected @endif>{{ $readingType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <label>Rule type</label>
                                <select type="text" name="ruleType" class="form-control form-control-line" onChange="populateForm(this.value)">
                                    <option value="0">Select</option>
                                    <option value="response" @if($rule->rule_type == "response") selected @endif>Assessment response check</option>
                                    <option value="range" @if($rule->rule_type == "range") selected @endif>Result within range</option>
                                    <option value="above" @if($rule->rule_type == "above") selected @endif>Result above value</option>
                                    <option value="below" @if($rule->rule_type == "below") selected @endif>Result below value</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div id="remainingForm"></div>
                    </div>
                </div>
            </div>
        </form>
        <!-- /.row -->

        <!-- JS DEPENDENCIES -->
        @include('components.footer') 
        <!-- END JS DEPENDENCIES -->
                    
        <!-- Page specific Javascript -->
        <script>
            function populateForm(value)
            {
                
                div = document.getElementById("remainingForm")
                innerHTML = ""

                if(value == "response")
                {
                    innerHTML += "<div class=\"row\"><div class=\"col-md-12\">"
                        innerHTML += "<label>Assessment</label>"
                        innerHTML += "<select name=\"assessmentID\" class=\"form-control form-control-line\" onChange=\"checkQuestions(this.value)\">"
                            innerHTML += "<option value=\"0\">Select</option>"
                            @foreach($assessments as $assessment)
                                innerHTML += "<option value=\"{{ $assessment->id }}\" @if($rule->id > 0) @if($assessment->id == $rule->assessment_id) selected @endif @endif>{{ $assessment->name }}</option>"
                            @endforeach
                        innerHTML += "</select>"
                    innerHTML += "</div></div><br>"
                    innerHTML += "<div class=\"row\"><div class=\"col-md-12\">"
                        innerHTML += "<label>Question</label>"
                        innerHTML += "<select name=\"questionID\" class=\"form-control form-control-line\" onChange=\"checkAnswers(this.value)\" id=\"questions\">"
                            innerHTML += "<option value=\"0\">Select</option>"
                        innerHTML += "</select>"
                    innerHTML += "</div></div><br>"
                    innerHTML += "<div class=\"row\"><div class=\"col-md-12\">"
                        innerHTML += "<label>Answer</label>"
                        innerHTML += "<select name=\"answerID\" class=\"form-control form-control-line\" id=\"answers\">"
                            innerHTML += "<option value=\"0\">Select</option>"
                        innerHTML += "</select>"
                    innerHTML += "</div></div><br>"
                }

                if(value == "range")
                {
                    innerHTML += "<div class=\"row\"><div class=\"col-md-12\">"
                        innerHTML += "<label>Minimum level</label>"
                        innerHTML += "<input type=\"text\" name=\"minimum\" placeholer=\"Minimum reading for the range\" @if($rule->id > 0) value=\"{{ $rule->within_range_min }}\" @endif class=\"form-control form-control-line\">"
                    innerHTML += "</div></div><br>"
                    innerHTML += "<div class=\"row\"><div class=\"col-md-12\">"
                        innerHTML += "<label>Maximum level</label>"
                        innerHTML += "<input type=\"text\" name=\"maximum\" placeholer=\"Maximum reading for the range\" @if($rule->id > 0) value=\"{{ $rule->within_range_max }}\" @endif class=\"form-control form-control-line\">"
                    innerHTML += "</div></div><br>"
                }
                
                if(value == "above")
                {
                    innerHTML += "<div class=\"row\"><div class=\"col-md-12\">"
                        innerHTML += "<label>Above reading</label>"
                        innerHTML += "<input type=\"text\" name=\"aboveMax\" placeholer=\"If reading above this level\" @if($rule->id > 0) value=\"{{ $rule->above_max }}\" @endif class=\"form-control form-control-line\">"
                    innerHTML += "</div></div><br>"
                }

                if(value == "below")
                {
                    innerHTML += "<div class=\"row\"><div class=\"col-md-12\">"
                        innerHTML += "<label>Below reading</label>"
                        innerHTML += "<input type=\"text\" name=\"belowMin\" placeholer=\"If reading below this level\" @if($rule->id > 0) value=\"{{ $rule->below_min }}\" @endif class=\"form-control form-control-line\">"
                    innerHTML += "</div></div><br>"
                }

                innerHTML += "<div class=\"row\"><div class=\"col-md-12\">"
                    innerHTML += "<label>Outcome</label>"
                    innerHTML += "<select name=\"outcome\" class=\"form-control form-control-line\" onChange=\"checkFormula(this.value)\">"
                        innerHTML += "<option value=\"ok\" @if($rule->id > 0) @if($rule->outcome == "ok") selected @endif @endif>Ok</option>"
                        innerHTML += "<option value=\"monitor\" @if($rule->id > 0) @if($rule->outcome == "monitor") selected @endif @endif>Monitor</option>"
                        innerHTML += "<option value=\"not ok\" @if($rule->id > 0) @if($rule->outcome == "not ok") selected @endif @endif>Not Ok</option>"
                        innerHTML += "<option value=\"formula\" @if($rule->id > 0) @if($rule->outcome == "formula") selected @endif @endif>Apply formula</option>"
                    innerHTML += "</select>"
                innerHTML += "</div></div><br><div id=\"formula\"></div>"
                
                div.innerHTML = innerHTML
            }

            function checkFormula(value)
            {
                innerHTML = ""
                if(value == "formula")
                {
                    innerHTML += "<div class=\"row\"><div class=\"col-md-12\">"
                        innerHTML += "<label>Formula</label> &nbsp; <small> format : operator (*/+-) followed by number (e.g. / 2)</small>"
                        innerHTML += "<input type=\"text\" name=\"formula\" placeholer=\"Formula to apply to stored reading\" @if($rule->id > 0) value=\"{{ $rule->formula }}\" @endif class=\"form-control form-control-line\">"
                    innerHTML += "</div></div><br>"
                }
                document.getElementById("formula").innerHTML = innerHTML
            }

            function checkQuestions(assessment)
            {
                innerHTML = ""
                innerHTML += "<option value=\"0\">Select</option>"

                jQuery.getJSON('/getAssessmentQuestions/' + assessment, function (details) {

                    $.each(details, function (d, detail) {                        
                        
                        $.each(detail.questions, function (q, question) {

                            asking = detail.group_name + " :: " + question.question
                            innerHTML += "<option value=\"" + question.question_id + "\""
                                if(question.question_id == "{{ $rule->question_id }}")
                                {
                                    innerHTML += " selected "
                                }
                            innerHTML += ">" + asking + "</option>"
                        });
                    });
                    document.getElementById("questions").innerHTML = innerHTML
                });
            }

            function checkAnswers(question)
            {
                innerHTML = ""
                innerHTML += "<option value=\"0\">Select</option>"

                jQuery.getJSON('/getAnswerDetails/' + question, function (details) {
                    $.each(details, function (d, detail) {
                        innerHTML += "<option value=\"" + detail.id + "\""
                        if(detail.id == "{{ $rule->answer_id }}")
                        {
                            innerHTML += " selected "
                        }
                        innerHTML += ">" + detail.answer + "</option>"
                    });
                    document.getElementById("answers").innerHTML = innerHTML
                });

            }

            $(document).ready(function() {
                populateForm("{{ $rule->rule_type }}");
                checkFormula("{{ $rule->outcome }}");
                if("{{ $rule->rule_type }}" == "response")
                {
                    checkQuestions("{{ $rule->assessment_id }}");
                    checkAnswers("{{ $rule->question_id }}");
                }
                
            });
        </script>

    @endsection