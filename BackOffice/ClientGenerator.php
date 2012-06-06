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
require_once(dirname(__FILE__) . '/ClientGenerator/Generators/AmfphpFlashClientGenerator/AmfphpFlashClientGenerator.php');
$addToTitle = ' - Client Generator';
require_once(dirname(__FILE__) . '/Top.php');
?>

<div id='menu'>
    Use one of the following generators to generate a client Stub project. <br/>
    These include :<br/><br/>
    <ul>
        <li>code to make calling your services easy</li>
        <li>a starting point for a user interface you can customize</li>
    </ul>
    <br/><br/>
    Code will be generated for the following services:
    <br/><br/>
<?php
$generatorManager = new Amfphp_BackOffice_ClientGenerator_GeneratorManager();
$generators = $generatorManager->loadGenerators(array('ClientGenerator/Generators'));

$config = new Amfphp_BackOffice_Config();
$amfphpUrl = $config->resolveAmfphpEntryPointUrl();
$serviceCaller = new Amfphp_BackOffice_ServiceCaller($amfphpUrl);
//load service descriptors
$services = $serviceCaller->makeAmfphpJsonServiceCall("AmfphpDiscoveryService", "discover");
//remove discovery service from list
unset ($services->AmfphpDiscoveryService);
//list services 
echo '<ul>';
foreach ($services as $service) {
    echo "<li>$service->name</li>";
}
echo '</ul>';
        

//links for each generator
echo "\n<table>";
foreach ($generators as $generator) {
    echo "\n    <tr>";
    $generatorName = $generator->getUiCallText();
    $generatorClass = get_class($generator);
    $infoUrl = $generator->getInfoUrl();
    echo "\n        <td>$generatorName</td>";
    echo "\n        <td><a href=\"$infoUrl\">Info</a></td>";
    echo "\n        <td><a href=\"?generate=$generatorClass\">Generate!</a></td>";
    echo "\n    </tr>";
}
echo '<tr><td>IOS</td><td><a href="http://www.silexlabs.org/amfphp/documentation/client-generators/ios/">Info/Vote Up</a></td>        <td>Not Available Yet</td>    </tr>';
echo '<tr><td>Flex</td><td><a href="http://www.silexlabs.org/amfphp/documentation/client-generators/flex/">Info/Vote Up</a></td>        <td>Not Available Yet</td></tr>';
echo '<tr><td>Haxe</td><td><a href="http://www.silexlabs.org/amfphp/documentation/client-generators/haxe/">Info/Vote Up</a></td>        <td>Not Available Yet</td>    </tr>';
echo '<tr><td>Android</td><td><a href="http://www.silexlabs.org/amfphp/documentation/client-generators/android/">Info/Vote Up</a></td>        <td>Not Available Yet </td></tr>';
echo '<tr><td>Write your Own?</td><td><a href="http://www.silexlabs.org/amfphp/documentation/client-generators/missing/">Info</a></td>        <td></td></tr>';


echo "\n</table>";
echo "\n</div>";
//
if (isset($_GET['generate'])) {
    //test values
    /* $services = json_decode('{"ExampleService":{"name":"ExampleService","methods":{"returnOneParam":{"name":"returnOneParam","parameters":[{"name":"param","type":"","_explicitType":"AmfphpDiscovery_ParameterDescriptor"}],"_explicitType":"AmfphpDiscovery_MethodDescriptor"}},"_explicitType":"AmfphpDiscovery_ServiceDescriptor"},"SommeriaSearchService":{"name":"SommeriaSearchService","methods":{"searchTwitter":{"name":"searchTwitter","parameters":[{"name":"query","type":"","_explicitType":"AmfphpDiscovery_ParameterDescriptor"}],"_explicitType":"AmfphpDiscovery_MethodDescriptor"}},"_explicitType":"AmfphpDiscovery_ServiceDescriptor"},"AmfphpDiscoveryService":{"name":"AmfphpDiscoveryService","methods":{"discover":{"name":"discover","parameters":[],"_explicitType":"AmfphpDiscovery_MethodDescriptor"}},"_explicitType":"AmfphpDiscovery_ServiceDescriptor"}}');
      $services = json_decode('{"AuthenticationService":{"name":"AuthenticationService","methods":{"login":{"name":"login","parameters":[{"name":"userId","type":"","_explicitType":"AmfphpDiscovery_ParameterDescriptor"},{"name":"password","type":"","_explicitType":"AmfphpDiscovery_ParameterDescriptor"}],"_explicitType":"AmfphpDiscovery_MethodDescriptor"},"logout":{"name":"logout","parameters":[],"_explicitType":"AmfphpDiscovery_MethodDescriptor"},"adminMethod":{"name":"adminMethod","parameters":[],"_explicitType":"AmfphpDiscovery_MethodDescriptor"}},"_explicitType":"AmfphpDiscovery_ServiceDescriptor"},"ByteArrayTestService":{"name":"ByteArrayTestService","methods":{"uploadCompressedByteArray":{"name":"uploadCompressedByteArray","parameters":[{"name":"ba","type":"Amfphp_Core_Amf_Types_ByteArray","_explicitType":"AmfphpDiscovery_ParameterDescriptor"}],"_explicitType":"AmfphpDiscovery_MethodDescriptor"}},"_explicitType":"AmfphpDiscovery_ServiceDescriptor"},"DummyService":{"name":"DummyService","methods":{"returnNull":{"name":"returnNull","parameters":[],"_explicitType":"AmfphpDiscovery_MethodDescriptor"}},"_explicitType":"AmfphpDiscovery_ServiceDescriptor"},"ExampleSerializationDebugService":{"name":"ExampleSerializationDebugService","methods":{"getDataThatCreatesProblems":{"name":"getDataThatCreatesProblems","parameters":[],"_explicitType":"AmfphpDiscovery_MethodDescriptor"},"getSerializedObject":{"name":"getSerializedObject","parameters":[],"_explicitType":"AmfphpDiscovery_MethodDescriptor"}},"_explicitType":"AmfphpDiscovery_ServiceDescriptor"},"bla\/BlaService":{"name":"bla\/BlaService","methods":{"returnDouble":{"name":"returnDouble","parameters":[{"name":"param","type":"","_explicitType":"AmfphpDiscovery_ParameterDescriptor"}],"_explicitType":"AmfphpDiscovery_MethodDescriptor"}},"_explicitType":"AmfphpDiscovery_ServiceDescriptor"},"SommeriaSearchService":{"name":"SommeriaSearchService","methods":{"searchTwitter":{"name":"searchTwitter","parameters":[{"name":"query","type":"","_explicitType":"AmfphpDiscovery_ParameterDescriptor"}],"_explicitType":"AmfphpDiscovery_MethodDescriptor"}},"_explicitType":"AmfphpDiscovery_ServiceDescriptor"},"TestService":{"name":"TestService","methods":{"returnOneParam":{"name":"returnOneParam","parameters":[{"name":"param","type":"","_explicitType":"AmfphpDiscovery_ParameterDescriptor"}],"_explicitType":"AmfphpDiscovery_MethodDescriptor"},"returnSum":{"name":"returnSum","parameters":[{"name":"number1","type":"","_explicitType":"AmfphpDiscovery_ParameterDescriptor"},{"name":"number2","type":"","_explicitType":"AmfphpDiscovery_ParameterDescriptor"}],"_explicitType":"AmfphpDiscovery_MethodDescriptor"},"returnNull":{"name":"returnNull","parameters":[],"_explicitType":"AmfphpDiscovery_MethodDescriptor"},"returnBla":{"name":"returnBla","parameters":[],"_explicitType":"AmfphpDiscovery_MethodDescriptor"},"throwException":{"name":"throwException","parameters":[{"name":"arg1","type":"","_explicitType":"AmfphpDiscovery_ParameterDescriptor"}],"_explicitType":"AmfphpDiscovery_MethodDescriptor"},"returnAfterOneSecond":{"name":"returnAfterOneSecond","parameters":[],"_explicitType":"AmfphpDiscovery_MethodDescriptor"}},"_explicitType":"AmfphpDiscovery_ServiceDescriptor"},"AmfphpDiscoveryService":{"name":"AmfphpDiscoveryService","methods":{"discover":{"name":"discover","parameters":[],"_explicitType":"AmfphpDiscovery_MethodDescriptor"}},"_explicitType":"AmfphpDiscovery_ServiceDescriptor"}} ');
      $services = json_decode('{"TestService":{"name":"TestService","methods":{"returnOneParam":{"name":"returnOneParam","parameters":[{"name":"param","type":"","_explicitType":"AmfphpDiscovery_ParameterDescriptor"}],"_explicitType":"AmfphpDiscovery_MethodDescriptor"},"returnSum":{"name":"returnSum","parameters":[{"name":"number1","type":"","_explicitType":"AmfphpDiscovery_ParameterDescriptor"},{"name":"number2","type":"","_explicitType":"AmfphpDiscovery_ParameterDescriptor"}],"_explicitType":"AmfphpDiscovery_MethodDescriptor"},"returnNull":{"name":"returnNull","parameters":[],"_explicitType":"AmfphpDiscovery_MethodDescriptor"},"returnBla":{"name":"returnBla","parameters":[],"_explicitType":"AmfphpDiscovery_MethodDescriptor"},"throwException":{"name":"throwException","parameters":[{"name":"arg1","type":"","_explicitType":"AmfphpDiscovery_ParameterDescriptor"}],"_explicitType":"AmfphpDiscovery_MethodDescriptor"},"returnAfterOneSecond":{"name":"returnAfterOneSecond","parameters":[],"_explicitType":"AmfphpDiscovery_MethodDescriptor"}},"_explicitType":"AmfphpDiscovery_ServiceDescriptor"}} ');
     */

    //here '/' in each service name is replaced by '__', to avoid dealing with packages        
    foreach ($services as $service) {
        $service->name = str_replace('/', '__', $service->name);
    }

    $generatorClass = $_GET['generate'];
    $generator = $generators[$generatorClass];
    $ret = $generator->generate($services, $amfphpUrl);
    $urlSuffix = $generator->getTestUrlSuffix();
    if($ret){
        echo '<div id="content">client project written to ' . AMFPHP_BACKOFFICE_ROOTPATH . $ret ;
        
    }
    
    if($urlSuffix !== false){
    	echo '<a href="'. $ret . $urlSuffix . '"> try it here</a>';
    }
    
    echo '</div>';
}