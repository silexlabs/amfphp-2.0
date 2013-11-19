<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * 
 * note: It would be nice to have logarithmic time rendering, but this doesn't seem to work nicely with horizontal graphs
 * @package Amfphp_BackOffice_PerformanceMonitor
 * 
 */
/**
 * includes
 */
require_once(dirname(__FILE__) . '/ClassLoader.php');
$accessManager = new Amfphp_BackOffice_AccessManager();
$isAccessGranted = $accessManager->isAccessGranted();
$config = new Amfphp_BackOffice_Config();
?>

<html>
    <?php require_once(dirname(__FILE__) . '/HtmlHeader.inc.php'); ?>
    <body>
        <?php
        require_once(dirname(__FILE__) . '/LinkBar.inc.php');
        ?>

        <div id='main' class="notParamEditor">
            <div id="left">
                <?php
                if (!$isAccessGranted) {
                    ?>
                    <script>
                        window.location = './SignIn.php';
                    </script>
                    <?php
                    return;
                }
                require_once(dirname(__FILE__) . '/MainMenu.inc.php');
                ?>
            </div>
            <div  id='right'>
                <div class="menu" id="performanceDisplay">
                    <div id="chartDivContainer">
                        <div id="chartDiv"></div>
                    </div>                  
                    <div id="statusMessage" style="max-width:100%"></div>
                </div>
            </div>
            <script>
                $(function () {	
                    document.title = "AmfPHP - Performance Monitor";
                    $("#titleSpan").text(document.title);

                    var callData = JSON.stringify({"serviceName":"AmfphpMonitorService", "methodName":"getData","parameters":[false]});
                    var request = $.ajax({
                        url: "<?php echo $config->resolveAmfphpEntryPointUrl() ?>?contentType=application/json",
                        type: "POST",
                        data: callData
                    });

                    request.done(onDataLoaded);

                    request.fail(function( jqXHR, textStatus ) {
                        displayCallErrorMessage(textStatus + "<br/><br/>" + jqXHR.responseText);
                    });

                    var availableWidthForRightDiv = $( "#main" ).width() - $("#left").outerWidth(true) - 50;
                    $( "#right" ).css( "width", availableWidthForRightDiv +  "px" );


                });    


                /**
                 * sets the max width for the right div.
                 * used on loading services, and when window resizes
                 * */
                function setRightDivWidth(){
                    var availableWidthForRightDiv = $( "#main" ).width() - $("#left").outerWidth(true) - 50;
                    $( "#right" ).css( "width", availableWidthForRightDiv +  "px" );
                }

                function displayCallErrorMessage(html){
                    $('#statusMessage').html(html);
                }

                /**
                 * callback for when performance data loaded from server . 
                 * generates graph with consolidated data
                 */
                function onDataLoaded(data)
                {
                    if(typeof data == "string"){
                        displayCallErrorMessage(data);
                        return;
                    }

                    console.log(data);

                    var seriesData = [];
                    var ticks = [];
                    var ignoredUris = [];
                    var missingTimes = [];
                    var missingTimesAssoc = {};
                    var seriesOptions = [];
                    var seriesOptionsSet = false;
                    
                    //here a uri referes to a service method, as that is what is used to sort the data
                    for(var uri in data.sortedData){
                        //                        //data for each target uri
                        var rawUriData = data.sortedData[uri];
                        var formattedUriData = [];
                        //sanity check: ignore uri if a time is missing
                        var ignoreUri = false;
                        for(var i = 0; i < data.timeNames.length; i++){
                            var expectedTimeName = data.timeNames[i];
                            if(!rawUriData.hasOwnProperty(expectedTimeName)){
                                ignoreUri = true;
                                if(!missingTimesAssoc.hasOwnProperty(expectedTimeName)){
                                    //use an associative array to make sure we don't have duplcate missing time names'
                                    missingTimesAssoc[expectedTimeName] = '';
                                    missingTimes.push(expectedTimeName);
                                }
                            }
                        }
                        if(ignoreUri){
                            ignoredUris.push(uri);
                            continue;
                        }
                        
                        //look at data for each time 
                        for(var timeName in rawUriData){
                            
                            var timeData = rawUriData[timeName];
                            //calculate average duration
                            var numTimes = timeData.length;
                            var totalDuration = 0;
                            if(numTimes == 0){
                                //should never happen, but as we divide by totalDuration below, better to check
                                continue;
                            }
                            for(var i = 0; i < numTimes; i++)
                            {
                                totalDuration = totalDuration + timeData[i];
                            }
                            formattedUriData.push(totalDuration / numTimes);
                            //first time round grab the time names for series labels
                            if(!seriesOptionsSet){
                                seriesOptions.push({label:timeName});
                            }
                        }
                        seriesData.push(formattedUriData);
                        ticks.push(uri);
                        seriesOptionsSet = true;
                    }
                    
                    console.log(seriesData);
                    //as chart is horizontal, data must be flipped. This is a jqplot design choice or limitation it seems
                    var flippedSeriesData = [];
                    
                    for(var i = 0; i < seriesData[0].length; i++){
                        flippedSeriesData.push([]);
                        for(var j = 0; j < seriesData.length; j++){
                            flippedSeriesData[i].push(seriesData[j][i]);
                        }
                    }
                    console.log(flippedSeriesData);
                    
                    if(ignoredUris.length > 0){
                        var message = "The following service methods were ignored because their data does not have all expected times.<br/>";
                        message += ignoredUris.join(', ');
                        message += "<br/>The missing times are the following : <br/>";
                        message += missingTimes.join(', ');
                        message += "<br/>For a service method to appear in the chart make sur it logs those times.<br/>";
                        $("#statusMessage").html(message);
                    }
                    $.jqplot('chartDiv', flippedSeriesData, {
                        // Tell the plot to stack the bars.
                        stackSeries: true,
                        captureRightClick: true,
                        seriesDefaults:{
                            renderer:$.jqplot.BarRenderer,
                            rendererOptions: {
                                // Put a 30 pixel margin between bars.
                                barMargin: 30,
                                barDirection: 'horizontal',
                                // Highlight bars when mouse button pressed.
                                // Disables default highlighting on mouse over.
                                highlightMouseDown: true
                            },
                            pointLabels: {show: true, labelsFromSeries:true}
                            //, labels:[['a','b'], ['c', 'd']]
                        },
                        axes: {
                           
                            yaxis: {
                                // Don't pad out the bottom of the data range.  By default,
                                // axes scaled as if data extended 10% above and below the
                                // actual range to prevent data points right on grid boundaries.
                                // Don't want to do that here.
                                padMin: 0,
                                renderer: $.jqplot.CategoryAxisRenderer,
                                ticks: ticks
                            }
                        },
                        legend: {
                            show: true,
                            location: 'e',
                            placement: 'inside'
                        }, 
                         series:seriesOptions, 
                    });
        
                    
                }

            </script>
