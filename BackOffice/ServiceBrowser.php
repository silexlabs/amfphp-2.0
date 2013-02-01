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
require_once(dirname(__FILE__) . '/../Amfphp/ClassLoader.php');
$accessManager = new Amfphp_BackOffice_AccessManager();
$isAccessGranted = $accessManager->isAccessGranted();
?>

<html>
    <?php require_once(dirname(__FILE__) . '/HtmlHeader.inc.php'); ?>
    <body>
        <?php
        $addToTitle = ' - Service Browser';
        require_once(dirname(__FILE__) . '/LinkBar.inc.php');
        ?>

        <div id='main'>
            <div class="left">
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

            /**
             * create tree data string for the representation of a result object. A bit like a var dump but for displaying with jstree
             * recursive.
             * @todo reunderstand and comment this objname thing...
             * @param mixed $obj
             * @param string $objName
             * @return mixed
             */
            function objToTreeData($obj, $objName) {
                if (is_array($obj) || is_object($obj)) {

                    $children = array();
                    foreach ($obj as $key => $subObj) {
                        $children[] = objToTreeData($subObj, $key);
                    }
                    if ($objName !== null) {
                        $ret = array();
                        $type = '';
                        if (is_array($obj)) {
                            $type = 'array';
                        } else {
                            $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
                            if (isset($obj->$explicitTypeField)) {
                                $type = $obj->$explicitTypeField;
                            } else {
                                $type = get_class($obj);
                                if ($type == 'stdClass') {
                                    //easier to read
                                    $type = 'anonymous object';
                                }
                            }
                        }
                        $ret['data'] = "$objName ( $type )";
                        $ret['children'] = $children;
                        //$ret['state'] = 'open';
                        return $ret;
                    } else {
                        return $children;
                    }
                } else {
                    return "$objName => $obj";
                }
            }

            $config = new Amfphp_BackOffice_Config();
            $serviceCaller = new Amfphp_BackOffice_IncludeServiceCaller($config->amfphpEntryPointPath);
//load service descriptors
            $services = $serviceCaller->call("AmfphpDiscoveryService", "discover");

//what are we calling? 
            $callMethodName = null;
            if (isset($_GET['methodName'])) {
                $callMethodName = $_GET['methodName'];
            }
            $callServiceName = null;
            if (isset($_GET['serviceName'])) {
                $callServiceName = $_GET['serviceName'];
            }

            $callParameters = $_POST;
            /**
             * 3 cases: 
             * - POST has some content, this means there is at least one parameter and the call must be made. set to true.
             * - GET callWithoutParams is set, this means it's a call to a method without parameters. set to true.
             * - lastly, it can be just a call to select a service method, but without a call. set to false.
             *  
             */
            $makeServiceCall = false;
            if ((count($_POST) > 0) || isset($_GET['callWithoutParams'])) {
                $makeServiceCall = true;
            }


            echo "\n<ul class='menu' id='serviceMethods'>";
            if($services instanceof Exception){
                throw $services;
            }
            if (!is_array($services)) {
                ?>
                No services available. Please check : <br/>
                <ul>
                    <li>That your service classes don't contain syntax errors</li>
                    <li>BackOffice Configuration in BackOffice/Config.php, specifically $amfphpEntryPointUrl</li>
                    
                </ul>
                Service Object as returned by AmfphpDiscoveryService:
                <br/> <br/>
                <pre><?php            var_dump($services)?></pre>
                <?php
                return;
            }
//generate service/method menu
            foreach ($services as $service) {
                echo "\n <li><b>$service->name</b>";
                echo "\n<ul>";
                foreach ($service->methods as $method) {
                    if (substr($method->name, 0, 1) == '_') {
                        //methods starting with a '_' as they are reserved, so filter them out 
                        continue;
                    }
                    echo "\n <li><a href='?serviceName=" . $service->name . "&methodName=" . $method->name . "'>" . $method->name . "</a></li>";
                }
                echo "\n</ul>";
                echo "</li>";
            }
            echo "\n</ul>\n";

            echo "</div>";

            echo "\n<div class='userInput' id='callDialog'>";
//generate method calling interface
            if ($callServiceName && $callMethodName) {
                $serviceDescriptor = $services[$callServiceName];
                $methodDescriptor = $serviceDescriptor->methods[$callMethodName];
                $parameterDescriptors = $methodDescriptor->parameters;
                echo "<h3>$callMethodName method on $callServiceName service</h3>";
                if (count($parameterDescriptors) > 0) {

                    echo "\nUse JSON notation for complex values. ";
                    echo "\n<form action='?serviceName=$callServiceName&amp;methodName=$callMethodName' method='POST'>\n<table>";
                    foreach ($parameterDescriptors as $parameterDescriptor) {
                        $parameterName = $parameterDescriptor->name;
                        echo "\n <tr><td>$parameterName</td><td><input name='$parameterName' ";
                        if ($callParameters) {
                            echo "value='" . $callParameters[$parameterName] . "'";
                        }
                        echo "></td></tr>";
                    }
                    echo "\n</table>\n<input type='submit' value='Call method &raquo;'></form>";
                } else {
                    echo "This method has no parameters.";
                    echo "\n<form action='?serviceName=$callServiceName&amp;methodName=$callMethodName&amp;callWithoutParams' method='POST'>\n";
                    echo "\n<input type='submit' value='Call method &raquo;'></form>";
                }
            }

//make service call and show results 
            $resultTreeData = null;
            if ($makeServiceCall) {
                $callStartTimeMs = microtime(true);
                $parsedCallParameters = array();

                foreach ($callParameters as $value) {
                    //try to get json decoded value
                    $decoded = json_decode($value);
                    if ($decoded !== null) {
                        $parsedCallParameters[] = $decoded;
                    } else {
                        $parsedCallParameters[] = $value;
                    }
                }
                $result = $serviceCaller->call($callServiceName, $callMethodName, $parsedCallParameters);
                $callDurationMs = round((microtime(true) - $callStartTimeMs) * 1000);


                $resultTreeData = objToTreeData($result, null);
                ?>
                <h3>Result ( call took <?php echo $callDurationMs; ?> " ms )  

                    <span class="showResultView">
                        <a id="tree">Tree</a>
                        <a id="print_r">print_r</a>
                        <a id="json">JSON</a>
                        <a id="php">PHP Serialized</a>
                        <a id="raw">Raw</a>
                    </span>
                </h3>
                <pre>
                    <div id='tree' class='resultView'></div>
                    <div id="print_r" class='resultView'><?php echo print_r($result, true); ?></div>
                    <div id="json" class='resultView'><?php echo json_encode($result); ?></div>
                    <div id="php" class='resultView'><?php echo serialize($result); ?></div>
                    <div id="raw" class='resultView'><?php
                    if(method_exists($result, '__toString')){
                        echo $result;
                    }else{
                        echo "cannot display raw";
                    }
                     ?></div>
                </pre>

                <?php
            }
            echo "\n</div>\n";
            echo "\n</div>\n";
            ?>

            <script>
        
                function setTreeData(data, targetDivSelector){
                    $(targetDivSelector).bind("loaded.jstree", function (event, data) {
                        resetWidth();
                    }).bind("after_open.jstree", function (node) {
                        resetWidth();
                    }).jstree({ 

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
                 * sets the main div width to avoid wrapping
                 * */
                function resetWidth(){
                    //adjust size of main div so that the callDialog doesn't wrap
                    var totalWidth = $('.menu#backOffice').width() + $('.menu#serviceMethods').width() + $('#callDialog').width();
                    $('#main').width(totalWidth + 200);
        
                }
    
                //current result view id
                var resultViewId;
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
    
                $(function () {	        
                    setTreeData(<?php echo json_encode($resultTreeData); ?>, ".resultView#tree");  
                    $('.showResultView a').click(function(eventObject){
                        showResultView(eventObject.currentTarget.id);
                        resetWidth();
            
                    });
                    //default result view is tree
                    var startResultViewId = $.cookie('resultViewId');
                    if(startResultViewId == null){
                        startResultViewId = 'tree';
                    };
        
                    showResultView(startResultViewId);
                    resetWidth();
        
                    //reset scroll position to one from last time. position callDialog div so that it's just below the top of the view port
                    var oldScrollPos = parseFloat($.cookie('scrollPos'));
                    window.scrollTo(0, oldScrollPos);
                    var callDialogY = Math.max(oldScrollPos + 20, $('#callDialog').offset().top);
                    $('#callDialog').offset({ top: callDialogY});
        
                    //save state on leaving page
                    $(window).bind('beforeunload', function(e) {   
                        $.cookie('scrollPos', $(window).scrollTop());
                        $.cookie('resultViewId', resultViewId);
            
                    });
                
                });





            </script>

        </div>
    </body>    
</html>



