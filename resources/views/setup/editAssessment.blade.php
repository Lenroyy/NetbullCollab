@extends('components.sideMenu')

    @section('content')

        <!-- Page specific CSS -->
        <link href="{{ asset('assets/plugins/bower_components/custom-select/custom-select.css') }}" rel="stylesheet" type="text/css" />
        <script src="{{ asset('assets/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
        
                
        <!-- Page Content -->
        <div class="row bg-title">
            <!-- .page title -->
            <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                <h4 class="page-title">Edit Assessement</h4>
            </div>
            <!-- /.page title -->
            <!-- .breadcrumb -->
                {!! $breadcrumbs !!}
            <!-- /.breadcrumb -->
        </div>

        <form class="form-horizontal form-material" action="/setup/saveAssessment/{{ $assessment->id }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <span class="pull-right">
                        <input type="submit" id="submit-all" name="submit" value="Save" class="btn btn-primary">
                        &nbsp;
                        <a class="btn btn-info" href="/setup/assessments">Cancel</a>
                    </span>
                </div>
            </div>
            <br>
                
            <!-- .row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <h3 class="box-title">Edit SWMS / Assessment</h3>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="white-box">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" id="name" name="name" placeholder="Name of the Assessment" @if($assessment->name != "New") value="{{ $assessment->name }}" @endif class="form-control form-control-line"> 
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Required permits</label>
                                    <select class="select2 m-b-10 select2-multiple" multiple="multiple" data-placeholder="Choose" name="permits[]" id="permits">
                                        @foreach($permits as $permit)
                                            <option value="{{ $permit->id }}" @if(in_array($permit->id, $assessmentPermits)) selected @endif>{{ $permit->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>When to run assessment</label>
                                    <select class="form-control form-control-line" name="oncePer" id="oncePer">
                                        <option value="assessment" @if($assessment->once_per == "assessment") selected @endif>Once per activity</option>
                                        <option value="day" @if($assessment->once_per == "day") selected @endif>First assessment of the day</option>
                                        <option value="site" @if($assessment->once_per == "site") selected @endif>First assessment on the site</option>
                                        <option value="zone" @if($assessment->once_per == "zone") selected @endif>First assessment in the zone</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Limit to sites</label>
                                    <select class="select2 m-b-10 select2-multiple" multiple="multiple" data-placeholder="Choose" name="sites[]" id="sites">
                                        @foreach($sites as $site)
                                            <option value="{{ $site->id }}" @if(in_array($site->id, $assessmentSites)) selected @endif>{{ $site->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Associated activities</label>
                                    <select class="select2 m-b-10 select2-multiple" multiple="multiple" data-placeholder="Choose" name="activities[]" id="tags">
                                        @foreach($activities as $act)
                                            <option value="{{ $act->id }}" @if(in_array($act->id, $assessmentActivities)) selected @endif>{{ $act->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="box-title">Structure</h3>
                                        <div id="structure" class=""></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label><div id="groupAction">New group</div></label>
                                            <input type="text" id="groupName" name="groupName" placeholder="Name of the question group" class="form-control form-control-line"> 
                                            <input type="hidden" id="groupId" name="groupId" value="0"> 
                                        </div>
                                        <div class="form-group">
                                            <label>Group parent</label>
                                            <select class="form-control form-control-line" name="groupParent" id="groupParent">
                                                <option value="0" selected>None</option>
                                                @foreach($groups as $g)
                                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <input type="submit" name="submit" class="btn btn-small btn-primary" value="Save group">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label><div id="groupQuestion">New question</div></label>
                                            <input type="text" id="questionName" name="questionName" placeholder="What is the question" class="form-control form-control-line"> 
                                            <input type="hidden" id="questionId" name="questionId" value="0"> 
                                        </div>
                                        <div class="form-group">
                                            <label>Question group</label>
                                            <select class="form-control form-control-line" name="questionGroup" id="questionGroup">
                                                @foreach($allGroups as $g)
                                                    <option value="{{ $g->id }}">{{ $g->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Answer type</label>
                                            <select class="form-control form-control-line" name="answerType" id="answerType" onChange="checkOptions(this.value)">
                                                <option value="options">Options</option>
                                                <option value="text">Free text</option>
                                            </select>
                                        </div>
                                        <div class="form-group" id="answers" style="visibility: hidden;">
                                            <label>Available options</label>
                                            <select class="select2 m-b-10 select2-multiple" multiple="multiple" data-placeholder="Choose" name="answerOptions[]" id="answerOptions">
                                                @foreach($answerOptions as $a)
                                                    <option value="{{ $a->id }}">{{ $a->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div class="table-responsive">
                                                <table class="table table-striped" id="answersTable" style="visibility: hidden;">
                                                </table>
                                            </div>
                                        </div>
                                        <input type="submit" name="submit" class="btn btn-small btn-primary" value="Save question">
                                    </div>
                                </div>
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
        <script src="{{ asset('assets/plugins/bower_components/custom-select/custom-select.min.js') }}" type="text/javascript"></script>
        <!-- Treeview Plugin JavaScript -->
        <script src="{{ asset('assets/plugins/bower_components/bootstrap-treeview-master/dist/bootstrap-treeview.min.js') }}"></script>
        <script src="{{ asset('assets/plugins/bower_components/bootstrap-treeview-master/dist/bootstrap-treeview-init.js') }}"></script>


        <script>
        $(document).ready(function() {

            $(".select2").select2();

        });

        function sleep (time) 
        {
            return new Promise((resolve) => setTimeout(resolve, time));
        }

        var structureData = [
            @foreach($questions as $structure)
            {
                text: '<i class="fa fa-folder"></i> &nbsp; {{ $structure['group_name'] }}',
                id: '{{ $structure['group_id'] }}',
                type: 'group',
                
                @if(is_array($structure['children']))
                    nodes: [
                        @foreach($structure['children'] as $child)
                        {
                            text: '<i class="fa fa-folder"></i> &nbsp; {{ $child['group_name'] }}',
                            id: '{{ $child['group_id'] }}',
                            type: 'group',
                            
                            nodes: [
                                @foreach($child['questions'] as $question)
                                {
                                    text: '<i class="fa fa-question"></i> &nbsp; {{ $question['question'] }}',
                                    id: '{{ $question['question_id'] }}',
                                    type: 'question',
                                }
                                @endforeach
                            ]
                        },
                        @endforeach
                        @foreach($structure['questions'] as $question)
                        {
                            text: '<i class="fa fa-question"></i> &nbsp; {{ $question['question'] }}',
                            id: '{{ $question['question_id'] }}',
                            type: 'question',
                        },
                        @endforeach
                    ]
                @endif
            },
            @endforeach
        ];


        $('#structure').treeview({
            levels: 1,
            selectedBackColor: "#03a9f3",
            onhoverColor: "rgba(0, 0, 0, 0.05)",
            expandIcon: 'ti-plus',
            collapseIcon: 'ti-minus',
            data: structureData
        }).on('nodeSelected', function(e, node){
            if(node.type == "group")
            {
                getGroup(node.id)
            }
            else
            {
                getQuestion(node.id)
            }
        });

        function getGroup(group)
        {
            console.log("retrieving group " + group)
            jQuery.getJSON('/getAssessmentGroup/' + group, function (list) {
                //wipe out the question settings
                document.getElementById("questionName").value = ""
                document.getElementById("questionId").value = "0"
                document.getElementById("answers").style.visibility = "hidden"
                document.getElementById("answersTable").innerHTML = ""
                document.getElementById("groupQuestion").innerHTML = "New question"
                

                //populate the group settings
                document.getElementById("groupName").value = list.name
                document.getElementById("groupId").value = list.id
                document.getElementById("groupAction").innerHTML = "Edit group"

                var val = list.parent_id
                var sel = document.getElementById("groupParent")
                var opts = sel.options;

                for (var opt, j = 0; opt = opts[j]; j++) {
                    if (opt.value == val) {
                    sel.selectedIndex = j;
                    break;
                    }
                }
            });
        }

        async function getQuestion(question)
        {
            console.log("retrieving question " + question)
            jQuery.getJSON('/getAssessmentQuestion/' + question, function (list) {
                //wipe out the group settingss
                document.getElementById("groupName").value = ""
                document.getElementById("groupId").value = "0"
                document.getElementById("groupAction").innerHTML = "New group"

                document.getElementById("groupQuestion").innerHTML = "Edit question"
                document.getElementById("questionName").value = list.question
                document.getElementById("questionId").value = list.id

                var val = list.assessments_questions_group_id
                var sel = document.getElementById("questionGroup")
                var opts = sel.options;

                for (var opt, j = 0; opt = opts[j]; j++) {
                    if (opt.value == val) {
                        sel.selectedIndex = j;
                        break;
                    }
                }

                var val = list.answer_type
                var sel = document.getElementById("answerType")
                var opts = sel.options;

                for (var opt, j = 0; opt = opts[j]; j++) {
                    if (opt.value == val) {
                        sel.selectedIndex = j;
                        break;
                    }
                }

                checkOptions(val)

                document.getElementById("answersTable").innerHTML = ""

                if(val == "options")
                {
                    var element = document.getElementById("answerOptions")
                    var opts = element.options;
                    var values = []


                    jQuery.getJSON('/getQuestionAnswers/' + question, function (options) {
                        $.each(options, function (o, option) {
                            values.push(option.option_id)
                        });
                        
                        sleep(1000).then(() => {
                            for (var i = 0; i < element.options.length; i++) 
                            {
                                var o = Number(element.options[i].value)
                                if(values.includes(o))
                                {
                                    opts[i].selected="selected"
                                }
                            }

                            

                        });
                    });

                    jQuery.getJSON('/getAnswerDetails/' + question, function (details) {
                        innerHTML = "<thead><tr><th>If</th><th>Action</th><th>Score</td><th>Comments</td><th>Jump to <small>If applicable</small></td></tr></thead>"
                        innerHTML += "<tbody>"
                            var i = 0;
                            $.each(details, function (d, detail) {
                                console.log(detail)
                                innerHTML += "<input type=\"hidden\" name=\"answerId[" + i + "]\" value=\"" + detail.id + "\">"
                                innerHTML += "<tr>"
                                    innerHTML += "<td>" + detail.answer + "</td>"
                                        innerHTML += "<td>"
                                            innerHTML += "<select class=\"form-control form-control-line\" name=\"action[" + i + "]\">"
                                                innerHTML += "<option " 
                                                    if(detail.action == 'proceed')
                                                    { 
                                                        innerHTML += " selected " 
                                                    } 
                                                    innerHTML +=" value=\"proceed\">Proceed to next question</option>"
                                                innerHTML += "<option "
                                                    if(detail.action == 'jump')
                                                    { 
                                                        innerHTML += " selected " 
                                                    }
                                                innerHTML += " value=\"jump\">Jump to question</option>"
                                                innerHTML += "<option "
                                                if(detail.action == 'end')
                                                {
                                                    innerHTML += " selected "
                                                }
                                                innerHTML += " value=\"end\">End Assessment</option>"
                                            innerHTML += "</select>"
                                        innerHTML += "</td>"
                                    innerHTML += "<td>"
                                        innerHTML += "<input type=\"text\" class=\"form-control form-control-line\""
                                        if(detail.score != null)
                                        {
                                            innerHTML += "value=\"" + detail.score + "\""
                                        }
                                    innerHTML += " name=\"score[" + i + "]\">"
                                    innerHTML += "</td>"
                                    innerHTML += "<td>"
                                        innerHTML += "<input type=\"text\" class=\"form-control form-control-line\""
                                        if(detail.comments != null)
                                        {
                                            innerHTML += " value=\"" + detail.comments + "\""
                                        }
                                        innerHTML += " name=\"comments[" + i + "]\">"
                                    innerHTML += "</td>"
                                    innerHTML += "<td>"
                                        innerHTML += "<select class=\"form-control form-control-line\" name=\"goto[" + i + "]\">"
                                            <?php
                                                foreach($questionList as $q)
                                                {
                                                    ?>
                                                        innerHTML += "<option value=\"{{ $q->id }}\""
                                                            if(detail.goto_id == "{{ $q->id }}")
                                                            {
                                                                innerHTML += " selected"
                                                            }
                                                        innerHTML += ">{{ $q->question }}</option>"
                                                    <?php
                                                }
                                            ?>
                                        innerHTML += "</select>"
                                    innerHTML += "</td>"
                                innerHTML += "</tr>"
                                i++;
                            });
                        innerHTML += "</tbody>"

                        document.getElementById("answersTable").innerHTML = innerHTML
                    });
                }
            });
        }

        function checkOptions(value)
        {
            if(value == "options")
            {
                document.getElementById("answers").style.visibility = "visible"
                document.getElementById("answersTable").style.visibility = "visible"
            }
            else
            {
                document.getElementById("answers").style.visibility = "hidden"
                document.getElementById("answersTable").style.visibility = "hidden"
            }
        }
                
        </script>

    @endsection