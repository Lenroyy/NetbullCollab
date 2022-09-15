@if($standardDisplay['profile']->theme == "dark")
    <script type="text/javascript" src="{{ asset('assets/plugins/bower_components/highcharts/themes/dark-unica.js') }}" charster="utf-8"></script>
    <script>
        fontColor = "white";
    </script>
@else
    <script>
        fontColor = "black";
    </script>
@endif

<script>

var gridster;

//Gridster initialisation and running
$(function () { 

    gridster = $(".gridster > ul").gridster({
        widget_margins: [20, 30],
        widget_base_dimensions: ['auto', 140],
        autogenerate_stylesheet: true,
        min_cols: 1,
        max_cols: 6,
        resize: {
            enabled: true
        },
        serialize_params: function($w, wgd)
        {
            return {
            id: $w.attr('id'),
            col: wgd.col,
            row: wgd.row,
            size_x: wgd.size_x,
            size_y: wgd.size_y,
            };
        },
    }).data('gridster');

    var widgets = [
        <?php 
        foreach($dashboardArray as $widget)
        {
            
            if($widget['widget'] == "url")
            {
                $content = urlDashboard($widget['content']);
            }
            elseif($widget['widget'] == "url2")
            {
                $content = urlDashboard2($widget['content']);
            }
            elseif($widget['widget'] == "url3")
            {
                $content = urlDashboard3($widget['content']);
            }
            elseif($widget['widget'] == "url4")
            {
                $content = urlDashboard4($widget['content']);
            }
            elseif($widget['widget'] == "url5")
            {
                $content = urlDashboard5($widget['content']);
            }
            elseif($widget['widget'] == "graph1")
            {
                $content = graphDashboard1($widget['id']);
            }
            elseif($widget['widget'] == "graph2")
            {
                $content = graphDashboard2($widget['id']);
            }
            elseif($widget['widget'] == "graph3")
            {
                $content = graphDashboard3($widget['id']);
            }
            elseif($widget['widget'] == "graph4")
            {
                $content = graphDashboard4($widget['id']);
            }
            elseif($widget['widget'] == "graph5")
            {
                $content = graphDashboard5($widget['id']);
            }
            elseif($widget['widget'] == "graph6")
            {
                $content = graphDashboard6($widget['id']);
            }
            elseif($widget['widget'] == "trafficLight1")
            {
                $content = trafficLight1($widget['id']);
            }
            elseif($widget['widget'] == "trafficLight2")
            {
                $content = trafficLight2($widget['id']);
            }
            elseif($widget['widget'] == "trafficLight3")
            {
                $content = trafficLight3($widget['id']);
            }
            elseif($widget['widget'] == "trafficLight4")
            {
                $content = trafficLight4($widget['id']);
            }
            elseif($widget['widget'] == "trafficLight5")
            {
                $content = trafficLight5($widget['id']);
            }
            elseif($widget['widget'] == "trafficLight6")
            {
                $content = trafficLight6($widget['id']);
            }
            elseif($widget['widget'] == "mySites")
            {
                $content = mySites();
            }
            elseif($widget['widget'] == "builderSites")
            {
                $content = builderSites();
            }
            elseif($widget['widget'] == "myExposures")
            {
                $content = myExposures();
            }
            elseif($widget['widget'] == "Welcome")
            {
                $content = welcome();
            }
            elseif($widget['widget'] == "siteParticipation")
            {
                $content = siteParticipation();
            }
            else
            {
                $content = dash4();
            }
            
            

            //now go and add the value to the widgets array to pass to Gridster
            ?>
            ['<li class="gs-w card card-content card-body" id="<?php echo $widget['id'];  ?>"><span class="gs-resize-handle gs-resize-handle-both"></span><?php echo $content; ?></li>', <?php echo $widget['size_x'];  ?>, <?php echo $widget['size_y'];  ?>, <?php echo $widget['col'];  ?>, <?php echo $widget['row'];  ?>],
            <?php
        }
        ?>
    ];

    $.each(widgets, function (i, widget) {
        gridster.add_widget.apply(gridster, widget)
    });
    
    $('.js-remove').on('click', '#js-remove', function () {
        var d = gridster.remove_widget( $(this).closest('li') );
        console.log(d)
    })
    
    $('#grd').on('click', '#delItem', function () {
        
        var d = gridster.remove_widget( $(this).closest('li') );

        var s = gridster.serialize();
        console.log(JSON.stringify(s))
        
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type' : 'application/json'
            }
        });

        $.ajax({
            type:'GET',
            url:'/saveWidgetLayout/{{ $dashboardType }}/{{ $moduleID }}',
            data:{
                widgets:JSON.stringify(s), 
            },
            success: function(response){ // What to do if we succeed
                console.log("Success response is " + response); 
            },
            error: function(response){
                alert('Error response is ' + response);
            }
        });
    })
    
    $('.js-seralize').on('click', function () {
        var s = gridster.serialize();
        console.log(JSON.stringify(s))
        
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type' : 'application/json'
            }
        });

        $.ajax({
            type:'GET',
            url:'/saveWidgetLayout/{{ $dashboardType }}/{{ $moduleID }}',
            data:{
                widgets:JSON.stringify(s), 
            },
            success: function(response){ // What to do if we succeed
                console.log("Success response is " + response); 
                alert("Saved dashboard layout");
            },
            error: function(response){
                alert('Error response is ' + response);
            }
        });
    })
});

function sleep (time) {
  return new Promise((resolve) => setTimeout(resolve, time));
}

spinnerHTML = "<div class=\"row\"><div class=\"col-md-12\" style=\"text-align: center;\"><img src=\"/assets/images/stationary/spinnerBlob.gif\" width=\"200px\"></div></div>"

sleep(300).then(() => {

    if(document.getElementById("urlDashboard"))
    {
        contentDiv = document.getElementById("urlContent")
        content = document.getElementById("urlDashboardContent").value
        contentDiv.innerHTML = spinnerHTML
        console.log(content)

        /*
            Load up the URL in an iFrame inside the dashboard widget
        */
        $("#urlDashboardContent").show();
        $('#urlContent').attr('src', content);       

    }

    if(document.getElementById("urlDashboard2"))
    {
        contentDiv = document.getElementById("urlContent2")
        content = document.getElementById("urlDashboardContent2").value
        contentDiv.innerHTML = spinnerHTML
        console.log(content)

        /*
            Load up the URL in an iFrame inside the dashboard widget
        */
        $("#urlDashboardContent2").show();
        $('#urlContent2').attr('src', content);       

    }

    if(document.getElementById("urlDashboard3"))
    {
        console.log("Found URL Dashboard 3")
        contentDiv = document.getElementById("urlContent3")
        content = document.getElementById("urlDashboardContent3").value
        contentDiv.innerHTML = spinnerHTML
        console.log(content)

        /*
            Load up the URL in an iFrame inside the dashboard widget
        */
        $("#urlDashboardContent3").show();
        $('#urlContent3').attr('src', content);       

    }

    if(document.getElementById("urlDashboard4"))
    {
        contentDiv = document.getElementById("urlContent4")
        content = document.getElementById("urlDashboardContent4").value
        contentDiv.innerHTML = spinnerHTML
        console.log(content)

        /*
            Load up the URL in an iFrame inside the dashboard widget
        */
        $("#urlDashboardContent4").show();
        $('#urlContent4').attr('src', content);       

    }

    if(document.getElementById("urlDashboard5"))
    {
        contentDiv = document.getElementById("urlContent5")
        content = document.getElementById("urlDashboardContent5").value
        contentDiv.innerHTML = spinnerHTML
        console.log(content)

        /*
            Load up the URL in an iFrame inside the dashboard widget
        */
        $("#urlDashboardContent5").show();
        $('#urlContent5').attr('src', content);       

    }

    if(document.getElementById("mySites"))
    {
        msDiv = document.getElementById("mySitesContent")
        msDiv.innerHTML = spinnerHTML

        innerHTML = "<div class=\"table-responsive\">"
            innerHTML += "<table class=\"table table-striped table-hover\">"
                innerHTML += "<thead><tr><th>Site</th><th>Status</th></thead>"
                innerHTML += "<tbody>"
                    jQuery.getJSON('/getMySites', function (list) {
                        $.each(list, function (n, item) {
                            innerHTML += "<tr>"
                                innerHTML += "<td><a href=\"/editSite/" + item.siteID + "\" style=\"color: " + fontColor + ";\">" + item.site + "</a></td>"
                                innerHTML += "<td>"
                                    if(item.outcome == "ok")
                                    {
                                        innerHTML += "<span class=\"smallGreenDot\">&nbsp;</span> &nbsp; "
                                        innerHTML += item.outcome + "</td>"
                                    }
                                    if(item.outcome == "not ok")
                                    {
                                        innerHTML += "<span class=\"smallRedDot\">&nbsp;</span> &nbsp; "
                                        innerHTML += "<a href=\"/logActivity/" + item.history + "\" style=\"color: " + fontColor + ";\">" + item.outcome + "</a></td>"
                                    }
                                    if(item.outcome == "monitor")
                                    {
                                        innerHTML += "<span class=\"smallOrangeDot\">&nbsp;</span> &nbsp; "
                                        innerHTML += "<a href=\"/logActivity/" + item.history + "\" style=\"color: " + fontColor + ";\">" + item.outcome + "</a></td>"
                                    }
                            innerHTML += "</tr>"
                        });
                        innerHTML += "</tbody>"
                    innerHTML += "</table>"
                innerHTML += "</div>"

            msDiv.innerHTML = innerHTML
        });
    }

    if(document.getElementById("builderSites"))
    {
        mbDiv = document.getElementById("builderSitesContent")
        mbDiv.innerHTML = spinnerHTML

        mbInnerHTML = "<div class=\"table-responsive\">"
            mbInnerHTML += "<table class=\"table table-striped table-hover\">"
                mbInnerHTML += "<thead><tr><th>Site</th><th>Status</th></thead>"
                    mbInnerHTML += "<tbody>"
                    jQuery.getJSON('/getBuilderSites', function (list) {
                        $.each(list, function (n, item) {
                            mbInnerHTML += "<tr>"
                                mbInnerHTML += "<td><a href=\"/editSite/" + item.siteID + "\" style=\"color: " + fontColor + ";\">" + item.site + "</a></td>"
                                mbInnerHTML += "<td>"
                                    if(item.outcome == "ok")
                                    {
                                        mbInnerHTML += "<span class=\"smallGreenDot\">&nbsp;</span> &nbsp; "
                                        mbInnerHTML += item.outcome + "</td>"
                                    }
                                    if(item.outcome == "not ok")
                                    {
                                        mbInnerHTML += "<span class=\"smallRedDot\">&nbsp;</span> &nbsp; "
                                        mbInnerHTML += "<a href=\"/logActivity/" + item.history + "\" style=\"color: " + fontColor + ";\">" + item.outcome + "</a></td>"
                                    }
                                    if(item.outcome == "monitor")
                                    {
                                        mbInnerHTML += "<span class=\"smallOrangeDot\">&nbsp;</span> &nbsp; "
                                        mbInnerHTML += "<a href=\"/logActivity/" + item.history + "\" style=\"color: " + fontColor + ";\">" + item.outcome + "</a></td>"
                                    }
                                    mbInnerHTML += "</tr>"
                        });
                        mbInnerHTML += "</tbody>"
                    mbInnerHTML += "</table>"
                mbInnerHTML += "</div>"

            mbDiv.innerHTML = mbInnerHTML
        });
    }

    if(document.getElementById("myExposures"))
    {
        meDiv = document.getElementById("myExposures")
        meDiv.innerHTML = spinnerHTML

        meInnerHTML = "<div class=\"table-responsive\">"
            meInnerHTML += "<table class=\"table table-striped table-hover\">"
                meInnerHTML += "<thead><tr><th>Site</th><th>Status</th></thead>"
                    meInnerHTML += "<tbody>"
                    jQuery.getJSON('/getMyExposures', function (list) {
                        $.each(list, function (n, item) {
                            meInnerHTML += "<tr style=\"cursor: pointer;\" onClick=\"window.location.href='/exposureDetail/{{ $standardDisplay['profile']->id }}/" + item.type + "'\">"
                                meInnerHTML += "<td style=\"color: " + fontColor + ";\">" + item.type + "</td>"
                                meInnerHTML += "<td>"
                                    if(item.outcome == "ok")
                                    {
                                        meInnerHTML += "<span class=\"smallGreenDot\">&nbsp;</span> &nbsp; "
                                    }
                                    if(item.outcome == "not ok")
                                    {
                                        meInnerHTML += "<span class=\"smallRedDot\">&nbsp;</span> &nbsp; "
                                    }
                                    if(item.outcome == "monitor")
                                    {
                                        meInnerHTML += "<span class=\"smallOrangeDot\">&nbsp;</span> &nbsp; "
                                    }
                                    if(item.outcome == "unknown")
                                    {
                                        meInnerHTML += "<span class=\"smallOrangeDot\">&nbsp;</span> &nbsp; "   
                                    }
                                    meInnerHTML += item.outcome
                                    meInnerHTML += "</td></tr>"
                        });
                        meInnerHTML += "</tbody>"
                    meInnerHTML += "</table>"
                meInnerHTML += "</div>"

            meDiv.innerHTML = meInnerHTML
        });
    }

    if(document.getElementById("welcome"))
    {
        wDiv = document.getElementById("welcome")
        wDiv.innerHTML = spinnerHTML

        meInnerHTML = "Welcome to Nextrack.<br><br>To get you started with setting up your dashboard, you can just remove this widget and start adding widgets with the blue button in the top right of this page OR select from the below to use a default dashboard."
        meInnerHTML += "<br><br>Be sure to hit the save button when you are finished playing around."
        meInnerHTML += "<br><br>"
        meInnerHTML += "<a href=\"/defaultDashboard/worker\" class=\"btn btn-primary\" style=\"color: " + fontColor + "\">Worker</a> &nbsp; "
        meInnerHTML += "<a href=\"/defaultDashboard/project\" class=\"btn btn-primary\" style=\"color: " + fontColor + "\">Project manager</a> &nbsp; "
        meInnerHTML += "<a href=\"/defaultDashboard/builder\" class=\"btn btn-primary\" style=\"color: " + fontColor + "\">Builder</a> &nbsp; "
        meInnerHTML += "<a href=\"/defaultDashboard/contractor\" class=\"btn btn-primary\" style=\"color: " + fontColor + "\">Contractor</a> &nbsp; "
        meInnerHTML += "<a href=\"/defaultDashboard/hygienist\" class=\"btn btn-primary\" style=\"color: " + fontColor + "\">Hygienist</a> &nbsp; "

        wDiv.innerHTML = meInnerHTML
        
    } 

    if(document.getElementById("graphDashboard1"))
    {
        g1Div = document.getElementById("graphContent1")
        g1Div.innerHTML = spinnerHTML
        
        settings = document.getElementById("graphDashboardContent1").value

        jQuery.getJSON('/getDashboardSettings/' + settings, function (details) {
            console.log(details)

            graphType = details.graphType
            period = details.period
            sensorID = details.sensor
            reading = details.readingType
            hazardContent = details.hazardContent
            hazardContent = hazardContent/100;
            siteID = details.module_id

        
            /*
                Prepare the graph and then go and fetch the data
            */
            
            var g1KeyOptions = {
                chart: {
                    renderTo: g1Div,
                    type: graphType,
                    height: '50%',
                },
                title: {
                    text: ''
                },
                tooltip: {
                    pointFormat: '{point.y}<br>{series.name}'
                },
                xAxis:[
                    {
                        type: 'datetime',
                        zoomEnabled: 'true',
                }],
                series: [],
        
                credits: {
                    enabled: false
                }
            };

            jQuery.getJSON('/identifySensor/' + sensorID + '/' + encodeURI(reading), function (list) {
                dashboardName = list.site + " : " + list.map + " " + list.zone + " : " + list.control + " : " + list.device + " : " + list.reading
                document.getElementById("g1Heading").innerHTML = dashboardName
                name1 = list.reading

                jQuery.getJSON('/getThingsboardGraph/' + sensorID + '/' + period + '/' + encodeURI(name1) + '/' + siteID, function (list) {
                    thisSeries = {};
                    var data =[]
                        
                    thisSeries.name = name1
                    $.each(list, function (n, item) {
                        values = []
                        
                        thisReading = parseFloat(item.reading)
                        values.push(item.timestamp)
                        values.push(thisReading*hazardContent)
                        
                        data.push(values)
                        
                    });
                    thisSeries.data = data;
                    g1KeyOptions.series.push(thisSeries);

                    var chart = new Highcharts.Chart(g1KeyOptions);
                });
            });
        });
        
        

        /*

        pDivider = settings.search(":")
        pStart = pDivider+1
        sDivider = settings.search("@")
        sStart = sDivider+1      
        tDivider = settings.search("-")
        tStart = tDivider+1
        hDivider = settings.search("#")
        hStart = hDivider+1
        
        graphType = settings.substring(0, pDivider);
        period = settings.substring(pStart, sDivider);
        sensorID = settings.substring(sStart, tDivider);
        reading = settings.substring(tStart, hDivider);
        hazardContent = settings.substring(hStart, 100);

        */


        
                
        
        
    }

    if(document.getElementById("graphDashboard2"))
    {
        g2Div = document.getElementById("graphContent2")
        g2Div.innerHTML = spinnerHTML
        
        settings2 = document.getElementById("graphDashboardContent2").value

        jQuery.getJSON('/getDashboardSettings/' + settings2, function (details2) {
            graphType2 = details2.graphType
            period2 = details2.period
            sensorID2 = details2.sensor
            reading2 = details2.readingType
            hazardContent2 = details2.hazardContent
            hazardContent2 = hazardContent2/100;
            siteID2 = details2.module_id

            /*
                Prepare the graph and then go and fetch the data
            */
            
            var g2KeyOptions = {
                chart: {
                    renderTo: g2Div,
                    type: graphType2,
                    height: '50%',
                },
                title: {
                    text: ''
                },
                tooltip: {
                    pointFormat: '{point.y}<br>{series.name}'
                },
                xAxis:[
                    {
                        type: 'datetime',
                        zoomEnabled: 'true',
                }],
                series: [],
        
                credits: {
                    enabled: false
                }
            };

            jQuery.getJSON('/identifySensor/' + sensorID2 + '/' + encodeURI(reading2), function (list) {
                dashboardName2 = list.site + " : " + list.map + " " + list.zone + " : " + list.control + " : " + list.device + " : " + list.reading
                document.getElementById("g2Heading").innerHTML = dashboardName2
                name2 = list.reading

                jQuery.getJSON('/getThingsboardGraph/' + sensorID2 + '/' + period2 + '/' + encodeURI(name2) + '/' + siteID2, function (list) {
                    thisSeries = {};
                    var data =[]
                        
                    thisSeries.name = name2
                    $.each(list, function (n, item) {
                        values = []
                        
                        thisReading = parseFloat(item.reading)
                        values.push(item.timestamp)
                        values.push(thisReading*hazardContent2)
                        
                        data.push(values)
                        
                    });
                    thisSeries.data = data;
                    g2KeyOptions.series.push(thisSeries);

                    var chart = new Highcharts.Chart(g2KeyOptions);
                });
            });
        });

    }

    if(document.getElementById("graphDashboard3"))
    {
        g3Div = document.getElementById("graphContent3")
        g3Div.innerHTML = spinnerHTML
        
        settings3 = document.getElementById("graphDashboardContent3").value

        jQuery.getJSON('/getDashboardSettings/' + settings3, function (details3) {
            graphType3 = details3.graphType
            period3 = details3.period
            sensorID3 = details3.sensor
            reading3 = details3.readingType
            hazardContent3 = details3.hazardContent
            hazardContent3 = hazardContent3/100;
            siteID3 = details3.module_id

            /*
                Prepare the graph and then go and fetch the data
            */
            
            var g3KeyOptions = {
                chart: {
                    renderTo: g3Div,
                    type: graphType3,
                    height: '50%',
                },
                title: {
                    text: ''
                },
                tooltip: {
                    pointFormat: '{point.y}<br>{series.name}'
                },
                xAxis:[
                    {
                        type: 'datetime',
                        zoomEnabled: 'true',
                }],
                series: [],
        
                credits: {
                    enabled: false
                }
            };

            jQuery.getJSON('/identifySensor/' + sensorID3 + '/' + encodeURI(reading3), function (list) {
                dashboardName3 = list.site + " : " + list.map + " " + list.zone + " : " + list.control + " : " + list.device + " : " + list.reading
                document.getElementById("g3Heading").innerHTML = dashboardName3
                name3 = list.reading

                jQuery.getJSON('/getThingsboardGraph/' + sensorID3 + '/' + period3 + '/' + encodeURI(name3) + '/' + siteID3, function (list) {
                    thisSeries = {};
                    var data =[]
                        
                    thisSeries.name = name3
                    $.each(list, function (n, item) {
                        values = []
                        
                        thisReading = parseFloat(item.reading)
                        values.push(item.timestamp)
                        values.push(thisReading*hazardContent3)
                        
                        data.push(values)
                        
                    });
                    thisSeries.data = data;
                    g3KeyOptions.series.push(thisSeries);

                    var chart = new Highcharts.Chart(g3KeyOptions);
                });
            });
        });
    }

    if(document.getElementById("graphDashboard4"))
    {
        g4Div = document.getElementById("graphContent4")
        g4Div.innerHTML = spinnerHTML
        
        settings4 = document.getElementById("graphDashboardContent4").value

        jQuery.getJSON('/getDashboardSettings/' + settings4, function (details4) {
            graphType4 = details4.graphType
            period4 = details4.period
            sensorID4 = details4.sensor
            reading4 = details4.readingType
            hazardContent4 = details4.hazardContent
            hazardContent4 = hazardContent4/100;
            siteID4 = details4.module_id

            /*
                Prepare the graph and then go and fetch the data
            */
            
            var g4KeyOptions = {
                chart: {
                    renderTo: g4Div,
                    type: graphType4,
                    height: '50%',
                },
                title: {
                    text: ''
                },
                tooltip: {
                    pointFormat: '{point.y}<br>{series.name}'
                },
                xAxis:[
                    {
                        type: 'datetime',
                        zoomEnabled: 'true',
                }],
                series: [],
        
                credits: {
                    enabled: false
                }
            };

            jQuery.getJSON('/identifySensor/' + sensorID4 + '/' + encodeURI(reading4), function (list) {
                dashboardName4 = list.site + " : " + list.map + " " + list.zone + " : " + list.control + " : " + list.device + " : " + list.reading
                document.getElementById("g4Heading").innerHTML = dashboardName4
                name4 = list.reading

                jQuery.getJSON('/getThingsboardGraph/' + sensorID4 + '/' + period4 + '/' + encodeURI(name4) + '/' + siteID4, function (list) {
                    thisSeries = {};
                    var data =[]
                        
                    thisSeries.name = name4
                    $.each(list, function (n, item) {
                        values = []
                        
                        thisReading = parseFloat(item.reading)
                        values.push(item.timestamp)
                        values.push(thisReading*hazardContent4)
                        
                        data.push(values)
                        
                    });
                    thisSeries.data = data;
                    g4KeyOptions.series.push(thisSeries);

                    var chart = new Highcharts.Chart(g4KeyOptions);
                });
            });
        });
    }

    if(document.getElementById("graphDashboard5"))
    {
        g5Div = document.getElementById("graphContent5")
        g5Div.innerHTML = spinnerHTML
        
        settings5 = document.getElementById("graphDashboardContent5").value

        jQuery.getJSON('/getDashboardSettings/' + settings5, function (details5) {
            graphType5 = details5.graphType
            period5 = details5.period
            sensorID5 = details5.sensor
            reading5 = details5.readingType
            hazardContent5 = details5.hazardContent
            hazardContent5 = hazardContent5/100;
            siteID5 = details5.module_id


            /*
                Prepare the graph and then go and fetch the data
            */
            
            var g5KeyOptions = {
                chart: {
                    renderTo: g5Div,
                    type: graphType5,
                    height: '50%',
                },
                title: {
                    text: ''
                },
                tooltip: {
                    pointFormat: '{point.y}<br>{series.name}'
                },
                xAxis:[
                    {
                        type: 'datetime',
                        zoomEnabled: 'true',
                }],
                series: [],
        
                credits: {
                    enabled: false
                }
            };

            jQuery.getJSON('/identifySensor/' + sensorID5 + '/' + encodeURI(reading5), function (list) {
                dashboardName5 = list.site + " : " + list.map + " " + list.zone + " : " + list.control + " : " + list.device + " : " + list.reading
                document.getElementById("g5Heading").innerHTML = dashboardName5
                name5 = list.reading

                jQuery.getJSON('/getThingsboardGraph/' + sensorID5 + '/' + period5 + '/' + encodeURI(name5) + '/' + siteID5, function (list) {
                    thisSeries = {};
                    var data =[]
                        
                    thisSeries.name = name5
                    $.each(list, function (n, item) {
                        values = []
                        
                        thisReading = parseFloat(item.reading)
                        values.push(item.timestamp)
                        values.push(thisReading*hazardContent5)
                        
                        data.push(values)
                        
                    });
                    thisSeries.data = data;
                    g5KeyOptions.series.push(thisSeries);

                    var chart = new Highcharts.Chart(g5KeyOptions);
                });
            });
        });
    }

    if(document.getElementById("graphDashboard6"))
    {
        g6Div = document.getElementById("graphContent6")
        g6Div.innerHTML = spinnerHTML
        
        settings6 = document.getElementById("graphDashboardContent6").value

        jQuery.getJSON('/getDashboardSettings/' + settings6, function (details6) {
            graphType6 = details6.graphType
            period6 = details6.period
            sensorID6 = details6.sensor
            reading6 = details6.readingType
            hazardContent6 = details6.hazardContent
            hazardContent6 = hazardContent6/100;
            siteID6 = details6.module_id

            /*
                Prepare the graph and then go and fetch the data
            */
            
            var g6KeyOptions = {
                chart: {
                    renderTo: g6Div,
                    type: graphType6,
                    height: '50%',
                },
                title: {
                    text: ''
                },
                tooltip: {
                    pointFormat: '{point.y}<br>{series.name}'
                },
                xAxis:[
                    {
                        type: 'datetime',
                        zoomEnabled: 'true',
                }],
                series: [],
        
                credits: {
                    enabled: false
                }
            };

            jQuery.getJSON('/identifySensor/' + sensorID6 + '/' + encodeURI(reading6), function (list) {
                dashboardName6 = list.site + " : " + list.map + " " + list.zone + " : " + list.control + " : " + list.device + " : " + list.reading
                document.getElementById("g6Heading").innerHTML = dashboardName6
                name6 = list.reading

                jQuery.getJSON('/getThingsboardGraph/' + sensorID6 + '/' + period6 + '/' + encodeURI(name6) + '/' + siteID6, function (list) {
                    thisSeries = {};
                    var data =[]
                        
                    thisSeries.name = name6
                    $.each(list, function (n, item) {
                        values = []
                        
                        thisReading = parseFloat(item.reading)
                        values.push(item.timestamp)
                        values.push(thisReading*hazardContent6)
                        
                        data.push(values)
                        
                    });
                    thisSeries.data = data;
                    g6KeyOptions.series.push(thisSeries);

                    var chart = new Highcharts.Chart(g6KeyOptions);
                });
            });
        });
    }

    if(document.getElementById("trafficLight1"))
    {
        t1Div = document.getElementById("trafficLightContent1")
        t1Div.innerHTML = spinnerHTML
        
        t1Settings = document.getElementById("trafficLightSettings1").value

        jQuery.getJSON('/getDashboardSettings/' + t1Settings, function (t1Details) {
            jQuery.getJSON('/identifySensor/' + t1Details.sensor + '/' + encodeURI(t1Details.readingType), function (t1List) {
                
                t1Name = t1List.site + " : " + t1List.map + " " + t1List.zone + " : " + t1List.device + " : " + t1List.reading
                document.getElementById("t1Heading").innerHTML = t1Name
                t1Name = t1List.reading

                jQuery.getJSON('/getTrafficLight/' + t1Details.sensor + '/' + t1Details.average + '/' + encodeURI(t1Name), function (t1Value) {

                    outcome = "largeWhiteDot";
                    outcomeComment = "unknown"
                    console.log(t1Value.value)

                    if(t1Details.type == "standard")
                    {
                        console.log("Standard")
                        if(t1Details.greenStart < t1Value.value && t1Details.greenEnd > t1Value.value)
                        {
                            outcome = "largeGreenDot";
                            outcomeComment = "Ok"
                        }
                        else if(t1Details.orangeStart < t1Value.value && t1Details.orangeEnd > t1Value.value)
                        {
                            outcome = "largeOrangeDot"
                            outcomeComment = "Monitor"
                        }
                        else if(t1Details.redStart < t1Value.value)
                        {
                            outcome = "largeRedDot"
                            outcomeComment = "Not Ok"
                        }
                    }
                    else
                    {
                        console.log("Reverse")
                        if(t1Details.redStart < t1Value.value && t1Details.redEnd > t1Value.value)
                        {
                            outcome = "largeRedDot";
                            outcomeComment = "No Ok"
                        }
                        else if(t1Details.orangeStart < t1Value.value && t1Details.orangeEnd > t1Value.value)
                        {
                            outcome = "largeOrangeDot"
                            outcomeComment = "Monitor"
                        }
                        else if(t1Details.greenStart < t1Value.value)
                        {
                            outcome = "largeGreenDot"
                            outcomeComment = "Ok"
                        }
                    }

                    t1InnerHTML = "<div class=\"col-md-12\" style=\"text-align: center;\">"
                    t1InnerHTML += "<br><br><span class=\"" + outcome + "\">&nbsp;</span><br><br>" + outcomeComment
                    t1InnerHTML += "</div>"

                    t1Div.innerHTML = t1InnerHTML
                });
            });
        });
    }

    if(document.getElementById("trafficLight2"))
    {
        t2Div = document.getElementById("trafficLightContent2")
        t2Div.innerHTML = spinnerHTML
        
        t2Settings = document.getElementById("trafficLightSettings2").value

        jQuery.getJSON('/getDashboardSettings/' + t2Settings, function (t2Details) {
            jQuery.getJSON('/identifySensor/' + t2Details.sensor + '/' + encodeURI(t2Details.readingType), function (t2List) {
                
                t2Name = t2List.site + " : " + t2List.map + " " + t2List.zone + " : " + t2List.control + " : " + t2List.device + " : " + t2List.reading
                document.getElementById("t2Heading").innerHTML = t2Name
                t2Name = t2List.reading

                jQuery.getJSON('/getTrafficLight/' + t2Details.sensor + '/' + t2Details.average + '/' + encodeURI(t2Name), function (t2Value) {

                    outcome2 = "largeWhiteDot";
                    outcomeComment2 = "unknown"
                    console.log(t2Value.value)


                    if(t2Details.type == "standard")
                    {
                        if(t2Details.greenStart < t2Value.value && t2Details.greenEnd > t2Value.value)
                        {
                            outcome2 = "largeGreenDot";
                            outcomeComment2 = "Ok"
                        }
                        else if(t2Details.orangeStart < t2Value.value && t2Details.orangeEnd > t2Value.value)
                        {
                            outcome2 = "largeOrangeDot"
                            outcomeComment2 = "Monitor"
                        }
                        else if(t2Details.redStart < t2Value.value)
                        {
                            outcome2 = "largeRedDot"
                            outcomeComment2 = "Not Ok"
                        }
                    }
                    else
                    {
                        console.log("Reverse")
                        if(t2Details.redStart < t2Value.value && t2Details.redEnd > t2Value.value)
                        {
                            outcome2 = "largeRedDot";
                            outcomeComment2 = "No Ok"
                        }
                        else if(t2Details.orangeStart < t2Value.value && t2Details.orangeEnd > t2Value.value)
                        {
                            outcome2 = "largeOrangeDot"
                            outcomeComment2 = "Monitor"
                        }
                        else if(t2Details.greenStart < t2Value.value)
                        {
                            outcome2 = "largeGreenDot"
                            outcomeComment2 = "Ok"
                        }
                    }

                    t2InnerHTML = "<div class=\"col-md-12\" style=\"text-align: center;\">"
                    t2InnerHTML += "<br><br><span class=\"" + outcome2 + "\">&nbsp;</span><br><br>" + outcomeComment2
                    t2InnerHTML += "</div>"

                    t2Div.innerHTML = t2InnerHTML
                });
            });
        });
    }

    if(document.getElementById("trafficLight3"))
    {
        t3Div = document.getElementById("trafficLightContent3")
        t3Div.innerHTML = spinnerHTML
        
        t3Settings = document.getElementById("trafficLightSettings3").value

        jQuery.getJSON('/getDashboardSettings/' + t3Settings, function (t3Details) {
            jQuery.getJSON('/identifySensor/' + t3Details.sensor + '/' + encodeURI(t3Details.readingType), function (t3List) {
                
                t3Name = t3List.site + " : " + t3List.map + " " + t3List.zone + " : " + t3List.control + " : " + t3List.device + " : " + t3List.reading
                document.getElementById("t3Heading").innerHTML = t3Name
                t3Name = t3List.reading

                jQuery.getJSON('/getTrafficLight/' + t3Details.sensor + '/' + t3Details.average + '/' + encodeURI(t3Name), function (t3Value) {

                    outcome3 = "largeWhiteDot";
                    outcomeComment3 = "unknown"
                    console.log(t3Value.value)

                    if(t3Details.type == "standard")
                    {
                        if(t3Details.greenStart < t3Value.value && t3Details.greenEnd > t3Value.value)
                        {
                            outcome3 = "largeGreenDot";
                            outcomeComment3 = "Ok"
                        }
                        else if(t3Details.orangeStart < t3Value.value && t3Details.orangeEnd > t3Value.value)
                        {
                            outcome3 = "largeOrangeDot"
                            outcomeComment3 = "Monitor"
                        }
                        else if(t3Details.redStart < t3Value.value)
                        {
                            outcome3 = "largeRedDot"
                            outcomeComment3 = "Not Ok"
                        }
                    }
                    else
                    {
                        console.log("Reverse")
                        if(t3Details.redStart < t3Value.value && t3Details.redEnd > t3Value.value)
                        {
                            outcome3 = "largeRedDot";
                            outcomeComment3 = "No Ok"
                        }
                        else if(t3Details.orangeStart < t3Value.value && t3Details.orangeEnd > t3Value.value)
                        {
                            outcome3 = "largeOrangeDot"
                            outcomeComment3 = "Monitor"
                        }
                        else if(t3Details.greenStart < t3Value.value)
                        {
                            outcome3 = "largeGreenDot"
                            outcomeComment3 = "Ok"
                        }
                    }

                    t3InnerHTML = "<div class=\"col-md-12\" style=\"text-align: center;\">"
                    t3InnerHTML += "<br><br><span class=\"" + outcome3 + "\">&nbsp;</span><br><br>" + outcomeComment3
                    t3InnerHTML += "</div>"

                    t3Div.innerHTML = t3InnerHTML
                });
            });
        });
    }

    if(document.getElementById("trafficLight4"))
    {
        t4Div = document.getElementById("trafficLightContent4")
        t4Div.innerHTML = spinnerHTML
        
        t4Settings = document.getElementById("trafficLightSettings4").value

        jQuery.getJSON('/getDashboardSettings/' + t4Settings, function (t4Details) {
            jQuery.getJSON('/identifySensor/' + t4Details.sensor + '/' + t4Details.readingType, function (t4List) {
                
                t4Name = t4List.site + " : " + t4List.map + " " + t4List.zone + " : " + t4List.control + " : " + t4List.device + " : " + t4List.reading
                document.getElementById("t4Heading").innerHTML = t4Name
                t4Name = t4List.reading

                jQuery.getJSON('/getTrafficLight/' + t4Details.sensor + '/' + t4Details.average + '/' + t4Name, function (t4Value) {

                    outcome4 = "largeWhiteDot";
                    outcomeComment4 = "unknown"
                    console.log(t4Value.value)

                    if(t4Details.type == "standard")
                    {
                        if(t4Details.greenStart < t4Value.value && t4Details.greenEnd > t4Value.value)
                        {
                            outcome4 = "largeGreenDot";
                            outcomeComment4 = "Ok"
                        }
                        else if(t4Details.orangeStart < t4Value.value && t4Details.orangeEnd > t4Value.value)
                        {
                            outcome4 = "largeOrangeDot"
                            outcomeComment4 = "Monitor"
                        }
                        else if(t4Details.redStart < t4Value.value)
                        {
                            outcome4 = "largeRedDot"
                            outcomeComment4 = "Not Ok"
                        }
                    }
                    else
                    {
                        console.log("Reverse")
                        if(t4Details.redStart < t4Value.value && t4Details.redEnd > t4Value.value)
                        {
                            outcome4 = "largeRedDot";
                            outcomeComment4 = "No Ok"
                        }
                        else if(t4Details.orangeStart < t4Value.value && t4Details.orangeEnd > t4Value.value)
                        {
                            outcome4 = "largeOrangeDot"
                            outcomeComment4 = "Monitor"
                        }
                        else if(t4Details.greenStart < t4Value.value)
                        {
                            outcome4 = "largeGreenDot"
                            outcomeComment4 = "Ok"
                        }
                    }

                    t4InnerHTML = "<div class=\"col-md-12\" style=\"text-align: center;\">"
                    t4InnerHTML += "<br><br><span class=\"" + outcome4 + "\">&nbsp;</span><br><br>" + outcomeComment4
                    t4InnerHTML += "</div>"

                    t4Div.innerHTML = t4InnerHTML
                });
            });
        });
    }

    if(document.getElementById("trafficLight5"))
    {
        t5Div = document.getElementById("trafficLightContent5")
        t5Div.innerHTML = spinnerHTML
        
        t5Settings = document.getElementById("trafficLightSettings5").value

        jQuery.getJSON('/getDashboardSettings/' + t5Settings, function (t5Details) {
            jQuery.getJSON('/identifySensor/' + t5List.map + " " + t5List.zone + " : " + t5Details.sensor + '/' + encodeURI(t5Details.readingType), function (t5List) {
                
                t5Name = t5List.site + " : " + t5List.control + " : " + t5List.device + " : " + t5List.reading
                document.getElementById("t5Heading").innerHTML = t5Name
                t5Name = t5List.reading

                jQuery.getJSON('/getTrafficLight/' + t5Details.sensor + '/' + t5Details.average + '/' + encodeURI(t5Name), function (t5Value) {

                    outcome5 = "largeWhiteDot";
                    outcomeComment5 = "unknown"
                    console.log(t5Value.value)

                    if(t5Details.type == "standard")
                    {
                        if(t5Details.greenStart < t5Value.value && t5Details.greenEnd > t5Value.value)
                        {
                            outcome5 = "largeGreenDot";
                            outcomeComment5 = "Ok"
                        }
                        else if(t5Details.orangeStart < t5Value.value && t5Details.orangeEnd > t5Value.value)
                        {
                            outcome5 = "largeOrangeDot"
                            outcomeComment5 = "Monitor"
                        }
                        else if(t5Details.redStart < t5Value.value)
                        {
                            outcome5 = "largeRedDot"
                            outcomeComment5 = "Not Ok"
                        }
                    }
                    else
                    {
                        console.log("Reverse")
                        if(t5Details.redStart < t5Value.value && t5Details.redEnd > t5Value.value)
                        {
                            outcome5 = "largeRedDot";
                            outcomeComment5 = "No Ok"
                        }
                        else if(t5Details.orangeStart < t5Value.value && t5Details.orangeEnd > t5Value.value)
                        {
                            outcome5 = "largeOrangeDot"
                            outcomeComment5 = "Monitor"
                        }
                        else if(t5Details.greenStart < t5Value.value)
                        {
                            outcome5 = "largeGreenDot"
                            outcomeComment5 = "Ok"
                        }
                    }

                    t5InnerHTML = "<div class=\"col-md-12\" style=\"text-align: center;\">"
                    t5InnerHTML += "<br><br><span class=\"" + outcome5 + "\">&nbsp;</span><br><br>" + outcomeComment5
                    t5InnerHTML += "</div>"

                    t5Div.innerHTML = t5InnerHTML
                });
            });
        });
    }

    if(document.getElementById("trafficLight6"))
    {
        t6Div = document.getElementById("trafficLightContent6")
        t6Div.innerHTML = spinnerHTML
        
        t6Settings = document.getElementById("trafficLightSettings6").value

        jQuery.getJSON('/getDashboardSettings/' + t6Settings, function (t6Details) {
            jQuery.getJSON('/identifySensor/' + t6List.map + " " + t6List.zone + " : " + t6Details.sensor + '/' + encodeURI(t6Details.readingType), function (t6List) {
                
                t6Name = t6List.site + " : " + t6List.control + " : " + t6List.device + " : " + t6List.reading
                document.getElementById("t6Heading").innerHTML = t6Name
                t6Name = t6List.reading

                jQuery.getJSON('/getTrafficLight/' + t6Details.sensor + '/' + t6Details.average + '/' + encodeURI(t6Name), function (t6Value) {

                    outcome6 = "largeWhiteDot";
                    outcomeComment6 = "unknown"
                    console.log(t6Value.value)

                    if(t6Details.type == "standard")
                    {
                        if(t6Details.greenStart < t6Value.value && t6Details.greenEnd > t6Value.value)
                        {
                            outcome6 = "largeGreenDot";
                            outcomeComment6 = "Ok"
                        }
                        else if(t6Details.orangeStart < t6Value.value && t6Details.orangeEnd > t6Value.value)
                        {
                            outcome6 = "largeOrangeDot"
                            outcomeComment6 = "Monitor"
                        }
                        else if(t6Details.redStart < t6Value.value)
                        {
                            outcome6 = "largeRedDot"
                            outcomeComment6 = "Not Ok"
                        }
                    }
                    else
                    {
                        console.log("Reverse")
                        if(t6Details.redStart < t6Value.value && t6Details.redEnd > t6Value.value)
                        {
                            outcome6 = "largeRedDot";
                            outcomeComment6 = "No Ok"
                        }
                        else if(t6Details.orangeStart < t6Value.value && t6Details.orangeEnd > t6Value.value)
                        {
                            outcome6 = "largeOrangeDot"
                            outcomeComment6 = "Monitor"
                        }
                        else if(t6Details.greenStart < t6Value.value)
                        {
                            outcome6 = "largeGreenDot"
                            outcomeComment6 = "Ok"
                        }
                    }

                    t6InnerHTML = "<div class=\"col-md-12\" style=\"text-align: center;\">"
                    t6InnerHTML += "<br><br><span class=\"" + outcome6 + "\">&nbsp;</span><br><br>" + outcomeComment6
                    t6InnerHTML += "</div>"

                    t6Div.innerHTML = t6InnerHTML
                });
            });
        });
    }
    
    if(document.getElementById("graphExample"))
    {
        avDiv = document.getElementById("assetValueGrowthGraph")
        avDiv.innerHTML = spinnerHTML

        /*
            Prepare the graph and then go and fetch the data
        */
        
        var avgKeyOptions = {
            chart: {
                renderTo: 'graphExample',
                type: 'line',
                height: '50%',
            },
            title: {
                text: ''
            },
            tooltip: {
                pointFormat: '{point.y}<br>{series.name}'
            },
            xAxis:[
                {
                    type: 'datetime',
            }],
            series: [],
    
            credits: {
                enabled: false
            }
        };
        
        
        jQuery.getJSON('/getGraph', function (list) {
            //console.log(list)
            $.each(list, function (n, item) {
                thisSeries = {};
                var data =[]
                
                thisSeries.name = n
                
                $.each(item, function (i, chunk) {
                    values = []
                    $.each(chunk, function (h, hit) {
                        values.push(hit)
                    });
                    data.push(values)
                });
                thisSeries.data = data;
                avgKeyOptions.series.push(thisSeries);
                
            });
            var chart = new Highcharts.Chart(avgKeyOptions);
        });
    }

    if(document.getElementById("siteParticipation"))
    {
        spDiv = document.getElementById("siteParticipation")
        spDiv.innerHTML = spinnerHTML
        spValue = document.getElementById("siteParticipationValue")

        /*
            Prepare the graph and then go and fetch the data
        */
        
        content = ""

        jQuery.getJSON('/getSiteParticipation/' + spValue, function (item) {
            console.log(item)
            
            if(item.participation < 20)
            {
                content += "<span class=\"largeRedDot\" style=\"text-align: center;\">" + item.participation + "</span>"        
            }
            else if(item.participation < 59.9 && item.participation > 20)
            {
                content += "<span class=\"largeOrangeDot\" style=\"text-align: center;\">" + item.participation + "</span>"        
            }
            else
            {
                content += "<span class=\"largeGreenDot\" style=\"text-align: center;\">" + item.participation + "</span>"        
            }
                
            spDiv.innerHTML = content
        });
    }

    
});

</script>

<?php

function urlDashboard($content)
{
    $content = "<input type=\"hidden\" id=\"urlDashboardContent\" value=\"$content\">";
    $content .= "<div class=\"panel panel-primary\" id=\"urlDashboard\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "DASHBOARD";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<iframe class=\"img-responsive\" id=\"urlContent\" style=\"width: 100%; height: 90%;\" ></iframe>";
    $content .= "</div>";

    return $content;
}

function urlDashboard2($content)
{
    $content = "<input type=\"hidden\" id=\"urlDashboardContent2\" value=\"$content\">";
    $content .= "<div class=\"panel panel-primary\" id=\"urlDashboard2\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "DASHBOARD";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<iframe class=\"img-responsive\" id=\"urlContent2\" style=\"width: 100%; height: 90%;\" ></iframe>";
    $content .= "</div>";

    return $content;
}

function urlDashboard3($content)
{
    $content = "<input type=\"hidden\" id=\"urlDashboardContent3\" value=\"$content\">";
    $content .= "<div class=\"panel panel-primary\" id=\"urlDashboard3\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "DASHBOARD";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<iframe class=\"img-responsive\" id=\"urlContent3\" style=\"width: 100%; height: 90%;\" ></iframe>";
    $content .= "</div>";

    return $content;
}

function urlDashboard4($content)
{
    $content = "<input type=\"hidden\" id=\"urlDashboardContent4\" value=\"$content\">";
    $content .= "<div class=\"panel panel-primary\" id=\"urlDashboard4\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "DASHBOARD";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<iframe class=\"img-responsive\" id=\"urlContent4\" style=\"width: 100%; height: 90%;\" ></iframe>";
    $content .= "</div>";

    return $content;
}

function urlDashboard5($content)
{
    $content = "<input type=\"hidden\" id=\"urlDashboardContent5\" value=\"$content\">";
    $content .= "<div class=\"panel panel-primary\" id=\"urlDashboard5\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "DASHBOARD";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<iframe class=\"img-responsive\" id=\"urlContent5\" style=\"width: 100%; height: 90%;\" ></iframe>";
    $content .= "</div>";

    return $content;
}

function graphDashboard1($content)
{
    $content = "<input type=\"hidden\" id=\"graphDashboardContent1\" value=\"" . addslashes($content) . "\">";
    $content .= "<div class=\"panel panel-primary\" id=\"graphDashboard1\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "<span id=\"g1Heading\">DASHBOARD</span>";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<div id=\"graphContent1\" style=\"width: 100%; height: 90%;\" ></div>";
    $content .= "</div>";

    return $content;
}

function graphDashboard2($content)
{
    $content = "<input type=\"hidden\" id=\"graphDashboardContent2\" value=\"$content\">";
    $content .= "<div class=\"panel panel-primary\" id=\"graphDashboard2\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "<span id=\"g2Heading\">DASHBOARD</span>";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<div id=\"graphContent2\" style=\"width: 100%; height: 90%;\" ></div>";
    $content .= "</div>";

    return $content;
}

function graphDashboard3($content)
{
    $content = "<input type=\"hidden\" id=\"graphDashboardContent3\" value=\"$content\">";
    $content .= "<div class=\"panel panel-primary\" id=\"graphDashboard3\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "<span id=\"g3Heading\">DASHBOARD</span>";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<div id=\"graphContent3\" style=\"width: 100%; height: 90%;\" ></div>";
    $content .= "</div>";

    return $content;
}

function graphDashboard4($content)
{
    $content = "<input type=\"hidden\" id=\"graphDashboardContent4\" value=\"$content\">";
    $content .= "<div class=\"panel panel-primary\" id=\"graphDashboard4\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "<span id=\"g4Heading\">DASHBOARD</span>";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<div id=\"graphContent4\" style=\"width: 100%; height: 90%;\" ></div>";
    $content .= "</div>";

    return $content;
}

function graphDashboard5($content)
{
    $content = "<input type=\"hidden\" id=\"graphDashboardContent5\" value=\"$content\">";
    $content .= "<div class=\"panel panel-primary\" id=\"graphDashboard5\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "<span id=\"g5Heading\">DASHBOARD</span>";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<div id=\"graphContent5\" style=\"width: 100%; height: 90%;\" ></div>";
    $content .= "</div>";

    return $content;
}

function graphDashboard6($content)
{
    $content = "<input type=\"hidden\" id=\"graphDashboardContent6\" value=\"$content\">";
    $content .= "<div class=\"panel panel-primary\" id=\"graphDashboard6\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "<span id=\"g6Heading\">DASHBOARD</span>";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<div id=\"graphContent6\" style=\"width: 100%; height: 90%;\" ></div>";
    $content .= "</div>";

    return $content;
}

function trafficLight1($content)
{
    $content = "<input type=\"hidden\" id=\"trafficLightSettings1\" value=\"$content\">";
    $content .= "<div class=\"panel panel-primary\" id=\"trafficLight1\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "<span id=\"t1Heading\">DASHBOARD</span>";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<div id=\"trafficLightContent1\" style=\"width: 100%; height: 90%;\" ></div>";
    $content .= "</div>";

    return $content;
}

function trafficLight2($content)
{
    $content = "<input type=\"hidden\" id=\"trafficLightSettings2\" value=\"$content\">";
    $content .= "<div class=\"panel panel-primary\" id=\"trafficLight2\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "<span id=\"t2Heading\">DASHBOARD</span>";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<div id=\"trafficLightContent2\" style=\"width: 100%; height: 90%;\" ></div>";
    $content .= "</div>";

    return $content;
}

function trafficLight3($content)
{
    $content = "<input type=\"hidden\" id=\"trafficLightSettings3\" value=\"$content\">";
    $content .= "<div class=\"panel panel-primary\" id=\"trafficLight3\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "<span id=\"t3Heading\">DASHBOARD</span>";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<div id=\"trafficLightContent3\" style=\"width: 100%; height: 90%;\" ></div>";
    $content .= "</div>";

    return $content;
}

function trafficLight4($content)
{
    $content = "<input type=\"hidden\" id=\"trafficLightSettings4\" value=\"$content\">";
    $content .= "<div class=\"panel panel-primary\" id=\"trafficLight4\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "<span id=\"t4Heading\">DASHBOARD</span>";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<div id=\"trafficLightContent4\" style=\"width: 100%; height: 90%;\" ></div>";
    $content .= "</div>";

    return $content;
}

function trafficLight5($content)
{
    $content = "<input type=\"hidden\" id=\"trafficLightSettings5\" value=\"$content\">";
    $content .= "<div class=\"panel panel-primary\" id=\"trafficLight5\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "<span id=\"t5Heading\">DASHBOARD</span>";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<div id=\"trafficLightContent5\" style=\"width: 100%; height: 90%;\" ></div>";
    $content .= "</div>";

    return $content;
}

function trafficLight6($content)
{
    $content = "<input type=\"hidden\" id=\"trafficLightSettings6\" value=\"$content\">";
    $content .= "<div class=\"panel panel-primary\" id=\"trafficLight6\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "<span id=\"t6Heading\">DASHBOARD</span>";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<div id=\"trafficLightContent6\" style=\"width: 100%; height: 90%;\" ></div>";
    $content .= "</div>";

    return $content;
}

function mySites()
{
    $content = "<div class=\"panel panel-primary\" id=\"mySites\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "MY SITES";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<div class=\"panel-body\" id=\"mySitesContent\" style=\"width: 100%; height: 100%; overflow: auto;\">";
        $content .= "</div>";
    $content .= "</div>";

    return $content;
}

function builderSites()
{
    $content = "<div class=\"panel panel-primary\" id=\"builderSites\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "ORGANISATION SITES";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<div class=\"panel-body\" id=\"builderSitesContent\" style=\"width: 100%; height: 100%; overflow: auto;\">";
        $content .= "</div>";
    $content .= "</div>";

    return $content;
}

function dash3()
{
    $content = "<div class=\"panel panel-primary\" id=\"assetsVliabilities\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "DASHBOARD WIDGET 3";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<div class=\"panel-body\" id=\"assetsVliabilitiesGraph\" style=\"width: 100%; height: 100%; overflow: auto;\">";
        $content .= "</div>";
    $content .= "</div>";

    return $content;
}

function dash4()
{
    $content = "<div class=\"panel panel-primary\" id=\"nettWorth\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "DASHBOARD WIDGET 4";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<div class=\"panel-body\" id=\"nettWorthShow\" style=\"width: 100%; height: 100%; overflow: auto;\">";
        $content .= "</div>";
    $content .= "</div>";

    return $content;
}

function myExposures()
{
    $content = "<div class=\"panel panel-primary\" id=\"myExposuresBox\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "EXPOSURE STATUS - LAST 7 DAYS";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<div class=\"panel-body\" id=\"myExposures\" style=\"width: 100%; height: 100%; overflow: auto;\">";
        $content .= "</div>";
    $content .= "</div>";

    return $content;
}

function siteParticipation()
{
    $content = "<div class=\"panel panel-primary\" id=\"siteParticipationBox\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "SITE PARTICIPATION - LAST 7 DAYS";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<div class=\"panel-body\" id=\"siteParticipation\" style=\"width: 100%; height: 100%; overflow: auto; text-align: center;\">";
        $content .= "</div>";
    $content .= "</div>";

    return $content;
}

function welcome()
{
    $content = "<div class=\"panel panel-primary\" id=\"welcomeBox\" style=\"width: 100%; height: 100%;\">";
        $content .= "<div class=\"panel-heading\">";
            $content .= "WELCOME TO NEXTRACK";
            $content .= "<i class=\"fa fa-minus-circle pull-right\" style=\"cursor: pointer;\" id=\"delItem\"></i>";
        $content .= "</div>";
        $content .= "<div class=\"panel-body\" id=\"welcome\" style=\"width: 100%; height: 100%; overflow: auto;\">";
        $content .= "</div>";
    $content .= "</div>";

    return $content;
}



?>

