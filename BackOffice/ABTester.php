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
 * @package Amfphp_BackOffice_Profiler
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
    <title>Amfphp Back Office - A/B Tester</title>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

        <link rel="stylesheet" type="text/css" href="css/jquery.jqplot.css" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />

        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="js/jquery.cookie.js"></script>
        <script type="text/javascript" src="js/amfphp_updates.js"></script>
        <!--[if IE]><script language="javascript" type="text/javascript" src="js/excanvas.min.js"></script><![endif]-->
        <script language="javascript" type="text/javascript" src="js/jquery.jqplot.js"></script>
        <script language="javascript" type="text/javascript" src="js/jqplot.barRenderer.js"></script>
        <script language="javascript" type="text/javascript" src="js/jqplot.categoryAxisRenderer.js"></script>
        <script language="javascript" type="text/javascript" src="js/jqplot.enhancedLegendRenderer.js"></script>
        <script language="javascript" type="text/javascript" src="js/jqplot.pointLabels.js"></script>
        <script type="text/javascript" src="js/swfobject.js"></script>
        <script type="text/javascript" src="js/services.js"></script>
        <script type="text/javascript" src="js/performanceCharting.js"></script>


        <script type="text/javascript">
<?php
echo 'var amfphpVersion = "' . AMFPHP_VERSION . "\";\n";
echo 'var amfphpEntryPointUrlA = "' . $config->resolveAmfphpEntryPointUrl() . "\";\n";
if ($config->fetchAmfphpUpdates) {
    echo "var shouldFetchUpdates = true;\n";
} else {
    echo "var shouldFetchUpdates = false;\n";
}
?>
    var amfphpEntryPointUrlB = amfphpEntryPointUrlA + '/nobaguette.php';
    var serviceMethodUri = "TestService/returnLargeDataSet";
    var titleChartA = "Performance With Baguette AMF(ms)";
    var titleChartB = "Performance Without Baguette AMF(ms)";
        </script>  

    </head>
    <body>
        <div class="page-wrap">
            <?php
            require_once(dirname(__FILE__) . '/Header.inc.php');
            ?>
            <div id='main' >
                <?php
                if (!$isAccessGranted) {
                    ?>
                    <script>
                        window.location = './SignIn.php';
                    </script>
                    <?php
                    return;
                }
                ?>
                <div id="performanceDisplay" title="The server parses the request, generates some data, and serializes the response.">
                    <div id="amfCallerContainer">
                        Flash Player is needed to make AMF calls. 
                        <a target="_blank" href="http://www.adobe.com/go/getflashplayer">
                            <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
                        </a>
                    </div>                       
                    <div id="controls">
                        Make a call to Amfphp with and without Baguette AMF. <br/>
                        
                        <input type="submit" id="runTest" value=">> Run Test" onclick="runTestClickHandler()"></input>
                        Number of items generated
                        <input value="1000" id="numItemsInput"></input>
                        <div id="statusMessage" class="warning"> </div>
                    </div>


                    <div class="chartDivContainer">
                        <div id="chartDiv"></div>
                    </div>                  
                </div>
            </div>
        </div>            
        <?php
        require_once(dirname(__FILE__) . '/Footer.inc.php');
        ?>            
        <script>
                
            /**
             * the chart
             * */
            var plot;

            /**
             * reference to amf caller, set once it is loaded. Used to make AMF calls.
             * */
            var amfCaller;
           
            /**
             * number of generated items
             * */
            var numItems;


            $(function () {	
                $("#line1").hide();
                $("#line2").hide();
                $("#currentVersion").hide();
                $("#currentVersionPre").hide();
                $("#tabName").html("A/B Tester. >> Try it!");
                $("footer").hide();

                var availableHeight = $( "body" ).height() - $("#chartDiv").offset().top - 40;
                $( "#chartDiv" ).css( "height", (availableHeight) +  "px" );
    
                var flashvars = {};
                var params = {};
                params.allowscriptaccess = "sameDomain";
                var attributes = {};
                attributes.id = "amfCaller";

                swfobject.embedSWF("AmfCaller.swf", "amfCallerContainer", "0", "0", "9.0.0", false, flashvars, params, attributes, function (e) {
                    if(e.success){
                        amfCaller = e.ref;
                    }else{
                        alert("could not load AMF Caller.");
                        if(console){
                            console.log(e);
                        }
                    }

                });
                amfphp.entryPointUrl = amfphpEntryPointUrlA + "?contentType=application/json";
                
                

            });    

            function displayStatusMessage(html){
                $('#statusMessage').html(html);
            }


            function showErrorMessage(errorMsg){
                errorMsg += "<br/>Once you have some data, this is what you should see : ";
                displayStatusMessage(errorMsg);
                $("#profilerImg").show();
                $("#chartDivContainer").hide();
            }


            
            /**
             * called by amf caller when ready
             */
            function onAmfCallerLoaded(){
                runTest();
            }
            
            function runTestClickHandler(){
                runTest();
            }
            /**
             * flush, make call then load performance data for A, then B
             */
            function runTest(){ 
                if(!amfCaller || !amfCaller.isAlive()){
                    alert('AMF Caller not available.');
                    return;
                }
                
                numItems = parseInt($("#numItemsInput").val());
                if(isNaN(numItems)){
                    alert("Invalid Number of generated items.");
                    $("#numItemsInput").val(1000);
                    return;
                }
                
                if(numItems <= 0){
                    alert('Please enter a strictly positive number of items to generate');
                    return;
                }
                    
                if(numItems > 3000){
                    alert('The number of generated items is limited in this demo to 3000.');
                    return;
                }
                   
                amfphp.services.AmfphpMonitorService.flush(makeCallA, onServerError);
                
    
            }
            
            function makeCallA(){
                amfCaller.call(amfphpEntryPointUrlA, serviceMethodUri, [numItems], 'makeCallB');
            }
            
            function makeCallB(){
                amfCaller.call(amfphpEntryPointUrlB, serviceMethodUri, [numItems], 'loadPerformanceData');
            }
            
            function loadPerformanceData(){
                amfphp.services.AmfphpMonitorService.getData(showPerformanceData, onServerError, false);
            }
            
            /**
             * callback for when performance data loaded from server . 
             * generates graph with consolidated data
             */
            function showPerformanceData(data)
            {
            
                if(data.sortedData.length == 0){
                    showErrorMessage("No data was available. Please make a service call then runTest. This can be done with the <a href='ServiceBrowser.php'>Service Browser</a>.");
                    return;
                }
    
    
                if(plot){
                    plot.destroy();
                }
    
                displayStatusMessage('');
    
                var seriesData = [];
                var orderedTimeNames = [];
                var ticks = [];
    
                //data for each target uri
                var rawUriData = data.sortedData[serviceMethodUri];
    
                var i = 0;
                //look at data for each time 
                for(var timeName in rawUriData){

                    var timeData = rawUriData[timeName];
                    timeData = timeData.slice(0, 20);
                    orderedTimeNames.push(timeName);
                    seriesData.push(timeData.reverse());

                }
                
                ticks.push(titleChartB);
                ticks.push(titleChartA);
                
                plot = buildChart("chartDiv", seriesData, ticks, getLegendLabels(orderedTimeNames), "A/B Test(ms)", getSeriesColors(orderedTimeNames));
                
                var serializationData = data.sortedData[serviceMethodUri].Serialization;
                displayStatusMessage("For " + numItems + " items, it takes " + serializationData[0] + " ms to serialize the data with Baguette AMF, and " + serializationData[1] + " ms without.");
                
            }
            
            
            function onServerError(jqXHR, textStatus ){
                var responseText = jqXHR.responseText;
                if(responseText.indexOf("AmfphpMonitorService service not found") != -1){
                    showErrorMessage("The AmfphpMonitorService could not be called. This is most likely because AmfphpMonitor plugin is not enabled. See the <a href='http://www.silexlabs.org/amfphp/documentation/using-the-back-office/profiler/'>documentation</a>.");
                    return;
                }

                var errorMessagePos = responseText.indexOf("AmfphpMonitor does not have permission to read and write");
                if(errorMessagePos != -1){
                    var filePathStart = responseText.indexOf("log file: ", errorMessagePos) + 10;
                    var filePathStop = responseText.indexOf("'", filePathStart);
                    var filePath = responseText.substring(filePathStart, filePathStop);
                    showErrorMessage("Could not read or write log file. Please check your webserver has read and write permissions on <br/>" + filePath);
                    return;
                }


                showErrorMessage(textStatus + "<br/><br/>" + jqXHR.responseText);
                
            }


        </script>
