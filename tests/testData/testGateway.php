<?php
/* 
 * a gateway php script like the normal gateway except that it uses the test services and the plgunis in "pluginRepository"
 * @author Ariel Sommeria-klein
 */

require_once dirname(__FILE__) . '/../../Amfphp/ClassLoader.php';
require_once dirname(__FILE__) . "/TestServicesConfig.php";
$config = new Amfphp_Core_Config();
$testServiceConfig = new TestServicesConfig();
$config->serviceFolderPaths = $testServiceConfig->serviceFolderPaths;
$config->serviceNames2ClassFindInfo = $testServiceConfig->serviceNames2ClassFindInfo;
$gateway = Amfphp_Core_HttpRequestGatewayFactory::createGateway($config);
header("Content-type: " . Amfphp_Core_Amf_Constants::CONTENT_TYPE);
echo $gateway->service();

?>
