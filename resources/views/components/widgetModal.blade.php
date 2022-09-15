<!-- Widget Modal -->
    <div class="modal fade text-left" id="widgets" tabindex="-1" role="dialog" aria-labelledby="widgets12" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header ">
                    <h4 class="modal-title" id="myModalLabel12"><i class="la la-dashboard"></i> Widgets</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form form-horizontal" method="POST" action="/home">
                    @csrf
                    <input type ="hidden" name="widget_id" id="widget_id" value="0">
                    <input type="hidden" name="referrer" value="{{ $dashboardType }}">
                    <input type="hidden" name="module_id" value="{{ $moduleID }}">
                    <div class="modal-body">
                        <label>Widget: </label>                                        
                        <select class="form-control border-primary" name="widget" onChange="checkWidget(this.value)">
                            <option>--- Please Select ---</option>
                            @foreach($widgetOptions as $option)
                                <option value="{{ $option['type'] }}">{{ $option['name'] }}</option>
                            @endforeach
                        </select>
                        <div id="additional"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                        <input type=submit name="addWidget" value="Add" class="btn btn-outline-warning">
                    </div>
                </form>
            </div>
        </div>
    </div>
<!-- End Widget Modal -->

<script>
    function checkWidget(value)
    {
        content = ""
        if(value == "url")
        {
            content += "<br><br><label>URL</label>"
            content += "<input type=\"text\" name=\"content\" class=\"form-control form-control-line\">"
        }
        if(value == "url2")
        {
            content += "<br><br><label>URL</label>"
            content += "<input type=\"text\" name=\"content\" class=\"form-control form-control-line\">"
        }
        if(value == "url3")
        {
            content += "<br><br><label>URL</label>"
            content += "<input type=\"text\" name=\"content\" class=\"form-control form-control-line\">"
        }
        if(value == "url4")
        {
            content += "<br><br><label>URL</label>"
            content += "<input type=\"text\" name=\"content\" class=\"form-control form-control-line\">"
        }
        if(value == "url5")
        {
            content += "<br><br><label>URL</label>"
            content += "<input type=\"text\" name=\"content\" class=\"form-control form-control-line\">"
        }
        if(value == "graph1")
        {
            content += "<br><br><label>Graph type</label>"
            content += "<select name=\"graphType\" class=\"form-control form-control-line\">"
                content += "<option value=\"line\">Line</option>"
                content += "<option value=\"column\">Column</option>"
            content += "</select>"
            content += "<br><br><label>Period</label>"
            content += "<select name=\"period\" class=\"form-control form-control-line\">"
                content += "<option value=\"day\">Day</option>"
                content += "<option value=\"week\">Week</option>"
                content += "<option value=\"month\">Month</option>"
            content += "</select>"
            content += "<br><br><label>Sensor</label>"
            content += "<select name=\"sensor\" class=\"form-control form-control-line\" onChange=\"getReadingTypes(this.value)\">"
            content += "<option>---Please Select---</option>"
                @foreach($devices as $device)
                content += "<option value=\"{{ $device['device']->id }}\">{{ $device['site']->name }} :: {{ $device['control']->Controls_Type->name }} :: ({{ $device['device']->thingsboard_id }} : {{ $device['device']->name }})</option>"
                @endforeach
            content += "</select>"
            content += "<br><br><label>Hazard content %</label>"
            content += "<input type=\"number\" step=\"1\" name=\"hazardContent\" class=\"form-control form-control-line\" value=\"100\">"
            content += "<div id=\"readingTypes\"></div>"
        }
        if(value == "graph2")
        {
            content += "<br><br><label>Graph type</label>"
            content += "<select name=\"graphType\" class=\"form-control form-control-line\">"
                content += "<option value=\"line\">Line</option>"
                content += "<option value=\"column\">Column</option>"
            content += "</select>"
            content += "<br><br><label>Period</label>"
            content += "<select name=\"period\" class=\"form-control form-control-line\">"
                content += "<option value=\"day\">Day</option>"
                content += "<option value=\"week\">Week</option>"
                content += "<option value=\"month\">Month</option>"
            content += "</select>"
            content += "<br><br><label>Sensor</label>"
            content += "<select name=\"sensor\" class=\"form-control form-control-line\" onChange=\"getReadingTypes(this.value)\">"
            content += "<option>---Please Select---</option>"
                @foreach($devices as $device)
                content += "<option value=\"{{ $device['device']->id }}\">{{ $device['site']->name }} :: {{ $device['control']->Controls_Type->name }} :: ({{ $device['device']->thingsboard_id }} : {{ $device['device']->name }})</option>"
                @endforeach
            content += "</select>"
            content += "<br><br><label>Hazard content %</label>"
            content += "<input type=\"number\" step=\"1\" name=\"hazardContent\" class=\"form-control form-control-line\" value=\"100\">"
            content += "<div id=\"readingTypes\"></div>"
        }
        if(value == "graph3")
        {
            content += "<br><br><label>Graph type</label>"
            content += "<select name=\"graphType\" class=\"form-control form-control-line\">"
                content += "<option value=\"line\">Line</option>"
                content += "<option value=\"column\">Column</option>"
            content += "</select>"
            content += "<br><br><label>Period</label>"
            content += "<select name=\"period\" class=\"form-control form-control-line\">"
                content += "<option value=\"day\">Day</option>"
                content += "<option value=\"week\">Week</option>"
                content += "<option value=\"month\">Month</option>"
            content += "</select>"
            content += "<br><br><label>Sensor</label>"
            content += "<select name=\"sensor\" class=\"form-control form-control-line\" onChange=\"getReadingTypes(this.value)\">"
            content += "<option>---Please Select---</option>"
                @foreach($devices as $device)
                content += "<option value=\"{{ $device['device']->id }}\">{{ $device['site']->name }} :: {{ $device['control']->Controls_Type->name }} :: ({{ $device['device']->thingsboard_id }} : {{ $device['device']->name }})</option>"
                @endforeach
            content += "</select>"
            content += "<br><br><label>Hazard content %</label>"
            content += "<input type=\"number\" step=\"1\" name=\"hazardContent\" class=\"form-control form-control-line\" value=\"100\">"
            content += "<div id=\"readingTypes\"></div>"
        }
        if(value == "graph4")
        {
            content += "<br><br><label>Graph type</label>"
            content += "<select name=\"graphType\" class=\"form-control form-control-line\">"
                content += "<option value=\"line\">Line</option>"
                content += "<option value=\"column\">Column</option>"
            content += "</select>"
            content += "<br><br><label>Period</label>"
            content += "<select name=\"period\" class=\"form-control form-control-line\">"
                content += "<option value=\"day\">Day</option>"
                content += "<option value=\"week\">Week</option>"
                content += "<option value=\"month\">Month</option>"
            content += "</select>"
            content += "<br><br><label>Sensor</label>"
            content += "<select name=\"sensor\" class=\"form-control form-control-line\" onChange=\"getReadingTypes(this.value)\">"
            content += "<option>---Please Select---</option>"
                @foreach($devices as $device)
                content += "<option value=\"{{ $device['device']->id }}\">{{ $device['site']->name }} :: {{ $device['control']->Controls_Type->name }} :: ({{ $device['device']->thingsboard_id }} : {{ $device['device']->name }})</option>"
                @endforeach
            content += "</select>"
            content += "<br><br><label>Hazard content %</label>"
            content += "<input type=\"number\" step=\"1\" name=\"hazardContent\" class=\"form-control form-control-line\" value=\"100\">"
            content += "<div id=\"readingTypes\"></div>"
        }
        if(value == "graph5")
        {
            content += "<br><br><label>Graph type</label>"
            content += "<select name=\"graphType\" class=\"form-control form-control-line\">"
                content += "<option value=\"line\">Line</option>"
                content += "<option value=\"column\">Column</option>"
            content += "</select>"
            content += "<br><br><label>Period</label>"
            content += "<select name=\"period\" class=\"form-control form-control-line\">"
                content += "<option value=\"day\">Day</option>"
                content += "<option value=\"week\">Week</option>"
                content += "<option value=\"month\">Month</option>"
            content += "</select>"
            content += "<br><br><label>Sensor</label>"
            content += "<select name=\"sensor\" class=\"form-control form-control-line\" onChange=\"getReadingTypes(this.value)\">"
            content += "<option>---Please Select---</option>"
                @foreach($devices as $device)
                content += "<option value=\"{{ $device['device']->id }}\">{{ $device['site']->name }} :: {{ $device['control']->Controls_Type->name }} :: ({{ $device['device']->thingsboard_id }} : {{ $device['device']->name }})</option>"
                @endforeach
            content += "</select>"
            content += "<br><br><label>Hazard content %</label>"
            content += "<input type=\"number\" step=\"1\" name=\"hazardContent\" class=\"form-control form-control-line\" value=\"100\">"
            content += "<div id=\"readingTypes\"></div>"
        }
        if(value == "graph6")
        {
            content += "<br><br><label>Graph type</label>"
            content += "<select name=\"graphType\" class=\"form-control form-control-line\">"
                content += "<option value=\"line\">Line</option>"
                content += "<option value=\"column\">Column</option>"
            content += "</select>"
            content += "<br><br><label>Period</label>"
            content += "<select name=\"period\" class=\"form-control form-control-line\">"
                content += "<option value=\"day\">Day</option>"
                content += "<option value=\"week\">Week</option>"
                content += "<option value=\"month\">Month</option>"
            content += "</select>"
            content += "<br><br><label>Sensor</label>"
            content += "<select name=\"sensor\" class=\"form-control form-control-line\" onChange=\"getReadingTypes(this.value)\">"
            content += "<option>---Please Select---</option>"
                @foreach($devices as $device)
                    content += "<option value=\"{{ $device['device']->id }}\">{{ $device['site']->name }} :: {{ $device['control']->Controls_Type->name }} :: ({{ $device['device']->thingsboard_id }} : {{ $device['device']->name }})</option>"
                @endforeach
            content += "</select>"
            content += "<br><br><label>Hazard content %</label>"
            content += "<input type=\"number\" step=\"1\" name=\"hazardContent\" class=\"form-control form-control-line\" value=\"100\">"
            content += "<div id=\"readingTypes\"></div>"
        }

        if(value == "trafficLight1" || value == "trafficLight2" || value == "trafficLight3" || value == "trafficLight4" || value == "trafficLight5" || value == "trafficLight6")
        {
            content += "<br><br><label>Type</label>"
            content += "<div class=\"row\"><div class=\"col-md-4\"><select class=\"form-control form-control-line\" onChange=\"checkDirection(this.value)\" name=\"type\"><option value=\"standard\">Standard</option><option value=\"reverse\">Reverse</option></select></div></div>"
            content += "<br><br><label id=\"greenLight\">Green light</label>"
            content += "<div class=\"row\"><div class=\"col-md-4\"><input type=\"number\" step=\".01\" id=\"greenStart\" name=\"greenStart\" class=\"form-control form-control-line\" value=\"0\"></div><div class=\"col-md-1\"> -> </div><div class=\"col-md-4\"><input type=\"number\" step=\".01\" id=\"greenEnd\" name=\"greenEnd\" class=\"form-control form-control-line\"></div></div>"
            content += "<br><br><label id=\"orangeLight\">Orange light</label>"
            content += "<div class=\"row\"><div class=\"col-md-4\"><input type=\"number\" step=\".01\" id=\"orangeStart\" name=\"orangeStart\" class=\"form-control form-control-line\"></div><div class=\"col-md-1\"> -> </div><div class=\"col-md-4\"><input type=\"number\" step=\".01\" id=\"orangeEnd\" name=\"orangeEnd\" class=\"form-control form-control-line\"></div></div>"
            content += "<br><br><label id=\"redLight\">Red light</label>"
            content += "<div class=\"row\"><div class=\"col-md-4\"><input type=\"number\" step=\".01\" id=\"redStart\" name=\"redStart\" class=\"form-control form-control-line\"></div></div>"
            content += "<br><br><label>Average over</label>"
            content += "<select name=\"average\" class=\"form-control form-control-line\">"
                content += "<option value=\"1\">1 minute</option>"
                content += "<option value=\"5\">5 minutes</option>"
                content += "<option value=\"60\">1 hour</option>"
            content += "</select>"
            content += "<br><br><label>Sensor</label>"
            content += "<select name=\"sensor\" class=\"form-control form-control-line\" onChange=\"getReadingTypes(this.value)\">"
            content += "<option>---Please Select---</option>"
                @foreach($devices as $device)
                    content += "<option value=\"{{ $device['device']->id }}\">{{ $device['site']->name }} :: {{ $device['control']->Controls_Type->name }} :: ({{ $device['device']->thingsboard_id }} : {{ $device['device']->name }})</option>"
                @endforeach
            content += "</select>"
            content += "<div id=\"readingTypes\"></div>"
        }
        
        document.getElementById("additional").innerHTML = content
    }

    function getReadingTypes(value)
    {
        rtDiv = document.getElementById("readingTypes")

        //this function goes and gets all the readings types from this device
        jQuery.getJSON('/getDeviceReadingTypes/' + value, function (list) {
            types = "<br><br><label>Reading type</label>"
            types += "<select name=\"readingType\" class=\"form-control form-control-line\">"
            console.log(list)

            $.each(list, function (n, item) {
                types += "<option value=\"" + item.id + "\">" + item.name + "</option>"
            });

            types += "</select>"

            rtDiv.innerHTML = types
        });
        
    }

    function checkDirection(value)
    {
        greenLight = document.getElementById("greenLight")
        orangeLight = document.getElementById("orangeLight")
        redLight = document.getElementById("redLight")

        greenStart = document.getElementById("greenStart")
        greenEnd = document.getElementById("greenEnd")
        orangeStart = document.getElementById("orangeStart")
        orangeEnd = document.getElementById("orangeEnd")
        redStart = document.getElementById("redStart")

        if(value == "standard")
        {
            greenLight.innerHTML = "Green light"
            orangeLight.innerHTML = "Orange light"
            redLight.innerHTML = "Red light"

            greenStart.name = "greenStart"
            greenEnd.name = "greenEnd"
            orangeStart.name = "orangeStart"
            orangeEnd.name = "orangeEnd"
            redStart.name = "redStart"
        }
        else
        {
            greenLight.innerHTML = "Red light"
            orangeLight.innerHTML = "Orange light"
            redLight.innerHTML = "Green light"

            greenStart.name = "redStart"
            greenEnd.name = "redEnd"
            orangeStart.name = "orangeStart"
            orangeEnd.name = "orangeEnd"
            redStart.name = "greenStart"
        }
        return 1
    }
</script>