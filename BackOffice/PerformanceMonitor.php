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
                    <div id="controls">
                        <input type="submit" value="Flush" onclick="flush()"></input>
                        <input type="submit" value="Refresh" onclick="refreshClickHandler()"></input>
                        <input type="checkbox" id="flushOnRefreshCb"></input>
                        Flush on refresh
                        <input type="submit" id="toggleAutoRefreshBtn" value="Start Auto Refresh" onclick="toggleAutoRefresh()"></input>
                        Every
                        <input value="1" id="autoRefreshIntervalInput"></input>
                        Seconds<br/>
                        <a onclick="showAllUris()">All Calls</a>
                        <span id="focusedUriInfo"></span> &nbsp;&nbsp;
                        <div id="statusMessage" class="warning"> </div>
                    </div>
                    <div id="chartDivContainer">
                        <div id="chartDiv"></div>
                    </div>                  
                </div>
            </div>
            <script>
                
/**
 * the chart
 * */
var plot;
/**
 * the full data received from the server
 * */
var serverData;

/**
 * the uri of the service method on which the focus is. null when all uris are shown
 * */
var focusedUri;

var isAutoRefreshing;

/**
 *auto refresh timer
 **/
var timer;

var amfphpEntryPointUrl = "<?php echo $config->resolveAmfphpEntryPointUrl() ?>?contentType=application/json";

$(function () {	
    document.title = "AmfPHP - Performance Monitor";
    $("#titleSpan").text(document.title);

    <?php if($config->fetchAmfphpUpdates){
        echo 'showAmfphpUpdates();';
    }?> 

    $( window ).bind( "resize", resize );                             
    resize();
    refresh();
    isAutoRefreshing = false;

});    

/**
 * sizes the div containing the chart
 * @todo redraw the graph
 **/
function resize(){
    var availableWidth = $( "#main" ).width() - $("#left").outerWidth(true) - 20;
    $( "#right" ).css( "width", availableWidth +  "px" );

    var availableHeight = $( "body" ).height() - $("#chartDiv").offset().top - 50;
    $( "#chartDiv" ).css( "height", availableHeight +  "px" );
    if(plot){
        plot.replot({resetAxes:true});
        //replotting removes listeners on labels which allow the user to select a call for details. So reset them.
        addLabelListeners();
    }
 


}

function displayStatusMessage(html){
    $('#statusMessage').html(html);
    resize();
}

function updateControls(){
    if(focusedUri){
        $("#focusedUriInfo").text("> " + focusedUri);
    }else{
        $("#focusedUriInfo").text("(Click method for details)");
    }
}

/**
 * callback for when performance data loaded from server . 
 * generates graph with consolidated data
 */
function onDataLoaded(data)
{
    serverData = data;

    if(typeof data == "string"){
        //some predictable error messages
        if(data.indexOf("AmfphpMonitorService service not found") != -1){
            displayStatusMessage("The AmfphpMonitorService could not be called. This is most likely because AmfphpMonitor plugin is not enabled. See the <a href='http://www.silexlabs.org/amfphp/documentation/using-the-back-office/performance-monitor/'>documentation</a>.")
        }else{
            displayStatusMessage(data);
        }
        return;
    }

    if(data.sortedData.length == 0){
        displayStatusMessage("No data was available. Please make a service call then refresh. This can be done with the <a href='ServiceBrowser.php'>Service Browser</a>.");
    }
    //test
    //focusedUri = "TestService/returnOneParam";
    
    if(focusedUri){
        focusOnUri(focusedUri);
    }else{
        showAllUris();
    }
    

}

/**
 * process ordered names to create labels linked to the doc
 * */
function getLegendLabels(orderedTimeNames){
    var labels = [];
    for(var i = 0; i < orderedTimeNames.length; i++){
        var timeName = orderedTimeNames[i];
        var label;
        switch(timeName){
            case "Deserialization": 
            case "Request Vo Conversion":
            case "Request Charset Conversion":
            case "Service Call":
            case "Response Vo Conversion":
            case "Response Charset Conversion":
            case "Serialization":
                label = timeName  + "<a href='http://www.silexlabs.org/amfphp/documentation/using-the-back-office/performance-monitor/#";
                label += timeName + "'> ?</a>";
                break;
            default:
                label = "CUSTOM " + timeName + "<a href='http://www.silexlabs.org/amfphp/documentation/using-the-back-office/performance-monitor/#more data'> ?</a>";
        }
        labels.push(label);
    }
    return labels;
}

function showAllUris(){
    if(plot){
        plot.destroy();
    }
    focusedUri = null;

    var seriesData = [];
    var ticks = [];
    var ignoredUris = [];
    var missingTimes = [];
    var missingTimesAssoc = {};
    var orderedTimedNamesSet = false;
    var orderedTimeNames = [];
    
    if(serverData.sortedData.length == 0){
        return;
    }
    //here a uri referes to a service method, as that is what is used to sort the data
    for(var uri in serverData.sortedData){
        //data for each target uri
        var rawUriData = serverData.sortedData[uri];
        var formattedUriData = [];
        //sanity check: ignore uri if a time is missing
        var ignoreUri = false;
        for(var i = 0; i < serverData.timeNames.length; i++){
            var expectedTimeName = serverData.timeNames[i];
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
            if(!orderedTimedNamesSet){
                orderedTimeNames.push(timeName);
            }
        }
        seriesData.push(formattedUriData);
        ticks.push(uri);
        orderedTimedNamesSet = true;
    }

    //as chart is horizontal, data must be flipped. This is a jqplot design choice or limitation it seems
    var flippedSeriesData = [];

    for(var i = 0; i < seriesData[0].length; i++){
        flippedSeriesData.push([]);
        for(var j = 0; j < seriesData.length; j++){
            flippedSeriesData[i].push(seriesData[j][i]);
        }
    }

    if(ignoredUris.length > 0){
        var message = "The following service methods were ignored because their data does not have all expected times: ";
        message += ignoredUris.join(', ');
        message += "<br/>The missing times are the following : ";
        message += missingTimes.join(', ');
        message += "<br/>For a service method to appear in the chart make sur it logs those times.<br/>";
        $("#statusMessage").html(message);
    }
    buildChart(flippedSeriesData, ticks, getLegendLabels(orderedTimeNames));

    updateControls();
}


/**
 * show data for 1 call uri.
 * note: only the first 20 calls are shown, so as not to drown the browser.
 */
function focusOnUri(uri){
    if(plot){
        plot.destroy();
    }
    focusedUri = uri;
    var seriesData = [];
    var orderedTimeNames = [];
    
    //data for each target uri
    var rawUriData = serverData.sortedData[uri];
    
    var i = 0;
    //look at data for each time 
    for(var timeName in rawUriData){

        var timeData = rawUriData[timeName];
        timeData = timeData.slice(0, 20);
        orderedTimeNames.push(timeName);
        seriesData.push(timeData.reverse());

    }

    //the empty ticks array is important, otherwise the category axis renderer 
    //messes up the layout
    buildChart(seriesData, [], getLegendLabels(orderedTimeNames));

    updateControls();
}

function buildChart(seriesData, ticks, legendLabels){
    plot = $.jqplot('chartDiv', seriesData, {
        // Tell the plot to stack the bars.
        stackSeries: true,
        seriesDefaults:{
            renderer:$.jqplot.BarRenderer,
            rendererOptions: {
                // Put a 30 pixel margin between bars.
                barMargin: 30,
                barDirection: 'horizontal'
            },
            pointLabels: {show: true}
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
            placement: 'inside',
            labels:legendLabels           
        }
    });

    $('.jqplot-yaxis-tick')
        .css({ cursor: "pointer", zIndex: "1" })
        .click(function (ev) { 
            focusOnUri($(this).text());

        });
/* not useful at this point.        
    $('#chartDiv').bind('jqplotDataClick', 
        function (ev, seriesIndex, pointIndex, data) {
            var message = "";
            if(!focusedUri){
                message = "Average ";
            }
            message += orderedTimeNames[seriesIndex] + ' Duration : '+data[0] + ' ms';
            $('#statusMessage').html(message);
            resize();
        }
    );     
    */
    
}

function refreshClickHandler(){
    displayStatusMessage("");
    refresh();
}
/**
 * load data, and optionally flush
 */
function refresh(){ 
    var flush = $("#flushOnRefreshCb").is(':checked');
    var callData = JSON.stringify({"serviceName":"AmfphpMonitorService", "methodName":"getData","parameters":[flush]});
    var request = $.ajax({
        url: amfphpEntryPointUrl,
        type: "POST",
        data: callData
    });

    request.done(onDataLoaded);

    request.fail(function( jqXHR, textStatus ) {
        displayStatusMessage(textStatus + "<br/><br/>" + jqXHR.responseText);
    });
    
}

/**
 * flush monitor data on server.
 */
function flush(){
    var callData = JSON.stringify({"serviceName":"AmfphpMonitorService", "methodName":"flush","parameters":[]});
    var request = $.ajax({
        url: amfphpEntryPointUrl,
        type: "POST",
        data: callData
    });
    request.done(function(){
        displayStatusMessage("Data Flushed");
    });
    request.fail(function( jqXHR, textStatus ) {
        displayStatusMessage(textStatus + "<br/><br/>" + jqXHR.responseText);
    });
    
}


/**
 * start and stop auto refresh
 */
function toggleAutoRefresh(){
    displayStatusMessage("");
    if(!isAutoRefreshing){
        var interval = parseInt($("#autoRefreshIntervalInput").val());
        if(isNaN(interval)){
            alert("Invalid auto refresh interval");
            return;
        }
    }
    
    isAutoRefreshing = !isAutoRefreshing;
    if(isAutoRefreshing){
        timer = setInterval(refresh, interval * 1000);
        $("#toggleAutoRefreshBtn").prop("value", "Stop Auto Refresh");
        
    }else{
        clearInterval(timer);
        $("#toggleAutoRefreshBtn").prop("value", "Start Auto Refresh");
        
    }
    
}

</script>
