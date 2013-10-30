<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_BackOffice_ClientGenerator
 */
/**
 * includes
 */
require_once(dirname(__FILE__) . '/ClassLoader.php');
$accessManager = new Amfphp_BackOffice_AccessManager();
$isAccessGranted = $accessManager->isAccessGranted();
?>

<html>
    <?php $addToTitle = ' - Client Generator';
    require_once(dirname(__FILE__) . '/HtmlHeader.inc.php'); ?>
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
                <div class="menu" id="callDialog">

                    Use one of the following generators to generate a client Stub project. <br/>
                    The project includes :<br/><br/>
                    <ul>
                        <li>code to make calling your services easy</li>
                        <li>a starting point for a user interface you can customize</li>
                    </ul>
                    <br/><br/>
                    <?php 
                    $writeTestFolder = AMFPHP_BACKOFFICE_ROOTPATH . 'ClientGenerator/Generated/';
                    if(!is_writable($writeTestFolder)){
                        echo "WARNING: could not write to ClientGenerator/Generated/. <br/> You need to change your permissions to be able to use the client generator.<br/><br/>";
                    }
                    
                    ?>
                    Code will be generated for the following services:
                    <br/><br/>
                    <ul id="serviceList"></ul><br/>
                    <?php
                    $generatorManager = new Amfphp_BackOffice_ClientGenerator_GeneratorManager();
                    $generators = $generatorManager->loadGenerators(array('ClientGenerator/Generators'));

                    $config = new Amfphp_BackOffice_Config();
                    

//links for each generator
                    echo "\n<table class='notParamEditor'>";
                    foreach ($generators as $generator) {
                        echo "\n    <tr>";
                        $generatorName = $generator->getUiCallText();
                        $generatorClass = get_class($generator);
                        $infoUrl = $generator->getInfoUrl();
                        echo "\n        <td>$generatorName</td>";
                        echo "\n        <td><a href=\"$infoUrl\">Info</a></td>";
                        echo "\n        <td><a onclick='generate(\"" . $generatorClass . "\")'>Generate!</a></td>";
                        echo "\n    </tr>";
                    }
                    ?>
                    <tr><td>IOS</td><td><a href="http://www.silexlabs.org/amfphp/documentation/client-generators/ios/">Info/Vote Up</a></td>        <td>Not Available Yet</td>    </tr>
                    <tr><td>Haxe</td><td><a href="http://www.silexlabs.org/amfphp/documentation/client-generators/haxe/">Info/Vote Up</a></td>        <td>Not Available Yet</td>    </tr>
                    <tr><td>Android</td><td><a href="http://www.silexlabs.org/amfphp/documentation/client-generators/android/">Info/Vote Up</a></td>        <td>Not Available Yet </td></tr>
                    <tr><td>Write your Own?</td><td><a href="http://www.silexlabs.org/amfphp/documentation/client-generators/writing-you-own-client-generator/">Info</a></td>        <td></td></tr>


                    </table>
                    <div id="statusMessage" style="max-width:100%"></div>
                </div>
            </div>
            <script>
                $(function () {	        
                    document.title = "AmfPHP - Client Generator";
                    $("#titleSpan").text("AmfPHP - Client Generator");
                    
                    var callData = JSON.stringify({"serviceName":"AmfphpDiscoveryService", "methodName":"discover","parameters":[]});
                    var request = $.ajax({
                      url: "<?php echo $config->resolveAmfphpEntryPointUrl() ?>?contentType=application/json",
                      type: "POST",
                      data: callData
                    });

                    request.done(onServicesLoaded);

                    request.fail(function( jqXHR, textStatus ) {
                        displayCallErrorMessage(textStatus + "<br/><br/>" + jqXHR.responseText);
                    });

                    setRightDivMaxWidth();
                    $( window ).bind( "resize", setRightDivMaxWidth ); 


                });    
                
                                
                /**
                 * sets the max width for the right div.
                 * used on loading services, and when window resizes
                 * */
                function setRightDivMaxWidth(){
                    var availableWidthForRightDiv = $( "#main" ).width() - $("#left").outerWidth(true) - 50;
                    $( "#right" ).css( "maxWidth", availableWidthForRightDiv +  "px" );
                }
                
                function displayCallErrorMessage(html){
                    $('#statusMessage').html(html);
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
                        displayCallErrorMessage(data);
                        return;
                    }
                    serviceData = data;
                        
                    //generate service/method list
                    var rootUl = $("ul#serviceList");
                    $(rootUl).empty();
                    for(serviceName in serviceData){
                        var service = serviceData[serviceName];
                        var serviceLi = $("<li>" + serviceName + "</li>")
                        .appendTo(rootUl);
                        $(serviceLi).attr("title", service.comment);
                        $("<ul/>").appendTo(serviceLi);
                    }
                     

                    
                }
                
                function generate(generatorClass){
                    var callData = JSON.stringify({"serviceName":"AmfphpDiscoveryService", "methodName":"discover","parameters":[]});
                    var request = $.ajax({
                      url: "ClientGeneratorBackend.php?generatorClass=" + generatorClass,
                      type: "POST",
                      data: JSON.stringify(serviceData)
                    });

                    request.done(onGenerationDone);

                    request.fail(function( jqXHR, textStatus ) {
                        displayCallErrorMessage(textStatus + "<br/><br/>" + jqXHR.responseText);
                    });

 
                }
                
                function onGenerationDone(data){
                    $('#statusMessage').html(data);
                }

            </script>
