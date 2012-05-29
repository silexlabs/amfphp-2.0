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
$addToTitle = ' - Service Browser';
require_once(dirname(__FILE__) . '/Top.php');

$config = new Amfphp_BackOffice_Config();
$serviceCaller = new Amfphp_BackOffice_ServiceCaller($config->resolveAmfphpEntryPointUrl());
//load service descriptors
$services = $serviceCaller->makeAmfphpJsonServiceCall("AmfphpDiscoveryService", "discover");

//what are we calling? 
$callMethodName = null;
if(isset($_GET['methodName'])){
    $callMethodName = $_GET['methodName'];
}
$callServiceName = null;
if(isset($_GET['serviceName'])){
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
if((count($_POST) > 0) || isset($_GET['callWithoutParams'])){
    $makeServiceCall = true;
}

echo "\n<ul id='menu'>";
if($services == null){
    ?>
    No services available. Please check : <br/>
    <ul>
        <li>That your service classes don't contain syntax errors</li>
        <li>BackOffice Configuration in BackOffice/Config.php, specifically $amfphpEntryPointUrl</li>
    </ul>
    <?php
    return;
}
//generate service/method menu
foreach ($services as $service) {
    echo "\n <li><b>$service->name</b>";
    echo "\n<ul>";
    foreach ($service->methods as $method) {
        if(substr($method->name, 0, 1) == '_'){
            //methods starting with a '_' as they are reserved, so filter them out 
            continue;
        }
        echo "\n <li><a href='?serviceName=" . $service->name . "&methodName=" . $method->name . "'>" . $method->name . "</a></li>";
    }
    echo "\n</ul>";
    echo "</li>";
}
echo "\n</ul>\n";
echo "\n<div id='content'>";

//generate method calling interface
if ($callServiceName && $callMethodName) {
    $serviceDescriptor = $services->$callServiceName;
    $methodDescriptor = $serviceDescriptor->methods->$callMethodName;
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
        echo "\n<input type='submit' value='Call method'></form>";
    }
}

//make service call and show results 
if ($makeServiceCall) {
    $callStartTimeMs = microtime(true);
    //$_POST is associative. Transform it into an array, as it's the format AmfphpJson expects.
    $paramArray = array();
    foreach($callParameters as $value){
        $paramArray[] = $value;
    }
    $result = $serviceCaller->makeAmfphpJsonServiceCall($callServiceName, $callMethodName, $paramArray);
    $callDurationMs = round((microtime(true) - $callStartTimeMs) * 1000);

    
    echo "<h3>Result ( call took " . $callDurationMs . " ms )</h3>";
    echo '<pre>';
    echo print_r($result, true);
    echo '</pre>';
}
echo "</div>\n";





?>
    </body>
</html>
