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

    <?php $addToTitle = ' - Service Browser';
    require_once(dirname(__FILE__) . '/HtmlHeader.inc.php'); ?>
    <body>
        <?php
        require_once(dirname(__FILE__) . '/LinkBar.inc.php');
        ?>

        <div id="main">
            <div id="left">
                <?php
                if (!$isAccessGranted) {
                    ?>
                    <script>
                        window.location = "./SignIn.php";
                    </script>
                    <?php
                    return;
                }
                require_once(dirname(__FILE__) . '/MainMenu.inc.php');
                ?>
                <div class='menu'>
                    <ul id='serviceMethods' >
                        Loading Service Data...

                    </ul>
                </div>                            
            </div>                    
            <div id="right" class="menu" >
                <div id="callDialog" class="notParamEditor">
                    Choose a Method From the list on the left.
                    <h3 id="serviceHeader"></h3>
                    <pre id="serviceComment"></pre>
                    <h3 id="methodHeader"></h3>
                    <pre id="methodComment"></pre>
                    <span class="notParamEditor" id="jsonTip">Use JSON notation for complex values. </span>    
                    <table id="paramDialogs"><tbody></tbody></table>
                    <span class="notParamEditor" id="noParamsIndicator">This method has no parameters.</span>
                    <div id="callBtn">
                        <input class="notParamEditor" type="submit" value="Call &raquo;" onclick="makeCall()"/>                    
                    </div>

                </div>
                <div id="result"  class="notParamEditor">
                    Result ( call took <span id="callDuration">-</span> ms )  
                    <span class="showResultView">
                        <a id="tree">Tree</a>
                        <a id="print_r">print_r</a>
                        <a id="json">JSON</a>
                        <a id="php">PHP Serialized</a>
                        <a id="raw">Raw</a>
                    </span>
                    <div id="dataView">
                        <div id="tree" class="resultView"></div>
                        <div id="print_r" class="resultView"></div>
                        <div id="json" class="resultView"></div>
                        <div id="php" class="resultView"></div>
                        <div id="raw" class="resultView"></div>
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
                 * name of service being manipulated
                 **/
                var serviceName = "";
                
                /**
                 * name of method being manipulated
                 * */
                var methodName = "";
                    
                /**
                 *call start time, in ms
                 */
                var callStartTime;
                
                /**
                 * id of currently visible result view
                 */
                var resultViewId;
                
                /**
                 * number of currently instanciated parameter edition dialogs. A dilaog is a label and an editor.
                 **/
                var numParamDialogs = 0;
                
                /**
                 * array of pointers to parameter editors
                 * */
                var paramEditors = [];
                
                $(function () {	        
                    var callData = JSON.stringify({"serviceName":"AmfphpDiscoveryService", "methodName":"discover","parameters":[]});
                    $.post("<?php echo $config->resolveAmfphpEntryPointUrl() ?>?contentType=application/json", callData, onServicesLoaded);
                    //@todo error handling with explicit messages. use $.ajax instead of $.post
                    $("#main").hide();  
                    showResultView("tree");
                    document.title = "AmfPHP - Service Browser";
                    $("#titleSpan").text("AmfPHP - Service Browser");


                });

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
                    var rootUl = $("ul#serviceMethods");
                    $(rootUl).empty();
                    for(serviceName in serviceData){
                        var service = serviceData[serviceName];
                        var serviceLi = $("<li><b>" + serviceName + "</b></li>")
                        .appendTo(rootUl);
                        $(serviceLi).attr("title", service.comment);
                        var serviceUl = $("<ul/>").appendTo(serviceLi);
                        for(methodName in service.methods){
                            var method = service.methods[methodName];
                            var li = $("<li/>")
                            .appendTo(serviceUl);
                            var dialogLink = $("<a/>",{
                                text: methodName,
                                title: method.comment,
                                click: function(){ 
                                    var savedServiceName = $(this).data("serviceName");
                                    var savedMethodName = $(this).data("methodName");
                                    manipulateMathod(savedServiceName, savedMethodName);
                                    return false;
                                }})
                            .appendTo(li);
                            $(dialogLink).data("serviceName", serviceName);    
                            $(dialogLink).data("methodName", methodName);    
                            
                                
                        }
                    }
                    $(".showResultView a").click(function(eventObject){
                        showResultView(eventObject.currentTarget.id);

                    });
                    $("#main").show();
                    setRightDivMaxWidth();
                    $( window ).bind( "resize", setRightDivMaxWidth ); 
                    $("#jsonTip").hide();
                    $("#noParamsIndicator").hide();
                    //test
                    //createParamDialog();
                    //createParamDialog();
                     

                    //manipulateMathod("TestService", "returnOneParam");
                    //manipulateMathod("TestService", "returnSum");
                    
                }
                
                /**
                 * sets the max width for the right div.
                 * used on loading services, and when window resizes
                 * */
                function setRightDivMaxWidth(){
                    var availableWidthForRightDiv = $( "#main" ).width() - $("#left").outerWidth(true) - 50;
                    $( "#right" ).css( "maxWidth", availableWidthForRightDiv +  "px" );
                }
                
                /**
                 * to manipulate a parameter we create a reusable dialog in a table.
                 * This dialog is a cell where the parameter name is shown, and a cell containing an editor.
                 * This editor uses a container because of the constraints of the editor: it replaces a div on creation,
                 * and this div must have absolute positioning. 
                 * This container is also used for resizing.
                 * 
                 * */
                function createParamDialog(){
                    //no function, just shorter to read
                    var i = numParamDialogs;
                    //note: this works because the tbody is defined in the html from the start.
                    $("#paramDialogs").find("tbody")
                        .append($("<tr/>")
                            .attr("id", "paramRow" + i)
                            .append($("<td/>").attr("id", "paramLabel" + i))
                            .append($("<td/>")
                                .append($("<div/>")
                                    .addClass("paramEditorContainer")
                                    .attr("id", "paramEditorContainer" + i)
                                    .append($("<div/>")
                                        .attr("id", "paramEditor" + i)
                                    )
                                    
                                )
                            )
                         );  

                    //note : tried doing the following with a css class (.paramEditor) and it failed, so do it directly here
                    $("#paramEditor" + i).css(
                    {"position": "absolute",
                        "top": 0,
                        "right": 0,
                        "bottom": 0,
                        "left": 0}
                    );
                    //for testing
                    /*
                    $("#paramLabel" + i).text("paramLabel");    
                    $("#paramEditor" + i).text("paramEditor");    
                    */  

                    var editor = ace.edit("paramEditor" + i);
                     
                    editor.setTheme("ace/theme/textmate");
                    editor.getSession().setMode("ace/mode/json");
                    editor.getSession().setUseWrapMode(true);
                    
                    paramEditors.push(editor);
                    
                    $("#paramEditorContainer" + i).resizable({
                        stop: function( event, ui ) {
                            editor.resize();
                        }
                    });
                    
                    numParamDialogs++;

                }
                /**
                 * manipulates call dialog so that the user can call the method.
                 * */
                function manipulateMathod(serviceName, methodName){
                    this.serviceName = serviceName;
                    this.methodName = methodName;
                    var service = serviceData[serviceName];
                    var method = service.methods[methodName];   
                    var parameters = method.parameters;
                    $("#serviceHeader").text(serviceName + " Service");
                    $("#serviceComment").text(service.comment);
                    $("#methodHeader").text(methodName + " Method");
                    $("#methodComment").text(method.comment);
                    if(parameters.length == 0){
                        $("#jsonTip").hide();
                        $("#noParamsIndicator").show();
                    }else{
                        $("#jsonTip").show();
                        $("#noParamsIndicator").hide();
                    }
                    
                    var i;
                    for (i = 0; i< parameters.length; i++) {
                        if(i > numParamDialogs - 1){
                            createParamDialog();
                        }
                        
                        var parameter = parameters[i];
                        $("#paramLabel" + i).text(parameter.name);
                        paramEditors[i].setValue(parameter.example);
                        //make sure dialog is visible
                        $("#paramRow" + i).show();

                    }
                    
                    //hide unused dialogs
                    for (i = parameters.length; i< numParamDialogs; i++) {
                        $("#paramRow" + i).hide();
                        
                    }
                    
                    var rightDivTop = Math.round(Math.max(0, $(window).scrollTop() - $("#main").offset().top));
                    //note that trying with jquery "offset" messes up!
                    $("#right").css("top", rightDivTop + "px");
                    
                      
                }
                
                /**
                 * takes the values typed by user and makes a json service call 
                 * */
                function makeCall(){
                    var parameters = [];
                    for(var i=0; i < paramEditors.length; i++){
                        parameters.push(paramEditors[i].getValue());
                         console.log(paramEditors[i].getValue());
                    }
                    
                    var callData = JSON.stringify({"serviceName":serviceName, "methodName":methodName,"parameters":parameters});
                    callStartTime = $.now();
                    $.post("<?php echo $config->resolveAmfphpEntryPointUrl() ?>?contentType=application/json", callData, onResult);
                }
                
                /**
                 * callback to show service call result
                 * */
                function onResult(data){
                    console.log(data);
                    var callEndTime = $.now() - callStartTime;
                    $("#callDuration").text(callEndTime);
                    var treeData = objToTreeData(data, null);
                    setTreeData(treeData, ".resultView#tree");  
                    $(".resultView#print_r").empty().append("<pre>" + print_r(data, true) + "</pre>");
                    $(".resultView#json").empty().append(JSON.stringify(data, null, true));
                    $(".resultView#php").empty().append(serialize(data));
                    $(".resultView#raw").empty().append("<pre>" + data + "</pre>");
                    $("#result").show();
                        
                                                
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
                    $(".showResultView a").removeClass("underline");
                    $(".showResultView a#" + viewId).addClass("underline");
                    $(".resultView").hide();
                    $(".resultView#" + viewId).show();
                    resultViewId = viewId;
                }
                    


            </script>

        </div>
    </body>    
</html>
