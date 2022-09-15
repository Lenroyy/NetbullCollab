<script>
    function sendCoords(map, json)
    {

        console.log(json)
        
        /*
        jQuery.getJSON('/updateMapCoords/' + json, function (details) {
            console.log(details)
        });
        */
        
        
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type' : 'application/json'
            }
        });
       

        $.ajax({
            type:'GET',
            url:'/updateMapCoords',
            data:{
                coords:JSON.stringify(json), 
            },
            success: function(response){ // What to do if we succeed
                console.log("Success response is " + response); 
            },
            error: function(response){
                alert('Error response is ' + response);
            }
        });
        
    }

    <?php foreach($maps as $map)
    {
        ?>

        width = $("#mapModal{{ $map['map']->id }}").width();
        width = width*0.6

    

        if(width > "{{ $map['map']->width }}")
        {
            widthDifference = width / "{{ $map['map']->width }}"
        }
        else
        {
            widthDifference = "{{ $map['map']->width }}" / width
        }
      
        var width = "{{ $map['map']->width }}" * widthDifference;
        var height = "{{ $map['map']->height }}" * widthDifference;
        console.log('Height is ' + height)

        var stage{{ $map['map']->id }} = new Konva.Stage({
            container: 'floorPlan{{ $map['map']->id }}',
            width: width,
            height: height,
        });

        var layer = new Konva.Layer();

        <?php
            $c = 0;
            foreach($map['controlsArray'] as $type)
            {
                foreach($type['controls'] as $control)
                {
                    ?>
                    var box = new Konva.{{ $type['type']->shape }}({
                        <?php 
                            if($control['control']->x > 0)
                            {
                                ?>
                                x: {{ $control['control']->x }},
                                
                                <?php
                            }
                            else
                            {
                                ?>
                                x: 100,
                                <?php
                            }
                            if($control['control']->y > 0)
                            {
                                ?>
                                y: {{ $control['control']->y }},
                                <?php
                            }
                            else
                            {
                                ?>
                                y: 100,
                                <?php
                            }
                        ?>
                        width: 50,
                        height: 25,
                        fill: '{{ $control['control']->colour }}',
                        id: {{ $control['control']->id }},
                        draggable: true,
                        <?php
                            if($type['type']->shape == "Arc")
                            {
                                ?>
                                innerRadius: 20,
                                outerRadius: 35,
                                <?php
                            }
                            elseif($type['type']->shape == "Star")
                            {
                                ?>
                                numPoints: 6,
                                innerRadius: 9,
                                outerRadius: 15,
                                <?php
                            }
                            else
                            {
                                ?>
                                innerRadius: 9,
                                outerRadius: 15,
                                <?php
                            }
                            ?>
                        sides: 6,
                        angle: 60,
                    });


                    // add cursor styling
                    box.on('mouseover', function () {
                        document.body.style.cursor = 'pointer';
                    });
                    box.on('mouseout', function () {
                        document.body.style.cursor = 'default';
                    });
                    box.on('dragend', function () {
                        var json{{ $map['map']->id }} = stage{{ $map['map']->id }}.toJSON();
                        sendCoords({{ $map['map']->id }}, json{{ $map['map']->id }})
                    });
                    

                    layer.add(box);
                    <?php
                }
            }
        ?>

        console.log('Adding to map {{ $map['map']->id }}')

        stage{{ $map['map']->id }}.add(layer);
        

        <?php
    }
    ?>
    </script>