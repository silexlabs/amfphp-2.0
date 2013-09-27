<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_BackOffice
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
        $addToTitle = ' - Service Browser';
        require_once(dirname(__FILE__) . '/LinkBar.inc.php');
        ?>

        <div id='main'>
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

                <ul id='serviceMethods'  class='menu'>
                    Loading Service Data...
                </ul>
            </div>                    
            <div id='right' class='menu' >
                <div id='callDialog'>
                </div>
                <div id="result">
                    Result ( call took <span id="callDuration">23</span> ms )  
                    <span class="showResultView">
                        <a id="tree">Tree</a>
                        <a id="print_r">print_r</a>
                        <a id="json">JSON</a>
                        <a id="php">PHP Serialized</a>
                        <a id="raw">Raw</a>
                    </span>
                    <div id="dataView">
                        <div id='tree' class='resultView'></div>
                        <div id="print_r" class='resultView'></div>
                        <div id="json" class='resultView'></div>
                        <div id="php" class='resultView'></div>
                        <div id="raw" class='resultView'></div>
                    </div>
                </div>
            </div>
            <script>
                /**
                 * data about the services, loaded from server via AmfphpDiscoveryService/discover
                 * @var array
                 * */ 
                var serviceData;
                    
                /**
                 *call start time, in ms
                 */
                var callStartTime;
                
                /**
                 * id of currently visible result view
                 */
                var resultViewId;

                $(function () {	        
                    console.log($('#right').offset());
                    var callData = JSON.stringify({"serviceName":"AmfphpDiscoveryService", "methodName":"discover","parameters":[]});
                    $.post("<?php echo $config->amfphpEntryPointPath ?>?contentType=application/json", callData, onServicesLoaded);
                    $('#main').hide();  
                    showResultView('tree');


                });

                function setRightDivMaxWidth() {
                    $( "#right" ).css( "maxWidth", ( $( '#main' ).width() - 400) +  "px" );
                }                    
                /**
                 * callback for when service data loaded from server . 
                 * generates method list. 
                 * each method link has its corresponding method object attached as data, and this is retrieved on click
                 * to call openMethodDialog with it.
                 */
                function onServicesLoaded(data)
                {
                    serviceData = data;
                        
                    //generate service/method list
                    var rootUl = $('ul#serviceMethods');
                    $(rootUl).empty();
                    for(serviceName in serviceData){
                        var service = serviceData[serviceName];
                        var serviceLi = $('<li><b>' + serviceName + '</b></li>')
                        .appendTo(rootUl);
                        $(serviceLi).attr('title', service.comment);
                        var serviceUl = $('<ul/>').appendTo(serviceLi);
                        for(methodName in service.methods){
                            var method = service.methods[methodName];
                            var li = $('<li/>')
                            .appendTo(serviceUl);
                            var dialogLink = $('<a/>',{
                                text: methodName,
                                title: method.comment,
                                click: function(){ 
                                    var savedServiceName = $(this).data("serviceName");
                                    var savedMethodName = $(this).data("methodName");
                                    openMethodDialog(savedServiceName, savedMethodName);
                                    return false;
                                }})
                            .appendTo(li);
                            $(dialogLink).data("serviceName", serviceName);    
                            $(dialogLink).data("methodName", methodName);    
                            
                                
                        }
                    }
                    $('.showResultView a').click(function(eventObject){
                        showResultView(eventObject.currentTarget.id);

                    });
                    $('#main').show();
                    $('#right').hide();  
                    setRightDivMaxWidth();
                    $( window ).bind( "resize", setRightDivMaxWidth ); 
                    
                    //test 
                    //openMethodDialog("AuthenticationService", "login");
                    openMethodDialog("AmfphpDiscoveryService", "discover");
                }
                
                /**
                 * sets the max width for the right div.
                 * used on loading services, and when window resizes
                 * */
                function setRightDivMaxWidth(){
                    var availableWidthForRightDiv = $( '#main' ).width() - $('#left').outerWidth(true) - 50;
                    $( "#right" ).css( "maxWidth", availableWidthForRightDiv +  "px" );
                }
                
                /**
                 * shows method dialog so that the user can call the method.
                 * */
                function openMethodDialog(serviceName, methodName){
                    var service = serviceData[serviceName];
                    method = service.methods[methodName];    
                    parameters = method.parameters;
                    //@todo style header for better readability
                    var html = "<h3>" + serviceName + " Service</h3>";
                    html += "<pre>" + service.comment + "</pre>";
                    html += "<h3>" + methodName + " Method</h3>";
                    html += "<pre>" + method.comment + "</pre>";
                    //var html = "<h3>" + method.name + " method on " + serviceName + " service</h3>";
                    if (parameters.length > 0) {

                        html += "\nUse JSON notation for complex values. ";
                        html += "\n<table>";
                        for (i in parameters) {
                            var parameter = parameters[i];
                            var parameterName = parameter.name;
                            html += "\n <tr><td>" + parameterName + "</td><td><textarea class='parameterInputs'/></td></tr>";
                        }
                        html += "\n</table>";
                            
                    } else {
                        html += "This method has no parameters.";
                    }
                    html += "\n<input type='submit' value='Call method &raquo;' onclick='callMethod(\"" + serviceName + "\", \"" + methodName + "\");'>";
                    $('#callDialog').empty();
                    $('#callDialog').append(html);
                    var rightDivTop = Math.round(Math.max(0, $(window).scrollTop() - $('#main').offset().top));
                    console.log(rightDivTop);
                    //note that trying with jquery 'offset' messes up!
                    $('#right').css('top', rightDivTop + 'px');
                    $('#right').show();
                    $('#result').hide();
                      
                }
                
                /**
                 * takes the values typed by user and makes a json service call 
                 * */
                function callMethod(serviceName, methodName){
                    var parameters = [];
                    $.each($('.parameterInputs'), function(index, paramInput){
                        var paramValue = $(paramInput).val();
                        try{
                            //if it's JSON it needs to be parsed to avoid being treated as a string 
                            parameters[index] = JSON.parse(paramValue); 
                        }catch(e){
                            //exception: it's not valid json, so keep as is
                            parameters[index] = paramValue;
                        }
                    });
                    var callData = JSON.stringify({"serviceName":serviceName, "methodName":methodName,"parameters":parameters});
                    callStartTime = $.now();
                    $.post("<?php echo $config->amfphpEntryPointPath ?>?contentType=application/json", callData, onResult);
                }
                
                /**
                 * callback to show service call result
                 * */
                function onResult(data){
                    console.log(data);
                    var callEndTime = $.now() - callStartTime;
                    $('#callDuration').text(callEndTime);
                    var treeData = objToTreeData(data, null);
                    setTreeData(treeData, ".resultView#tree");  
                    $('.resultView#print_r').empty().append('<pre>' + print_r(data, true) + '</pre>');
                    $('.resultView#json').empty().append(JSON.stringify(data, null, true));
                    $('.resultView#php').empty().append(serialize(data));
                    $('.resultView#raw').empty().append('<pre>' + data + '</pre>');
                    $('#result').show();
                        
                                                
                }
                function setTreeData(data, targetDivSelector){
                    $(targetDivSelector).jstree({ 

                        "json_data" : {
                            "data" : data
                            ,
                            "progressive_render" : true

                        },
                        "core" : {
                            "animation" : 0
                        },
                        "plugins" : [ "themes", "json_data", "ui", "hotkeys"],
                        "themes" : {
                            "theme" : "apple"
                        }

                    });

                }

                /**
                 * underline active result view link only
                 * show right result view
                 */
                function showResultView(viewId){
                    $('.showResultView a').removeClass('underline');
                    $('.showResultView a#' + viewId).addClass('underline');
                    $('.resultView').hide();
                    $('.resultView#' + viewId).show();
                    resultViewId = viewId;
                }
                    


            </script>

        </div>
    </body>    
</html>
