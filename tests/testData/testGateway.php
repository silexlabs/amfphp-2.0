<?php
/* 
 * a gateway php script like the normal gateway except that it uses the test services and the plgunis in "pluginRepository"
 * @author Ariel Sommeria-klein
 */

require_once dirname(__FILE__) . '/../../Amfphp/ClassLoader.php';
require_once dirname(__FILE__) . "/TestServicesConfig.php";
$rawInputData = Amfphp_Core_Amf_Util::getRawPostData();
$gateway = new Amfphp_Core_Gateway($rawInputData);
$testServiceConfig = new TestServicesConfig();
$gateway->config->serviceFolderPaths = $testServiceConfig->serviceFolderPaths;
$gateway->config->serviceNames2ClassFindInfo = $testServiceConfig->serviceNames2ClassFindInfo;
$gateway->config->pluginsFolder = dirname(__FILE__) . "/../../pluginRepository/";
header(Amfphp_Core_Amf_Constants::CONTENT_TYPE);
echo $gateway->service();

?>
