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

    <?php
    require_once(dirname(__FILE__) . '/HtmlHeader.inc.php');
    ?>
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
                    <h2>Services and Methods</h2>
                    <ul id='serviceMethods' >
                        Loading Service Data...

                    </ul>
                </div>                            
            </div>                    
            <div id="right" class="menu" >
                <div id="methodDialog" class="notParamEditor">
                    <h3 id="serviceHeader">Choose a Method From the list on the left. </h3>
                    <pre id="serviceComment"></pre>
                    <h3 id="methodHeader"></h3>
                    <pre id="methodComment"></pre>
                    <span class="notParamEditor" id="jsonTip">Use JSON notation for complex values. </span>    
                    <table id="paramDialogs"><tbody></tbody></table>
                    <span class="notParamEditor" id="noParamsIndicator">This method has no parameters.</span>
                    <div id="callDialog">
                        <a onclick="toggleAdvanced()" id="toggleAdvancedLink">Show Advanced Call Options</a>   
                        <div id="basicCall">
                            <input class="notParamEditor" type="submit" value="Call" onclick="makeJsonCall()"/>  
                        </div>
                        <div id="advancedCall">
                            <input class="notParamEditor" type="submit" value="Call JSON" onclick="makeJsonCall()"/>  
                            <input class="notParamEditor" type="submit" value="Call AMF" onclick="makeAmfCall()"/>       
                            <input class="notParamEditor" type="submit" id="toggleLoadTestBtn" value="Start Load Test AMF" onclick="toggleLoadTest()"/>       
                            Number of Concurrent Requests
                            <input id="concurrencyInput" value="1"/>       
                            <div id="amfCallerContainer">
                                Flash Player is needed to make AMF calls. 
                                <a href="http://www.adobe.com/go/getflashplayer">
                                    <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
                                </a>
                            </div>                        

                        </div>

                    </div>

                </div>
                <div id="result"  class="notParamEditor">
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
 * The current method's parameters. Here just for easy access.
 */
var methodParams;

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
 * array of pointers to parameter editors. 
 * */
var paramEditors = [];

/**
 * reference to amf caller, set once it is loaded. Used to make AMF calls.
 * */
var amfCaller;

/**
 * is load testing
 */
var isLoadTesting;

/**
 * maintains state for showing or hiding advanced call options
 * */
var isAdvancedDialogVisible;

/**
 * where the requests are sent to
 * */
var amfphpEntryPointUrl = "<?php echo $config->resolveAmfphpEntryPointUrl() ?>";

$(function () {	       
    //fix for ie 8
    if (!window.console) console = {log: function() {}}; 

    var callData = JSON.stringify({"serviceName":"AmfphpDiscoveryService", "methodName":"discover","parameters":[]});
    var request = $.ajax({
        url: "<?php echo $config->resolveAmfphpEntryPointUrl() ?>?contentType=application/json",
        type: "POST",
        data: callData
    });

    request.done(onServicesLoaded);

    request.fail(function( jqXHR, textStatus ) {
        displayStatusMessage(textStatus + "<br/><br/>" + jqXHR.responseText);
    });

    showResultView("tree");
    document.title = "AmfPHP - Service Browser";
    $("#titleSpan").text("AmfPHP - Service Browser");
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
            console.log(e);
        }

    });

    isLoadTesting = false;
    $("#callDialog").hide();
    $("#advancedCall").hide();
    isAdvancedDialogVisible = false;
    if($.cookie('advanced')){
        toggleAdvanced();
    }
    


});

function displayStatusMessage(html){
    $('#methodDialog').html(html);
}

/**
 * callback for when service data loaded from server . 
 * generates method list. 
 * each method link has its corresponding method object attached as data, and this is retrieved on click
 * to call openMethodDialog with it.
 */
function onServicesLoaded(data)
{
    if(typeof data == "string"){
        displayStatusMessage(data);
        return;
    }
    serviceData = data;

    //generate service/method list
    var rootUl = $("ul#serviceMethods");
    $(rootUl).empty();
    for(serviceName in serviceData){
        var service = serviceData[serviceName];
        var serviceLi = $("<li>" + serviceName + "</li>")
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
                    manipulateMethod(savedServiceName, savedMethodName);
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
    resize();
    $( window ).bind( "resize", resize ); 
    $("#jsonTip").hide();
    $("#noParamsIndicator").hide();


    //for testing
    //manipulateMethod("TestService", "testComplicatedTypedObj");

    <?php
    if ($config->fetchAmfphpUpdates) {
        //only load update info once services loaded(that's the important stuff)
        echo 'showAmfphpUpdates();';
    }
    ?>

    $( "#divRss" ).css( "maxWidth", $("#serviceMethods").outerWidth(true) +  "px" );

}

/**
 * sets the max width for the right div.
 * used on loading services, and when window resizes
 * */
function resize(){
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

    var i = paramEditors.length;
    //note: this works because the tbody is defined in the html from the start.
    $("#paramDialogs").find("tbody")
    .append($("<tr/>")
    .attr("id", "paramRow" + i)
    .append($("<td/>").attr("id", "paramLabel" + i).attr("class", "notParamEditor"))
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



}
/**
 * manipulates call dialog so that the user can call the method.
 * */
function manipulateMethod(serviceName, methodName){
    $("#callDialog").show();
    this.serviceName = serviceName;
    this.methodName = methodName;
    var service = serviceData[serviceName];
    var method = service.methods[methodName];   
    methodParams = method.parameters;
    $("#serviceHeader").text(serviceName + " Service");
    $("#serviceComment").text(service.comment);
    $("#methodHeader").text(methodName + " Method");
    $("#methodComment").text(method.comment);
    if(methodParams.length == 0){
        $("#jsonTip").hide();
        $("#noParamsIndicator").show();
    }else{
        $("#jsonTip").show();
        $("#noParamsIndicator").hide();
    }

    var i;
    for (i = 0; i< methodParams.length; i++) {
        if(i > paramEditors.length - 1){
            createParamDialog();
        }

        var parameter = methodParams[i];
        $("#paramLabel" + i).text(parameter.name);
        paramEditors[i].setValue(parameter.example);
        //make sure dialog is visible
        $("#paramRow" + i).show();

    }

    //hide unused dialogs
    for (i = methodParams.length; i< paramEditors.length; i++) {
        $("#paramRow" + i).hide();

    }

    var rightDivTop = Math.round(Math.max(0, $(window).scrollTop() - $("#main").offset().top));
    //note that trying with jquery "offset" messes up!
    $("#right").css("top", rightDivTop + "px");

    resize();  
}

/**
 * get the call parameter values from the user interface
 * @returns array
 * */
function getCallParameterValues(){
    var values = [];
    for(var i=0; i < methodParams.length; i++){
        var value = paramEditors[i].getValue();
        try{
            //if it's JSON it needs to be parsed to avoid being treated as a string 
            value = JSON.parse(value.trim()); 
        }catch(e){
            //exception: it's not valid json, so keep as is

        }
        values.push(value);
    }
    return values;

}
/**
 * takes the values typed by user and makes a json service call 
 * */
function makeJsonCall(){

    var callData = JSON.stringify({"serviceName":serviceName, "methodName":methodName,"parameters":getCallParameterValues()});
    callStartTime = $.now();
    $.post(amfphpEntryPointUrl + "?contentType=application/json", callData, onResult);
}

/**
 * make a call using AMF(via the AMF Caller SWF)
 * show an error message if the AMF Caller is not available
 * */
function makeAmfCall(){
    if(!amfCaller || !amfCaller.isAlive()){
        alert('AMF Caller not available.');
    }
    callStartTime = $.now();
    amfCaller.call(amfphpEntryPointUrl, serviceName + "/" + methodName, getCallParameterValues());

}

function toggleLoadTest(){
    if(!isLoadTesting){
        var concurrency = parseInt($("#concurrencyInput").val());
        if(isNaN(concurrency)){
            alert("Invalid number of concurrent requests");
            return;
        }
    }
    
    isLoadTesting = !isLoadTesting;
    if(isLoadTesting){
        $("#toggleLoadTestBtn").prop("value", "Stop Load Test AMF");
        amfCaller.loadTest(amfphpEntryPointUrl, serviceName + "/" + methodName, concurrency, getCallParameterValues());
        
    }else{
        $("#toggleLoadTestBtn").prop("value", "Start Load Test AMF");
        amfCaller.stopLoadTest();
        
    }

}

function onLoadTestResult(callsPerSec){
    console.log(callsPerSec);
    onResult(callsPerSec + ' calls per second');
}

/**
 * callback to show service call result. 
 * @param data the returned data
 * */
function onResult(data){
    console.log(data);

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

function toggleAdvanced(){
    isAdvancedDialogVisible = !isAdvancedDialogVisible;

    if(isAdvancedDialogVisible){
        $("#advancedCall").show();
        $("#basicCall").hide();
        $("#toggleAdvancedLink").text("Hide Advanced Call Options");
        $.cookie('advanced', true);
    }else{
        $("#advancedCall").hide();
        $("#basicCall").show();
        $("#toggleAdvancedLink").text("Show Advanced Call Options");
        $.removeCookie('advanced');

    }
    
}



            </script>

        </div>
    </body>    
</html>
